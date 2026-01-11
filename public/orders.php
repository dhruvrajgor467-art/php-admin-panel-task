<?php require_once __DIR__ . '/../auth/check.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

<div class="container mt-4">
    <h3>Orders</h3>

    <!-- Filters -->
    <div class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="text" id="region" class="form-control" placeholder="Region">
        </div>
        <div class="col-md-2">
            <input type="text" id="country" class="form-control" placeholder="Country">
        </div>
        <div class="col-md-2">
            <input type="date" id="from" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" id="to" class="form-control">
        </div>
        <div class="col-md-2">
            <button id="filterBtn" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <button id="exportBtn" class="btn btn-success w-100">
                Export CSV
            </button>
        </div>
    </div>

    <!-- Table -->
    <table id="ordersTable" class="table table-bordered table-sm">
        <thead class="table-dark">
        <tr>
            <th>Order ID</th>
            <th>Region</th>
            <th>Country</th>
            <th>Order Date</th>
            <th>Revenue</th>
        </tr>
        </thead>
    </table>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let table = $('#ordersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/order-data.php',
        type: 'POST',
        data: function (d) {
            d.region  = $('#region').val();
            d.country = $('#country').val();
            d.from    = $('#from').val();
            d.to      = $('#to').val();
        }
    }
});

$('#filterBtn').on('click', function () {
    table.draw();
});


$('#exportBtn').on('click', function () {
    const params = new URLSearchParams({
        region: $('#region').val(),
        country: $('#country').val(),
        from: $('#from').val(),
        to: $('#to').val()
    });

    window.location = '/orders-export.php?' + params.toString();
});

</script>

</body>
</html>
