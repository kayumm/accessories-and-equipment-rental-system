<?php
session_start();
include 'db.php';
include 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$owner_id = $_SESSION['user_id'];

if (isset($_GET['approve'])) {
    $booking_id = intval($_GET['approve']);

    mysqli_query($conn, "UPDATE bookings b
        JOIN items i ON b.item_id = i.id
        SET b.status='approved'
        WHERE b.id=$booking_id AND i.owner_id=$owner_id");

    mysqli_query($conn, "UPDATE items 
        SET availability='unavailable' 
        WHERE id = (SELECT item_id FROM bookings WHERE id=$booking_id)");

    $res = mysqli_query($conn, "SELECT borrower_id, item_id FROM bookings WHERE id=$booking_id");
    $data = mysqli_fetch_assoc($res);
    $borrower_id = $data['borrower_id'];
    $item_id = $data['item_id'];

    $item_res = mysqli_query($conn, "SELECT title FROM items WHERE id=$item_id");
    $item_data = mysqli_fetch_assoc($item_res);
    addNotification($conn, $borrower_id, "Your booking for '{$item_data['title']}' has been approved.");

}

if (isset($_GET['reject'])) {
    $booking_id = intval($_GET['reject']);
    mysqli_query($conn, "UPDATE bookings b
        JOIN items i ON b.item_id = i.id
        SET b.status='rejected'
        WHERE b.id=$booking_id AND i.owner_id=$owner_id");

    $res = mysqli_query($conn, "SELECT borrower_id, item_id FROM bookings WHERE id=$booking_id");
    $data = mysqli_fetch_assoc($res);
    $borrower_id = $data['borrower_id'];
    $item_id = $data['item_id'];

    $item_res = mysqli_query($conn, "SELECT title FROM items WHERE id=$item_id");
    $item_data = mysqli_fetch_assoc($item_res);
    addNotification($conn, $borrower_id, "Your booking for '{$item_data['title']}' has been rejected.");
}
if (isset($_GET['return'])) {
    $booking_id = intval($_GET['return']);

    mysqli_query($conn, "UPDATE bookings b
        JOIN items i ON b.item_id = i.id
        SET b.status='returned'
        WHERE b.id=$booking_id AND i.owner_id=$owner_id");

    mysqli_query($conn, "UPDATE items 
        SET availability='available' 
        WHERE id = (SELECT item_id FROM bookings WHERE id=$booking_id)");

    $res = mysqli_query($conn, "SELECT b.borrower_id, i.title 
                                FROM bookings b 
                                JOIN items i ON b.item_id = i.id
                                WHERE b.id=$booking_id");
    $data = mysqli_fetch_assoc($res);
    addNotification($conn, $data['borrower_id'], "The item '{$data['title']}' has been marked as returned.");
}

$sql = "SELECT b.*, u.name AS borrower_name, i.title AS item_title
        FROM bookings b
        JOIN items i ON b.item_id = i.id
        JOIN users u ON b.borrower_id = u.id
        WHERE i.owner_id=$owner_id
        ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Manage Bookings for My Items</h1>
    <p><a href="index.php">Back to Home</a></p>

    <table border="1" cellpadding="8">
        <tr>
            <th>Item</th>
            <th>Borrower</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['item_title']); ?></td>
                <td><?php echo htmlspecialchars($row['borrower_name']); ?></td>
                <td class="status-<?php echo $row['status']; ?>">
                    <?php echo ucfirst($row['status']); ?>
                </td>
                <td>
                    <?php if ($row['status'] == 'pending') { ?>
                        <a href="manage_bookings.php?approve=<?php echo $row['id']; ?>">Approve</a> |
                        <a href="manage_bookings.php?reject=<?php echo $row['id']; ?>">Reject</a>
                    <?php } elseif ($row['status'] == 'approved') { ?>
                        <a href="manage_bookings.php?return=<?php echo $row['id']; ?>">Mark as Returned</a>
                    <?php } else { echo "No actions"; } ?>

                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
