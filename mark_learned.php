<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$word_id = $_POST['word_id'];
$status = $_POST['status']; // 1 - umie, 0 - nie umie
$set_id = $_GET['set_id'];

$stmt = $conn->prepare("UPDATE words SET learned = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("iii", $status, $word_id, $user['id']);
$stmt->execute();

header("Location: flashcards.php?set_id=$set_id");
exit;
?>
