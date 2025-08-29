<?php
session_start();
include 'db.php';
include 'functions.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['book_item'])) {
    $item_id = intval($_POST['item_id']);
    $borrower_id = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT id, title, owner_id FROM items WHERE id=$item_id AND availability='available'");
    $item = mysqli_fetch_assoc($result);

    if (!$item) {
        $_SESSION['error'] = "Item is not available.";
        header("Location: item.php?id=$item_id");
        exit();
    }

    $owner_id = $item['owner_id'];
    $title = $item['title'];

    $query = "INSERT INTO bookings (item_id, borrower_id, status, created_at) 
              VALUES ($item_id, $borrower_id, 'approved', NOW())";
    if (mysqli_query($conn, $query)) {
        $booking_id = mysqli_insert_id($conn);

        mysqli_query($conn, "UPDATE items SET availability='unavailable' WHERE id=$item_id");
        addNotification($conn, $owner_id, "Your item '$title' has been booked by " . $_SESSION['name'] . ".");
        header("Location: booking_receipt.php?id=$booking_id");
        exit();
    }
}
header("Location: item.php?id=$item_id");
exit();
?>
