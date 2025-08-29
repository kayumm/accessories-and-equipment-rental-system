
<?php
session_start();
include 'db.php';
include 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['return'])) {
    $booking_id = intval($_GET['return']);
    $result = mysqli_query($conn, "SELECT b.item_id, i.owner_id, i.title 
                                   FROM bookings b
                                   JOIN items i ON b.item_id = i.id
                                   WHERE b.id = $booking_id");

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        if ($data['owner_id'] == $user_id) {
            mysqli_query($conn, "UPDATE bookings SET status='returned' WHERE id=$booking_id");
            mysqli_query($conn, "UPDATE items SET availability='available' WHERE id={$data['item_id']}");

            $borrower_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT borrower_id FROM bookings WHERE id=$booking_id"))['borrower_id'];
            addNotification($conn, $borrower_id, "Your booking for '{$data['title']}' has been marked as returned.");
        }
    }
    header("Location: manage_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - Equipment and Accessories Rental System</title>
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
        
        .bookings-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .bookings-header h1 {
            margin-bottom: 10px;
        }
        
        .tabs-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .tab-button {
            flex: 1;
            padding: 18px 20px;
            background: #f8f9fa;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .tab-button:hover {
            background: #e9ecef;
        }
        
        .tab-button.active {
            background: #007bff;
            color: white;
        }
        
        .tab-content {
            display: none;
            padding: 25px;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .section-title {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .bookings-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
        }
        
        .bookings-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .bookings-table tr:last-child td {
            border-bottom: none;
        }
        
        .bookings-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-borrowed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-returned {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .action-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        
        .action-link:hover {
            text-decoration: underline;
        }
        
        .return-btn {
            background: #28a745;
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
        
        .return-btn:hover {
            background: #218838;
            text-decoration: none;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
        }
    </style>
    <script>
        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
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
                <li><a href="manage_bookings.php" class="active">
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
                <h1>Manage Bookings</h1>
                <p>Track your rentals and manage incoming requests</p>
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
        <div class="tabs-container">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="openTab('outgoing')">My Bookings (Outgoing)</button>
                <button class="tab-button" onclick="openTab('incoming')">My Items (Incoming)</button>
            </div>

            <div id="outgoing" class="tab-content active">
                <h2 class="section-title">My Bookings</h2>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $outgoing = mysqli_query($conn, "SELECT b.*, i.title AS item_title, u.name AS owner_name
                                                          FROM bookings b
                                                          JOIN items i ON b.item_id = i.id
                                                          JOIN users u ON i.owner_id = u.id
                                                          WHERE b.borrower_id = $user_id
                                                          ORDER BY b.created_at DESC");
                        if (mysqli_num_rows($outgoing) == 0) {
                            echo "<tr><td colspan='5' class='no-bookings'>No bookings found.</td></tr>";
                        }
                        while ($b = mysqli_fetch_assoc($outgoing)) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['item_title']); ?></td>
                                <td><?php echo htmlspecialchars($b['owner_name']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $b['status']; ?>">
                                        <?php echo ucfirst($b['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($b['created_at'])); ?></td>
                                <td>
                                    <a href="booking_receipt.php?id=<?php echo $b['id']; ?>" class="action-link">View Receipt</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="incoming" class="tab-content">
                <h2 class="section-title">Bookings for My Items</h2>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Borrower</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $incoming = mysqli_query($conn, "SELECT b.*, i.title AS item_title, u.name AS borrower_name
                                                          FROM bookings b
                                                          JOIN items i ON b.item_id = i.id
                                                          JOIN users u ON b.borrower_id = u.id
                                                          WHERE i.owner_id = $user_id
                                                          ORDER BY b.created_at DESC");
                        if (mysqli_num_rows($incoming) == 0) {
                            echo "<tr><td colspan='5' class='no-bookings'>No bookings received.</td></tr>";
                        }
                        while ($b = mysqli_fetch_assoc($incoming)) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($b['item_title']); ?></td>
                                <td><?php echo htmlspecialchars($b['borrower_name']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $b['status']; ?>">
                                        <?php echo ucfirst($b['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($b['created_at'])); ?></td>
                                <td>
                                    <?php if ($b['status'] == 'approved') { ?>
                                        <a href="manage_bookings.php?return=<?php echo $b['id']; ?>"
                                           onclick="return confirm('Mark this item as returned?')"
                                           class="return-btn">Mark as Returned</a>
                                    <?php } else { ?>
                                        <span class="status-<?php echo $b['status']; ?>">
                                            <?php echo ucfirst($b['status']); ?>
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
