<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['user_input']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM tblUser WHERE username=? OR email=?");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $u = $res->fetch_assoc();

        if (password_verify($pass, $u['password_hash'])) {
            if ($u['is_verified'] == 1) {
                $_SESSION['user_id'] = $u['user_id'];
                $_SESSION['full_name'] = $u['full_name'];
                $_SESSION['is_admin'] = $u['is_admin'];

                if ($u['is_admin'] == 1) {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error = "Account not verified yet. Please contact admin.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes - Second Hand Clothing</title>
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

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
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

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo h1 {
            font-size: 32px;
            color: #333;
            letter-spacing: 2px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 8px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-text h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #666;
            font-size: 13px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .admin-link {
            text-align: center;
            margin-top: 15px;
        }

        .admin-link a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
        }

        .admin-link a:hover {
            color: #667eea;
        }

        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #c00;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">
        <h1>🛍️ Pastimes</h1>
        <p>Second-Hand Clothing</p>
    </div>

    <div class="welcome-text">
        <h2>Welcome to Pastimes</h2>
        <p>Login to buy or sell second-hand branded clothing</p>
    </div>

    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label>📧 Email Address or Username</label>
            <input type="text" name="user_input" placeholder="Enter your email or username" required>
        </div>

        <div class="input-group">
            <label>🔒 Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="login-btn">Login</button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>

    <div class="admin-link">
        <a href="admin_login.php">🔐 Admin Login</a>
    </div>
</div>

</body>
</html>