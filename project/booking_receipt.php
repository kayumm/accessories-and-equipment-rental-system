<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, i.title AS item_title, i.cost, i.cost_type,
               u1.name AS borrower_name, u1.email AS borrower_email,
               u2.name AS owner_name, u2.email AS owner_name
        FROM bookings b
        JOIN items i ON b.item_id = i.id
        JOIN users u1 ON b.borrower_id = u1.id
        JOIN users u2 ON i.owner_id = u2.id
        WHERE b.id = $booking_id 
          AND (b.borrower_id = $user_id OR i.owner_id = $user_id)";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "Access denied or booking not found.";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Receipt #<?php echo $booking['id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .receipt { border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #0056b3; }
        .info { margin: 15px 0; }
        .info p { margin: 5px 0; }
        .footer { margin-top: 30px; text-align: center; font-style: italic; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>Booking Receipt</h2>
            <p><strong>Receipt ID:</strong> #<?php echo $booking['id']; ?></p>
            <p><strong>Date:</strong> <?php echo $booking['created_at']; ?></p>
        </div>

        <div class="info">
            <p><strong>Item:</strong> <?php echo htmlspecialchars($booking['item_title']); ?></p>
            <p><strong>Cost:</strong> <?php echo htmlspecialchars($booking['cost'] . " " . $booking['cost_type']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></p>
        </div>

        <div class="info">
            <p><strong>Borrower:</strong> <?php echo htmlspecialchars($booking['borrower_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['borrower_email']); ?></p>
        </div>

        <div class="info">
            <p><strong>Owner:</strong> <?php echo htmlspecialchars($booking['owner_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['owner_name']); ?></p>
        </div>

        <div class="footer">
            <p>This is a digital receipt for your booking. You may print this for your records.</p>
            <button onclick="window.print()" class="no-print">Print Receipt</button>
            <a href="manage_bookings.php" class="no-print">Back to Bookings</a>
        </div>
    </div>
</body>
</html>
