<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if (isset($_POST['add_question'])) {
    // Get the question and options from the form
    $question = $_POST['question'];
    $option1 = $_POST['optionA'];
    $option2 = $_POST['optionB'];
    $option3 = $_POST['optionC'];
    $option4 = $_POST['optionD'];
    $correct_option = $_POST['correct_option'];

    // Insert the new question into the database
    $stmt = $conn->prepare("INSERT INTO questions (question, option1, option2, option3, option4, correct_option) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $question, $optionA, $optionB, $optionC, $optionD, $correct_option);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Failed to add question.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Add New Question</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="question" class="form-label">Question</label>
            <textarea name="question" id="question" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="optionA" class="form-label">Option A</label>
            <input type="text" name="option1" id="optionA" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="optionB" class="form-label">Option B</label>
            <input type="text" name="option2" id="optionB" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="optionC" class="form-label">Option C</label>
            <input type="text" name="option3" id="optionC" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="optionD" class="form-label">Option D</label>
            <input type="text" name="option4" id="optionD" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="correct_option" class="form-label">Correct Option</label>
            <select name="correct_option" id="correct_option" class="form-select" required>
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
            </select>
        </div>

        <button type="submit" name="add_question" class="btn btn-primary">Add Question</button>
    </form>

    <br>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

</body>
</html>
