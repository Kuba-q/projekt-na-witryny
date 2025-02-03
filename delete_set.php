<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$set_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM sets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $set_id, $user['id']);
$stmt->execute();

header("Location: index.php");
exit;
?>
