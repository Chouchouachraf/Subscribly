<?php
// Set session timeout to 2 hours
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);

// Start the session
session_start();

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Function to set user session
function setUserSession($userId, $email, $name) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
}

// Function to clear user session
function clearUserSession() {
    session_unset();
    session_destroy();
}
?>
