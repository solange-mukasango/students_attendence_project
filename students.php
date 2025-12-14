<?php
session_start();
require 'config/db.php';
if(!isset($_SESSION['user'])) header("Location: auth/login.php");

$error = "";
$success = "";

/* HANDLE CREATE OR UPDATE */
if(isset($_POST['save'])){
    $reg   = $_POST['reg'] ?? '';
    $name  = $_POST['name'] ?? '';
    $class = $_POST['class'] ?? '';
    $id    = $_POST['student_id'] ?? null; // for edit

    if($reg && $name && $class){
        if($id){
            // UPDATE
            $stmt = $pdo->prepare("UPDATE students SET reg_no=?, full_name=?, class=? WHERE id=?");
            $stmt->execute([$reg, $name, $class, $id]);
            $success = "Student updated successfully";
        } else {
            // CREATE
            $stmt = $pdo->prepare("INSERT INTO students(reg_no,full_name,class) VALUES(?,?,?)");
            $stmt->execute([$reg,$name,$class]);
            $success = "Student added successfully";
        }
    } else {
        $error = "All fields are required";
    }
}

/* HANDLE DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
    $stmt->execute([$id]);
    $success = "Student deleted successfully";
}

/* HANDLE EDIT */
$edit_student = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id=?");
    $stmt->execute([$id]);
    $edit_student = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* FETCH ALL STUDENTS */
$students = $pdo->query("SELECT * FROM students ORDER BY reg_no ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students CRUD</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { background-color: #ececec; font-family: Arial, sans-serif; margin:0; padding:0; }
        .nav { background-color: #2c3e50; padding: 15px; }
        .nav a { color: white; margin-right: 20px; text-decoration: none; font-weight: bold; }
        h2 { text-align: center; margin-top: 20px; color: #2c3e50; }
        .card { background: white; padding: 20px; max-width: 500px; margin: 20px auto; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background-color: #1abc9c; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #16a085; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background:white; border-radius:10px; overflow:hidden; }
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
    <a href="attendance.php">Attendance</a>
    <a href="auth/logout.php">Logout</a>
</div>

<div class="card">
    <h2><?= $edit_student ? "Edit Student" : "Add Student" ?></h2>

    <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="student_id" value="<?= $edit_student['id'] ?? '' ?>">
        <input name="reg" placeholder="Reg No" value="<?= $edit_student['reg_no'] ?? '' ?>" required>
        <input name="name" placeholder="Full Name" value="<?= $edit_student['full_name'] ?? '' ?>" required>
        <input name="class" placeholder="Class" value="<?= $edit_student['class'] ?? '' ?>" required>
        <button name="save"><?= $edit_student ? "Update Student" : "Add Student" ?></button>
    </form>
</div>

<!-- Students Table -->
<table>
    <tr>
        <th>Reg No</th>
        <th>Name</th>
        <th>Class</th>
        <th>Actions</th>
    </tr>
    <?php foreach($students as $s): ?>
    <tr>
        <td><?= $s['reg_no'] ?></td>
        <td><?= $s['full_name'] ?></td>
        <td><?= $s['class'] ?></td>
        <td class="action">
            <a href="?edit=<?= $s['id'] ?>" class="edit">Edit</a>
            <a href="?delete=<?= $s['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="back">
    <a href="dashboard.php"><button type="button">Back to Dashboard</button></a>
</div>

</body>
</html>
