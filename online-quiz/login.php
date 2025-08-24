<?php
session_start();
include 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No user found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #5f2c82, #49a09d);
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h3 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            color: #f8f9fa;
        }

        .form-label {
            font-size: 1.1rem;
            color: #f8f9fa;
        }

        .form-control {
            background-color: #333;
            color: #fff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #444;
            border-color: #888;
        }

        .btn-login {
            background-color: #49a09d;
            border-color: #49a09d;
            color: white;
            padding: 10px 20px;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #5f2c82;
            border-color: #5f2c82;
        }

        .alert {
            margin-top: 20px;
            text-align: center;
        }

        .register-link {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #49a09d;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h3>Login</h3>

    <!-- Display login errors if any -->
    <?php if ($message): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login">Login</button>

        <div class="text-center mt-3">
            <a href="register.php" class="register-link">Don't have an account? Register</a>
        </div>
    </form>
</div>

<!-- Bootstrap JS (Optional for dynamic features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
