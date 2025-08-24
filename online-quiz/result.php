<?php
include 'db.php';

$score = 0;
$total = 0;

if (isset($_POST['quiz'])) {
    foreach ($_POST['quiz'] as $question_id => $answer) {
        $sql = "SELECT correct_option FROM questions WHERE id = $question_id";
        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            if ($row['correct_option'] == $answer) {
                $score++;
            }
            $total++;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Quiz Completed!</h2>
    <p>Your Score: <?php echo $score; ?> / <?php echo $total; ?></p>
    <a href="index.php" class="btn">Try Again</a>
</div>
</body>
</html>
