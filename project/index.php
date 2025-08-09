<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accessories & Equipment Rental System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
    <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

    <h3>Main Menu</h3>
    <ul>
        <li><a href="manage_items.php">Manage My Items</a></li>
        <li><a href="search.php">Search Items</a></li>
        <li><a href="manage_bookings.php">Manage Incoming Bookings</a></li>
        <li><a href="booking_history.php">Booking History</a></li>
        <li><a href="notifications.php">Notifications</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>

</body>
</html>

