<?php
require 'auth.php';
redirect_if_not_logged_in();

$user = get_logged_in_user();
$set_id = $_GET['set_id'] ?? null;

if (!$set_id) {
    die("Nieprawidłowy zestaw.");
}

// Pobierz słówka do nauki
$words = $conn->query("SELECT * FROM words WHERE set_id = $set_id AND user_id = " . $user['id'] . " AND learned = 0");
$word_ids = array_column($words->fetch_all(MYSQLI_ASSOC), 'id');

// Jeśli brak słówek do nauki załaduj wszystkie
if (count($word_ids) === 0) {
    $words = $conn->query("SELECT * FROM words WHERE set_id = $set_id AND user_id = " . $user['id']);
    $word_ids = array_column($words->fetch_all(MYSQLI_ASSOC), 'id');
}

// Jeśli nadal brak słówek, coś jest nie tak
if (count($word_ids) === 0) {
    die("Brak słówek w zestawie.");
}

// Zarządzanie sesją
if (!isset($_SESSION)) {
    session_start();
}

// Załaduj identyfikatory słówek do sesji, jeśli jeszcze ich nie ma
if (!isset($_SESSION['word_ids']) || count($_SESSION['word_ids']) === 0) {
    $_SESSION['word_ids'] = $word_ids;
}

// Pobierz aktualne słówko
$current_word_id = array_shift($_SESSION['word_ids']);
$current_word = $conn->query("SELECT * FROM words WHERE id = $current_word_id AND user_id = " . $user['id'])->fetch_assoc();

if (!$current_word) {
    die("Błąd ładowania słówka.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tryb nauki</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <a href="set_details.php?set_id=<?= $set_id ?>" class="button">Powrót</a>

    <h1>Tryb nauki</h1>
    <div class="flashcard">
        <div id="card-content">
            <h2 id="word"><?= htmlspecialchars($current_word['word']) ?></h2>
        </div>
        <button id="toggle-button" onclick="toggleCard()">Pokaż tłumaczenie</button>
        <form method="POST" action="mark_learned.php?set_id=<?= $set_id ?>">
            <input type="hidden" name="word_id" value="<?= $current_word['id'] ?>">
            <button type="submit" name="status" value="1">Umiem</button>
            <button type="submit" name="status" value="0" style="background:#f31d33;">Nie umiem</button>
        </form>
    </div>
    <script>
        let isTranslation = false;
        const word = "<?= htmlspecialchars($current_word['word']) ?>";
        const translation = "<?= htmlspecialchars($current_word['translation']) ?>";

        function toggleCard() {
            const cardContent = document.getElementById('word');
            const button = document.getElementById('toggle-button');
            if (isTranslation) {
                cardContent.innerText = word;
                button.innerText = "Pokaż tłumaczenie";
            } else {
                cardContent.innerText = translation;
                button.innerText = "Pokaż słówko";
            }
            isTranslation = !isTranslation;
        }
    </script>
</body>
</html>
