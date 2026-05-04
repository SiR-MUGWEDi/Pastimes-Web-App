<?php

$path = __DIR__ . '/includes/DBConn.php';

echo "Looking for: " . $path . "<br>";

if (file_exists($path)) {
    echo "✅ File EXISTS<br>";
} else {
    die("❌ File NOT FOUND");
}

include $path;

if (!isset($conn)) {
    die("❌ DBConn loaded but \$conn missing");
}

echo "✅ Connection variable exists";

?>