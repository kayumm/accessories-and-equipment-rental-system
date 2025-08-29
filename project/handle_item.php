<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_item'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    }

    $sql = "INSERT INTO items (owner_id, title, description, location, cost, cost_type, phone, image) 
        VALUES ('$user_id', '$title', '$description',
                '".mysqli_real_escape_string($conn,$_POST['location'])."', 
                '".mysqli_real_escape_string($conn,$_POST['cost'])."', 
                '".mysqli_real_escape_string($conn,$_POST['cost_type'])."', 
                '".mysqli_real_escape_string($conn,$_POST['phone'])."',
                '$image_name')";
    mysqli_query($conn, $sql);

    $item_id = mysqli_insert_id($conn);
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $img_name = time()."_".basename($_FILES['images']['name'][$key]);
            move_uploaded_file($tmp_name, "uploads/".$img_name);
            mysqli_query($conn, "INSERT INTO item_images (item_id, image) VALUES ($item_id, '$img_name')");
        }
    }

    header("Location: manage_items.php");
    exit();
}
?>
