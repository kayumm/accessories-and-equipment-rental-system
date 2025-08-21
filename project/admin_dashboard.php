<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete user
if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    
    if ($uid != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$uid");
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Delete item
if (isset($_GET['delete_item'])) {
    $item_id = intval($_GET['delete_item']);
    mysqli_query($conn, "DELETE FROM items WHERE id=$item_id");
    header("Location: admin_dashboard.php");
    exit();
}

$users = mysqli_query($conn, "SELECT id, name, email, role FROM users");

$items = mysqli_query($conn, "SELECT i.id, i.title, u.name AS owner FROM items i JOIN users u ON i.owner_id = u.id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (<a href="logout.php">Logout</a>)</p>
    <hr>

    <h2>Manage Users</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while ($u = mysqli_fetch_assoc($users)) { ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo $u['role']; ?></td>
                <td>
                    <?php if ($u['id'] != $_SESSION['user_id']) { ?>
                        <a href="admin_dashboard.php?delete_user=<?php echo $u['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
                    <?php } else { echo "—"; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Manage Items</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Owner</th>
            <th>Action</th>
        </tr>
        <?php while ($i = mysqli_fetch_assoc($items)) { ?>
            <tr>
                <td><?php echo $i['id']; ?></td>
                <td><?php echo htmlspecialchars($i['title']); ?></td>
                <td><?php echo htmlspecialchars($i['owner']); ?></td>
                <td>
                    <a href="admin_dashboard.php?delete_item=<?php echo $i['id']; ?>" onclick="return confirm('Delete this item?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
