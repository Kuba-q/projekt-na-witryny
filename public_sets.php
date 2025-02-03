<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();

// Pobranie publicznych zestawów innych użytkowników
$stmt = $conn->prepare("SELECT sets.id, sets.name, users.username 
                        FROM sets 
                        JOIN users ON sets.user_id = users.id 
                        WHERE sets.public = 1 AND sets.user_id != ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Publiczne zestawy</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="index.php" class="button">Powrót</a>
    
    <h1>Publiczne zestawy</h1>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="sets-container">
            <?php while ($set = $result->fetch_assoc()): ?>
                <div class="set-card">
                    <h2><?= htmlspecialchars($set['name']) ?></h2>
                    <p><strong>Autor:</strong> <?= htmlspecialchars($set['username']) ?></p>
                    <a href="set_details.php?set_id=<?= $set['id'] ?>" class="button">Otwórz</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Brak dostępnych publicznych zestawów.</p>
    <?php endif; ?>

</body>
</html>
