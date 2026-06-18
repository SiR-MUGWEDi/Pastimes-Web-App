<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get orders for the logged-in user
$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM tblOrder WHERE user_id = $user_id ORDER BY created_at DESC");
$grandTotal = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order History - Pastimes</title>
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
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .orders-container {
            max-width: 1000px;
            width: 100%;
            margin: 20px auto;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
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

        /* Header */
        .orders-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .orders-header h1 {
            font-size: 28px;
            color: #333;
        }

        .orders-header h1 span {
            color: #667eea;
        }

        .orders-header p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Order Card */
        .order-box {
            border: 1px solid #e9ecef;
            margin-bottom: 25px;
            padding: 20px 25px;
            border-radius: 12px;
            background: #f8f9fa;
            transition: box-shadow 0.3s, transform 0.2s;
        }

        .order-box:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .order-box .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .order-box .order-header h3 {
            color: #333;
            font-size: 17px;
            margin: 0;
        }

        .order-box .order-header h3 .order-id {
            color: #667eea;
        }

        .order-box .order-header .order-status {
            padding: 5px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background: #cce5ff;
            color: #004085;
        }
        .status-shipped {
            background: #d4edda;
            color: #155724;
        }
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-box .order-date {
            color: #888;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .order-box .order-date .icon {
            margin-right: 5px;
        }

        /* Order Items Table */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .order-items-table th,
        .order-items-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .order-items-table th {
            background: #f1f3f5;
            color: #555;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-items-table tr:last-child td {
            border-bottom: none;
        }

        .order-items-table .item-name {
            font-weight: 500;
            color: #333;
        }

        .order-box .order-total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            color: #333;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
            margin-top: 5px;
        }

        .order-box .order-total .amount {
            color: #667eea;
            font-size: 18px;
        }

        /* Grand Total */
        .grand-total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            padding: 18px 20px;
            border-top: 3px solid #667eea;
            margin-top: 10px;
            background: #f8f9ff;
            border-radius: 8px;
            color: #333;
        }

        .grand-total .amount {
            color: #667eea;
            font-size: 24px;
        }

        /* Empty State */
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-orders .empty-icon {
            font-size: 72px;
            margin-bottom: 15px;
            display: block;
        }

        .empty-orders h2 {
            color: #333;
            margin-bottom: 8px;
            font-size: 24px;
        }

        .empty-orders p {
            color: #999;
            margin-bottom: 25px;
            font-size: 15px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 35px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, opacity 0.2s;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        /* Back Link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #5a67d8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .orders-container {
                padding: 20px;
                margin: 10px;
            }

            .orders-header h1 {
                font-size: 22px;
            }

            .order-box {
                padding: 15px;
            }

            .order-box .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .order-items-table th,
            .order-items-table td {
                font-size: 12px;
                padding: 8px 6px;
            }

            .grand-total {
                font-size: 16px;
                padding: 12px 15px;
            }

            .grand-total .amount {
                font-size: 18px;
            }

            .btn {
                padding: 10px 25px;
                font-size: 14px;
            }

            .empty-orders .empty-icon {
                font-size: 50px;
            }

            .empty-orders h2 {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .orders-container {
                padding: 15px;
            }

            .order-box {
                padding: 12px;
            }

            .order-items-table th,
            .order-items-table td {
                font-size: 11px;
                padding: 6px 4px;
            }

            .order-box .order-total {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="orders-container">
    <div class="orders-header">
        <h1>📋 <span>Order History</span></h1>
        <p>View all your past purchases</p>
    </div>

    <?php if ($orders && $orders->num_rows > 0): ?>
        <?php while ($o = $orders->fetch_assoc()): 
            $grandTotal += $o['total_amount'];
            $items = $conn->query("SELECT oi.*, c.item_name FROM tblOrderItem oi JOIN tblClothes c ON oi.product_id = c.item_id WHERE oi.order_id = {$o['order_id']}");
        ?>
        <div class="order-box">
            <div class="order-header">
                <h3>📦 Order <span class="order-id">#<?php echo $o['order_number']; ?></span></h3>
                <span class="order-status status-<?php echo strtolower($o['status'] ?? 'pending'); ?>">
                    <?php echo ucfirst($o['status'] ?? 'Pending'); ?>
                </span>
            </div>
            <div class="order-date">
                <span class="icon">📅</span> <?php echo date('F j, Y g:i A', strtotime($o['created_at'])); ?>
            </div>
            
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($items && $items->num_rows > 0): ?>
                    <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>R<?php echo number_format($item['price_at_time'], 2); ?></td>
                        <td>R<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999; padding: 15px;">
                            No items found for this order
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            
            <div class="order-total">
                Order Total: <span class="amount">R<?php echo number_format($o['total_amount'], 2); ?></span>
            </div>
        </div>
        <?php endwhile; ?>
        
        <div class="grand-total">
            🏆 Grand Total of All Purchases: <span class="amount">R<?php echo number_format($grandTotal, 2); ?></span>
        </div>
    <?php else: ?>
        <div class="empty-orders">
            <span class="empty-icon">🛒</span>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders yet. Start shopping now!</p>
            <a href="user_dashboard.php" class="btn btn-primary">🛍️ Start Shopping</a>
        </div>
    <?php endif; ?>

    <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>