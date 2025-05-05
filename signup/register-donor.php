<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and fetch form data
        $nom = htmlspecialchars($_POST["nom"]);
        $prenom = htmlspecialchars($_POST["prenom"]);
        $email = htmlspecialchars($_POST["email"]);
        $cin = htmlspecialchars($_POST["cin"]);
        $pseudo = htmlspecialchars($_POST["pseudo"]);
        $motDePasse = $_POST["password"]; // Do not sanitize passwords

        // Hash the password
        $motDePasseHashe = password_hash($motDePasse, PASSWORD_DEFAULT);

        // Insert into database
        $sth = $dbco->prepare("INSERT INTO donateur (nom, prenom, email, CIN, pseudo, pwrd) 
                               VALUES (:nom, :prenom, :email, :cin, :pseudo, :pwrd)");
        $sth->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':cin' => $cin,
            ':pseudo' => $pseudo,
            ':pwrd' => $motDePasseHashe
        ]);

        header("Location: ../login.php"); // Redirect after success
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription Donateur - TuniDons</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="donor.css">
</head>
<body class="d-flex align-items-center min-vh-100 py-5">
  <div class="container">
    <div class="registration-container">
      <div class="registration-header">
        <h1 class="registration-title">
          <i class="fas fa-hand-holding-heart me-2"></i>Inscription Donateur
        </h1>
        <p class="text-muted">Rejoignez notre communauté de donateurs engagés</p>
      </div>
      
      <form id="registrationForm" method="POST" action="">
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="nom" class="form-label">Nom</label>
            <div class="input-icon">
              <i class="fas fa-user"></i>
              <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label for="prenom" class="form-label">Prénom</label>
            <div class="input-icon">
              <i class="fas fa-user"></i>
              <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="email" class="form-label">Adresse Email</label>
          <div class="input-icon">
            <i class="fas fa-envelope"></i>
            <input type="email" class="form-control" id="email" name="email" placeholder="exemple@email.com" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="cin" class="form-label">CIN</label>
          <div class="input-icon">
            <i class="fas fa-id-card"></i>
            <input type="text" class="form-control" id="cin" name="cin" maxlength="8" placeholder="12345678" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="pseudo" class="form-label">Pseudo</label>
          <div class="input-icon">
            <i class="fas fa-at"></i>
            <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Choisissez un pseudo" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="password-container">
            <div class="input-icon">
              <i class="fas fa-lock"></i>
              <input type="password" class="form-control" id="password" name="password" placeholder="Créez un mot de passe" required>
            </div>
            <span class="toggle-password" onclick="togglePassword('password')">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
          <div class="password-container">
            <div class="input-icon">
              <i class="fas fa-lock"></i>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre mot de passe" required>
            </div>
            <span class="toggle-password" onclick="togglePassword('confirmPassword')">
              <i class="fas fa-eye"></i>
            </span>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 py-3">
          <i class="fas fa-user-plus me-2"></i>Créer mon compte
        </button>
      </form>
      
      <div class="text-center mt-4">
        <p class="text-muted">Vous avez déjà un compte? <a href="../login.php" class="text-success">Connectez-vous</a></p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = field.nextElementSibling.querySelector('i');
      
      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  </script>
  <script src="js/validation.js"></script>
</body>
</html>