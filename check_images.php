<?php
include 'includes/DBConn.php';

echo "<h2>Image Path Diagnostic</h2>";

$result = $conn->query("SELECT item_id, item_name, image_path FROM tblClothes");
echo "<table border='1' cellpadding='8'>";
echo "<table><th>ID</th><th>Product</th><th>DB Path</th><th>File Exists?</th><th>Full Path</th></tr>";

while ($row = $result->fetch_assoc()) {
    $fullPath = __DIR__ . '/' . $row['image_path'];
    $exists = file_exists($fullPath) ? '✅ YES' : '❌ NO';
    echo "<tr>";
    echo "<td>{$row['item_id']}</td>";
    echo "<td>{$row['item_name']}</td>";
    echo "<td>{$row['image_path']}</td>";
    echo "<td>$exists</td>";
    echo "<td>$fullPath</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><strong>Current directory:</strong> " . __DIR__ . "<br>";
echo "<strong>Images folder exists?</strong> " . (file_exists(__DIR__ . '/images') ? '✅ Yes' : '❌ No');
?>