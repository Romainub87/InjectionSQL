<?php
session_start();

$conn = new SQLite3('db.sqlite');

$conn->exec('CREATE TABLE IF NOT EXISTS contacts (id INTEGER PRIMARY KEY, name TEXT, email TEXT, message TEXT)');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['attempts'][$ip])) {
    $_SESSION['attempts'][$ip] = ['count' => 0, 'time' => time()];
}

$attempts = &$_SESSION['attempts'][$ip];
if ($attempts['count'] >= 5 && time() - $attempts['time'] < 300) {
    die("Trop de tentatives, veuillez réessayer dans 5 minutes.");
} elseif (time() - $attempts['time'] > 300) {
    $attempts = ['count' => 0, 'time' => time()];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $message = htmlspecialchars(trim($_POST['message']));

        if ($name && $email && $message) {
            $stmt = $conn->prepare('INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)');
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':message', $message, SQLITE3_TEXT);
            $stmt->execute();

            echo "Votre message a été envoyé avec succès.";
        } else {
            echo "Veuillez remplir tous les champs correctement.";
        }
    } else {
        echo "Erreur de validation du formulaire (CSRF).";
    }
    $attempts['count']++;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Contact Form</title>
</head>
<body>
<form method="post" action="">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <label for="name">Nom :</label>
    <input type="text" id="name" name="name" required><br><br>
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required><br><br>
    <label for="message">Message :</label>
    <textarea id="message" name="message" rows="5" required></textarea><br><br>
    <input type="submit" value="Envoyer">
</form>
</body>
</html>
