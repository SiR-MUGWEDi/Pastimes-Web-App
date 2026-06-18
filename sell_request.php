<?php
session_start();
include 'includes/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $brand = mysqli_real_escape_string($conn, trim($_POST['brand']));
        $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
        $price = floatval($_POST['price']);
        $condition = isset($_POST['condition']) ? mysqli_real_escape_string($conn, $_POST['condition']) : 'good';
        $seller_id = $_SESSION['user_id'];
        
        // Validate
        $errors = [];
        if (empty($name)) $errors[] = "Item name is required";
        if (empty($brand)) $errors[] = "Brand is required";
        if ($price <= 0) $errors[] = "Price must be greater than 0";
        if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
            $errors[] = "Please upload an image of the item";
        }
        
        // Validate image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['image']['type'], $allowed)) {
                $errors[] = "Only JPG, PNG, GIF, and WEBP images are allowed";
            }
            if ($_FILES['image']['size'] > $maxSize) {
                $errors[] = "Image size must be less than 5MB";
            }
        }
        
        if (!empty($errors)) {
            $error = "❌ " . implode("<br>", $errors);
        } else {
            $imagePath = 'images/placeholder.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newName = 'item_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $target = 'images/' . $newName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imagePath = $target;
                } else {
                    $error = "❌ Failed to upload image. Please try again.";
                }
            }
            
            if (empty($error)) {
                // Check if condition column exists, if not use a simpler insert
                $check_column = $conn->query("SHOW COLUMNS FROM tblClothes LIKE 'condition'");
                if ($check_column->num_rows > 0) {
                    $sql = "INSERT INTO tblClothes (item_name, brand, description, price, seller_id, image_path, status, quantity, `condition`) 
                            VALUES ('$name','$brand','$desc',$price,$seller_id,'$imagePath','pending',0,'$condition')";
                } else {
                    // If condition column doesn't exist, insert without it
                    $sql = "INSERT INTO tblClothes (item_name, brand, description, price, seller_id, image_path, status, quantity) 
                            VALUES ('$name','$brand','$desc',$price,$seller_id,'$imagePath','pending',0)";
                }
                
                if (mysqli_query($conn, $sql)) {
                    $msg = "✅ Your request to sell has been submitted successfully!<br>
                            <strong>Item:</strong> " . htmlspecialchars($name) . "<br>
                            <strong>Status:</strong> Pending Admin Approval<br><br>
                            📋 You will be notified once your item is approved.";
                } else {
                    $error = "❌ Database Error: " . mysqli_error($conn);
                }
            }
        }
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sell Your Clothes - Pastimes</title>
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

        .sell-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 600px;
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

        .sell-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .sell-header .icon {
            font-size: 60px;
            display: block;
            margin-bottom: 10px;
        }

        .sell-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .sell-header h1 span {
            color: #667eea;
        }

        .sell-header p {
            color: #666;
            font-size: 14px;
        }

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 5px solid transparent;
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

        .message .highlight {
            font-weight: bold;
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

        .form-group label .hint {
            font-weight: normal;
            color: #999;
            font-size: 12px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
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
            min-height: 100px;
            resize: vertical;
        }

        .form-group select {
            appearance: auto;
            cursor: pointer;
        }

        .form-group .file-upload-wrapper {
            position: relative;
            border: 2px dashed #e0e0e0;
            border-radius: 10px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-group .file-upload-wrapper:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .form-group .file-upload-wrapper .upload-icon {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
        }

        .form-group .file-upload-wrapper .upload-text {
            color: #666;
            font-size: 14px;
        }

        .form-group .file-upload-wrapper .upload-text strong {
            color: #667eea;
        }

        .form-group .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .form-group .file-name {
            display: none;
            margin-top: 10px;
            padding: 8px 12px;
            background: #e7f3ff;
            border-radius: 6px;
            color: #004085;
            font-size: 13px;
        }

        .form-group .file-name.show {
            display: block;
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
            margin-top: 10px;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-row-single {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }

        .btn-row-single .btn {
            flex: 0 1 auto;
            min-width: 200px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #5a67d8;
        }

        .info-box {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .info-box p {
            color: #004085;
            font-size: 13px;
            margin: 0;
        }

        .info-box .emoji {
            font-size: 18px;
            margin-right: 8px;
        }

        @media (max-width: 600px) {
            .sell-container {
                padding: 25px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
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

            .sell-header h1 {
                font-size: 22px;
            }

            .sell-header .icon {
                font-size: 48px;
            }
        }
    </style>
</head>
<body>

<div class="sell-container">
    <div class="sell-header">
        <span class="icon">📤</span>
        <h1>Sell Your <span>Clothes</span></h1>
        <p>Fill in the details and submit for admin approval.</p>
    </div>

    <?php if ($msg): ?>
        <div class="message success"><?php echo $msg; ?></div>
        <div class="btn-row-single">
            <a href="user_dashboard.php" class="btn btn-primary">🛍️ Back to Dashboard</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="info-box">
            <p>
                <span class="emoji">📋</span>
                <strong>How it works:</strong> Submit your item for review. An admin will verify your listing and make it available for buyers within 24-48 hours.
            </p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Item Name <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="e.g., Levi's 501 Jeans" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Brand <span class="required">*</span></label>
                    <input type="text" name="brand" placeholder="e.g., Levi's, Nike, Zara" value="<?php echo isset($_POST['brand']) ? htmlspecialchars($_POST['brand']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description <span class="required">*</span></label>
                <textarea name="description" rows="3" placeholder="Describe the item - size, color, condition, features..." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price (R) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Condition <span class="required">*</span></label>
                    <select name="condition" required>
                        <option value="">Select condition...</option>
                        <option value="new" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'new') ? 'selected' : ''; ?>>🆕 New with tags</option>
                        <option value="excellent" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'excellent') ? 'selected' : ''; ?>>🌟 Excellent</option>
                        <option value="good" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'good') ? 'selected' : ''; ?>>👍 Good</option>
                        <option value="fair" <?php echo (isset($_POST['condition']) && $_POST['condition'] == 'fair') ? 'selected' : ''; ?>>📉 Fair</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Upload Image <span class="required">*</span> <span class="hint">(JPG, PNG, GIF, WEBP - Max 5MB)</span></label>
                <div class="file-upload-wrapper">
                    <span class="upload-icon">🖼️</span>
                    <span class="upload-text">Click or drag to upload an image<br><strong>Browse Files</strong></span>
                    <input type="file" name="image" accept="image/*" required onchange="showFileName(this)">
                </div>
                <div class="file-name" id="file-name-display"></div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-success">📤 Submit Request</button>
                <a href="user_dashboard.php" class="btn btn-secondary">← Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function showFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.textContent = '📎 Selected: ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
            display.className = 'file-name show';
        } else {
            display.className = 'file-name';
        }
    }
</script>

</body>
</html>