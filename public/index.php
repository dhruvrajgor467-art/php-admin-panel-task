<?php
require '../auth/check.php';
require_once __DIR__ . '/../config/database.php';

/* Total orders */
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

/* Total profit */
$totalProfit = $pdo->query("SELECT SUM(total_profit) FROM orders")->fetchColumn();

/* Orders per month */
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(order_date, '%b') AS month,
        COUNT(*) AS total
    FROM orders
    GROUP BY MONTH(order_date), DATE_FORMAT(order_date, '%b')
    ORDER BY MONTH(order_date)
");
$ordersPerMonth = $stmt->fetchAll();

/* Revenue per month */
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(order_date, '%b') AS month,
        SUM(total_profit) AS revenue
    FROM orders
    GROUP BY MONTH(order_date), DATE_FORMAT(order_date, '%b')
    ORDER BY MONTH(order_date)
");
$revenuePerMonth = $stmt->fetchAll();

$months = array_column($ordersPerMonth, 'month');
$orderCounts = array_column($ordersPerMonth, 'total');
$revenues = array_column($revenuePerMonth, 'revenue');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">Admin Panel</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>"
                       href="/index.php">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>"
                       href="/orders.php">
                        Orders
                    </a>
                </li>

            </ul>

            <a href="/logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                <h5 class="mb-0">Welcome, Admin 👋</h5>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h3><?= $totalOrders ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Profit</h6>
                    <h3>₹<?= number_format($totalProfit, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Orders Per Month</h5>
            <canvas id="ordersChart"></canvas>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Revenue Per Month</h5>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script>
const ordersChart = new Chart(document.getElementById('ordersChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode($orderCounts) ?>,
        }]
    }
});

const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?= json_encode($revenues) ?>,
            tension: 0.3
        }]
    }
});
</script>

</body>
</html>
