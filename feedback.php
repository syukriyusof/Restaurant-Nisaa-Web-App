<?php 
require_once __DIR__ . "/../includes/navbar_customer.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";

require_login();

$message = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $rating = (int) ($_POST["rating"] ?? 0);
    $remark = trim($_POST["remark"] ?? "");

    if ($rating < 1 || $rating > 5) {
        $errors[] = "Please select a valid rating.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, remark) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $rating, $remark);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
        } else {
            $errors[] = "Failed to submit feedback.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
</head>
<body>
<?php require_once __DIR__ . "/../includes/navbar_customer.php"; ?>

<div class="container page-section" style="max-width: 900px;">
    <h2>Submit Feedback</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="card card-body shadow-sm">
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                <option value="">Select rating</option>
                <option value="1">1 - Very Poor</option>
                <option value="2">2 - Poor</option>
                <option value="3">3 - Average</option>
                <option value="4">4 - Good</option>
                <option value="5">5 - Excellent</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="4" placeholder="Write your feedback here..."></textarea>
        </div>

        <button class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

</body>
</html>