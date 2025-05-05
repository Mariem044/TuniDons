<?php
session_start();

// Check if the user is already logged in
if (!isset($_SESSION['pseudo'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch donor information
    $stmt = $dbco->prepare("SELECT * FROM donateur WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Donateur - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="donor.css">
  
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline"><?php echo htmlspecialchars($donor['pseudo']); ?></span>
        <a href="../logout.php" class="btn btn-danger">
          <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="dashboard-container">
    <?php if ($donor): ?>
      <h1 class="welcome-title">
        <i class="fas fa-user-circle me-2"></i>Bonjour, <?php echo htmlspecialchars($donor['prenom']); ?> !
      </h1>

      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-user me-2"></i>Informations personnelles
            </div>
            <div class="card-body">
              <div class="info-item">
                <div class="info-label">Nom complet</div>
                <div class="info-value"><?php echo htmlspecialchars($donor['prenom'] . ' ' . $donor['nom']); ?></div>
              </div>
              
              <div class="info-item">
                <div class="info-label">Adresse email</div>
                <div class="info-value"><?php echo htmlspecialchars($donor['email']); ?></div>
              </div>
              
              <div class="info-item">
                <div class="info-label">Numéro CIN</div>
                <div class="info-value"><?php echo htmlspecialchars($donor['CIN']); ?></div>
              </div>
              
              <div class="info-item">
                <div class="info-label">Pseudo</div>
                <div class="info-value"><?php echo htmlspecialchars($donor['pseudo']); ?></div>
              </div>
              
              <a href="edit-profile-donor.php" class="btn btn-primary mt-3">
                <i class="fas fa-user-edit me-2"></i>Modifier le profil
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-bolt me-2"></i>Actions rapides
            </div>
            <div class="card-body">
              <a href="../make-donation.php" class="btn btn-success action-btn">
                <i class="fas fa-search-dollar"></i> Voir les projets disponibles
              </a>
              
              <a href="my-donations.php" class="btn btn-outline-primary action-btn">
                <i class="fas fa-hand-holding-usd"></i> Mes dons
              </a>
              
              <a href="#" class="btn btn-outline-secondary action-btn">
                <i class="fas fa-heart"></i> Projets favoris
              </a>
              
              <a href="#" class="btn btn-outline-secondary action-btn">
                <i class="fas fa-bell"></i> Notifications
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>Impossible de récupérer vos informations. Veuillez réessayer plus tard.
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 