<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['cart'])) {
    header("Location: /fyp-nisaa/pages/cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$errors = [];

// Get latest user reward points
$stmt = $conn->prepare("SELECT reward_points FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultUser = $stmt->get_result();
$userData = $resultUser->fetch_assoc();
$stmt->close();

$currentPoints = (int) ($userData['reward_points'] ?? 0);

$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

$discount = 0;
$pointsToRedeem = 0;

// Redemption rule
// 100 points = RM5 discount
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_type = $_POST['order_type'] ?? 'dine-in';
    $use_points = isset($_POST['use_points']) ? 1 : 0;

    $allowed_types = ['dine-in', 'takeaway'];

    if (!in_array($order_type, $allowed_types, true)) {
        $errors[] = "Invalid order type.";
    } else {
        if ($use_points && $currentPoints >= 100) {
            $pointsToRedeem = 100;
            $discount = 5.00;
        }

        $final_total = max($total_amount - $discount, 0);

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, total_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $user_id, $order_type, $final_total);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Insert order items
            foreach ($_SESSION['cart'] as $item_id => $item) {
                $quantity = $item['quantity'];
                $subtotal = $item['price'] * $quantity;

                $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $item_id, $quantity, $subtotal);
                $stmt->execute();
                $stmt->close();
            }

            // Deduct points if redeemed
            if ($pointsToRedeem > 0) {
                $stmt = $conn->prepare("UPDATE users SET reward_points = reward_points - ? WHERE user_id = ?");
                $stmt->bind_param("ii", $pointsToRedeem, $user_id);
                $stmt->execute();
                $stmt->close();

                // Update session value too
                $_SESSION['reward_points'] -= $pointsToRedeem;
            }

            $_SESSION['cart'] = [];
            $message = "Checkout successful! Your order has been placed.";
        } else {
            $errors[] = "Failed to place order.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section" style="max-width:700px;">
    <h2 class="mb-4">Checkout</h2>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($message) ?>
        </div>
        <a href="/fyp-nisaa/pages/order_history.php" class="btn btn-primary">View Order History</a>

    <?php elseif (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">Order Summary</h5>

            <p><strong>Total Amount:</strong> RM <?= number_format($total_amount, 2) ?></p>
            <p><strong>Current Reward Points:</strong> <?= $currentPoints ?></p>

            <?php if ($currentPoints >= 100): ?>
                <div class="alert alert-info">
                    You are eligible to redeem <strong>100 points</strong> for <strong>RM5 discount</strong>.
                </div>
            <?php else: ?>
                <div class="alert alert-secondary">
                    You need at least <strong>100 points</strong> to redeem RM5 discount.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Order Type</label>
                    <select name="order_type" class="form-select" required>
                        <option value="dine-in">Dine-in</option>
                        <option value="takeaway">Takeaway</option>
                    </select>
                </div>

                <div class="alert alert-warning">
                    <strong>Notice:</strong> Restoran Nisaa only accepts <strong>dine-in</strong> and <strong>takeaway</strong> orders. Delivery is not available.
                </div>

                <?php if ($currentPoints >= 100): ?>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="use_points" id="use_points">
                        <label class="form-check-label" for="use_points">
                            Redeem 100 points for RM5 discount
                        </label>
                    </div>
                <?php endif; ?>

                <button class="btn btn-primary w-100">Confirm Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>