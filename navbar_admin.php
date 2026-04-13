<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/fyp-nisaa/admin/dashboard.php">Admin Panel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/fyp-nisaa/admin/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/fyp-nisaa/admin/orders.php">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/fyp-nisaa/admin/reservations.php">Reservations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/fyp-nisaa/admin/feedbacks.php">Feedback</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/fyp-nisaa/admin/rewards.php">Rewards</a>
                </li>
            </ul>

            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($_SESSION["full_name"] ?? "Admin") ?> (<?= htmlspecialchars($_SESSION["role"] ?? "") ?>)
            </span>
            <a class="btn btn-outline-light btn-sm" href="/fyp-nisaa/auth/logout.php">Logout</a>
        </div>
    </div>
</nav>