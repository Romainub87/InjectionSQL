# Exercice "Sécu by design"

## Sommaire
- [Installation](#installation)
- [Politique de sécurité du contenu](#politique-de-sécurité-du-contenu)
- [Attaque 1 - Injection SQL](#attaque-1---injection-sql)
- [Attaque 2 - CSRF](#attaque-2---csrf)
- [Attaque 3 - Brute Force](#attaque-3---brute-force)
- [Attaque 4 - XSS](#attaque-4---xss)

// tuto pour lancer l'application

## Installation
1. Cloner le dépôt GitHub
   ```bash
   git clone https://github.com/Romainub87/Secure_form.git
    ```
   
2. Lancer le conteneur Docker
   ```bash
   docker-compose up -d --build
   ```
   
3. Accéder à l'application dans un navigateur
   ```
    http://localhost:8080
    ```
   
4. Arrêter le conteneur Docker
    ```bash
    docker-compose down
    ```

## Politique de sécurité du contenu
  ```php
  header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self';");
  ```
  Ce code est une mesure de sécurité pour protéger le site contre les attaques XSS et l'injection de contenu malveillant. Il limite les ressources que le navigateur peut charger en autorisant uniquement celles provenant du même domaine ('self'). Cela empêche l'exécution de scripts malveillants injectés depuis des sources non vérifiées.
  
## Attaque 1 - Injection SQL

### Mesures de sécurité prises

1. **Utilisation de requêtes préparées** :
    - Les requêtes SQL sont préparées avec des paramètres nommés pour éviter l'injection de code SQL malveillant.
      ```php
      $stmt = $conn->prepare('INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)');
      $stmt->bindValue(':name', $name, SQLITE3_TEXT);
      $stmt->bindValue(':email', $email, SQLITE3_TEXT);
      $stmt->bindValue(':message', $message, SQLITE3_TEXT);
      $stmt->execute();
      ```

2. **Validation et assainissement des entrées utilisateur** :
    - Les données saisies par l'utilisateur sont validées et assainies avant d'être utilisées dans les requêtes SQL.
      ```php
      $name = htmlspecialchars(trim($_POST['name']));
      $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
      $message = htmlspecialchars(trim($_POST['message']));
      ```

3. **Utilisation de filtres de validation** :
    - Les entrées utilisateur sont filtrées pour s'assurer qu'elles respectent le format attendu.
      ```php
      $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
      ```

Ces mesures permettent de réduire considérablement les risques d'injection SQL en s'assurant que les données manipulées sont sécurisées et correctement formatées.

## Attaque 2 - CSRF

### Mesures de sécurité prises

1. **Utilisation de jetons CSRF** :
    - Un jeton CSRF est généré pour chaque formulaire et vérifié lors de la soumission pour s'assurer que la requête provient bien du site et non d'une source externe.
      ```php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
              die('Token CSRF invalide');
          }
          // Traitement du formulaire
      }
      ```
      
2. **Stockage du jeton CSRF en session** :
    - Le jeton CSRF est stocké en session pour éviter qu'il ne soit facilement accessible par des attaquants.
      ```php
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      ```
      
3. **Validation de l'origine de la requête** :
    - L'origine de la requête est validée en vérifiant que le jeton CSRF soumis correspond à celui stocké en session.
      ```php
      if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
          die('Token CSRF invalide');
      }
      ```

## Attaque 3 - Brute Force

### Mesures de sécurité prises

1. **Limitation des tentatives de connexion** :
   - Le nombre de tentatives de connexion est limité pour éviter les attaques par force brute.
     ```php
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
     ```

2. **Verrouillage du compte** :
   - Le compte utilisateur est verrouillé après un certain nombre de tentatives infructueuses pour empêcher les attaques par force brute.
     ```php
     if ($_SERVER["REQUEST_METHOD"] === "POST") {
         $attempts['count']++;
     }
     ```
     
## Attaque 4 - XSS

### Mesures de sécurité prises

1. **Échappement des données de sortie** :
   - Les données saisies par l'utilisateur sont échappées avant d'être affichées pour éviter l'exécution de scripts malveillants.
     ```php
     $name = htmlspecialchars(trim($_POST['name']));
     $message = htmlspecialchars(trim($_POST['message']));
     ```

2. **Utilisation de l'en-tête Content-Security-Policy (CSP)** :
   - L'en-tête CSP est utilisé pour restreindre les sources de contenu et empêcher l'exécution de scripts non autorisés.
     ```php
     header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self';");
     ```
