<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

$error = '';

// If already logged in as admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Get admin user
    $stmt = $conn->prepare("SELECT * FROM tblUser WHERE email = ? AND is_admin = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['full_name'] = $admin['full_name'];
            $_SESSION['is_admin'] = 1;

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Additional styles to match exactly */
        .admin-icon {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }
        .form-container {
            position: relative;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="admin-icon">👑</div>
        <h1>Admin Login</h1>
        <p>Access the administration panel</p>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@pastimes.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit">Login to Admin Panel</button>
        </form>

        <div class="back-link">
            <a href="login.php">← Back to User Login</a>
        </div>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>