<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/fyp-nisaa/index.php">Restoran Nisaa</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/fyp-nisaa/pages/menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="/fyp-nisaa/pages/order_history.php">My Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="/fyp-nisaa/pages/reservation.php">Reservation</a></li>
                <li class="nav-item"><a class="nav-link" href="/fyp-nisaa/pages/feedback.php">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="/fyp-nisaa/pages/rewards.php">Rewards</a></li>
            </ul>

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </span>

            <a href="/fyp-nisaa/auth/logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>