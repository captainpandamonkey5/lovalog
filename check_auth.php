<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAuthenticated()
{
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function requireAuth()
{
    if (!isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
}
