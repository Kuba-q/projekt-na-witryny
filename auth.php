<?php
session_start();
require 'database.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_logged_in_user() {
    global $conn;
    if (!is_logged_in()) return null;

    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}
?>
