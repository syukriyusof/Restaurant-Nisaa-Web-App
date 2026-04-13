<?php 
require_once __DIR__ . "/../includes/navbar_admin.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

if ($_SESSION["role"] !== "admin" && $_SESSION["role"] !== "staff") {
    http_response_code(403);
    die("403 Forbidden");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reservation_id"], $_POST["status"])) {
    $reservation_id = (int) $_POST["reservation_id"];
    $status = $_POST["status"];

    $allowed = ['pending','confirmed','cancelled','completed'];

    if (in_array($status, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "
    SELECT r.*, u.full_name, u.email
    FROM reservations r
    JOIN users u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>
<?php require_once __DIR__ . "/../includes/navbar_admin.php"; ?>

<div class="container mt-4">
    <h2>Manage Reservations</h2>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Date</th>
            <th>Time</th>
            <th>Pax</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row["reservation_id"] ?></td>
                <td><?= htmlspecialchars($row["full_name"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["reservation_date"]) ?></td>
                <td><?= htmlspecialchars($row["reservation_time"]) ?></td>
                <td><?= htmlspecialchars($row["pax"]) ?></td>
                <td><?= htmlspecialchars($row["status"]) ?></td>
                <td>
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="reservation_id" value="<?= $row["reservation_id"] ?>">
                        <select name="status" class="form-select form-select-sm">
                            <option value="pending" <?= $row["status"] === "pending" ? "selected" : "" ?>>Pending</option>
                            <option value="confirmed" <?= $row["status"] === "confirmed" ? "selected" : "" ?>>Confirmed</option>
                            <option value="cancelled" <?= $row["status"] === "cancelled" ? "selected" : "" ?>>Cancelled</option>
                            <option value="completed" <?= $row["status"] === "completed" ? "selected" : "" ?>>Completed</option>
                        </select>
                        <button class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>