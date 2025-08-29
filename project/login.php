<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment and Accessories Rental System - Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('uploads/2631190_7998.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center; 
            background-attachment: fixed;
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 450px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .project-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 20px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .login-container form {
            box-shadow: none;
            padding: 0;
        }
        .login-container form label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
        }
        .login-container form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #2980b9;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-container button:hover {
            background: #1c5980;
        }
        .login-container p {
            text-align: center;
            margin-top: 20px;
        }
        .login-container a {
            color: #2980b9;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="project-title">Equipment  Rental System</div>
        <h2>User Login</h2>
        <form action="handle_login.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
