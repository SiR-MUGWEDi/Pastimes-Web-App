<?php
session_start();
include 'includes/DBConn.php';
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin_login.php");
    exit();
}

$id = intval($_GET['id']);
$msg = '';
$product = $conn->query("SELECT * FROM tblClothes WHERE item_id = $id")->fetch_assoc();
if (!$product) { die("Product not found"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $imagePath = $product['image_path'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = 'item' . time() . '.' . $ext;
        $target = 'images/' . $newName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    $sql = "UPDATE tblClothes SET item_name='$name', brand='$brand', description='$desc', price=$price, quantity=$qty, image_path='$imagePath', status='$status' WHERE item_id=$id";
    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Product updated successfully!";
        $product = $conn->query("SELECT * FROM tblClothes WHERE item_id = $id")->fetch_assoc();
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin</title>
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
            align-items: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .page-header h1 {
            color: #333;
            font-size: 24px;
            margin: 0;
        }

        .page-header h1 span {
            color: #667eea;
        }

        .message {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-group .current-image {
            max-width: 150px;
            max-height: 150px;
            display: block;
            margin: 10px 0;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 5px;
            background: #f8f9fa;
        }

        .form-group .file-input-wrapper {
            position: relative;
        }

        .form-group .file-input-wrapper input[type="file"] {
            padding: 8px;
            border: 2px dashed #ddd;
            background: #fafafa;
            cursor: pointer;
        }

        .form-group .file-input-wrapper input[type="file"]:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn-row {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .btn {
            display: inline-block;
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
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
            background: #28a745;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        /* Status badge styling */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-sold {
            background: #f8d7da;
            color: #721c24;
        }
        .status-inactive {
            background: #e2e3e5;
            color: #383d41;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .btn-row {
                flex-direction: column;
            }
            .page-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>✏️ Edit <span>Product</span></h1>
            <a href="admin_products.php" class="btn btn-secondary" style="padding: 6px 20px; font-size: 13px; flex: 0;">← Back</a>
        </div>

        <?php if ($msg): ?>
            <div class="message <?php echo strpos($msg, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name <span class="required">*</span></label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['item_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Brand <span class="required">*</span></label>
                <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price (R) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Quantity <span class="required">*</span></label>
                    <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="available" <?php if ($product['status'] == 'available') echo 'selected'; ?>>Available</option>
                    <option value="pending" <?php if ($product['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="sold" <?php if ($product['status'] == 'sold') echo 'selected'; ?>>Sold</option>
                    <option value="inactive" <?php if ($product['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <?php if (!empty($product['image_path']) && file_exists($product['image_path'])): ?>
                    <img src="<?php echo $product['image_path']; ?>" class="current-image" alt="Current product image">
                <?php else: ?>
                    <p style="color: #999; font-size: 13px;">No image uploaded</p>
                <?php endif; ?>
                <div class="file-input-wrapper">
                    <input type="file" name="image" accept="image/*">
                </div>
                <p style="font-size: 12px; color: #999; margin-top: 5px;">Leave empty to keep current image</p>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-success">💾 Update Product</button>
                <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>