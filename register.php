<?php
require '../config/db.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Simple validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {

        // Check if username or email exists
        $check = $pdo->prepare(
            "SELECT * FROM users WHERE username = ? OR email = ?"
        );
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $error = "Username or Email already exists";
        } else {

            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password)
                 VALUES (?, ?, ?)"
            );

            if ($stmt->execute([$username, $email, $hashed])) {
                $success = "Registration successful. You can login now.";
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="card">
    <h2>Create Account</h2>

    <p class="error"><?php echo $error; ?></p>
    <p style="color:green"><?php echo $success; ?></p>

    <form method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <button name="register">Register</button>
    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>
