<?php
include 'includes/DBConn.php';

// Add status to tblOrder
$conn->query("ALTER TABLE tblOrder ADD status ENUM('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending'");

// Add status to tblClothes
$conn->query("ALTER TABLE tblClothes ADD status ENUM('available','pending','sold','inactive') DEFAULT 'available'");

// Create tblOrderItem
$conn->query("CREATE TABLE IF NOT EXISTS tblOrderItem (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES tblOrder(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES tblClothes(item_id) ON DELETE CASCADE
)");

// (Optional) tblMessage
$conn->query("CREATE TABLE IF NOT EXISTS tblMessage (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(100),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES tblUser(user_id),
    FOREIGN KEY (receiver_id) REFERENCES tblUser(user_id)
)");

echo "Database upgraded successfully!";
?>