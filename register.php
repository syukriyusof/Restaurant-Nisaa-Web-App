<?php
require_once __DIR__ . "/../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($full_name === "") $errors[] = "Full name is required.";
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === "" || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered. Please login.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = "customer";

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $email, $phone, $password_hash, $role);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/fyp-nisaa/assets/css">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa, #f1f3f5);
        }

        .auth-card {
            max-width: 520px;
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
        }

        .auth-header {
            background-color: #C41E3A;
            color: white;
            text-align: center;
            padding: 28px 20px;
        }

        .auth-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }

        .auth-header p {
            margin: 8px 0 0;
            opacity: 0.9;
        }

        .auth-body {
            padding: 32px;
            background: white;
        }

        .auth-body h3 {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-footer-link {
            text-align: center;
            margin-top: 18px;
        }

        .btn-theme {
            background-color: #C41E3A;
            border: none;
            color: white;
        }

        .btn-theme:hover {
            background-color: #a3192f;
            color: white;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="card shadow auth-card">
        <div class="auth-header">
            <h1>Restoran Nisaa</h1>
            <p>Customer Registration Portal</p>
        </div>

        <div class="auth-body">
            <h3>Create Account</h3>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone (optional)</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button class="btn btn-theme w-100 py-2">Register</button>
            </form>

            <div class="auth-footer-link">
                Already have an account? <a href="/fyp-nisaa/auth/login.php">Login</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>