<?php 
require_once __DIR__ . "/../includes/navbar_admin.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

if ($_SESSION["role"] !== "admin" && $_SESSION["role"] !== "staff") {
    http_response_code(403);
    die("403 Forbidden");
}

$sql = "
    SELECT f.*, u.full_name, u.email
    FROM feedback f
    JOIN users u ON f.user_id = u.user_id
    ORDER BY f.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>
<?php require_once __DIR__ . "/../includes/navbar_admin.php"; ?>

<div class="container mt-4">
    <h2>Customer Feedback</h2>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Rating</th>
            <th>Remark</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row["feedback_id"] ?></td>
                <td><?= htmlspecialchars($row["full_name"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["rating"]) ?>/5</td>
                <td><?= htmlspecialchars($row["remark"]) ?></td>
                <td><?= htmlspecialchars($row["created_at"]) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>