<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$set_id = $_GET['set_id'];

// Pobranie informacji o zestawie, aby sprawdzić status publiczny
$stmt = $conn->prepare("SELECT * FROM sets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $set_id, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Nie masz dostępu do tego zestawu.");
}

$set = $result->fetch_assoc();

// Obsługa formularzy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_word'])) {
        $word = $_POST['word'];
        $translation = $_POST['translation'];
        $stmt = $conn->prepare("INSERT INTO words (word, translation, set_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $word, $translation, $set_id, $user['id']);
        $stmt->execute();
    } elseif (isset($_POST['edit_word'])) {
        $word_id = $_POST['word_id'];
        $word = $_POST['word'];
        $translation = $_POST['translation'];
        $stmt = $conn->prepare("UPDATE words SET word = ?, translation = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $word, $translation, $word_id, $user['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete_word'])) {
        $word_id = $_POST['word_id'];
        $stmt = $conn->prepare("DELETE FROM words WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $word_id, $user['id']);
        $stmt->execute();
    } elseif (isset($_POST['update_public'])) {
        $public = isset($_POST['public']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE sets SET public = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $public, $set_id, $user['id']);
        $stmt->execute();

        // Aktualizacja wartości w zmiennej $set, aby odzwierciedlić zmiany na stronie
        $set['public'] = $public;
    }
}

$words = $conn->query("SELECT * FROM words WHERE set_id = $set_id AND user_id = " . $user['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edycja zestawu</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="set_details.php?set_id=<?= $set_id ?>" class="button">Powrót</a>

    <h1>Edycja zestawu</h1>
    <form method="POST">
        <input type="text" name="word" placeholder="Słówko" required>
        <input type="text" name="translation" placeholder="Tłumaczenie" required>
        <button type="submit" name="add_word">Dodaj</button>
    </form>
    
    <form method="POST">
        <label>
            <input type="checkbox" name="public" value="1" <?= $set['public'] ? 'checked' : '' ?>> Udostępnij publicznie
        </label>
        <button type="submit" name="update_public">Zapisz</button>
    </form>

    <h2>Słówka:</h2>
    <table class="word-table">
        <thead>
            <tr>
                <th>Słówko</th>
                <th>Tłumaczenie</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($word = $words->fetch_assoc()): ?>
                <tr>
                    <form method="POST">
                        <td><input type="text" name="word" value="<?= htmlspecialchars($word['word']) ?>" required></td>
                        <td><input type="text" name="translation" value="<?= htmlspecialchars($word['translation']) ?>" required></td>
                        <td>
                            <input type="hidden" name="word_id" value="<?= $word['id'] ?>">
                            <button type="submit" name="edit_word">Edytuj</button>
                            <button type="submit" name="delete_word" style="background: red;">Usuń</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
