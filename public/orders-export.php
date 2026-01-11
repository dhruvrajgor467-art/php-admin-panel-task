<?php
require_once __DIR__ . '/../auth/check.php';
require_once __DIR__ . '/../config/database.php';

/* Filters */
$region  = $_GET['region'] ?? '';
$country = $_GET['country'] ?? '';
$from    = $_GET['from'] ?? '';
$to      = $_GET['to'] ?? '';

$where = [];
$params = [];

/* Same filter logic as DataTable */
if ($region) {
    $where[] = "region = ?";
    $params[] = $region;
}

if ($country) {
    $where[] = "country = ?";
    $params[] = $country;
}

if ($from && $to) {
    $where[] = "order_date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT 
        order_id,
        region,
        country,
        item_type,
        order_date,
        total_revenue,
        total_profit
    FROM orders
    $whereSql
    ORDER BY order_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

/* CSV Headers */
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_export_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'w');

/* CSV column headers */
fputcsv($output, [
    'Order ID',
    'Region',
    'Country',
    'Item Type',
    'Order Date',
    'Revenue',
    'Profit'
]);

/* Data rows */
foreach ($orders as $o) {
    fputcsv($output, [
        $o['order_id'],
        $o['region'],
        $o['country'],
        $o['item_type'],
        $o['order_date'],
        $o['total_revenue'],
        $o['total_profit']
    ]);
}

fclose($output);
exit;
