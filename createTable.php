<?php
include __DIR__ . '/includes/DBConn.php';

echo "<h2>Setting up database...</h2>";

// DROP TABLES
mysqli_query($conn, "DROP TABLE IF EXISTS tblOrders");
mysqli_query($conn, "DROP TABLE IF EXISTS tblClothes");
mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");

// CREATE USER TABLE
mysqli_query($conn, "CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE,
    password_hash VARCHAR(255),
    is_verified TINYINT DEFAULT 0,
    is_admin TINYINT DEFAULT 0,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// CREATE CLOTHES TABLE
mysqli_query($conn, "CREATE TABLE tblClothes (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100),
    brand VARCHAR(50),
    description TEXT,
    price DECIMAL(10,2),
    quantity INT,
    image_path VARCHAR(255),
    seller_id INT,
    status VARCHAR(20)
)");

// CREATE ORDERS TABLE
mysqli_query($conn, "CREATE TABLE tblOrders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "✔ Tables created<br>";

// INSERT ADMIN
$pass = password_hash("admin123", PASSWORD_DEFAULT);

mysqli_query($conn, "INSERT INTO tblUser (full_name,email,username,password_hash,is_verified,is_admin)
VALUES ('Admin','admin@pastimes.com','admin','$pass',1,1)");

echo "✔ Admin created<br>";

// INSERT PRODUCTS
mysqli_query($conn, "INSERT INTO tblClothes (item_name,brand,description,price,image_path)
VALUES 
('Levis Jeans','Levis','Blue jeans',350,'images/item1.jpg'),
('Nike Shoes','Nike','Running shoes',800,'images/item2.jpg'),
('Zara Blazer','Zara','Formal wear',450,'images/item3.jpg'),
('Adidas Hoodie','Adidas','Grey hoodie',300,'images/item4.jpg'),
('Polo Shirt','Polo','Striped shirt',250,'images/item5.jpg')
");

echo "✔ Products inserted<br>";

echo "<br><b>DONE ✔</b><br>";
echo "<a href='login.php'>Go to Login</a>";

mysqli_close($conn);
?>