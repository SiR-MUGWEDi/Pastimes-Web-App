<?php
session_start();
include __DIR__ . '/includes/DBConn.php';

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message_text = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = "⚠️ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email address.";
    } else {
        // In a real application, you would send an email here
        // For now, we'll just store it in the database or show a success message
        $msg = "✅ Your message has been sent successfully! <br> We will get back to you within 24 hours.";
        
        // Optional: Save to database
        // $stmt = $conn->prepare("INSERT INTO tblMessages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        // $stmt->bind_param("ssss", $name, $email, $subject, $message_text);
        // $stmt->execute();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Support - Pastimes</title>
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

        .contact-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 550px;
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

        .contact-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .contact-header .icon {
            font-size: 60px;
            display: block;
            margin-bottom: 10px;
        }

        .contact-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .contact-header h1 span {
            color: #667eea;
        }

        .contact-header p {
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
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
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

        /* Contact info footer */
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            flex-wrap: wrap;
        }

        .contact-info .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 13px;
        }

        .contact-info .info-item .emoji {
            font-size: 18px;
        }

        @media (max-width: 600px) {
            .contact-container {
                padding: 25px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .btn-row {
                flex-direction: column;
            }

            .contact-header h1 {
                font-size: 22px;
            }

            .contact-header .icon {
                font-size: 48px;
            }

            .contact-info {
                gap: 15px;
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<div class="contact-container">
    <div class="contact-header">
        <span class="icon">📧</span>
        <h1>Contact <span>Support</span></h1>
        <p>Send us a message and we'll assist you.</p>
    </div>

    <?php if ($msg): ?>
        <div class="message success"><?php echo $msg; ?></div>
        <div class="btn-row">
            <a href="user_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Your Name <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="your@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Subject <span class="required">*</span></label>
                <input type="text" name="subject" placeholder="What is this about?" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Message <span class="required">*</span></label>
                <textarea name="message" rows="4" placeholder="Describe your issue or question in detail..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-primary">📤 Send Message</button>
                <a href="user_dashboard.php" class="btn btn-secondary">← Back</a>
            </div>
        </form>

        <div class="contact-info">
            <span class="info-item">
                <span class="emoji">📞</span> +27 123 4567
            </span>
            <span class="info-item">
                <span class="emoji">📧</span> support@pastimes.com
            </span>
            <span class="info-item">
                <span class="emoji">🕐</span> Mon-Fri 9am-5pm
            </span>
        </div>
    <?php endif; ?>
</div>

</body>
</html>