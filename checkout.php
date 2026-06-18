<?php
session_start();
include __DIR__ . '/includes/DBConn.php';
include __DIR__ . '/includes/ShoppingCart.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart = new ShoppingCart();
$message = "";
$orderData = null;
$items = $cart->getItems();
$total = $cart->getTotal();
$paymentMethod = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    $paymentMethod = isset($_POST['payment']) ? $_POST['payment'] : 'cod';
    
    try {
        $orderData = $cart->checkout($_SESSION['user_id']);
        
        // Different messages based on payment method
        if ($paymentMethod == 'cod') {
            $message = "✅ Order Placed Successfully! <br><br>
            <strong>Order Number:</strong> " . $orderData['orderNumber'] . "<br>
            <strong>Payment Method:</strong> Cash on Delivery<br>
            <strong>Total Amount:</strong> R" . number_format($total,2) . "<br><br>
            📦 Your order will be delivered to your address. Please have cash ready upon delivery.";
        } else {
            $message = "✅ Payment Successful! <br><br>
            <strong>Order Number:</strong> " . $orderData['orderNumber'] . "<br>
            <strong>Payment Method:</strong> Card Payment<br>
            <strong>Total Paid:</strong> R" . number_format($total,2) . "<br><br>
            📧 A confirmation email has been sent to your registered email address.";
        }
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage() . "<br><br>Please contact support.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Pastimes</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .checkout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 700px;
            padding: 40px;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .checkout-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .checkout-header h1 span {
            color: #667eea;
        }

        .checkout-header p {
            color: #666;
            font-size: 14px;
        }

        .success-box {
            background: #d4edda;
            color: #155724;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 5px solid #28a745;
            text-align: center;
        }

        .success-box .order-number {
            font-size: 20px;
            font-weight: bold;
            color: #155724;
        }

        .success-box .delivery-note {
            margin-top: 12px;
            padding: 12px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
            font-size: 14px;
            color: #155724;
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .order-summary h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .order-summary .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 15px;
            color: #555;
        }

        .order-summary .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border-top: 2px solid #e9ecef;
            margin-top: 5px;
        }

        .order-summary .summary-total .amount {
            color: #667eea;
        }

        .payment-options {
            margin: 20px 0;
        }

        .payment-options h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 12px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .payment-option input[type="radio"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: #667eea;
            cursor: pointer;
        }

        .payment-option label {
            cursor: pointer;
            font-size: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .payment-option .icon {
            font-size: 22px;
        }

        .payment-option .payment-desc {
            font-size: 12px;
            color: #999;
            display: block;
            margin-top: 2px;
        }

        .btn-row {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, opacity 0.2s;
            text-align: center;
            flex: 1;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
        }

        .btn-continue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #dc3545;
        }

        .btn-row-single {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            justify-content: center;
        }

        .btn-row-single .btn {
            flex: 0 1 auto;
            min-width: 200px;
        }

        @media (max-width: 600px) {
            .checkout-container {
                padding: 25px;
            }
            .btn-row {
                flex-direction: column;
            }
            .btn-row-single {
                flex-direction: column;
                align-items: center;
            }
            .btn-row-single .btn {
                min-width: 100%;
            }
            .checkout-header h1 {
                font-size: 22px;
            }
            .order-summary .summary-total {
                font-size: 16px;
            }
            .success-box {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>💳 <span>Checkout</span></h1>
        <p>Review your payment details.</p>
    </div>

    <?php if ($message): ?>
        <div class="success-box">
            <?php echo $message; ?>
        </div>
        <div class="btn-row-single">
            <a href="user_dashboard.php" class="btn btn-continue">🛍️ Continue Shopping</a>
        </div>
    <?php else: ?>
        <!-- Order Summary -->
        <div class="order-summary">
            <h2>📋 Order Summary</h2>
            
            <?php if (empty($items)): ?>
                <p style="color: #999; text-align: center; padding: 20px 0;">Your cart is empty.</p>
                <a href="user_dashboard.php" class="btn btn-secondary" style="display: block; text-align: center;">← Back to Shopping</a>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                <div class="summary-row">
                    <span><?php echo htmlspecialchars($item['item_name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span>R<?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="summary-total">
                    <span>Total:</span>
                    <span class="amount">R<?php echo number_format($total, 2); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($items)): ?>
            <form method="POST">
                <div class="payment-options">
                    <h3>💳 Select Payment Method</h3>
                    
                    <div class="payment-option">
                        <input type="radio" name="payment" value="cod" checked id="cod">
                        <label for="cod">
                            <span class="icon">💵</span>
                            <div>
                                Cash on Delivery
                                <span class="payment-desc">Pay when your order arrives</span>
                            </div>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" name="payment" value="card" id="card">
                        <label for="card">
                            <span class="icon">💳</span>
                            <div>
                                Card Payment (Simulated)
                                <span class="payment-desc">Pay instantly with your card</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" name="confirm" class="btn btn-success">✅ Place Order</button>
                    <a href="user_dashboard.php" class="btn btn-secondary">← Back to Cart</a>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>