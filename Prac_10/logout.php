<?php
/**
 * Practical 10 - Secure Logout Handler
 * 
 * Securely terminates the authenticated user session:
 *  1. Revokes the database Remember-Me token from 'auth_tokens' table
 *  2. Expires the 'remember_me' client cookie
 *  3. Clears all in-memory session variables ($_SESSION = [])
 *  4. Deletes the PHP session cookie from the browser
 *  5. Destroys the server-side session via session_destroy()
 *  6. Redirects user to login.php with a success logout confirmation
 */

require_once "db.php";
require_once "auth_helpers.php";

start_secure_session();

// 1. Revoke Remember-Me Token if cookie exists
if (!empty($_COOKIE['remember_me'])) {
    $cookieParts = explode(':', $_COOKIE['remember_me']);
    if (count($cookieParts) === 2) {
        $selector = $cookieParts[0];

        $stmt = $conn->prepare("DELETE FROM auth_tokens WHERE selector = ?");
        if ($stmt) {
            $stmt->bind_param("s", $selector);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// If user_id is in session, also clean any lingering tokens for this user
if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
    $clearTokens = $conn->prepare("DELETE FROM auth_tokens WHERE user_id = ?");
    if ($clearTokens) {
        $clearTokens->bind_param("i", $userId);
        $clearTokens->execute();
        $clearTokens->close();
    }
}

// 2. Expire Remember-Me Client Cookie
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
setcookie('remember_me', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// 3. Clear all session variables
$_SESSION = [];

// 4. Delete the session cookie in the browser
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

// 5. Destroy server-side session data
session_destroy();

// 6. Redirect to login page
header("Location: login.php?logout=1");
exit;
?>
