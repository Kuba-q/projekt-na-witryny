<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $public = isset($_POST['public']) ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO sets (name, user_id, public) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $name, $user['id'], $public);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dodaj zestaw</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="index.php" class="button">Powrót</a>
    <h1>Dodaj nowy zestaw</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Nazwa zestawu" required>
        <label>
            <input type="checkbox" name="public" value="1"> Udostępnij publicznie
        </label>
        <button type="submit">Dodaj</button>
    </form>
</body>
</html>
