<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

$error = "";
$success = "";

/* HANDLE FORM SUBMISSION - CREATE OR UPDATE */
if (isset($_POST['mark'])) {
    $student_id = $_POST['student_id'] ?? '';
    $status     = $_POST['status'] ?? '';
    $record_id  = $_POST['record_id'] ?? null; // for edit

    if (empty($student_id) || empty($status)) {
        $error = "Please select student and status";
    } else {
        if ($record_id) {
            // UPDATE existing record
            $stmt = $pdo->prepare("UPDATE attendance SET student_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$student_id, $status, $record_id]);
            $success = "Attendance updated successfully";
        } else {
            // CHECK if attendance already exists today
            $stmt_check = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND date = CURDATE()");
            $stmt_check->execute([$student_id]);

            if ($stmt_check->rowCount() > 0) {
                $error = "Attendance already marked for this student today";
            } else {
                // INSERT new record
                $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, CURDATE(), ?)");
                $stmt->execute([$student_id, $status]);
                $success = "Attendance recorded successfully";
            }
        }
    }
}

/* HANDLE DELETE */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Attendance record deleted successfully";
}

/* HANDLE EDIT */
$edit_record = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->execute([$id]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* FETCH STUDENTS */
$students = $pdo->query("SELECT * FROM students ORDER BY reg_no ASC")->fetchAll(PDO::FETCH_ASSOC);

/* FETCH ATTENDANCE RECORDS */
$records = $pdo->query(
    "SELECT a.id, s.reg_no, s.full_name, a.date, a.status
     FROM attendance a
     JOIN students s ON a.student_id = s.id
     ORDER BY a.date DESC"
)->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance CRUD</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { background-color: #ececec; font-family: Arial, sans-serif; }
        .nav { background-color: #2c3e50; padding: 15px; }
        .nav a { color: white; margin-right: 20px; text-decoration: none; font-weight: bold; }
        .card { background: white; padding: 20px; max-width: 500px; margin: 30px auto; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        select, button, input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background-color: #1abc9c; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #16a085; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        .error { color: red; text-align: center; }
        .success { color: green; text-align: center; }
        .action a { margin: 0 5px; color: #fff; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .edit { background-color: #3498db; }
        .delete { background-color: #e74c3c; }
        .back { text-align: center; margin: 20px; }
    </style>
</head>
<body>

<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="auth/logout.php">Logout</a>
</div>

<div class="card">
    <h2><?= $edit_record ? "Edit Attendance" : "Mark Attendance" ?></h2>

    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="record_id" value="<?= $edit_record['id'] ?? '' ?>">

        <label>Select Student (Reg No):</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($edit_record && $edit_record['student_id'] == $s['id']) ? "selected" : "" ?>>
                    <?= $s['reg_no'] ?> - <?= $s['full_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Status:</label>
        <select name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Present" <?= ($edit_record && $edit_record['status'] == 'Present') ? 'selected' : '' ?>>Present</option>
            <option value="Absent" <?= ($edit_record && $edit_record['status'] == 'Absent') ? 'selected' : '' ?>>Absent</option>
        </select>

        <button name="mark"><?= $edit_record ? "Update Attendance" : "Save Attendance" ?></button>
    </form>
</div>

<!-- Attendance Table -->
<table>
    <tr>
        <th>Reg No</th>
        <th>Student Name</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($records as $r): ?>
        <tr>
            <td><?= $r['reg_no'] ?></td>
            <td><?= $r['full_name'] ?></td>
            <td><?= $r['date'] ?></td>
            <td><?= $r['status'] ?></td>
            <td class="action">
                <a href="?edit=<?= $r['id'] ?>" class="edit">Edit</a>
                <a href="?delete=<?= $r['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<div class="back">
    <a href="dashboard.php"><button type="button">Back to Dashboard</button></a>
</div>

</body>
</html>
