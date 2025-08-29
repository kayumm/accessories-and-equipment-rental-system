<?php
include 'db.php';

$message = '';
$messageType = '';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already registered. <a href='register.php'>Try again</a>";
        $messageType = 'error';
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful. <a href='login.php'>Login here</a>";
            $messageType = 'success';
        } else {
            $message = "Error: " . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Status</title>
    <style>
        body {
            background-image: url('uploads/2631190_7998.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center; 
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        <h2>Registration Status</h2>
        <?php if ($message): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php else: ?>
            <div class="message error">
                No registration data received. <a href="register.php">Go back</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
