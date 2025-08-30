<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_GET['delete_request'])) {
    $rid = intval($_GET['delete_request']);
    mysqli_query($conn, "DELETE FROM item_requests WHERE id=$rid AND user_id=".$_SESSION['user_id']);
    header("Location: view_requests.php");
    exit();
}

if (isset($_POST['respond'])) {
    $rid = intval($_POST['request_id']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO request_responses (request_id, responder_id, message) 
                         VALUES ($rid, ".$_SESSION['user_id'].", '$msg')");
    header("Location: view_requests.php");
    exit();
}

$requests = mysqli_query($conn, "SELECT r.*, u.name FROM item_requests r JOIN users u ON r.user_id=u.id ORDER BY r.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Requests - Equipment and Accessories Rental System</title>
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
        .action-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            margin-bottom: 20px;
        }
        
        .action-btn:hover {
            background: #0056b3;
            text-decoration: none;
        }
        
        .requests-container {
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
        
        .request-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            transition: box-shadow 0.3s ease;
        }
        .request-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .request-title {
            color: #2c3e50;
            margin: 0;
            font-size: 1.3rem;
        }
        
        .request-meta {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .requester-name {
            font-weight: 600;
            color: #007bff;
        }
        
        .request-date {
            color: #666;
        }
        
        .request-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .request-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .delete-btn:hover {
            background: #c82333;
            text-decoration: none;
        }
        
        /* Responses Section */
        .responses-section {
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .responses-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .response-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .response-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .responder-name {
            font-weight: 600;
            color: #007bff;
        }
        
        .response-date {
            color: #666;
            font-size: 0.85rem;
        }
        
        .response-message {
            color: #555;
            line-height: 1.5;
        }
        
        .no-responses {
            color: #666;
            font-style: italic;
            margin-bottom: 15px;
        }
        
        /* Response Form */
        .response-form {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin-top: 15px;
        }
        
        .response-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            margin-bottom: 15px;
            resize: vertical;
            min-height: 80px;
        }
        
        .response-form button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .response-form button:hover {
            background: #0056b3;
        }
        
        .no-requests {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        
        .no-requests p {
            margin-bottom: 20px;
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
                <li><a href="view_requests.php" class="active">
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
                <h1>View Requests</h1>
                <p>Find or respond to item requests from other users</p>
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
        <a href="request_item.php" class="action-btn">+ Make a New Request</a>

        <div class="requests-container">
            <div class="section-header">
                <h2>All Requests</h2>
                <div class="results-count">
                    <?php echo mysqli_num_rows($requests); ?> requests
                </div>
            </div>
            
            <?php if (mysqli_num_rows($requests) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($requests)): ?>
                    <div class="request-item">
                        <div class="request-header">
                            <h3 class="request-title"><?php echo htmlspecialchars($r['title']); ?></h3>
                            <div class="request-meta">
                                <span class="requester-name">by <?php echo htmlspecialchars($r['name']); ?></span>
                                <span class="request-date"><?php echo date('M j, Y g:i A', strtotime($r['created_at'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="request-description">
                            <?php echo nl2br(htmlspecialchars($r['description'])); ?>
                        </div>
                        
                        <?php if ($r['user_id'] == $_SESSION['user_id']): ?>
                            <div class="request-actions">
                                <a href="view_requests.php?delete_request=<?php echo $r['id']; ?>" 
                                   onclick="return confirm('Delete this request?')" 
                                   class="delete-btn">Delete Request</a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="responses-section">
                            <h3>Responses</h3>
                            <?php
                            $responses = mysqli_query($conn, "SELECT rr.*, u.name FROM request_responses rr 
                                                              JOIN users u ON rr.responder_id=u.id
                                                              WHERE rr.request_id=".$r['id']." ORDER BY rr.created_at DESC");
                            
                            if (mysqli_num_rows($responses) > 0):
                                while ($resp = mysqli_fetch_assoc($responses)):
                            ?>
                                <div class="response-item">
                                    <div class="response-header">
                                        <span class="responder-name"><?php echo htmlspecialchars($resp['name']); ?></span>
                                        <span class="response-date"><?php echo date('M j, Y g:i A', strtotime($resp['created_at'])); ?></span>
                                    </div>
                                    <div class="response-message">
                                        <?php echo nl2br(htmlspecialchars($resp['message'])); ?>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            else:
                                echo "<p class='no-responses'>No responses yet. Be the first to respond!</p>";
                            endif;
                            ?>
                            
                            <div class="response-form">
                                <form method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                                    <textarea name="message" required placeholder="Write your response here..."></textarea>
                                    <button type="submit" name="respond">Send Response</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-requests">
                    <p>No item requests found.</p>
                    <p>
                        <a href="request_item.php" class="action-btn">Be the first to make a request</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>