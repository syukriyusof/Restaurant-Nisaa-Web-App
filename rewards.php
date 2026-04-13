<?php 
require_once __DIR__ . "/../includes/navbar_customer.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT full_name, reward_points FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$points = (int) $user["reward_points"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reward Points</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section" style="max-width:700px;">
    <h2 class="mb-4">My Reward Points</h2>

    <div class="card shadow-sm p-4 text-center">
        <h4><?= htmlspecialchars($user["full_name"]) ?></h4>
        <p class="mb-2">Current Reward Points</p>
        <h1 class="text-primary"><?= $points ?></h1>

        <hr>

        <p><strong>Redemption Rule:</strong> 100 points = RM5 discount</p>

        <?php if ($points >= 100): ?>
            <div class="alert alert-success">
                You can redeem your points during checkout.
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">
                You need <?= 100 - $points ?> more points to redeem RM5 discount.
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>