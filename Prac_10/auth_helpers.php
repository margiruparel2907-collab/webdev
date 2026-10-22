<?php
/**
 * Practical 10 - Authentication & Session Helper Functions
 * 
 * Features:
 *  - Secure Session Configuration (HttpOnly, SameSite, Localhost HTTP compatible)
 *  - Inactivity Timeout Enforcement (15 minutes)
 *  - Role-Based Access Control (Student vs Admin)
 *  - OWASP Remember-Me Token Verification & Token Rotation
 */

// Inactivity timeout limit: 15 minutes = 900 seconds
define('INACTIVITY_TIMEOUT', 900);

/**
 * Start a secure PHP session with hardened cookie parameters.
 */
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;

        // Configure session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,          // Expire when browser closes
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,   // False for local HTTP, True on HTTPS
            'httponly' => true,       // Prevent JavaScript access (XSS protection)
            'samesite' => 'Lax'       // Protect against CSRF
        ]);

        session_start();
    }
}

/**
 * Enforces a 15-minute inactivity timeout.
 * Destroys session and redirects to login if idle for more than 15 minutes.
 */
function check_session_timeout() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
        $inactiveTime = time() - $_SESSION['last_activity'];

        if ($inactiveTime > INACTIVITY_TIMEOUT) {
            // Idle timeout exceeded
            clear_user_session();
            header("Location: login.php?timeout=1");
            exit;
        }
    }
    // Update last activity timestamp
    if (isset($_SESSION['user_id'])) {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Checks for a valid Remember-Me cookie and restores the user session.
 * Implements OWASP Token Rotation on every automatic login.
 */
function check_remember_me($conn) {
    if (isset($_SESSION['user_id'])) {
        return true; // Already authenticated
    }

    if (empty($_COOKIE['remember_me'])) {
        return false;
    }

    $cookieParts = explode(':', $_COOKIE['remember_me']);
    if (count($cookieParts) !== 2) {
        setcookie('remember_me', '', time() - 3600, '/');
        return false;
    }

    [$selector, $validator] = $cookieParts;

    if (!ctype_alnum($selector) || !ctype_alnum($validator)) {
        setcookie('remember_me', '', time() - 3600, '/');
        return false;
    }

    // Lookup token by selector
    $stmt = $conn->prepare(
        "SELECT t.token_id, t.user_id, t.token_hash, t.expires_at,
                u.name, u.username, u.email, u.role
         FROM auth_tokens t
         JOIN users u ON t.user_id = u.user_id
         WHERE t.selector = ? AND t.expires_at > NOW()
         LIMIT 1"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $selector);
    $stmt->execute();
    $result = $stmt->get_result();
    $tokenRow = $result->fetch_assoc();
    $stmt->close();

    if (!$tokenRow) {
        // Expired or invalid token
        setcookie('remember_me', '', time() - 3600, '/');
        return false;
    }

    // Verify hashed validator using timing-safe comparison
    $expectedHash = hash('sha256', $validator);
    if (!hash_equals($tokenRow['token_hash'], $expectedHash)) {
        // Potential token theft: invalidate all tokens for this selector
        $del = $conn->prepare("DELETE FROM auth_tokens WHERE selector = ?");
        if ($del) {
            $del->bind_param("s", $selector);
            $del->execute();
            $del->close();
        }
        setcookie('remember_me', '', time() - 3600, '/');
        return false;
    }

    // Valid token! Restore session
    session_regenerate_id(true);
    $_SESSION['user_id']       = $tokenRow['user_id'];
    $_SESSION['name']          = $tokenRow['name'];
    $_SESSION['username']      = $tokenRow['username'];
    $_SESSION['email']         = $tokenRow['email'];
    $_SESSION['role']          = $tokenRow['role'];
    $_SESSION['login_time']    = time();
    $_SESSION['last_activity'] = time();

    // Rotate validator to prevent token replay attacks
    $newValidator = bin2hex(random_bytes(32));
    $newTokenHash = hash('sha256', $newValidator);
    $newExpiresAt = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 days

    $updateStmt = $conn->prepare(
        "UPDATE auth_tokens SET token_hash = ?, expires_at = ? WHERE token_id = ?"
    );
    if ($updateStmt) {
        $updateStmt->bind_param("ssi", $newTokenHash, $newExpiresAt, $tokenRow['token_id']);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
    setcookie(
        'remember_me',
        $selector . ':' . $newValidator,
        [
            'expires'  => time() + (86400 * 30),
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    return true;
}

/**
 * Guard function: Ensures user is logged in.
 * If not authenticated, attempts remember-me or redirects to login.
 */
function require_login($conn) {
    start_secure_session();
    check_session_timeout();

    if (!isset($_SESSION['user_id'])) {
        if (!check_remember_me($conn)) {
            header("Location: login.php?auth_required=1");
            exit;
        }
    }
}

/**
 * Guard function: Ensures authenticated user has the expected role.
 * If role does not match, redirects user to their appropriate dashboard.
 */
function require_role($expectedRole) {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php?auth_required=1");
        exit;
    }

    if ($_SESSION['role'] !== $expectedRole) {
        // Redirect to authorized dashboard
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php?error=unauthorized");
        } else {
            header("Location: student_dashboard.php?error=unauthorized");
        }
        exit;
    }
}

/**
 * Clears session data and session cookie completely.
 */
function clear_user_session() {
    start_secure_session();
    $_SESSION = [];

    // Expire session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}
?>
