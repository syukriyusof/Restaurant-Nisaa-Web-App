<?php
require_once __DIR__ . "/../includes/navbar_admin.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

if ($_SESSION["role"] !== "admin" && $_SESSION["role"] !== "staff") {
    http_response_code(403);
    die("403 Forbidden");
}

// ===== Summary cards =====
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$totalReservations = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'] ?? 0;
$totalFeedback = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'] ?? 0;
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='customer'")->fetch_assoc()['total'] ?? 0;

// ===== Chart 1: Orders analytics range =====
$range = $_GET['range'] ?? '7days';

$orderLabels = [];
$orderData = [];

if ($range === '30days') {
    $chartTitle = 'Orders in Last 30 Days';
    $intervalDays = 29;

    $orderQuery = "
        SELECT DATE(order_date) AS order_day, COUNT(*) AS total_orders
        FROM orders
        WHERE order_date >= CURDATE() - INTERVAL 29 DAY
        GROUP BY DATE(order_date)
        ORDER BY order_day ASC
    ";

    $orderResult = $conn->query($orderQuery);

    $orderMap = [];
    while ($row = $orderResult->fetch_assoc()) {
        $orderMap[$row['order_day']] = (int)$row['total_orders'];
    }

    for ($i = $intervalDays; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $orderLabels[] = date('d M', strtotime($date));
        $orderData[] = $orderMap[$date] ?? 0;
    }

} elseif ($range === '1year') {
    $chartTitle = 'Orders in Last 1 Year';

    $orderQuery = "
        SELECT DATE_FORMAT(order_date, '%Y-%m') AS order_month, COUNT(*) AS total_orders
        FROM orders
        WHERE order_date >= CURDATE() - INTERVAL 1 YEAR
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
        ORDER BY order_month ASC
    ";

    $orderResult = $conn->query($orderQuery);

    $orderMap = [];
    while ($row = $orderResult->fetch_assoc()) {
        $orderMap[$row['order_month']] = (int)$row['total_orders'];
    }

    for ($i = 11; $i >= 0; $i--) {
        $monthKey = date('Y-m', strtotime("-$i months"));
        $orderLabels[] = date('M Y', strtotime($monthKey . '-01'));
        $orderData[] = $orderMap[$monthKey] ?? 0;
    }

} else {
    $chartTitle = 'Orders in Last 7 Days';
    $intervalDays = 6;

    $orderQuery = "
        SELECT DATE(order_date) AS order_day, COUNT(*) AS total_orders
        FROM orders
        WHERE order_date >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(order_date)
        ORDER BY order_day ASC
    ";

    $orderResult = $conn->query($orderQuery);

    $orderMap = [];
    while ($row = $orderResult->fetch_assoc()) {
        $orderMap[$row['order_day']] = (int)$row['total_orders'];
    }

    for ($i = $intervalDays; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $orderLabels[] = date('d M', strtotime($date));
        $orderData[] = $orderMap[$date] ?? 0;
    }
}

// ===== Chart 2: Reservation status distribution =====
$reservationStatusLabels = ['Pending', 'Confirmed', 'Cancelled', 'Completed'];
$reservationStatusData = [0, 0, 0, 0];

$reservationQuery = "
    SELECT status, COUNT(*) AS total
    FROM reservations
    GROUP BY status
";
$reservationResult = $conn->query($reservationQuery);

while ($row = $reservationResult->fetch_assoc()) {
    switch ($row['status']) {
        case 'pending':
            $reservationStatusData[0] = (int)$row['total'];
            break;
        case 'confirmed':
            $reservationStatusData[1] = (int)$row['total'];
            break;
        case 'cancelled':
            $reservationStatusData[2] = (int)$row['total'];
            break;
        case 'completed':
            $reservationStatusData[3] = (int)$row['total'];
            break;
    }
}

// ===== Chart 3: Feedback ratings =====
$feedbackLabels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
$feedbackData = [0, 0, 0, 0, 0];

$feedbackQuery = "
    SELECT rating, COUNT(*) AS total
    FROM feedback
    GROUP BY rating
    ORDER BY rating ASC
";
$feedbackResult = $conn->query($feedbackQuery);

while ($row = $feedbackResult->fetch_assoc()) {
    $rating = (int)$row['rating'];
    if ($rating >= 1 && $rating <= 5) {
        $feedbackData[$rating - 1] = (int)$row['total'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/navbar_admin.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <h5>Total Orders</h5>
                <h2><?= $totalOrders ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <h5>Pending Orders</h5>
                <h2><?= $pendingOrders ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <h5>Total Reservations</h5>
                <h2><?= $totalReservations ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <h5>Total Feedback</h5>
                <h2><?= $totalFeedback ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <h5>Total Customers</h5>
                <h2><?= $totalUsers ?></h2>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><?= htmlspecialchars($chartTitle) ?></h5>

    <form method="GET" class="d-flex align-items-center gap-2">
        <label for="range" class="mb-0">View:</label>
        <select name="range" id="range" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="7days" <?= $range === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
            <option value="30days" <?= $range === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
            <option value="1year" <?= $range === '1year' ? 'selected' : '' ?>>Last 1 Year</option>
        </select>
    </form>
</div>

<canvas id="ordersChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <h5 class="mb-3">Reservation Status Distribution</h5>
                <canvas id="reservationChart"></canvas>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card shadow-sm p-3">
                <h5 class="mb-3">Customer Feedback Ratings</h5>
                <canvas id="feedbackChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
const orderLabels = <?= json_encode($orderLabels) ?>;
const orderData = <?= json_encode($orderData) ?>;

const reservationLabels = <?= json_encode($reservationStatusLabels) ?>;
const reservationData = <?= json_encode($reservationStatusData) ?>;

const feedbackLabels = <?= json_encode($feedbackLabels) ?>;
const feedbackData = <?= json_encode($feedbackData) ?>;

// Orders line chart
new Chart(document.getElementById('ordersChart'), {
    type: 'line',
    data: {
        labels: orderLabels,
        datasets: [{
            label: 'Orders',
            data: orderData,
            borderWidth: 2,
            fill: false,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

// Reservation pie chart
new Chart(document.getElementById('reservationChart'), {
    type: 'pie',
    data: {
        labels: reservationLabels,
        datasets: [{
            data: reservationData
        }]
    },
    options: {
        responsive: true
    }
});

// Feedback bar chart
new Chart(document.getElementById('feedbackChart'), {
    type: 'bar',
    data: {
        labels: feedbackLabels,
        datasets: [{
            label: 'Number of Feedback',
            data: feedbackData,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>

</body>
</html>