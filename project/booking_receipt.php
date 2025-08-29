
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, i.title AS item_title, i.cost, i.cost_type,
               u1.name AS borrower_name, u1.email AS borrower_email,
               u2.name AS owner_name, u2.email AS owner_email
        FROM bookings b
        JOIN items i ON b.item_id = i.id
        JOIN users u1 ON b.borrower_id = u1.id
        JOIN users u2 ON i.owner_id = u2.id
        WHERE b.id = $booking_id 
          AND (b.borrower_id = $user_id OR i.owner_id = $user_id)";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    echo "Access denied or booking not found.";
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt #<?php echo $booking['id']; ?></title>
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
            line-height: 1.6;
            padding: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .receipt-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .receipt-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .receipt-details {
            padding: 30px;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .detail-section h2 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.4rem;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
            font-size: 1.1rem;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .status-approved, .status-borrowed {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-returned {
            background: #e2e3e5;
            color: #383d41;
        }
        .receipt-footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .receipt-footer p {
            color: #666;
            margin-bottom: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .print-btn, .back-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .print-btn {
            background: #28a745;
            color: white;
        }
        .print-btn:hover {
            background: #218838;
        }
        .back-btn {
            background: #6c757d;
            color: white;
        }
        .back-btn:hover {
            background: #5a6268;
            text-decoration: none;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border-radius: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .receipt-id {
            font-size: 1.2rem;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Booking Receipt</h1>
            <p>Thank you for using our rental service</p>
            <div class="receipt-id">Receipt ID: #<?php echo $booking['id']; ?></div>
        </div>

        <div class="receipt-details">
            <div class="detail-section">
                <h2>Booking Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Booking Date</div>
                        <div class="detail-value"><?php echo date('F j, Y g:i A', strtotime($booking['created_at'])); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Item</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['item_title']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Cost</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['cost'] . " " . $booking['cost_type']); ?></div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h2>Borrower Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['borrower_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['borrower_email']); ?></div>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h2>Owner Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['owner_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['owner_email']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="receipt-footer no-print">
            <p>This is a digital receipt for your booking. You may print this for your records.</p>
            <div class="action-buttons">
                <button onclick="window.print()" class="print-btn">Print Receipt</button>
                <a href="manage_bookings.php" class="back-btn">Back to Bookings</a>
            </div>
        </div>
    </div>
</body>
</html>
