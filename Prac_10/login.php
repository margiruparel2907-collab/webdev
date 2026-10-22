<?php
/**
 * Practical 10 - Login Page
 * 
 * Entry point for secure user authentication:
 *  - Checks if user is already logged in (or has a valid Remember-Me cookie)
 *  - Renders login form accepting username or email
 *  - Provides safe status and error notices (timeout, logout, failed login)
 */

require_once "db.php";
require_once "auth_helpers.php";

start_secure_session();

// If already logged in, redirect directly to user's dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

// If remember-me cookie is active, attempt automatic login
if (check_remember_me($conn)) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

// Evaluate status messages
$alertMessage = "";
$alertType    = "";

if (isset($_GET['error'])) {
    $alertType = "alert-error";
    if ($_GET['error'] === 'empty') {
        $alertMessage = "Please enter both username/email and password.";
    } elseif ($_GET['error'] === 'invalid') {
        $alertMessage = "Invalid username/email or password. Please try again.";
    } elseif ($_GET['error'] === 'unauthorized') {
        $alertMessage = "You do not have permission to access that page.";
    }
} elseif (isset($_GET['logout'])) {
    $alertType    = "alert-success";
    $alertMessage = "You have been logged out successfully. Session destroyed.";
} elseif (isset($_GET['timeout'])) {
    $alertType    = "alert-warning";
    $alertMessage = "Your session expired after 15 minutes of inactivity. Please log in again.";
} elseif (isset($_GET['auth_required'])) {
    $alertType    = "alert-warning";
    $alertMessage = "Please log in to access the protected dashboard.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Secure Login (Practical 10)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header -->
    <header class="navbar">
        <a href="login.php" class="brand">
            StudentHub
            <span class="brand-badge">Practical 10</span>
        </a>
        <div class="nav-user">
            <span>Secure Authentication Portal</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="page-container">
        <div class="auth-card">
            
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in with your username or registered email</p>
            </div>

            <!-- Status Alerts -->
            <?php if (!empty($alertMessage)): ?>
                <div class="alert <?= htmlspecialchars($alertType) ?>">
                    <?= htmlspecialchars($alertMessage) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login_process.php" method="POST" autocomplete="off">
                
                <div class="form-group">
                    <label for="identifier">Username or Email</label>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        class="form-control" 
                        placeholder="e.g. Margi12 or admin@studenthub.com" 
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter your password" 
                        required
                    >
                </div>

                <div class="form-options">
                    <label class="checkbox-label" for="remember_me">
                        <input type="checkbox" id="remember_me" name="remember_me" value="1">
                        <span>Remember me for 30 days</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary">Sign In</button>

            </form>

            <!-- Demo Credentials & Info for Viva Demonstration -->
            <div class="auth-footer">
                <p><strong>Demo Accounts for Viva Testing:</strong></p>
                <p style="margin-top: 6px;">
                    <strong>Student:</strong> <code>student</code> | Password: <code>Student@123</code><br>
                    <strong>Admin:</strong> <code>admin</code> | Password: <code>Admin@123</code>
                </p>
                <p style="margin-top: 10px;">
                    <a href="../Prac_9/register.php">&larr; Register New Account (Prac 9)</a>
                </p>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer>
        StudentHub &copy; <?= date('Y') ?> &bull; Practical 10: Secure Authentication, RBAC, Timeout &amp; Tokens
    </footer>

</body>
</html>
