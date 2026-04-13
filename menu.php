<?php
require_once __DIR__ . "/../includes/navbar_customer.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

// Add item to cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["item_id"])) {
    $item_id = (int) $_POST["item_id"];

    $stmt = $conn->prepare("SELECT item_id, item_name, price, image, description FROM menu_items WHERE item_id = ? AND status='available'");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $resultItem = $stmt->get_result();
    $item = $resultItem->fetch_assoc();
    $stmt->close();

    if ($item) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$item_id] = [
                'item_name' => $item['item_name'],
                'price' => $item['price'],
                'quantity' => 1,
                'image' => $item['image'],
                'description' => $item['description']
            ];
        }

        $message = $item['item_name'] . " added to cart.";
    } else {
        $message = "Menu item not found.";
    }
}

$result = $conn->query("SELECT * FROM menu_items WHERE status='available'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Restaurant Menu</h2>
        <a href="/fyp-nisaa/pages/cart.php" class="btn btn-outline-dark">
            View Cart
            <?php
            $cartCount = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $cartItem) {
                    $cartCount += $cartItem['quantity'];
                }
            }
            ?>
            <span class="badge bg-danger"><?= $cartCount ?></span>
        </a>
    </div>

<div class="menu-disclaimer mb-4">
    <strong>Notice:</strong> All payments are made physically at the restaurant. 
    Restoran Nisaa currently accepts <strong>dine-in</strong> and <strong>takeaway</strong> orders only.
</div>


    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($item = $result->fetch_assoc()): ?>
            <?php $img = !empty($item['image']) ? $item['image'] : 'default-food.jpg'; ?>
            <div class="col-md-4 mb-4">
                <div class="card menu-card h-100">
                    <img src="/fyp-nisaa/assets/images/<?= rawurlencode($img) ?>"
                         class="card-img-top"
                         style="height:200px; object-fit:cover;"
                         alt="<?= htmlspecialchars($item['item_name']) ?>">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($item['item_name']) ?></h5>
                        <p class="card-text text-muted small"><?= htmlspecialchars($item['description']) ?></p>
                        <h5 class="price mb-3">RM <?= number_format($item['price'], 2) ?></h5>

                        <form method="POST" class="mt-auto">
                            <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                            <button class="btn btn-primary w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No menu items available.</p>
    <?php endif; ?>
    </div>
</div>

</body>
</html>