<?php
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = "Message sent successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Contact Support - Pastimes</title>
<link rel="stylesheet" href="css/style.css">
<style>
    .contact-icon {
        text-align: center;
        font-size: 40px;
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<div class="container">
<div class="form-container">
<div class="contact-icon">📧</div>
<h1>Contact Support</h1>
<p>Send us a message and we'll assist you.</p>

<?php if ($msg): ?>
<div class="success-message"><?php echo $msg; ?></div>
<?php endif; ?>

<form method="POST">
<div class="form-group">
<label>Your Name</label>
<input type="text" name="name" required>
</div>

<div class="form-group">
<label>Email Address</label>
<input type="email" name="email" required>
</div>

<div class="form-group">
<label>Message</label>
<textarea name="message" rows="4" required></textarea>
</div>

<button type="submit">Send Message</button>
</form>

<p style="text-align:center; margin-top:15px;">
<a href="user_dashboard.php" style="color:#667eea;">← Back to Dashboard</a>
</p>

</div>
</div>

<script src="js/main.js"></script>
</body>
</html>