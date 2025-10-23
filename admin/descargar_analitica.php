<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../index.html");
  exit();
}

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
  ->setCreator("TecnoStore Guate")
  ->setTitle("Reporte de Analítica")
  ->setDescription("Reporte generado automáticamente con PhpSpreadsheet");

// 🔹 Función para aplicar estilo profesional
function aplicarEstiloEncabezado($sheet, $rango) {
  $sheet->getStyle($rango)->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
  ]);
}

// 🔹 Ventas por día
$ventas = $conn->query("SELECT DATE(fecha) AS dia, SUM(total) AS ingresos FROM pedidos GROUP BY dia ORDER BY dia DESC");
$hojaVentas = $spreadsheet->createSheet();
$hojaVentas->setTitle('Ventas por Día');
$hojaVentas->setCellValue('A1', 'Fecha');
$hojaVentas->setCellValue('B1', 'Ingresos');
aplicarEstiloEncabezado($hojaVentas, 'A1:B1');
$fila = 2;
while ($v = $ventas->fetch_assoc()) {
  $hojaVentas->setCellValue("A$fila", $v['dia']);
  $hojaVentas->setCellValue("B$fila", $v['ingresos']);
  $fila++;
}
$hojaVentas->getColumnDimension('A')->setAutoSize(true);
$hojaVentas->getColumnDimension('B')->setAutoSize(true);

// 🔹 Productos más vendidos
$productos = $conn->query("SELECT p.nombre, SUM(dp.cantidad) AS vendidos FROM detalle_pedido dp JOIN productos p ON dp.producto_id = p.id GROUP BY p.id ORDER BY vendidos DESC");
$hojaProductos = $spreadsheet->createSheet();
$hojaProductos->setTitle('Productos Vendidos');
$hojaProductos->setCellValue('A1', 'Producto');
$hojaProductos->setCellValue('B1', 'Unidades Vendidas');
aplicarEstiloEncabezado($hojaProductos, 'A1:B1');
$fila = 2;
while ($p = $productos->fetch_assoc()) {
  $hojaProductos->setCellValue("A$fila", $p['nombre']);
  $hojaProductos->setCellValue("B$fila", $p['vendidos']);
  $fila++;
}
$hojaProductos->getColumnDimension('A')->setAutoSize(true);
$hojaProductos->getColumnDimension('B')->setAutoSize(true);

// 🔹 Ingresos por método de pago
$pagos = $conn->query("SELECT IFNULL(metodo_pago, 'Sin especificar') AS metodo, SUM(total) AS ingresos FROM pedidos GROUP BY metodo");
$hojaPagos = $spreadsheet->createSheet();
$hojaPagos->setTitle('Métodos de Pago');
$hojaPagos->setCellValue('A1', 'Método');
$hojaPagos->setCellValue('B1', 'Ingresos');
aplicarEstiloEncabezado($hojaPagos, 'A1:B1');
$fila = 2;
while ($p = $pagos->fetch_assoc()) {
  $hojaPagos->setCellValue("A$fila", $p['metodo']);
  $hojaPagos->setCellValue("B$fila", $p['ingresos']);
  $fila++;
}
$hojaPagos->getColumnDimension('A')->setAutoSize(true);
$hojaPagos->getColumnDimension('B')->setAutoSize(true);

// 🔹 Visitas por página
$visitas = $conn->query("SELECT pagina, COUNT(*) AS total FROM visitas GROUP BY pagina");
$hojaVisitas = $spreadsheet->createSheet();
$hojaVisitas->setTitle('Visitas por Página');
$hojaVisitas->setCellValue('A1', 'Página');
$hojaVisitas->setCellValue('B1', 'Visitas');
aplicarEstiloEncabezado($hojaVisitas, 'A1:B1');
$fila = 2;
while ($v = $visitas->fetch_assoc()) {
  $hojaVisitas->setCellValue("A$fila", $v['pagina']);
  $hojaVisitas->setCellValue("B$fila", $v['total']);
  $fila++;
}
$hojaVisitas->getColumnDimension('A')->setAutoSize(true);
$hojaVisitas->getColumnDimension('B')->setAutoSize(true);

// 🔹 Clientes frecuentes
$clientes = $conn->query("SELECT usuario, COUNT(*) AS pedidos FROM pedidos GROUP BY usuario ORDER BY pedidos DESC");
$hojaClientes = $spreadsheet->createSheet();
$hojaClientes->setTitle('Clientes Frecuentes');
$hojaClientes->setCellValue('A1', 'Usuario');
$hojaClientes->setCellValue('B1', 'Pedidos');
aplicarEstiloEncabezado($hojaClientes, 'A1:B1');
$fila = 2;
while ($c = $clientes->fetch_assoc()) {
  $hojaClientes->setCellValue("A$fila", $c['usuario']);
  $hojaClientes->setCellValue("B$fila", $c['pedidos']);
  $fila++;
}
$hojaClientes->getColumnDimension('A')->setAutoSize(true);
$hojaClientes->getColumnDimension('B')->setAutoSize(true);

// 🔹 Métricas generales
$hojaResumen = $spreadsheet->getSheet(0);
$hojaResumen->setTitle('Resumen');
$hojaResumen->setCellValue('A1', 'Métrica');
$hojaResumen->setCellValue('B1', 'Valor');
aplicarEstiloEncabezado($hojaResumen, 'A1:B1');

$total_visitas = $conn->query("SELECT COUNT(*) AS total FROM visitas")->fetch_assoc()['total'];
$total_pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'];
$tasa_conversion = $total_visitas > 0 ? round(($total_pedidos / $total_visitas) * 100, 2) : 0;
$promedio = $conn->query("SELECT AVG(total) AS promedio FROM pedidos")->fetch_assoc()['promedio'];

$hojaResumen->setCellValue('A2', 'Total de Visitas');
$hojaResumen->setCellValue('B2', $total_visitas);
$hojaResumen->setCellValue('A3', 'Total de Pedidos');
$hojaResumen->setCellValue('B3', $total_pedidos);
$hojaResumen->setCellValue('A4', 'Tasa de Conversión');
$hojaResumen->setCellValue('B4', $tasa_conversion . '%');
$hojaResumen->setCellValue('A5', 'Promedio por Pedido');
$hojaResumen->setCellValue('B5', 'Q' . number_format($promedio, 2));
$hojaResumen->getColumnDimension('A')->setAutoSize(true);
$hojaResumen->getColumnDimension('B')->setAutoSize(true);

// 🔻 Descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Analitica.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
