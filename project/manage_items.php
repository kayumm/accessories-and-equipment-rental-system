
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM items WHERE id=$item_id AND owner_id=$user_id");
    header("Location: manage_items.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM items WHERE owner_id=$user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Items - Equipment and Accessories Rental System</title>
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
        .add-item-btn {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: block;
            margin: 0 auto 30px;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .add-item-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .add-item-form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            display: none;
        }
        
        .add-item-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-title {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
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
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .cost-row {
            display: flex;
            gap: 15px;
        }
        
        .cost-row input {
            flex: 2;
        }
        
        .cost-row select {
            flex: 1;
        }
        
        .file-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .file-group > div {
            flex: 1;
            min-width: 250px;
        }
        
        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .submit-btn {
            background: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #218838;
        }
        
        .cancel-btn {
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .cancel-btn:hover {
            background: #5a6268;
        }
        
        /* Items Section */
        .items-section {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
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
        
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .item-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .item-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        
        .item-content {
            padding: 15px;
        }
        
        .item-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        
        .item-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        
        .item-availability {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .available {
            background: #d4edda;
            color: #155724;
        }
        
        .unavailable {
            background: #f8d7da;
            color: #721c24;
        }
        
        .item-actions {
            display: flex;
            gap: 10px;
            padding: 0 15px 15px;
        }
        
        .action-btn {
            flex: 1;
            padding: 8px;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .view-btn {
            background: #007bff;
            color: white;
        }
        
        .view-btn:hover {
            background: #0056b3;
        }
        
        .edit-btn {
            background: #ffc107;
            color: #212529;
        }
        
        .edit-btn:hover {
            background: #e0a800;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
        
        .no-items {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            grid-column: 1 / -1;
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
                <h1>Manage Items</h1>
                <p>Add, edit, or remove your rental items</p>
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

        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="success-message">
                Item added successfully!
            </div>
        <?php endif; ?>

        <button class="add-item-btn" onclick="toggleForm()">+ Add New Item</button>

        <div class="add-item-form" id="addItemForm">
            <h2 class="form-title">Add New Item</h2>
            <form action="handle_item.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="title" required>
                        </div>

                        <div class="form-group">
                            <label>Description:</label>
                            <textarea name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Location:</label>
                            <input type="text" name="location" required>
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label>Cost:</label>
                            <div class="cost-row">
                                <input type="number" step="0.01" name="cost" required>
                                <select name="cost_type">
                                    <option value="per_day">Per Day</option>
                                    <option value="per_hour">Per Hour</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" name="phone" required>
                        </div>

                        <div class="file-group">
                            <div class="form-group">
                                <label>Main Image:</label>
                                <input type="file" name="image">
                            </div>

                            <div class="form-group">
                                <label>Additional Images:</label>
                                <input type="file" name="images[]" multiple>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" name="add_item" class="submit-btn">Add Item</button>
                    <button type="button" class="cancel-btn" onclick="toggleForm()">Cancel</button>
                </div>
            </form>
        </div>

        <div class="items-section">
            <div class="section-header">
                <h2>My Items</h2>
                <div class="results-count">
                    <?php echo mysqli_num_rows($result); ?> items
                </div>
            </div>
            <div class="items-grid">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="item-card">
                            <?php if ($row['image']): ?>
                                <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="item-image">
                            <?php else: ?>
                                <div class="item-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9rem;">
                                    No Image
                                </div>
                            <?php endif; ?>
                            <div class="item-content">
                                <div class="item-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                <div class="item-desc"><?php echo htmlspecialchars(substr($row['description'], 0, 80)) . (strlen($row['description']) > 80 ? '...' : ''); ?></div>
                                <span class="item-availability <?php echo $row['availability']; ?>">
                                    <?php echo ucfirst($row['availability']); ?>
                                </span>
                            </div>
                            <div class="item-actions">
                                <a href="item.php?id=<?php echo $row['id']; ?>&redirect=manage" class="action-btn view-btn">View</a>
                                <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="manage_items.php?delete=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Delete this item?')" 
                                   class="action-btn delete-btn">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-items">
                        <p>You haven't added any items yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('addItemForm');
            form.classList.toggle('active');
        }
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            setTimeout(function() {
                document.getElementById('addItemForm').classList.remove('active');
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>