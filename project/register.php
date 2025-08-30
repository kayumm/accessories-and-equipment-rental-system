
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment and Accessories Rental System - Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('uploads/2631190_7998.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center; 
            background-attachment: fixed;
        }
        .register-container {
            max-width: 450px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .register-container form {
            box-shadow: none;
            padding: 0;
        }
        .register-container p {
            text-align: center;
            margin-top: 20px;
        }
        .project-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="project-title">Equipment  Rental System</div>
        <h2>User Registration</h2>
        <form action="handle_register.php" method="POST">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>