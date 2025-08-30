
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_POST['submit_request'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $uid = $_SESSION['user_id'];
    mysqli_query($conn, "INSERT INTO item_requests (user_id, title, description) 
                         VALUES ($uid, '$title', '$desc')");
    header("Location: view_requests.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Item - Equipment and Accessories Rental System</title>
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
        
        /* Sidebar Styles */
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
        
        /* Main Content Styles */
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
        
        /* Request Form Container */
        .request-form-container {
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
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-hint {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
        
        /* Form Buttons */
        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .submit-btn {
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
        
        .submit-btn:hover {
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
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
                <li><a href="request_item.php" class="active">
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
                <h1>Request Item</h1>
                <p>Can't find what you need? Request it and we'll help you find it!</p>
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
        <div class="request-form-container">
            <h2 class="form-title">Item Request Form</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label for="title">Item Title *</label>
                    <input type="text" id="title" name="title" required placeholder="Enter the name of the item you're looking for">
                    <div class="form-hint">Be specific about the item you need (e.g., "Canon EOS R5 Camera")</div>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required placeholder="Describe the item in detail:
- Brand/Model
- Specifications
- Condition preferred
- Rental duration
- Any other requirements"></textarea>
                    <div class="form-hint">Provide as much detail as possible to help others find the right item for you</div>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="submit_request" class="submit-btn">Submit Request</button>
                    <a href="view_requests.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>