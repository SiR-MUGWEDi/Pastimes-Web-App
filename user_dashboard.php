<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add_to_cart'])) {
    $id = intval($_GET['add_to_cart']);
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header("Location: user_dashboard.php");
    exit();
}

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][intval($_GET['remove'])]);
    header("Location: user_dashboard.php");
    exit();
}

// Get all products
$products = $conn->query("SELECT * FROM tblClothes");

$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $res = $conn->query("SELECT * FROM tblClothes WHERE item_id=$id");
    if ($item = $res->fetch_assoc()) {
        $item['qty'] = $qty;
        $item['subtotal'] = $qty * $item['price'];
        $total += $item['subtotal'];
        $cart_items[] = $item;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pastimes - Second Hand Clothing</title>
<link rel="stylesheet" href="css/style.css">
<style>
    /* Additional image styling */
    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background-color: #f0f0f0;
    }
    .product-card {
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>
</head>
<body>

<div class="shop-container">

<header class="shop-header">
<h1>🛍️ Pastimes Second-Hand Clothing</h1>
<div>
<span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span> |
<a href="order_history.php">📋 My Orders</a> |
<a href="contact.php">📧 Support</a> |
<a href="logout.php">🚪 Logout</a>
</div>
</header>

<div class="shop-content">

<div class="products-section">
<h2>📦 Available Items</h2>

<div class="products-grid">
<?php while ($p = $products->fetch_assoc()): ?>
<div class="product-card">
    <?php 
    // Check if image exists, if not use placeholder
    $imgPath = $p['image_path'];
    $fullPath = __DIR__ . '/' . $imgPath;
    
    if (!file_exists($fullPath) || empty($imgPath)) {
        $imgPath = 'images/placeholder.jpg';
    }
    ?>
    <img src="<?php echo $imgPath; ?>" class="product-image" 
         alt="<?php echo htmlspecialchars($p['item_name']); ?>"
         onerror="this.src='images/placeholder.jpg'; this.onerror=null;">
    
    <h3><?php echo htmlspecialchars($p['item_name']); ?></h3>
    <p><strong>Brand:</strong> <?php echo htmlspecialchars($p['brand']); ?></p>
    <p><?php echo htmlspecialchars($p['description']); ?></p>
    <p class="price">💰 R<?php echo number_format($p['price'],2); ?></p>
    <a href="?add_to_cart=<?php echo $p['item_id']; ?>" class="add-to-cart-btn">➕ Add to Cart</a>
</div>
<?php endwhile; ?>
</div>
</div>

<div class="cart-section">
<h2>🛒 Your Cart</h2>

<?php if (empty($cart_items)): ?>
<p>Your cart is empty. Add some items above!</p>
<?php else: ?>

<table class="cart-table">
<thead>
<tr>
<th>Item</th>
<th>Qty</th>
<th>Total</th>
<th></th>
</tr>
</thead>
<tbody>
<?php foreach ($cart_items as $c): ?>
<tr>
<td><?php echo htmlspecialchars($c['item_name']); ?></td>
<td><?php echo $c['qty']; ?></td>
<td>R<?php echo number_format($c['subtotal'],2); ?></td>
<td><a href="?remove=<?php echo $c['item_id']; ?>" class="btn-delete" onclick="return confirm('Remove item?')">❌ Remove</a></td>
</tr>
<?php endforeach; ?>
<tr style="border-top: 2px solid #ddd;">
<td colspan="2"><strong>Total Amount:</strong></td>
<td colspan="2"><strong>R<?php echo number_format($total,2); ?></strong></td>
</tr>
</tbody>
</table>

<a href="checkout.php" class="checkout-btn">💳 Proceed to Checkout</a>

<?php endif; ?>
</div>

</div>

<footer style="text-align: center; padding: 20px; background: #333; color: white; margin-top: 20px;">
© 2026 Pastimes Second-Hand Clothing - Quality Pre-loved Fashion
</footer>

</div>

<script src="js/main.js"></script>
</body>
</html>