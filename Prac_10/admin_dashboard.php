<?php
/**
 * Practical 10 - Protected Administrator Dashboard
 * 
 * Accessible only by users authenticated with the 'admin' role.
 * Enforces role-based access control and inactivity timeout.
 */

require_once "db.php";
require_once "auth_helpers.php";

require_login($conn);
require_role('admin');

// Fetch user statistics
$statsQuery = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$totalUsers = $statsQuery ? $statsQuery->fetch_assoc()['total_users'] : 0;

$tokensQuery = $conn->query("SELECT COUNT(*) AS active_tokens FROM auth_tokens WHERE expires_at > NOW()");
$activeTokens = $tokensQuery ? $tokensQuery->fetch_assoc()['active_tokens'] : 0;

$studentsQuery = $conn->query("SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'");
$totalStudents = $studentsQuery ? $studentsQuery->fetch_assoc()['total_students'] : 0;

// Fetch all registered users
$usersResult = $conn->query(
    "SELECT user_id, name, username, email, role, created_at 
     FROM users 
     ORDER BY user_id ASC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Administrator Portal (Practical 10)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Header -->
    <header class="navbar">
        <a href="admin_dashboard.php" class="brand">
            StudentHub Admin
            <span class="brand-badge">Practical 10</span>
        </a>
        <div class="nav-user">
            <span>Welcome, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></span>
            <span class="role-badge role-admin">Administrator</span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- Main Container -->
    <div class="dashboard-container">

        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <div class="alert alert-warning">
                <strong>Notice:</strong> You attempted to view the student portal, but your session is authenticated as <em>Administrator</em>.
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 24px;">
            <h1 style="color: #1e293b; font-size: 24px;">Administrator Control Panel</h1>
            <p style="color: #64748b; font-size: 14px;">Role-Based Access Control verified &bull; Administrative oversight</p>
        </div>

        <!-- Metric Cards -->
        <div class="dashboard-grid">
            
            <div class="card">
                <div class="card-title">
                    <span>&#128101;</span> Total Registered Users
                </div>
                <div class="card-number"><?= (int) $totalUsers ?></div>
                <p style="color: #64748b; font-size: 13px;">Accounts currently in the database</p>
            </div>

            <div class="card">
                <div class="card-title">
                    <span>&#127891;</span> Enrolled Students
                </div>
                <div class="card-number"><?= (int) $totalStudents ?></div>
                <p style="color: #64748b; font-size: 13px;">Users with 'student' role</p>
            </div>

            <div class="card">
                <div class="card-title">
                    <span>&#128273;</span> Active Remember Tokens
                </div>
                <div class="card-number"><?= (int) $activeTokens ?></div>
                <p style="color: #64748b; font-size: 13px;">Valid 30-day persistent logins</p>
            </div>

        </div>

        <!-- User Accounts Table -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-title" style="justify-content: space-between;">
                <span><span>&#128203;</span> Registered User Accounts</span>
                <span style="font-size: 13px; font-weight: normal; color: #64748b;">Queried via MySQLi</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersResult && $usersResult->num_rows > 0): ?>
                            <?php while ($row = $usersResult->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($row['user_id']) ?></td>
                                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                    <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <?php if ($row['role'] === 'admin'): ?>
                                            <span class="role-badge role-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="role-badge role-student">Student</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">No registered users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Security & Session Card for Viva -->
        <div class="card">
            <div class="card-title">
                <span>&#128737;</span> Security Status &amp; Policy Inspector
            </div>
            <ul class="info-list">
                <li class="info-item">
                    <span class="info-label">Active Admin Session</span>
                    <span class="info-value"><?= htmlspecialchars($_SESSION['username']) ?> (#<?= htmlspecialchars($_SESSION['user_id']) ?>)</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Role Privilege</span>
                    <span class="info-value" style="color: #7e22ce; font-weight: bold;">Full Administrative Access</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Inactivity Timeout Enforcement</span>
                    <span class="info-value">15 minutes server-side tracking (INACTIVITY_TIMEOUT = 900)</span>
                </li>
                <li class="info-item">
                    <span class="info-label">Remember-Me Token Scheme</span>
                    <span class="info-value">OWASP Selector + SHA-256 Validator in `auth_tokens` table</span>
                </li>
            </ul>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        StudentHub &copy; <?= date('Y') ?> &bull; Practical 10: Secure Authentication, RBAC, Timeout &amp; Tokens
    </footer>

</body>
</html>
