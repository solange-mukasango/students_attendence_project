<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: auth/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
<style>
    body {
        margin: 0;
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f8;
        color: #333;
    }

    /* Navigation Bar */
    .nav {
        background-color: #2c3e50;
        display: flex;
        justify-content: flex-start;
        padding: 15px 30px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .nav a {
        color: white;
        text-decoration: none;
        margin-right: 20px;
        font-weight: bold;
        transition: 0.3s;
    }
    .nav a:hover {
        color: #1abc9c;
    }

    /* Welcome Heading */
    h1 {
        text-align: center;
        margin-top: 50px;
        color: #2c3e50;
    }

    /* Dashboard Cards */
    .dashboard {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 50px;
        gap: 30px;
    }

    .card {
        background-color: white;
        width: 200px;
        height: 150px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        text-decoration: none;
        color: #2c3e50;
        font-weight: bold;
        font-size: 18px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        color: #1abc9c;
    }

    .logout-btn {
        position: fixed;
        top: 15px;
        right: 30px;
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s;
    }

    .logout-btn:hover {
        background-color: #c0392b;
    }
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background-color: #ececec; /* Soft light gray */
    color: #333;
}

/* Optional: cards slightly darker for contrast */
.card {
    background-color: #ffffff; /* white card is okay */
    color: #2c3e50;
}

</style>
</head>
<body>

<!-- Navigation Bar -->
<div class="nav">
    <span style="color:white; font-size:20px; font-weight:bold;">Student Attendance System</span>
    <a href="auth/logout.php" class="logout-btn">Logout</a>
</div>

<!-- Welcome Heading -->
<h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>

<!-- Dashboard Cards -->
<div class="dashboard">
    <a href="students.php" class="card">Students</a>
    <a href="attendance.php" class="card">Attendance</a>
</div>

</body>
</html>
