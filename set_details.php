<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$set_id = $_GET['set_id'];

// Pobranie informacji o zestawie
$stmt = $conn->prepare("SELECT sets.*, users.username FROM sets 
                        JOIN users ON sets.user_id = users.id 
                        WHERE sets.id = ?");
$stmt->bind_param("i", $set_id);
$stmt->execute();
$set = $stmt->get_result()->fetch_assoc();

// Sprawdzenie, czy zestaw istnieje
if (!$set) {
    die("Zestaw nie istnieje.");
}

// Sprawdzenie, czy użytkownik jest właścicielem zestawu
$is_owner = ($set['user_id'] == $user['id']);

// Pobranie słówek z zestawu
$stmt = $conn->prepare("SELECT * FROM words WHERE set_id = ?");
$stmt->bind_param("i", $set_id);
$stmt->execute();
$words = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($set['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="<?= $is_owner ? 'index.php' : 'public_sets.php' ?>" class="button">Powrót</a>

    <h1><?= htmlspecialchars($set['name']) ?> (Autor: <?= htmlspecialchars($set['username']) ?>)</h1>

    <?php if ($is_owner): ?>
        <a href="edit_set.php?set_id=<?= $set_id ?>" class="button">Edytuj zestaw</a>
        <a href="flashcards.php?set_id=<?= $set_id ?>" class="button">Rozpocznij naukę</a>
    <?php endif; ?>

    <h2>Słówka:</h2>
    <table class="word-table">
        <thead>
            <tr>
                <th>Słówko</th>
                <th>Tłumaczenie</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($word = $words->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($word['word']) ?></td>
                    <td><?= htmlspecialchars($word['translation']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    

</body>
</html>
