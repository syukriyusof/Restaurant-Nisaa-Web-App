<?php
require_once __DIR__ . "/../includes/navbar_customer.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT o.order_id, o.order_type, o.total_amount, o.status, o.order_date
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body class="container mt-4">

<h2>My Order History</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Order Type</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["order_id"] ?></td>
            <td><?= htmlspecialchars($row["order_type"]) ?></td>
            <td>RM <?= number_format($row["total_amount"], 2) ?></td>
            <td><?= htmlspecialchars($row["status"]) ?></td>
            <td><?= $row["order_date"] ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<a href="/fyp-nisaa/pages/menu.php" class="btn btn-primary">Back to Menu</a>

</body>
</html>