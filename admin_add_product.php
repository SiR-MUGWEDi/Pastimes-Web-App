<?php
session_start();
include 'includes/DBConn.php';
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin_login.php");
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $imagePath = 'images/placeholder.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = 'item' . time() . '.' . $ext;
        $target = 'images/' . $newName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    $sql = "INSERT INTO tblClothes (item_name, brand, description, price, quantity, image_path, status) 
            VALUES ('$name','$brand','$desc',$price,$qty,'$imagePath','$status')";
    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Product added!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product - Admin</title>
<link rel="stylesheet" href="css/style.css">
<style>
    .container { max-width:600px; margin:30px auto; background:white; padding:20px; border-radius:10px; }
    .form-group { margin-bottom:15px; }
    label { display:block; font-weight:bold; }
    input, textarea, select { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
</style>
</head>
<body>
<div class="container">
<h1>➕ Add New Product</h1>
<?php if ($msg) echo "<div class='message success'>$msg</div>"; ?>
<form method="POST" enctype="multipart/form-data">
<div class="form-group"><label>Product Name</label><input type="text" name="name" required></div>
<div class="form-group"><label>Brand</label><input type="text" name="brand" required></div>
<div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
<div class="form-group"><label>Price (R)</label><input type="number" step="0.01" name="price" required></div>
<div class="form-group"><label>Quantity</label><input type="number" name="quantity" value="0" required></div>
<div class="form-group"><label>Status</label>
    <select name="status">
        <option value="available">Available</option>
        <option value="pending">Pending</option>
        <option value="sold">Sold</option>
        <option value="inactive">Inactive</option>
    </select>
</div>
<div class="form-group"><label>Image</label><input type="file" name="image" accept="image/*"></div>
<button type="submit" style="background:#28a745; color:white; padding:10px 20px; border:none; border-radius:4px;">Add Product</button>
<a href="admin_products.php" style="margin-left:15px;">Cancel</a>
</form>
</div>
</body>
</html>