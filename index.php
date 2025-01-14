<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Injection Test</title>
</head>
<body>
<form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">
    <input type="submit" value="Submit">
</form>

<?php
// Connexion à la base de données SQLite
$conn = new SQLite3('db.sqlite');

// Create the users table if it does not exist
$conn->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT)');

// Insert initial data if the table is empty
$result = $conn->query('SELECT COUNT(*) as count FROM users');
$row = $result->fetchArray(SQLITE3_ASSOC);
if ($row['count'] == 0) {
    $conn->exec("INSERT INTO users (username) VALUES ('user1'), ('user2'), ('user3')");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    // Requête SQL non sécurisée
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "id: " . $row["id"]. " - Name: " . $row["username"]. "<br>";
    }
}

$conn->close();
?>
</body>
</html>