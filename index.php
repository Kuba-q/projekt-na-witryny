<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$sets = $conn->query("SELECT * FROM sets WHERE user_id = " . $user['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Twoje zestawy</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="logout.php" class="button logout-button">Wyloguj się</a>
    <a href="add_set.php" class="button">Dodaj nowy zestaw</a>
    <a href="public_sets.php" class="button search-button">Szukaj publicznych zestawów</a>

    <h1>Twoje zestawy</h1>
    <div class="sets-container">
        <?php while ($set = $sets->fetch_assoc()): ?>
            <div class="set-card">
                <h2><?= $set['name'] ?></h2>
                <a href="set_details.php?set_id=<?= $set['id'] ?>" class="button">Otwórz</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
