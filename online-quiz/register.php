<?php
include 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $message = "Registration successful. <a href='login.php' class='text-primary'>Login here</a>.";
    } else {
        $message = "Error: Username may already exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #5f2c82, #49a09d);
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .register-container {
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

        .btn-register {
            background-color: #49a09d;
            border-color: #49a09d;
            color: white;
            padding: 10px 20px;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-register:hover {
            background-color: #5f2c82;
            border-color: #5f2c82;
        }

        .alert {
            margin-top: 20px;
            text-align: center;
        }

        .login-link {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #49a09d;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h3>Register</h3>

    <!-- Display registration errors if any -->
    <?php if ($message): ?>
        <div class="alert alert-info text-center">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-register">Register</button>

        <div class="text-center mt-3">
            <a href="login.php" class="login-link">Already have an account? Login</a>
        </div>
    </form>
</div>

<!-- Bootstrap JS (Optional for dynamic features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
