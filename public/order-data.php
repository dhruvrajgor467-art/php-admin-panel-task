<?php
require_once __DIR__ . '/../auth/check.php';
require_once __DIR__ . '/../config/database.php';

/* DataTables params */
$draw   = intval($_POST['draw']);
$start  = intval($_POST['start']);
$length = intval($_POST['length']);
$search = $_POST['search']['value'] ?? '';

/* Custom filters */
$region  = $_POST['region'] ?? '';
$country = $_POST['country'] ?? '';
$from    = $_POST['from'] ?? '';
$to      = $_POST['to'] ?? '';

$where = [];
$params = [];

/* Global search */
if ($search) {
    $where[] = "(order_id LIKE ? OR region LIKE ? OR country LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

/* Filters */
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

/* Total records */
$total = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

/* Filtered records */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders $whereSql");
$stmt->execute($params);
$filtered = $stmt->fetchColumn();

/* Data */
$sql = "
    SELECT order_id, region, country, order_date, total_revenue
    FROM orders
    $whereSql
    ORDER BY order_date DESC
    LIMIT $length OFFSET $start
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

/* JSON response */
echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total,
    'recordsFiltered' => $filtered,
    'data'            => array_map(fn($o) => [
        $o['order_id'],
        $o['region'],
        $o['country'],
        $o['order_date'],
        number_format($o['total_revenue'], 2)
    ], $data)
]);
