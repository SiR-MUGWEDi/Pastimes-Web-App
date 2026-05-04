<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$orders = $conn->query("SELECT * FROM tblOrders WHERE user_id={$_SESSION['user_id']} ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Order History - Pastimes</title>
<link rel="stylesheet" href="css/style.css">
<style>
    .orders-container {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    .orders-container h1 {
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    .order-table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-table th, .order-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .order-table th {
        background: #f8f9fa;
        color: #333;
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #667eea;
        text-decoration: none;
    }
    .empty-message {
        text-align: center;
        color: #666;
        padding: 40px;
    }
</style>
</head>
<body>

<div class="container">
<div class="orders-container">
<h1>📋 Order History</h1>

<?php if ($orders->num_rows == 0): ?>
<div class="empty-message">
    <p>You haven't placed any orders yet.</p>
    <a href="user_dashboard.php" style="color:#667eea;">← Start Shopping</a>
</div>
<?php else: ?>

<table class="order-table">
<thead>
<tr>
<th>Order ID</th>
<th>Total Amount</th>
<th>Order Date</th>
</tr>
</thead>
<tbody>
<?php while($o = $orders->fetch_assoc()): ?>
<tr>
<td>#<?php echo $o['order_id']; ?></td>
<td>R<?php echo number_format($o['total'],2); ?></td>
<td><?php echo date('F j, Y g:i A', strtotime($o['order_date'])); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php endif; ?>

<a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>

</div>
</div>

<script src="js/main.js"></script>
</body>
</html>