<?php
require_once __DIR__ . "/../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user["password_hash"])) {
            if ($user["role"] !== "admin" && $user["role"] !== "staff") {
                $errors[] = "Access denied. Admin account required.";
            } else {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                header("Location: /fyp-nisaa/admin/dashboard.php");
                exit();
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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
            max-width: 460px;
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
            <p>Admin / Staff Login Portal</p>
        </div>

        <div class="auth-body">
            <h3>Admin Login</h3>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-theme w-100 py-2">Admin Login</button>
            </form>

            <div class="auth-footer-link">
                <a href="/fyp-nisaa/auth/login.php">Customer Login</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>