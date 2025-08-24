<?php 
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete user
if (isset($_GET['delete_user_id'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_user_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Delete question
if (isset($_GET['delete_question_id'])) {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_question_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Edit user
if (isset($_POST['edit_user'])) {
    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $_POST['username'], $_POST['role'], $_POST['user_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch users
$search_user = $_GET['search_user'] ?? '';
$like_user = "%$search_user%";
$user_query = $conn->prepare("SELECT * FROM users WHERE username LIKE ?");
$user_query->bind_param("s", $like_user);
$user_query->execute();
$users = $user_query->get_result();

// Fetch questions
$search_question = $_GET['search_question'] ?? '';
$like_question = "%$search_question%";
$question_query = $conn->prepare("SELECT * FROM questions WHERE question LIKE ?");
$question_query->bind_param("s", $like_question);
$question_query->execute();
$questions = $question_query->get_result();

// Fetch results
$results = $conn->query("SELECT r.id, u.username, r.score, r.total, r.timestamp FROM results r JOIN users u ON r.user_id = u.id ORDER BY r.timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Admin Dashboard</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- USERS -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Management</h5>
        </div>
        <div class="card-body">
            <form class="d-flex mb-3" method="get">
                <input type="text" name="search_user" value="<?= htmlspecialchars($search_user) ?>" class="form-control me-2" placeholder="Search username">
                <button class="btn btn-outline-primary">Search</button>
            </form>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Edit/Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <form method="post" class="d-flex flex-wrap gap-2">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="text" name="username" value="<?= $user['username'] ?>" class="form-control form-control-sm" required>
                                <select name="role" class="form-select form-select-sm">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button name="edit_user" class="btn btn-sm btn-primary">Save</button>
                                <a href="?delete_user_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- QUESTIONS -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Question Management</h5>
        </div>
        <div class="card-body">
            <form class="d-flex mb-3" method="get">
                <input type="text" name="search_question" value="<?= htmlspecialchars($search_question) ?>" class="form-control me-2" placeholder="Search question">
                <button class="btn btn-outline-success">Search</button>
            </form>
            <a href="add_question.php" class="btn btn-success mb-3">+ Add New Question</a>
            <table class="table table-striped">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Correct Option</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($q = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $q['id'] ?></td>
                        <td><?= htmlspecialchars($q['question']) ?></td>
                        <td><?= $q['correct_option'] ?></td>
                        <td>
                            <a href="edit_questions.php?id=<?= $q['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete_question_id=<?= $q['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this question?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RESULTS -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Quiz Results</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Score</th>
                    <th>Total</th>
                    <th>Timestamp</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($r = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['username']) ?></td>
                        <td><?= $r['score'] ?></td>
                        <td><?= $r['total'] ?></td>
                        <td><?= $r['timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
