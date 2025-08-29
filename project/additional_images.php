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
$sql = "SELECT title FROM items WHERE id = $item_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Item not found.";
    exit();
}

$item = mysqli_fetch_assoc($result);
$title = htmlspecialchars($item['title']);

$images = mysqli_query($conn, "SELECT * FROM item_images WHERE item_id = $item_id");

$image_array = [];
while ($img = mysqli_fetch_assoc($images)) {
    $image_array[] = $img;
}

$current_index = isset($_GET['img']) ? intval($_GET['img']) : 0;
$total_images = count($image_array);

if ($total_images > 0 && ($current_index < 0 || $current_index >= $total_images)) {
    $current_index = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Additional Images - <?php echo $title; ?></title>
    <style>
        * {
            margin: 0; padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: #2c2c2c;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .header h1 { font-size: 1.2rem; font-weight: 500; }
        .nav-links a {
            color: #66b2ff;
            text-decoration: none;
            margin-left: 15px;
            font-size: 0.9rem;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .image-container {
            flex: 1; display: flex;
            align-items: center; justify-content: center;
            padding: 20px; position: relative;
        }
        .main-image {
            max-width: 90%; max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.5);
        }
        .no-image {
            color: #999;
            font-size: 1.2rem;
            text-align: center;
        }
        .navigation {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            width: 50px; height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center; justify-content: center;
            transition: background 0.3s ease;
        }
        .navigation:hover { background: rgba(0,0,0,0.8); }
        .prev { left: 20px; }
        .next { right: 20px; }
        .image-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        .thumbnails {
            display: flex;
            gap: 10px;
            padding: 15px 20px;
            background: #2c2c2c;
            overflow-x: auto;
            max-height: 120px;
        }
        .thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.3s ease;
        }
        .thumbnail:hover { border-color: #66b2ff; }
        .thumbnail.active { border-color: #66b2ff; }
        .back-link {
            color: #66b2ff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Additional Images: <?php echo $title; ?></h1>
        <div class="nav-links">
            <a href="item.php?id=<?php echo $item_id; ?>" class="back-link">← Back to Item</a>
        </div>
    </div>

    <?php if ($total_images > 0): ?>
        <div class="image-container">
            <img src="uploads/<?php echo htmlspecialchars($image_array[$current_index]['image']); ?>" 
                 alt="Image <?php echo ($current_index + 1); ?>" 
                 class="main-image">
            
            <?php if ($total_images > 1): ?>
                <a href="additional_images.php?id=<?php echo $item_id; ?>&img=<?php echo ($current_index - 1 + $total_images) % $total_images; ?>" 
                   class="navigation prev">‹</a>
                <a href="additional_images.php?id=<?php echo $item_id; ?>&img=<?php echo ($current_index + 1) % $total_images; ?>" 
                   class="navigation next">›</a>
            <?php endif; ?>
            
            <div class="image-counter">
                <?php echo ($current_index + 1); ?> of <?php echo $total_images; ?>
            </div>
        </div>
        
        <div class="thumbnails">
            <?php foreach ($image_array as $index => $img): ?>
                <a href="additional_images.php?id=<?php echo $item_id; ?>&img=<?php echo $index; ?>">
                    <img src="uploads/<?php echo htmlspecialchars($img['image']); ?>" 
                         alt="Thumbnail <?php echo ($index + 1); ?>" 
                         class="thumbnail <?php echo $index === $current_index ? 'active' : ''; ?>">
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="image-container">
            <div class="no-image">
                <p>No additional images available for this item.</p>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>