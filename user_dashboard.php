<?php
session_start();
include __DIR__ . '/includes/DBConn.php';
include __DIR__ . '/includes/ShoppingCart.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart = new ShoppingCart();

// Add to cart
if (isset($_GET['add_to_cart'])) {
    $id = intval($_GET['add_to_cart']);
    $cart->addItem($id);
    header("Location: user_dashboard.php");
    exit();
}

// Remove single item
if (isset($_GET['remove'])) {
    $cart->removeItem(intval($_GET['remove']));
    header("Location: user_dashboard.php");
    exit();
}

// Get all products (only available) - using DISTINCT to prevent duplicates
$products = $conn->query("SELECT DISTINCT * FROM tblClothes WHERE status = 'available' OR status IS NULL ORDER BY item_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pastimes - Second Hand Clothing</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .shop-container {
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }

        .shop-header {
            background: white;
            color: #333;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 10px;
            border-radius: 10px 10px 0 0;
        }

        .shop-header h1 {
            color: #667eea;
            margin: 0;
            font-size: 1.5rem;
        }

        .shop-header .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .shop-header a {
            color: #667eea;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 0.9rem;
        }

        .shop-header a:hover {
            background: #f0f2ff;
            text-decoration: none;
        }

        .shop-header .welcome-text {
            color: #333;
            font-weight: 500;
            margin-right: 10px;
        }

        .shop-content {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            flex-wrap: wrap;
        }

        .products-section {
            flex: 2;
            min-width: 250px;
        }

        .products-section h2 {
            color: white;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f0f0f0;
            display: block;
        }

        .product-card h3 {
            padding: 12px 15px 5px;
            font-size: 16px;
            color: #333;
            margin: 0;
        }

        .product-card p {
            padding: 0 15px;
            font-size: 13px;
            color: #666;
            margin: 3px 0;
        }

        .product-card .price {
            padding: 10px 15px;
            font-weight: bold;
            color: #667eea;
            font-size: 18px;
            margin-top: auto;
        }

        .add-to-cart-btn {
            display: block;
            margin: 10px 15px 15px;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s, transform 0.2s;
        }

        .add-to-cart-btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        /* Cart Section */
        .cart-section {
            flex: 1;
            min-width: 280px;
            max-width: 380px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
        }

        .cart-section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .cart-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .cart-table th,
        .cart-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .cart-table th {
            text-align: left;
            color: #666;
            font-weight: 600;
        }

        .cart-table .qty-input {
            width: 50px;
            padding: 4px 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background 0.3s;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .checkout-btn {
            display: block;
            margin-top: 15px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: opacity 0.3s, transform 0.2s;
        }

        .checkout-btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .update-cart-btn {
            display: block;
            margin-top: 10px;
            background: #ffc107;
            color: #333;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: opacity 0.3s, transform 0.2s;
        }

        .update-cart-btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .empty-cart {
            text-align: center;
            color: #999;
            padding: 30px 0;
        }

        .empty-cart p {
            font-size: 14px;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            background: rgba(0,0,0,0.15);
            border-radius: 0 0 10px 10px;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .shop-content {
                flex-direction: column;
            }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
            .shop-header {
                flex-direction: column;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .shop-header .nav-links {
                justify-content: center;
            }
            .cart-section {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            .shop-header h1 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="shop-container">
        <header class="shop-header">
            <h1>🛍️ Pastimes Second-Hand Clothing</h1>
            <div class="nav-links">
                <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="sell_request.php">📤 Sell</a>
                <a href="order_history.php">📋 My Orders</a>
                <a href="contact.php">📧 Support</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </header>

        <div class="shop-content">
            <!-- Products Section -->
            <div class="products-section">
                <h2>📦 Available Items</h2>
                <div class="products-grid">
                    <?php 
                    // Check if products exist
                    if ($products && $products->num_rows > 0):
                        while ($p = $products->fetch_assoc()): 
                    ?>
                    <div class="product-card">
                        <?php 
                        // Check if image exists, if not use placeholder
                        $imgPath = $p['image_path'] ?? 'images/placeholder.jpg';
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
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <p style="color: white; text-align: center; grid-column: 1/-1; padding: 40px; font-size: 1.1rem;">
                            No products available at the moment.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section">
                <h2>🛒 Your Cart</h2>
                <?php $items = $cart->getItems(); ?>
                <?php if (empty($items)): ?>
                    <div class="empty-cart">
                        <p>🛒 Your cart is empty</p>
                        <p style="font-size: 12px; color: #ccc;">Add some items above!</p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="update_cart.php">
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
                                <?php foreach ($items as $c): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($c['item_name']); ?></td>
                                    <td><input type="number" name="qty[<?php echo $c['item_id']; ?>]" value="<?php echo $c['quantity']; ?>" min="1" max="99" class="qty-input"></td>
                                    <td>R<?php echo number_format($c['subtotal'],2); ?></td>
                                    <td><a href="?remove=<?php echo $c['item_id']; ?>" class="btn-delete" onclick="return confirm('Remove item?')">✕</a></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr style="border-top:2px solid #ddd; font-weight:bold;">
                                    <td colspan="2">Total:</td>
                                    <td colspan="2">R<?php echo number_format($cart->getTotal(),2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" name="update_cart" class="update-cart-btn">🔄 Update Quantities</button>
                    </form>
                    <a href="checkout.php" class="checkout-btn">💳 Proceed to Checkout</a>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            © 2026 Pastimes Second-Hand Clothing - Quality Pre-loved Fashion
        </footer>
    </div>
    <script src="js/main.js"></script>
</body>
</html>