<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $cin = $_POST["cin"];
        $email = $_POST["email"];
        $nomAssociation = $_POST["associationName"];
        $adresseAssociation = $_POST["associationAddress"];
        $matriculeFiscal = $_POST["fiscalId"];
        $pseudo = $_POST["pseudo"];
        $motDePasse = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];

        if ($motDePasse !== $confirmPassword) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        // Validate uploaded file
        if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors de l'upload du logo.");
        }

        $logoTmp = $_FILES["logo"]["tmp_name"];
        $fileSize = $_FILES["logo"]["size"];
        $mimeType = mime_content_type($logoTmp);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception("Format de logo non supporté (seulement JPG, PNG, GIF).");
        }

        if ($fileSize > 2 * 1024 * 1024) {
            throw new Exception("Le fichier logo est trop volumineux (max 2MB).");
        }

        $logoData = file_get_contents($logoTmp);
        $motDePasseHashe = password_hash($motDePasse, PASSWORD_DEFAULT);

        $sth = $dbco->prepare("INSERT INTO responsable_association (nom, prenom, CIN, email, nom_association, adresse_association, matricule_fiscal, logo, pseudo, pwrd) 
                               VALUES (:nom, :prenom, :cin, :email, :nom_association, :adresse_association, :matricule_fiscal, :logo, :pseudo, :pwrd)");

        $sth->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':cin' => $cin,
            ':email' => $email,
            ':nom_association' => $nomAssociation,
            ':adresse_association' => $adresseAssociation,
            ':matricule_fiscal' => $matriculeFiscal,
            ':logo' => $logoData,
            ':pseudo' => $pseudo,
            ':pwrd' => $motDePasseHashe
        ]);

        header("Location: ../login.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur base de données : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription Association - TuniDons</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="asso.css">
    
</head>
<body>
  <div class="container py-5">
    <div class="registration-container">
      <div class="registration-header">
        <h1 class="registration-title">
          <i class="fas fa-hands-helping me-2"></i>Inscription Association
        </h1>
        <p class="text-muted">Créez votre compte responsable d'association</p>
      </div>
      
      <form id="registrationForm" method="POST" action="" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="nom" class="form-label">Nom</label>
            <div class="input-icon">
              <i class="fas fa-user"></i>
              <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom" required>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label for="prenom" class="form-label">Prénom</label>
            <div class="input-icon">
              <i class="fas fa-user"></i>
              <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Votre prénom" required>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="cin" class="form-label">Numéro CIN</label>
            <div class="input-icon">
              <i class="fas fa-id-card"></i>
              <input type="text" class="form-control" id="cin" name="cin" placeholder="12345678" required>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label for="email" class="form-label">Adresse Email</label>
            <div class="input-icon">
              <i class="fas fa-envelope"></i>
              <input type="email" class="form-control" id="email" name="email" placeholder="contact@association.tn" required>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="associationName" class="form-label">Nom de l'association</label>
          <div class="input-icon">
            <i class="fas fa-building"></i>
            <input type="text" class="form-control" id="associationName" name="associationName" placeholder="Nom officiel de l'association" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="associationAddress" class="form-label">Adresse de l'association</label>
          <div class="input-icon">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" class="form-control" id="associationAddress" name="associationAddress" placeholder="Adresse complète" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="fiscalId" class="form-label">Identifiant fiscal</label>
          <div class="input-icon">
            <i class="fas fa-file-invoice-dollar"></i>
            <input type="text" class="form-control" id="fiscalId" name="fiscalId" placeholder="Ex: ABC123" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="logo" class="form-label">Logo de l'association</label>
          <div class="file-upload">
            <input type="file" class="file-upload-input" id="logo" name="logo" accept="image/*" required>
            <label for="logo" class="file-upload-label">
              <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
              <span id="file-name">Choisir un fichier (JPG, PNG, GIF - max 2MB)</span>
            </label>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="pseudo" class="form-label">Pseudo</label>
          <div class="input-icon">
            <i class="fas fa-at"></i>
            <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Votre pseudo" required>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-4">
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
            <div class="form-text">Au moins 8 caractères lettres/chiffres, finissant par $ ou #</div>
          </div>
          <div class="col-md-6 mb-4">
            <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
            <div class="password-container">
              <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmez le mot de passe" required>
              </div>
              <span class="toggle-password" onclick="togglePassword('confirmPassword')">
                <i class="fas fa-eye"></i>
              </span>
            </div>
          </div>
        </div>
        
        <button type="submit" class="btn btn-outline-success w-100 py-3">
          <i class="fas fa-user-plus me-2"></i>Créer mon compte
        </button>
      </form>
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
    
    document.getElementById('logo').addEventListener('change', function(e) {
      const fileName = e.target.files[0] ? e.target.files[0].name : 'Choisir un fichier (JPG, PNG, GIF - max 2MB)';
      document.getElementById('file-name').textContent = fileName;
    });
  </script>
  <script src="js/main.js"></script>
</body>
</html>