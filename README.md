# Exercice "Sécu by design"

## Attaque 1 - Injection SQL

### Mesures de sécurité prises

1. **Utilisation de requêtes préparées** :
    - Les requêtes SQL sont préparées avec des paramètres nommés pour éviter l'injection de code SQL malveillant.
    - Exemple :
      ```php
      $stmt = $conn->prepare('INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)');
      $stmt->bindValue(':name', $name, SQLITE3_TEXT);
      $stmt->bindValue(':email', $email, SQLITE3_TEXT);
      $stmt->bindValue(':message', $message, SQLITE3_TEXT);
      $stmt->execute();
      ```

2. **Validation et assainissement des entrées utilisateur** :
    - Les données saisies par l'utilisateur sont validées et assainies avant d'être utilisées dans les requêtes SQL.
    - Exemple :
      ```php
      $name = htmlspecialchars(trim($_POST['name']));
      $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
      $message = htmlspecialchars(trim($_POST['message']));
      ```

3. **Utilisation de filtres de validation** :
    - Les entrées utilisateur sont filtrées pour s'assurer qu'elles respectent le format attendu.
    - Exemple :
      ```php
      $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
      ```

Ces mesures permettent de réduire considérablement les risques d'injection SQL en s'assurant que les données manipulées sont sécurisées et correctement formatées.