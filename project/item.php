<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: search.php");
    exit();
}

$item_id = intval($_GET['id']);
$sql = "SELECT items.*, users.name AS owner_name, users.email AS owner_email
        FROM items
        JOIN users ON items.owner_id = users.id
        WHERE items.id=$item_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Item not found.";
    exit();
}

$item = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($item['title']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($item['title']); ?></h1>
    <p><a href="search.php">Back to Search</a></p>

    <?php if ($item['image']) { ?>
        <img src="uploads/<?php echo $item['image']; ?>" width="200"><br><br>
    <?php } ?>

    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
    <p><strong>Owner:</strong> <?php echo htmlspecialchars($item['owner_name']); ?> (<?php echo htmlspecialchars($item['owner_email']); ?>)</p>
    <p><strong>Availability:</strong> <?php echo htmlspecialchars($item['availability']); ?></p>

    <?php if ($item['availability'] == 'available' && $item['owner_id'] != $_SESSION['user_id']) { ?>
        <form action="handle_booking.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <button type="submit" name="book_item">Book Now</button>
        </form>
    <?php } elseif ($item['owner_id'] == $_SESSION['user_id']) { ?>
        <p>You are the owner of this item.</p>
    <?php } else { ?>
        <p>This item is not available for booking.</p>
    <?php } ?>
</body>
</html>
