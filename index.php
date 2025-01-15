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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = $_POST['username'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->fetchArray(SQLITE3_ASSOC) === false) {
        $conn->exec("INSERT INTO users (username) VALUES ('$username')");
    }

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "id: " . $row["id"]. " - Name: " . $row["username"]. "<br>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dos'])) {
    while (true) {
        $x = 0;
        for ($i = 0; $i < 1000000; $i++) {
            $x += $i;
        }
    }
}

$conn->close();
?>

<form method="post" action="">
    <label for="dos">Click the button to simulate a DoS attack:</label>
    <input type="submit" name="dos" value="Simulate DoS">
</form>
</body>
</html>