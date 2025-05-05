<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['_username'] ?? '';
    $passwordInput = $_POST['_password'] ?? '';

    // Check Donateur
    $stmt = $dbco->prepare("SELECT * FROM donateur WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $pseudo]);
    $donateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check Responsable
    $stmt_responsable = $dbco->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $stmt_responsable->execute([':pseudo' => $pseudo]);
    $responsable = $stmt_responsable->fetch(PDO::FETCH_ASSOC);

    // Donateur login
    if ($donateur && ($passwordInput === $donateur['pwrd'] || password_verify($passwordInput, $donateur['pwrd']))) {
        $_SESSION['pseudo'] = $donateur['pseudo'];
        $_SESSION['id_donateur'] = $donateur['id_donateur'];
        $_SESSION['user_type'] = 'donateur';
        header('Location: dashboard/donor-dashboard.php');
        exit();

    // Responsable login
    } elseif ($responsable && ($passwordInput === $responsable['pwrd'] || password_verify($passwordInput, $responsable['pwrd']))) {
        $_SESSION['pseudo'] = $responsable['pseudo'];
        $_SESSION['id_responsable'] = $responsable['id_responsable'];
        $_SESSION['user_type'] = 'responsable';
        header('Location: dashboard/responsable-dashboard.php');
        exit();

    } else {
        $error = "Pseudo ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion | TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php" style="color: var(--primary-color);">
      <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
    </a>
    <div class="nav-buttons">
      <a href="inscri.php" class="btn btn-outline-primary">S'inscrire</a>
    </div>
  </div>
</nav>

<section class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card login-card">
        <div class="card-header">
          <h3 class="mb-0">Connectez-vous à votre compte</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-4">
              <label for="username" class="form-label">Pseudo</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="_username" id="username" placeholder="Entrez votre pseudo" required>
              </div>
            </div>
            
            <div class="mb-4">
              <label for="password" class="form-label">Mot de passe</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control password-input" name="_password" id="password" placeholder="Entrez votre mot de passe" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            
            <div class="mb-4 remember-me">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="_remember_me" id="remember_me">
                <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
              </div>
              <a href="#" class="forgot-password">Mot de passe oublié ?</a>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
              <i class="fas fa-sign-in-alt me-2"></i>Se connecter
            </button>
            
            <div class="divider">
              <span class="divider-text">OU</span>
            </div>
            
            <p class="text-center mb-3">Pas encore de compte ?</p>
            <a href="signup/register-donor.php" class="btn btn-outline-primary w-100 mb-2">
              <i class="fas fa-user-plus me-2"></i>Créer un compte Donateur
            </a>
            <a href="signup/register-association.php" class="btn btn-outline-primary w-100">
              <i class="fas fa-building me-2"></i>Inscrire une association
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="bg-dark">
  <div class="container text-center py-3">
    <p class="mb-0">© 2025 TuniDons. Tous droits réservés.</p>
    <div class="mt-2">
      <a href="#" class="text-white mx-2"><i class="fab fa-facebook-f"></i></a>
      <a href="#" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
      <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
      <a href="#" class="text-white mx-2"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle password visibility
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });
</script>
</body>
</html>