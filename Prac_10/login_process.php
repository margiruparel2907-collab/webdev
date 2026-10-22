<?php
/**
 * Practical 10 - Login Processing Script
 * 
 * Handles authentication backend:
 *  - Sanitizes identifier (username or email) and password
 *  - Executes prepared statement lookup on 'users' table
 *  - Verifies password securely using password_verify()
 *  - Mitigates session fixation via session_regenerate_id(true)
 *  - Generates OWASP-compliant Remember-Me token if requested
 *  - Performs Role-Based Redirection (Student vs Administrator)
 */

require_once "db.php";
require_once "auth_helpers.php";

start_secure_session();

// Reject any non-POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = $_POST['password'] ?? '';
$rememberMe = !empty($_POST['remember_me']);

// Validate presence of credentials
if ($identifier === '' || $password === '') {
    header("Location: login.php?error=empty");
    exit;
}

// Query user by username OR email using prepared statement
$stmt = $conn->prepare(
    "SELECT user_id, name, username, email, password_hash, role 
     FROM users 
     WHERE username = ? OR email = ? 
     LIMIT 1"
);

if (!$stmt) {
    error_log("Database error preparing login statement: " . $conn->error);
    header("Location: login.php?error=invalid");
    exit;
}

$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// Verify credentials safely
if ($user && password_verify($password, $user['password_hash'])) {
    
    // Prevent Session Fixation attack
    session_regenerate_id(true);

    // Initialize session state
    $_SESSION['user_id']       = (int) $user['user_id'];
    $_SESSION['name']          = $user['name'];
    $_SESSION['username']      = $user['username'];
    $_SESSION['email']         = $user['email'];
    $_SESSION['role']          = $user['role'];
    $_SESSION['login_time']    = time();
    $_SESSION['last_activity'] = time();

    // Handle Remember-Me Token (Selector + Validator Pattern)
    if ($rememberMe) {
        $selector  = bin2hex(random_bytes(16)); // 32 hex chars
        $validator = bin2hex(random_bytes(32)); // 64 hex chars
        $tokenHash = hash('sha256', $validator);
        $expiresAt = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 days valid

        $tokenStmt = $conn->prepare(
            "INSERT INTO auth_tokens (user_id, selector, token_hash, expires_at) 
             VALUES (?, ?, ?, ?)"
        );

        if ($tokenStmt) {
            $tokenStmt->bind_param("isss", $user['user_id'], $selector, $tokenHash, $expiresAt);
            $tokenStmt->execute();
            $tokenStmt->close();

            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
            setcookie(
                'remember_me',
                $selector . ':' . $validator,
                [
                    'expires'  => time() + (86400 * 30),
                    'path'     => '/',
                    'domain'   => '',
                    'secure'   => $isHttps,   // Localhost HTTP: false, HTTPS: true
                    'httponly' => true,       // Inaccessible to JavaScript
                    'samesite' => 'Lax'       // Protection against CSRF
                ]
            );
        }
    }

    // Role-based redirection
    if ($user['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;

} else {
    // Authentication failed: return generic error message
    header("Location: login.php?error=invalid");
    exit;
}
?>
