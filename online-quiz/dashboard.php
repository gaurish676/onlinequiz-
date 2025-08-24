<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #5f2c82, #49a09d);
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .welcome-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px 30px;
            background: rgba(0, 0, 0, 0.75);
            border-radius: 15px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h2 {
            font-size: 2.2rem;
            margin-bottom: 15px;
            color: #f8f9fa;
        }

        p {
            font-size: 1.1rem;
            color: #ccc;
            margin-bottom: 30px;
        }

        .btn-quiz, .btn-logout {
            padding: 12px 25px;
            font-size: 1.1rem;
            border-radius: 6px;
            width: 100%;
        }

        .btn-quiz {
            background-color: #49a09d;
            border: none;
            color: #fff;
            margin-bottom: 15px;
        }

        .btn-quiz:hover {
            background-color: #5f2c82;
        }

        .btn-logout {
            background-color: #dc3545;
            border: none;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="welcome-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>

    <!-- Attempt Quiz Button -->
    <a href="quiz.php" class="btn btn-quiz">Attempt Quiz</a>

    <!-- Logout Button -->
    <a href="logout.php" class="btn btn-logout">Logout</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
