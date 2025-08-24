<?php
session_start();
include 'db.php';

// Restrict to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch the question data
if (isset($_GET['id'])) {
    $question_id = $_GET['id'];
    $query = $conn->query("SELECT * FROM questions WHERE id = $question_id");
    if ($query->num_rows == 0) {
        // Redirect if the question doesn't exist
        header("Location: admin_dashboard.php");
        exit();
    }
    $question_data = $query->fetch_assoc();
}

// Handle form submission to update the question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $stmt = $conn->prepare("UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $question_id);
    $stmt->execute();

    // Redirect to admin dashboard after successful update
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Question</h3>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card p-4 shadow-sm">
        <form method="post">
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" name="question" class="form-control" value="<?= htmlspecialchars($question_data['question']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Option A</label>
                <input type="text" name="option_a" class="form-control" value="<?= htmlspecialchars($question_data['option_a']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Option B</label>
                <input type="text" name="option_b" class="form-control" value="<?= htmlspecialchars($question_data['option_b']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Option C</label>
                <input type="text" name="option_c" class="form-control" value="<?= htmlspecialchars($question_data['option_c']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Option D</label>
                <input type="text" name="option_d" class="form-control" value="<?= htmlspecialchars($question_data['option_d']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Correct Option</label>
                <select name="correct_option" class="form-select" required>
                    <option value="A" <?= $question_data['correct_option'] === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $question_data['correct_option'] === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $question_data['correct_option'] === 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $question_data['correct_option'] === 'D' ? 'selected' : '' ?>>D</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Question</button>
        </form>
    </div>
</div>
</body>
</html>
