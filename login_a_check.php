<?php
// Session security settings - MUST be set before session_start()


session_start(); // Start the session

// Session timeout and regeneration to prevent fixation attacks
$timeout_duration = 18000; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login71.php");
    exit();
} else {
    // Regenerate session ID to prevent fixation attacks
    if (!isset($_SESSION['regenerate_time']) || (time() - $_SESSION['regenerate_time']) > 600) {
        session_regenerate_id(true);
        $_SESSION['regenerate_time'] = time();
    }
}
$_SESSION['last_activity'] = time();

if (isset($_SESSION['login_user'])) {
    list($username, $u_id, $role) = explode('|', $_SESSION['login_user']);

    // Implement stricter role-based access control
    $allowed_roles = ['admin'];
    if (!in_array($role, $allowed_roles)) {
        header("Location: no_access.php");
        exit();
    }

    // Rest of your page code...
} else {
    header('Location: login71.php');
    exit();
}
?>
