<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$score = null;
$attempted = $conn->query("SELECT * FROM results WHERE user_id = $user_id")->num_rows > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$attempted) {
    $correct = 0;
    $total = isset($_POST['answers']) && is_array($_POST['answers']) ? count($_POST['answers']) : 0;

    if ($total > 0) {
        foreach ($_POST['answers'] as $id => $user_answer) {
            $query = $conn->query("SELECT correct_option FROM questions WHERE id = $id");
            $correct_option = $query->fetch_assoc()['correct_option'];
            if ($user_answer === $correct_option) $correct++;
        }
    }

    $stmt = $conn->prepare("INSERT INTO results (user_id, score, total) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $correct, $total);
    $stmt->execute();

    $score = ($total === 0) ? "You didn't attempt any questions." : "You scored $correct out of $total.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Take Quiz</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #5f2c82, #49a09d);
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
        }

        .quiz-container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 40px;
            margin: 50px auto;
            border-radius: 15px;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h3, h4, h5 {
            color: #f8f9fa;
        }

        .form-check-label {
            color: #ddd;
        }

        .form-check-input:checked ~ .form-check-label {
            color: #fff;
        }

        .alert {
            font-size: 1.1rem;
        }

        .btn-primary {
            background-color: #49a09d;
            border: none;
        }

        .btn-primary:hover {
            background-color: #3c7f7d;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #b02a37;
        }

        .question-block {
            background-color: rgba(255, 255, 255, 0.05);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .timer-box {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="quiz-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h4 class="mb-3">Quiz</h4>

        <?php if ($score): ?>
            <div class="alert alert-success text-center"><?= $score ?></div>
        <?php elseif ($attempted): ?>
            <div class="alert alert-info text-center">You have already attempted the quiz.</div>
        <?php else: ?>
            <div class="timer-box text-center" id="timer">Time Remaining: 02:00</div>

            <form method="post" id="quizForm">
                <?php
                $questions = $conn->query("SELECT * FROM questions");
                while ($row = $questions->fetch_assoc()):
                    $qid = $row['id'];
                ?>
                    <div class="question-block">
                        <p><strong><?= $row['question'] ?></strong></p>
                        <?php foreach (['A', 'B', 'C', 'D'] as $option): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[<?= $qid ?>]" value="<?= $option ?>" required>
                                <label class="form-check-label">
                                    <?= $row["option_" . strtolower($option)] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="btn btn-primary w-100">Submit Quiz</button>
            </form>

            <!-- Timer Script -->
            <script>
                let duration = 2 * 60;
                const timer = document.getElementById('timer');
                const form = document.getElementById('quizForm');

                const interval = setInterval(() => {
                    let minutes = Math.floor(duration / 60);
                    let seconds = duration % 60;
                    timer.textContent = `Time Remaining: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    duration--;

                    if (duration < 0) {
                        clearInterval(interval);
                        alert("Time is up! Submitting your quiz.");
                        form.submit();
                    }
                }, 1000);
            </script>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$attempted): ?>
            <hr class="my-4">
            <h5 class="mb-3">Correct Answers:</h5>
            <?php
            $questions = $conn->query("SELECT * FROM questions");
            while ($row = $questions->fetch_assoc()):
                $qid = $row['id'];
                $correct_option = $row['correct_option'];
                $user_answer = $_POST['answers'][$qid] ?? '';
            ?>
                <div class="question-block">
                    <p><strong><?= $row['question'] ?></strong></p>
                    <?php foreach (['A', 'B', 'C', 'D'] as $option):
                        $selected = ($user_answer === $option);
                        $is_correct = ($option === $correct_option);
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled
                                   <?= $selected ? 'checked' : '' ?>
                                   <?= $is_correct ? 'style="outline: 2px solid #28a745;"' : '' ?>>
                            <label class="form-check-label <?= $is_correct ? 'text-success' : ($selected ? 'text-danger' : '') ?>">
                                <?= $row["option_" . strtolower($option)] ?>
                                <?php if ($is_correct) echo "(Correct)"; ?>
                                <?php if ($selected && !$is_correct) echo "(Your Answer)"; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
