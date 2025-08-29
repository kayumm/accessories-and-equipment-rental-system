<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: manage_items.php");
    exit();
}

$item_id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM items WHERE id=$item_id AND owner_id=$user_id");

if (mysqli_num_rows($result) == 0) {
    echo "Item not found or you don't have permission.";
    exit();
}

$item = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item - Equipment and Accessories Rental System</title>
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
        .edit-form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-title {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }
        .form-group {    margin-bottom: 20px;}
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .image-section {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .current-image {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .current-image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .no-image {
            width: 120px;
            height: 120px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        .update-btn {
            background: #28a745;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
        }
        .update-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .cancel-btn {
            background: #6c757d;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(108, 117, 125, 0.3);
        }
        .cancel-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
            text-decoration: none;
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
                <li><a href="manage_items.php" class="active">
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
                <h1>Edit Item</h1>
                <p>Update your item details</p>
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
        
        <a href="manage_items.php" class="back-link">← Back to Manage Items</a>
        <div class="edit-form-container">
            <h2 class="form-title">Edit Item Details</h2>
            
            <form action="handle_edit_item.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">

                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($item['location']); ?>" required>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label for="cost">Cost *</label>
                            <input type="number" id="cost" name="cost" step="0.01" value="<?php echo htmlspecialchars($item['cost']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cost_type">Cost Type *</label>
                            <select id="cost_type" name="cost_type" required>
                                <option value="per_day" <?php if ($item['cost_type'] == 'per_day') echo 'selected'; ?>>Per Day</option>
                                <option value="per_hour" <?php if ($item['cost_type'] == 'per_hour') echo 'selected'; ?>>Per Hour</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($item['phone']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="image-section">
                    <div class="form-group">
                        <label>Current Image</label>
                        <div class="current-image">
                            <?php if ($item['image']): ?>
                                <img src="uploads/<?php echo $item['image']; ?>" alt="Current image">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <div>
                                <label for="image">Change Image:</label>
                                <input type="file" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="availability">Availability *</label>
                    <select id="availability" name="availability" required>
                        <option value="available" <?php if ($item['availability'] == 'available') echo 'selected'; ?>>Available</option>
                        <option value="unavailable" <?php if ($item['availability'] == 'unavailable') echo 'selected'; ?>>Unavailable</option>
                    </select>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="update_item" class="update-btn">Update Item</button>
                    <a href="manage_items.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>