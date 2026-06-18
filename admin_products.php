<?php
session_start();
include 'includes/DBConn.php';
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tblClothes WHERE item_id = $id");
    header("Location: admin_products.php");
    exit();
}

$products = $conn->query("SELECT * FROM tblClothes ORDER BY item_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 25px; 
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .page-header h1 {
            color: #333;
            margin: 0;
        }
        .btn { 
            padding: 8px 18px; 
            text-decoration: none; 
            border-radius: 5px; 
            color: white; 
            font-weight: 600;
            transition: transform 0.2s, opacity 0.2s;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .btn-add { background: #28a745; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-del { background: #dc3545; }
        .btn-back { background: #6c757d; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
        }
        th, td { 
            padding: 10px 8px; 
            border-bottom: 1px solid #ddd; 
            text-align: left; 
        }
        th { 
            background: #f8f9fa; 
            font-weight: 600;
            color: #495057;
        }
        .actions a { 
            margin-right: 5px; 
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            background: #f0f0f0;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>📦 Manage Products</h1>
        <div>
            <a href="admin_add_product.php" class="btn btn-add">➕ Add New Product</a>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products->num_rows == 0): ?>
                <tr><td colspan="7" style="text-align:center; padding:30px;">No products found</td></tr>
            <?php endif; ?>
            <?php while ($p = $products->fetch_assoc()): ?>
            <tr>
                <td><?php echo $p['item_id']; ?></td>
                <td><img src="<?php echo $p['image_path']; ?>" class="product-image" onerror="this.src='images/placeholder.jpg'"></td>
                <td><?php echo htmlspecialchars($p['item_name']); ?></td>
                <td><?php echo htmlspecialchars($p['brand']); ?></td>
                <td>R<?php echo number_format($p['price'],2); ?></td>
                <td><?php echo $p['status'] ?? 'available'; ?></td>
                <td class="actions">
                    <a href="admin_edit_product.php?id=<?php echo $p['item_id']; ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="?delete=<?php echo $p['item_id']; ?>" class="btn btn-del" onclick="return confirm('Delete this product?')">🗑️ Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="action-buttons">
        <a href="admin_dashboard.php" class="btn btn-back">⬅️ Back to Dashboard</a>
    </div>
</div>
</body>
</html>