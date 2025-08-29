<?php
include 'db.php';
session_start();

$message = '';
$messageType = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "Invalid password. <a href='login.php'>Try again</a>";
            $messageType = 'error';
        }
    } else {
        $message = "No user found with that email. <a href='login.php'>Try again</a>";
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('uploads/2631190_7998.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
        }
        .success {
            background-color: #e6ffed;
            color: #1a7f37;
            border: 1px solid #1a7f37;
        }
        .error {
            background-color: #ffe6e6;
            color: #d93025;
            border: 1px solid #d93025;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Login Status</h2>
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php else: ?>
            <div class="message error">
                No login data received. <a href="login.php">Go back</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
