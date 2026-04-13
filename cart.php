<?php
require_once __DIR__ . "/../includes/auth.php";
require_login();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove item
if (isset($_GET['remove'])) {
    $remove_id = (int) $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: /fyp-nisaa/pages/cart.php");
    exit();
}

// Update quantity
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $item_id => $qty) {
        $qty = (int) $qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$item_id]);
        } else {
            $_SESSION['cart'][$item_id]['quantity'] = $qty;
        }
    }
    header("Location: /fyp-nisaa/pages/cart.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section">
    <h2 class="mb-4">My Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="/fyp-nisaa/pages/menu.php" class="btn btn-primary">Back to Menu</a>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="update_cart" value="1">

            <div class="table-responsive">
                <table class="table table-bordered align-middle bg-white">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th width="120">Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item_id => $item): ?>
                            <?php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td>RM <?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <input type="number" name="quantities[<?= $item_id ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control">
                                </td>
                                <td>RM <?= number_format($subtotal, 2) ?></td>
                                <td>
                                    <a href="/fyp-nisaa/pages/cart.php?remove=<?= $item_id ?>" class="btn btn-sm btn-danger">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <h4>Total: RM <?= number_format($total, 2) ?></h4>
                <div>
                    <button type="submit" class="btn btn-secondary">Update Cart</button>
                    <a href="/fyp-nisaa/pages/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>