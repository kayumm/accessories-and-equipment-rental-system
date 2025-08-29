<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$user_id ORDER BY created_at DESC");

mysqli_query($conn, "UPDATE notifications SET read_status=1 WHERE user_id=$user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Equipment and Accessories Rental System</title>
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
        
        .notifications-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h2 {
            color: #2c3e50;
        }
        
        .results-count {
            color: #666;
            font-size: 0.95rem;
        }
        
        .notification-item {
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .notification-item.unread {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid #2196f3;
        }
        
        .notification-item.booking {
            border-left-color: #4caf50;
        }
        
        .notification-item.request {
            border-left-color: #ff9800;
        }
        
        .notification-item.system {
            border-left-color: #9c27b0;
        }
        
        .notification-content {
            margin-bottom: 15px;
            line-height: 1.6;
            font-size: 1.1rem;
            color: #333;
        }
        
        .notification-date {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .notification-icon {
            font-size: 1.2rem;
        }
        
        .unread-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            background: #2196f3;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.3);
        }
        
        .no-notifications {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .no-notifications i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.3;
            display: block;
        }
        
        .notification-category {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .category-booking {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .category-request {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .category-system {
            background: #f3e5f5;
            color: #7b1fa2;
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
                <li><a href="notifications.php" class="active">
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
                <h1>Notifications</h1>
                <p>Stay updated with your rental activities</p>
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
        <div class="notifications-container">
            <div class="section-header">
                <h2>Recent Notifications</h2>
                <div class="results-count">
                    <?php echo mysqli_num_rows($result); ?> notifications
                </div>
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="notification-item <?php echo $row['read_status'] == 0 ? 'unread' : ''; ?>">
                        <?php if ($row['read_status'] == 0): ?>
                            <div class="unread-indicator"></div>
                        <?php endif; ?>
                        
                        <div class="notification-category category-system">
                            System Notification
                        </div>
                        
                        <div class="notification-content">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </div>
                        
                        <div class="notification-date">
                            <span class="notification-icon">📅</span>
                            <?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-notifications">
                    <span class="notification-icon">🔔</span>
                    <h3>No Notifications</h3>
                    <p>You don't have any notifications at this time.</p>
                    <p>Check back later for updates on your bookings and requests.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>