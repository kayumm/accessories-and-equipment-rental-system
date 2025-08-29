<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    if ($uid != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$uid");
    }
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_GET['delete_item'])) {
    $item_id = intval($_GET['delete_item']);
    mysqli_query($conn, "DELETE FROM items WHERE id=$item_id");
    header("Location: admin_dashboard.php");
    exit();
}

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM items"))['count'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings"))['count'];
$total_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM item_requests"))['count'];
$users = mysqli_query($conn, "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$items = mysqli_query($conn, "SELECT i.id, i.title, u.name AS owner, i.created_at FROM items i JOIN users u ON i.owner_id = u.id ORDER BY i.created_at DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Equipment and Accessories Rental System</title>
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
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu h3 {
            padding: 15px 20px 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            color: white;
        }
        .sidebar-menu ul { list-style: none; }
        .sidebar-menu li { margin: 0; }
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
        .user-details { text-align: right; }
        .user-details .name {
            font-weight: 600;
            color: #2c3e50;
        }
        .user-details .role {
            font-size: 0.9rem;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
            color: white;
        }
        
        .users-icon { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
        .items-icon { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }
        .bookings-icon { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .requests-icon { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); }
        
        .stat-info h3 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #666;
            font-size: 0.95rem;
        }
        .admin-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .admin-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .admin-section-header h2 { color: #2c3e50; }
        .view-all-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .view-all-link:hover { text-decoration: underline; }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
        }
        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .admin-table tr:hover { background-color: #f8f9fa; }
        .action-links a {
            color: #dc3545;
            font-weight: 500;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-links a:hover { text-decoration: underline; }
        .date-text {
            color: #666;
            font-size: 0.9rem;
        }
        .no-data {
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
            <p>Admin Panel</p>
        </div>
        
        <div class="sidebar-menu">
            <h3>Navigation</h3>
            <ul>
                <li><a href="admin_dashboard.php" class="active">
                    <i>📊</i> <span>Dashboard</span>
                </a></li>
                
            </ul>
            <h3>Account</h3>
            <ul>
                <li><a href="logout.php">
                    <i>🚪</i> <span>Logout</span>
                </a></li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
            </div>
            <div class="user-info">
                <div class="user-details">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                    <div class="role">Administrator</div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users-icon">👥</div>
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon items-icon">📦</div>
                <div class="stat-info">
                    <h3><?php echo $total_items; ?></h3>
                    <p>Total Items</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bookings-icon">📅</div>
                <div class="stat-info">
                    <h3><?php echo $total_bookings; ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon requests-icon">💬</div>
                <div class="stat-info">
                    <h3><?php echo $total_requests; ?></h3>
                    <p>Item Requests</p>
                </div>
            </div>
        </div>
        
        <div class="admin-section">
            <div class="admin-section-header">
                <h2>All Users</h2>
            </div>
            
            <?php if (mysqli_num_rows($users) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo ucfirst($u['role']); ?></td>
                                <td class="date-text"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                                <td class="action-links">
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="admin_dashboard.php?delete_user=<?php echo $u['id']; ?>" 
                                           onclick="return confirm('Delete this user?')">Delete</a>
                                    <?php else: ?>
                                        <span style="color: #666;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No users found.</div>
            <?php endif; ?>
        </div>
        
        <div class="admin-section">
            <div class="admin-section-header">
                <h2>All Items</h2>
            </div>
            <?php if (mysqli_num_rows($items) > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item Title</th>
                            <th>Owner</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($i = mysqli_fetch_assoc($items)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($i['title']); ?></td>
                                <td><?php echo htmlspecialchars($i['owner']); ?></td>
                                <td class="date-text"><?php echo date('M j, Y', strtotime($i['created_at'])); ?></td>
                                <td class="action-links">
                                    <a href="admin_dashboard.php?delete_item=<?php echo $i['id']; ?>" 
                                       onclick="return confirm('Delete this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No items found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
