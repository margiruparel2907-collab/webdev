<?php
/**
 * Practical 10 - Protected Student Dashboard
 * 
 * Accessible only by users authenticated with the 'student' role.
 * Enforces server-side inactivity timeout (15 minutes).
 */

require_once "db.php";
require_once "auth_helpers.php";

require_login($conn);
require_role('student');

// Calculate session timeout metrics
$sessionLifetime = INACTIVITY_TIMEOUT;
$idleSeconds     = time() - ($_SESSION['last_activity'] ?? time());
$remainingSeconds = max(0, $sessionLifetime - $idleSeconds);
$remainingMinutes = ceil($remainingSeconds / 60);

// Fetch student profile details from database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, username, email, role, created_at FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userProfile = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Student Dashboard (Practical 10)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Header -->
    <header class="navbar">
        <a href="student_dashboard.php" class="brand">
            StudentHub
            <span class="brand-badge">Practical 10</span>
        </a>
        <div class="nav-user">
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            <span class="role-badge role-student">Student</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="dashboard-container">
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <div class="alert alert-error">
                <strong>Access Restricted:</strong> You attempted to access the Administrator Portal, but your account has the <em>Student</em> role.
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 24px;">
            <h1 style="color: #1e293b; font-size: 24px;">Student Portal Dashboard</h1>
            <p style="color: #64748b; font-size: 14px;">Secure session active &bull; Protected by server-side authentication</p>
        </div>

        <div class="dashboard-grid">
            
            <!-- Student Information Card -->
            <div class="card">
                <div class="card-title">
                    <span>&#128100;</span> Student Profile Details
                </div>
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?= htmlspecialchars($userProfile['name'] ?? $_SESSION['name']) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Username</span>
                        <span class="info-value"><?= htmlspecialchars($userProfile['username'] ?? $_SESSION['username']) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($userProfile['email'] ?? $_SESSION['email']) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">User ID</span>
                        <span class="info-value">#<?= htmlspecialchars($_SESSION['user_id']) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Registered On</span>
                        <span class="info-value"><?= htmlspecialchars($userProfile['created_at'] ?? 'N/A') ?></span>
                    </li>
                </ul>
            </div>

            <!-- Session & Security Inspector (For Viva) -->
            <div class="card">
                <div class="card-title">
                    <span>&#128274;</span> Active Session Inspector
                </div>
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Session ID</span>
                        <span class="info-value"><?= htmlspecialchars(substr(session_id(), 0, 10)) ?>... (Protected)</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Logged In At</span>
                        <span class="info-value"><?= date("h:i:s A", $_SESSION['login_time'] ?? time()) ?></span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Inactivity Timeout</span>
                        <span class="info-value">15 Minutes (900s)</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Session Status</span>
                        <span class="info-value" style="color: #166534; font-weight: bold;">Active (Refreshed)</span>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Cookie Protection</span>
                        <span class="info-value">HttpOnly &bull; SameSite=Lax</span>
                    </li>
                </ul>
            </div>

            <!-- Academic Shortcuts -->
            <div class="card">
                <div class="card-title">
                    <span>&#127891;</span> StudentHub Academic Modules
                </div>
                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">Student Portal Home</span>
                        <a href="../HTML/dashboard.html" style="color: #2f6690; text-decoration: none; font-weight: 600;">View HTML Dashboard &rarr;</a>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Upcoming Events</span>
                        <a href="../Prac_6/events.html" style="color: #2f6690; text-decoration: none; font-weight: 600;">Events Portal (Prac 6) &rarr;</a>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Register Account</span>
                        <a href="../Prac_9/register.php" style="color: #2f6690; text-decoration: none; font-weight: 600;">Registration (Prac 9) &rarr;</a>
                    </li>
                    <li class="info-item">
                        <span class="info-label">Test Admin Access</span>
                        <a href="admin_dashboard.php" style="color: #ef4444; text-decoration: none; font-weight: 600;">Try Admin Panel (Should Block) &rarr;</a>
                    </li>
                </ul>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <footer>
        StudentHub &copy; <?= date('Y') ?> &bull; Practical 10: Secure Authentication, RBAC, Timeout &amp; Tokens
    </footer>

</body>
</html>
