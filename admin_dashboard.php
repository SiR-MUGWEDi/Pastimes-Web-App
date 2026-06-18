<?php
session_start();
include 'includes/DBConn.php';

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin_login.php");
    exit();
}

// Handle user verification
if (isset($_GET['verify'])) {
    $user_id = intval($_GET['verify']);
    $updateSQL = "UPDATE tblUser SET is_verified = 1 WHERE user_id = $user_id";
    if (mysqli_query($conn, $updateSQL)) {
        $_SESSION['message'] = "✅ User verified successfully!";
    } else {
        $_SESSION['error'] = "Verification failed: " . mysqli_error($conn);
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "❌ You cannot delete your own account!";
    } else {
        $deleteSQL = "DELETE FROM tblUser WHERE user_id = $user_id AND is_admin = 0";
        if (mysqli_query($conn, $deleteSQL)) {
            $_SESSION['message'] = "✅ User deleted successfully!";
        } else {
            $_SESSION['error'] = "Deletion failed: " . mysqli_error($conn);
        }
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    $updateSQL = "UPDATE tblUser SET full_name='$full_name', email='$email', username='$username' WHERE user_id=$user_id";
    if (mysqli_query($conn, $updateSQL)) {
        $_SESSION['message'] = "✅ User updated successfully!";
    } else {
        $_SESSION['error'] = "Update failed: " . mysqli_error($conn);
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Get all users
$usersSQL = "SELECT user_id, full_name, email, username, is_verified, is_admin, registration_date FROM tblUser ORDER BY is_verified ASC, registration_date DESC";
$usersResult = mysqli_query($conn, $usersSQL);

// Get messages
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pastimes</title>
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
            padding: 15px; 
        }
        
        .dashboard-container { 
            max-width: 1300px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        
        .dashboard-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 15px 20px; 
            border-radius: 10px 10px 0 0; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-wrap: wrap; 
            gap: 10px; 
        }
        
        .dashboard-header h1 { 
            font-size: 1.3rem; 
        }
        
        .dashboard-header p { 
            font-size: 0.85rem; 
            opacity: 0.9; 
        }
        
        .logout-btn { 
            background: #dc3545; 
            color: white; 
            padding: 6px 15px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-size: 0.9rem; 
        }
        
        .logout-btn:hover { 
            background: #c82333; 
        }
        
        .admin-content { 
            padding: 20px; 
        }
        
        .admin-content h2 { 
            font-size: 1.3rem; 
            margin-bottom: 8px; 
            color: #333; 
        }
        
        .info-text { 
            background: #e7f3ff; 
            padding: 8px 12px; 
            border-radius: 5px; 
            margin-bottom: 15px; 
            font-size: 0.85rem; 
            color: #004085; 
        }
        
        .message { 
            padding: 10px 15px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
            font-size: 0.85rem; 
        }
        
        .message.success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        
        .message.error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        
        .table-wrapper { 
            overflow-x: auto; 
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 0.8rem; 
        }
        
        .data-table th { 
            background: #f8f9fa; 
            padding: 10px 6px; 
            text-align: left; 
            font-weight: 600; 
            color: #495057; 
            border-bottom: 2px solid #dee2e6; 
        }
        
        .data-table td { 
            padding: 8px 6px; 
            border-bottom: 1px solid #e9ecef; 
            vertical-align: middle; 
        }
        
        .status-verified { 
            background: #28a745; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 0.7rem; 
            display: inline-block; 
            white-space: nowrap; 
        }
        
        .status-pending { 
            background: #ffc107; 
            color: #333; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 0.7rem; 
            display: inline-block; 
            white-space: nowrap; 
        }
        
        .admin-badge { 
            background: #17a2b8; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 0.7rem; 
            display: inline-block; 
        }
        
        .user-badge { 
            background: #6c757d; 
            color: white; 
            padding: 3px 8px; 
            border-radius: 12px; 
            font-size: 0.7rem; 
            display: inline-block; 
        }
        
        .actions { 
            display: flex; 
            gap: 4px; 
            justify-content: center; 
            flex-wrap: wrap; 
        }
        
        .btn-verify, .btn-edit, .btn-delete { 
            padding: 4px 8px; 
            text-decoration: none; 
            border-radius: 3px; 
            font-size: 0.7rem; 
            cursor: pointer; 
            border: none; 
            transition: opacity 0.2s; 
        }
        
        .btn-verify { 
            background: #28a745; 
            color: white; 
        }
        
        .btn-edit { 
            background: #ffc107; 
            color: #333; 
        }
        
        .btn-delete { 
            background: #dc3545; 
            color: white; 
        }
        
        .btn-verify:hover, .btn-edit:hover, .btn-delete:hover { 
            opacity: 0.8; 
        }
        
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5); 
        }
        
        .modal-content { 
            background: white; 
            margin: 10% auto; 
            padding: 20px; 
            width: 90%; 
            max-width: 400px; 
            border-radius: 10px; 
            position: relative; 
        }
        
        .close { 
            position: absolute; 
            right: 15px; 
            top: 10px; 
            font-size: 24px; 
            cursor: pointer; 
            color: #999; 
        }
        
        .close:hover { 
            color: #333; 
        }
        
        .form-group { 
            margin-bottom: 15px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 600; 
            font-size: 0.85rem; 
        }
        
        .form-group input { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-size: 0.85rem; 
        }
        
        button[type="submit"] { 
            background: #667eea; 
            color: white; 
            padding: 8px 15px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 0.9rem; 
        }
        
        button[type="submit"]:hover { 
            background: #5a67d8; 
        }
        
        /* Button container - centered at bottom */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 10px;
            padding: 15px 0;
            border-top: 1px solid #eee;
        }
        
        .btn-primary {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s, opacity 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        
        .btn-secondary {
            display: inline-block;
            padding: 10px 25px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s, opacity 0.2s;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        
        @media (max-width: 900px) { 
            .data-table th, .data-table td { 
                padding: 6px 4px; 
                font-size: 0.7rem; 
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <div>
                <h1>👑 Pastimes Admin Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </header>
        
        <div class="admin-content">
            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <h2>📋 User Management</h2>
            
            <div class="info-text">
                ℹ️ <strong>Pending verifications appear at the top</strong> | Total users: <?php echo mysqli_num_rows($usersResult); ?>
            </div>
            
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($usersResult) == 0): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                        <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td style="text-align: center;">
                                    <?php if ($user['is_verified'] == 1): ?>
                                        <span class="status-verified">✓ Verified</span>
                                    <?php else: ?>
                                        <span class="status-pending">⏳ Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($user['is_admin'] == 1): ?>
                                        <span class="admin-badge">👑 Admin</span>
                                    <?php else: ?>
                                        <span class="user-badge">👤 User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($user['registration_date'])); ?></td>
                                <td class="actions">
                                    <?php if ($user['is_verified'] == 0 && $user['is_admin'] == 0): ?>
                                        <a href="?verify=<?php echo $user['user_id']; ?>" class="btn-verify" onclick="return confirm('Verify this user?')">✓ Verify</a>
                                    <?php endif; ?>
                                    <button onclick="editUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars(addslashes($user['full_name'])); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['username']); ?>')" class="btn-edit">✏️ Edit</button>
                                    <?php if ($user['is_admin'] == 0): ?>
                                        <a href="?delete=<?php echo $user['user_id']; ?>" class="btn-delete" onclick="return confirm('⚠️ Delete this user? This cannot be undone!')">🗑️ Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Centered action buttons at the bottom -->
            <div class="action-buttons">
                <a href="admin_products.php" class="btn-primary">📦 Manage Products</a>
                <a href="user_dashboard.php" class="btn-secondary">⬅️ Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>✏️ Edit User</h3>
            <form method="POST" action="">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" id="edit_full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>
                <button type="submit" name="update_user">💾 Save Changes</button>
            </form>
        </div>
    </div>
    
    <script>
        function editUser(id, name, email, username) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_full_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_username').value = username;
            document.getElementById('editModal').style.display = 'block';
        }
        
        var modal = document.getElementById('editModal');
        var span = document.getElementsByClassName('close')[0];
        span.onclick = function() { modal.style.display = 'none'; }
        window.onclick = function(event) { if (event.target == modal) modal.style.display = 'none'; }
        
        // Auto-hide messages after 3 seconds
        setTimeout(function() {
            var messages = document.querySelectorAll('.message');
            messages.forEach(function(msg) {
                msg.style.display = 'none';
            });
        }, 3000);
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>