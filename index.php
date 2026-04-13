<?php 
require_once __DIR__ . "/includes/auth.php";
require_login();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>

<?php require_once __DIR__ . "/includes/navbar_customer.php"; ?>

<div class="container page-section">

    <!-- Hero Carousel -->
    <div id="homepageCarousel" class="carousel slide hero-carousel mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner rounded-4 overflow-hidden">

            <div class="carousel-item active">
                <img src="/fyp-nisaa/assets/images/banners/banner1.jpg" class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption hero-caption">
                    <div class="hero-overlay">
                        <h1>Welcome to Restoran Nisaa</h1>
                        <p>Enjoy authentic meals with a modern ordering experience.</p>
                        <a href="/fyp-nisaa/pages/menu.php" class="btn btn-primary">Order Now</a>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <img src="/fyp-nisaa/assets/images/banners/banner2.jpg" class="d-block w-100" alt="Banner 2">
                <div class="carousel-caption hero-caption">
                    <div class="hero-overlay">
                        <h2>Reserve Your Table Easily</h2>
                        <p>Book your dining experience online in just a few clicks.</p>
                        <a href="/fyp-nisaa/pages/reservation.php" class="btn btn-primary">Make Reservation</a>
                    </div>
                </div>
            </div>

            <div class="carousel-item">
                <img src="/fyp-nisaa/assets/images/banners/banner3.jpg" class="d-block w-100" alt="Banner 3">
                <div class="carousel-caption hero-caption">
                    <div class="hero-overlay">
                        <h2>Earn Reward Points</h2>
                        <p>Collect points from completed orders and redeem discounts.</p>
                        <a href="/fyp-nisaa/pages/rewards.php" class="btn btn-primary">View Rewards</a>
                    </div>
                </div>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homepageCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homepageCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Welcome Section -->
    <div class="card p-4 mb-5 text-center">
        <h2>Hello, <?= htmlspecialchars($_SESSION["full_name"]) ?>!</h2>
        <p class="text-muted mb-1">Welcome back to your restaurant customer portal.</p>
        <p class="text-muted">Current Reward Points: <strong><?= htmlspecialchars($_SESSION["reward_points"]) ?></strong></p>
    </div>

    <!-- Quick Access Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card quick-action-card text-center p-4 h-100">
                <h5>Menu</h5>
                <p class="text-muted">Browse and add food items to your cart.</p>
                <a href="/fyp-nisaa/pages/menu.php" class="btn btn-primary">View Menu</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card text-center p-4 h-100">
                <h5>Reservation</h5>
                <p class="text-muted">Book your preferred table and check status.</p>
                <a href="/fyp-nisaa/pages/reservation.php" class="btn btn-outline-danger">Reserve Now</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card text-center p-4 h-100">
                <h5>Feedback</h5>
                <p class="text-muted">Share your dining experience with us.</p>
                <a href="/fyp-nisaa/pages/feedback.php" class="btn btn-outline-dark">Give Feedback</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card text-center p-4 h-100">
                <h5>Rewards</h5>
                <p class="text-muted">Track points and redeem loyalty discounts.</p>
                <a href="/fyp-nisaa/pages/rewards.php" class="btn btn-warning">View Rewards</a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>