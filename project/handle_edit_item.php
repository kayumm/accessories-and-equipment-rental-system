<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_item'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);

    $res = mysqli_query($conn, "SELECT image FROM items WHERE id=$item_id AND owner_id=$user_id");
    if (mysqli_num_rows($res) == 0) {
        echo "Item not found.";
        exit();
    }
    $old_item = mysqli_fetch_assoc($res);
    $image_name = $old_item['image'];

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    }

    $sql = "UPDATE items 
            SET title='$title', description='$description', image='$image_name', availability='$availability'
            WHERE id=$item_id AND owner_id=$user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_items.php");
        exit();
    } else {
        echo "Error updating item: " . mysqli_error($conn);
    }
}
?>
