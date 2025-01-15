<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XSS Vulnerability Test</title>
</head>
<body>
<h1>Comment Section</h1>
<form method="post" action="">
    <label for="comment">Enter your comment:</label>
    <textarea id="comment" name="comment"></textarea>
    <input type="submit" value="Submit">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $comment = $_POST['comment'];

    // Afficher le commentaire sans échapper les caractères spéciaux
    echo "<h2>Comments:</h2>";
    echo "<p>$comment</p>";
}
?>
</body>
</html>
