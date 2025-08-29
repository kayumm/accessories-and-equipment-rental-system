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
        WHERE items.id = $item_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Item not found.";
    exit();
}

$item = mysqli_fetch_assoc($result);

if (isset($_GET['delete_review'])) {
    $review_id = intval($_GET['delete_review']);
    mysqli_query($conn, "DELETE FROM reviews WHERE id=$review_id AND user_id=" . $_SESSION['user_id']);
    header("Location: item.php?id=" . $item_id);
    exit();
}

$can_review = false;
if ($_SESSION['user_id'] != $item['owner_id']) {
    $check_borrow = mysqli_query($conn, "SELECT * FROM bookings 
                                         WHERE borrower_id=" . $_SESSION['user_id'] . " 
                                           AND item_id=$item_id 
                                           AND status IN ('approved', 'returned')");
    if (mysqli_num_rows($check_borrow) > 0) {
        $can_review = true;
    }
}

if (isset($_POST['submit_review']) && $can_review) {
    $review_text = mysqli_real_escape_string($conn, $_POST['review']);
    mysqli_query($conn, "INSERT INTO reviews (item_id, user_id, review) 
                         VALUES ($item_id, " . $_SESSION['user_id'] . ", '$review_text')");
    header("Location: item.php?id=" . $item_id);
    exit();
}

$reviews = mysqli_query($conn, "SELECT r.*, u.name 
                                FROM reviews r
                                JOIN users u ON r.user_id = u.id
                                WHERE r.item_id = $item_id
                                ORDER BY r.created_at DESC");

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'search';
$back_url = ($redirect === 'manage') ? 'manage_items.php' : 'search.php';
$back_text = ($redirect === 'manage') ? 'My Items' : 'Search';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($item['title']); ?> - Equipment and Accessories Rental System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eaeaea;
            color: #333332;
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
            color: white;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: #6a11cb;
        }
        
        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu h3 {
            padding: 15px 20px 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            color: white;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin: 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            border-left: 3px solid #6a11cb;
            color: white;
        }
        
        .sidebar-menu a.active {
            background: rgba(106, 17, 203, 0.3);
            border-left: 3px solid #6a11cb;
            color: white;
        }
        
        .sidebar-menu i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .welcome-section h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }
        
        .welcome-section p {
            color: #666;
            margin-top: 5px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .user-details {
            text-align: right;
        }
        
        .user-details .name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .user-details .role {
            font-size: 0.9rem;
            color: #666;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 6px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: #e9ecef;
            text-decoration: none;
        }
        
        .item-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .item-header h1 {
            margin-bottom: 10px;
            color: white;
        }
        
        .item-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .item-image-section {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        
        .item-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .view-more-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s ease;
            margin-top: 10px;
        }
        
        .view-more-btn:hover {
            background: #0056b3;
            text-decoration: none;
        }
        
        .item-details {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }
        
        .item-details h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .detail-label {
            font-weight: 600;
            width: 120px;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
            color: #333;
        }
        
        .booking-section {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .booking-btn {
            background: #28a745;
            color: white;
            padding: 16px 40px;
            border: none;
            border-radius: 6px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .booking-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .message {
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .info-message {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .warning-message {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        .reviews-section {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .reviews-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .review-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .review-form textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            margin-bottom: 15px;
            resize: vertical;
            min-height: 100px;
            font-size: 1rem;
        }
        
        .review-form button {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .review-form button:hover {
            background: #0056b3;
        }
        
        .reviews-list {
            margin-top: 20px;
        }
        
        .review-item {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .review-author {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .review-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .review-text {
            color: #555;
            line-height: 1.6;
        }
        
        .delete-review {
            color: #dc3545;
            font-size: 0.9rem;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }
        
        .delete-review:hover {
            text-decoration: underline;
        }
        
        .no-reviews {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Equipment and Accessories Rental System</h2>
            <p>Rent. Share. Connect.</p>
        </div>
        
        <div class="sidebar-menu">
            <h3>Navigation</h3>
            <ul>
                <li><a href="index.php">
                    <i>🏠</i> <span>Dashboard</span>
                </a></li>
                <li><a href="search.php">
                    <i>🔍</i> <span>Search Items</span>
                </a></li>
                <li><a href="manage_items.php">
                    <i>📦</i> <span>My Items</span>
                </a></li>
                <li><a href="manage_bookings.php">
                    <i>📅</i> <span>My Bookings</span>
                </a></li>
                <li><a href="request_item.php">
                    <i>➕</i> <span>Request Item</span>
                </a></li>
                <li><a href="view_requests.php">
                    <i>💬</i> <span>View Requests</span>
                </a></li>
            </ul>
            
            <h3>Account</h3>
            <ul>
                <li><a href="notifications.php">
                    <i>🔔</i> <span>Notifications</span>
                </a></li>
                <li><a href="logout.php">
                    <i>🚪</i> <span>Logout</span>
                </a></li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Item Details</h1>
                <p>View detailed information about this rental item</p>
            </div>
            <div class="user-info">
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                    <div class="role"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <a href="<?php echo $back_url; ?>" class="back-link">← Back to <?php echo $back_text; ?></a>
        
        <div class="item-header">
            <h1><?php echo htmlspecialchars($item['title']); ?></h1>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
        </div>

        <div class="item-container">
            <div class="item-details">
                <h2>Item Details</h2>
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($item['location']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Cost:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($item['cost']) . " Taka " . htmlspecialchars($item['cost_type']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($item['phone']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Owner:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($item['owner_name']); ?> (<?php echo htmlspecialchars($item['owner_email']); ?>)</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Availability:</div>
                    <div class="detail-value">
                        <span style="font-weight: 600; color: <?php echo $item['availability'] == 'available' ? '#28a745' : '#dc3545'; ?>">
                            <?php echo ucfirst($item['availability']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="item-image-section">
                <?php if ($item['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-image">
                <?php else: ?>
                    <div class="item-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
                        No Image
                    </div>
                <?php endif; ?>
                <a href="additional_images.php?id=<?php echo $item_id; ?>" class="view-more-btn">View Additional Images</a>
            </div>
        </div>

        <div class="booking-section">
            <?php if ($item['availability'] == 'available' && $item['owner_id'] != $_SESSION['user_id']): ?>
                <form action="handle_booking.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="book_item" class="booking-btn">Book This Item Now</button>
                </form>
            <?php elseif ($item['owner_id'] == $_SESSION['user_id']): ?>
                <div class="message info-message">
                    You are the owner of this item.
                </div>
            <?php else: ?>
                <div class="message warning-message">
                    This item is not available for booking.
                </div>
            <?php endif; ?>
        </div>

        <div class="reviews-section">
            <h2>Reviews</h2>
            
            <?php if ($can_review): ?>
                <div class="review-form">
                    <form method="POST">
                        <textarea name="review" required placeholder="Write your review here..."></textarea>
                        <button type="submit" name="submit_review">Submit Review</button>
                    </form>
                </div>
            <?php elseif ($_SESSION['user_id'] == $item['owner_id']): ?>
                <div class="message info-message">
                    Owners cannot review their own item.
                </div>
            <?php else: ?>
                <div class="message warning-message">
                    You must have borrowed this item before you can review it.
                </div>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if (mysqli_num_rows($reviews) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="review-author"><?php echo htmlspecialchars($r['name']); ?></span>
                                <span class="review-date"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></span>
                            </div>
                            <div class="review-text">
                                <?php echo nl2br(htmlspecialchars($r['review'])); ?>
                            </div>
                            <?php if ($r['user_id'] == $_SESSION['user_id']): ?>
                                <div>
                                    <a href="item.php?id=<?php echo $item_id; ?>&delete_review=<?php echo $r['id']; ?>" 
                                       onclick="return confirm('Delete this review?')" 
                                       class="delete-review">Delete Review</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-reviews">
                        No reviews yet. Be the first to review this item!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
