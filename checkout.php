<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_SESSION['cart'])) {
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $res = $conn->query("SELECT price FROM tblClothes WHERE item_id=$id");
            if ($row = $res->fetch_assoc()) {
                $total += $row['price'] * $qty;
            }
        }
        
        $order_number = "ORD-" . time();
        $conn->query("INSERT INTO tblOrders (user_id, total) VALUES ({$_SESSION['user_id']}, $total)");
        $_SESSION['cart'] = [];
        
        $message = "✅ Payment Successful! <br><br>
        <strong>Order Number:</strong> $order_number <br>
        <strong>Total Paid:</strong> R" . number_format($total,2);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout - Pastimes</title>
<link rel="stylesheet" href="css/style.css">
<style>
    .checkout-container {
        text-align: center;
    }
    .payment-option {
        margin: 15px 0;
        text-align: left;
    }
    .success-box {
        background: #d4edda;
        color: #155724;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
</head>
<body>

<div class="container">
<div class="form-container checkout-container">
<h1>💳 Checkout</h1>

<?php if ($message): ?>
<div class="success-box">
    <?php echo $message; ?>
</div>
<a href="user_dashboard.php" style="color:#667eea;">← Continue Shopping</a>
<?php else: ?>

<form method="POST">
<div class="payment-option">
    <label>
        <input type="radio" name="payment" checked> 💵 Cash on Delivery
    </label>
</div>
<div class="payment-option">
    <label>
        <input type="radio" name="payment"> 💳 Card Payment (Simulated)
    </label>
</div>
<button type="submit">Confirm Payment</button>
</form>

<p style="text-align:center; margin-top:15px;">
<a href="user_dashboard.php" style="color:#667eea;">← Back to Cart</a>
</p>

<?php endif; ?>

</div>
</div>

<script src="js/main.js"></script>
</body>
</html>