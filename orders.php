<?php
require_once __DIR__ . "/../includes/navbar_admin.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

if ($_SESSION["role"] !== "admin" && $_SESSION["role"] !== "staff") {
    http_response_code(403);
    echo "403 Forbidden";
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["order_id"], $_POST["status"])) {
    $order_id = (int) $_POST["order_id"];
    $status = $_POST["status"];

    $allowed = ['pending', 'preparing', 'ready', 'completed', 'cancelled'];

    if (in_array($status, $allowed, true)) {

        // Get existing order data
        $stmt = $conn->prepare("SELECT user_id, total_amount, status FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $resultOrder = $stmt->get_result();
        $orderData = $resultOrder->fetch_assoc();
        $stmt->close();

        if ($orderData) {
            $previousStatus = $orderData["status"];

            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $status, $order_id);
            $stmt->execute();
            $stmt->close();

            // Add reward points only once when changing to completed
            if ($status === "completed" && $previousStatus !== "completed") {
                $user_id = $orderData["user_id"];
                $points = floor($orderData["total_amount"]); // RM1 = 1 point

                $stmt = $conn->prepare("UPDATE users SET reward_points = reward_points + ? WHERE user_id = ?");
                $stmt->bind_param("ii", $points, $user_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

// Get all orders with customer names
$sql = "
    SELECT o.order_id, o.order_type, o.total_amount, o.status, o.order_date,
           u.full_name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body class="container mt-4">

<h2>Manage Orders</h2>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Order Type</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Update Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["order_id"] ?></td>
            <td><?= htmlspecialchars($row["full_name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= htmlspecialchars($row["order_type"]) ?></td>
            <td>RM <?= number_format($row["total_amount"], 2) ?></td>
            <td><strong><?= htmlspecialchars($row["status"]) ?></strong></td>
            <td><?= $row["order_date"] ?></td>
            <td>
                <form method="POST" class="d-flex gap-2">
                    <input type="hidden" name="order_id" value="<?= $row["order_id"] ?>">
                    <select name="status" class="form-select form-select-sm">
                        <option value="pending" <?= $row["status"] === "pending" ? "selected" : "" ?>>Pending</option>
                        <option value="preparing" <?= $row["status"] === "preparing" ? "selected" : "" ?>>Preparing</option>
                        <option value="ready" <?= $row["status"] === "ready" ? "selected" : "" ?>>Ready</option>
                        <option value="completed" <?= $row["status"] === "completed" ? "selected" : "" ?>>Completed</option>
                        <option value="cancelled" <?= $row["status"] === "cancelled" ? "selected" : "" ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<a href="/fyp-nisaa/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

</body>
</html>