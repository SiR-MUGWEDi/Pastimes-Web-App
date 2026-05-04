<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

$error = '';
$success = '';
$full_name = $email = $username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize inputs
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    $errors = [];
    
    // Check if fields are empty
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    
    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check password length
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // Check if passwords match
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email or username already exists
    if (empty($errors)) {
        $check_sql = "SELECT user_id FROM tblUser WHERE email = '$email' OR username = '$username'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email or Username already exists. Please use different ones.";
        }
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insert_sql = "INSERT INTO tblUser (full_name, email, username, password_hash, is_verified, is_admin) 
                       VALUES ('$full_name', '$email', '$username', '$password_hash', 0, 0)";
        
        if (mysqli_query($conn, $insert_sql)) {
            $success = "✅ Registration successful! Please wait for admin verification before logging in.";
            // Clear form
            $full_name = $email = $username = '';
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register - Pastimes</title>
<link rel="stylesheet" href="css/style.css">
<style>
    .form-container {
        max-width: 450px;
        padding: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
    }
    .form-group input:focus {
        border-color: #667eea;
        outline: none;
    }
    .password-hint {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }
    button:hover {
        background: #5a67d8;
    }
    .error-message {
        background: #fee;
        color: #c00;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #c00;
    }
    .success-message {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #2e7d32;
    }
    .login-link {
        text-align: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    h1 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
    }
    .subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 25px;
    }
</style>
</head>
<body>

<div class="container">
<div class="form-container">
<h1>📝 Create Account</h1>
<p class="subtitle">Join Pastimes Second-Hand Clothing</p>

<?php if ($error): ?>
<div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success-message"><?php echo $success; ?></div>
<div class="login-link">
    <a href="login.php" style="color: #667eea; text-decoration: none;">→ Click here to Login ←</a>
</div>
<?php else: ?>

<form method="POST">
<div class="form-group">
<label>👤 Full Name</label>
<input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Enter your full name" required>
</div>

<div class="form-group">
<label>📧 Email Address</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="your@email.com" required>
</div>

<div class="form-group">
<label>🔑 Username</label>
<input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Choose a username" required>
</div>

<div class="form-group">
<label>🔒 Password</label>
<input type="password" name="password" placeholder="Minimum 6 characters" required>
<div class="password-hint">Password must be at least 6 characters</div>
</div>

<div class="form-group">
<label>✓ Confirm Password</label>
<input type="password" name="confirm_password" placeholder="Re-enter your password" required>
</div>

<button type="submit">Register Account</button>
</form>

<p style="text-align:center; margin-top:20px;">
Already have an account? <a href="login.php" style="color: #667eea;">Login here</a>
</p>

<?php endif; ?>

</div>
</div>

<script src="js/main.js"></script>
</body>
</html>