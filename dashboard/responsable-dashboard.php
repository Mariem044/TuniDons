<?php
session_start();

// Check if the user is logged in and is a responsable
if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
    header('Location: ../login.php');
    exit();
}

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch responsable information
    $stmt = $dbco->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($responsable === false) {
        throw new Exception("Aucun responsable trouvé avec ce pseudo.");
    }

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Responsable - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="responsable.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline"><?php echo htmlspecialchars($responsable['pseudo']); ?></span>
        <a href="../logout.php" class="btn btn-danger">
          <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="dashboard-container">
    <h1 class="welcome-title">
      <i class="fas fa-user-tie me-2"></i>Bienvenue, <?php echo htmlspecialchars($responsable['prenom']); ?> !
    </h1>

    <div class="row">
      <!-- Profile Information -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-id-card me-2"></i>Profil du Responsable
          </div>
          <div class="card-body">
            <?php if (!empty($responsable['logo'])): ?>
              <img src="data:image/jpeg;base64,<?php echo base64_encode($responsable['logo']); ?>" class="association-logo" alt="Logo de l'association">
            <?php else: ?>
              <div class="association-logo bg-light d-flex align-items-center justify-content-center">
                <i class="fas fa-building text-muted" style="font-size: 2rem;"></i>
              </div>
            <?php endif; ?>
            
            <div class="info-item">
              <div class="info-label">Nom complet</div>
              <div class="info-value"><?php echo htmlspecialchars($responsable['prenom'] . ' ' . $responsable['nom']); ?></div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Association</div>
              <div class="info-value"><?php echo htmlspecialchars($responsable['nom_association']); ?></div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Adresse</div>
              <div class="info-value"><?php echo htmlspecialchars($responsable['adresse_association']); ?></div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Identifiant fiscal</div>
              <div class="info-value"><?php echo htmlspecialchars($responsable['matricule_fiscal']); ?></div>
            </div>
            
            <div class="info-item">
              <div class="info-label">Coordonnées</div>
              <div class="info-value">
                <div><i class="fas fa-id-card me-2"></i><?php echo htmlspecialchars($responsable['CIN']); ?></div>
                <div><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($responsable['email']); ?></div>
                <div><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($responsable['pseudo']); ?></div>
              </div>
            </div>
            
            <a href="../edit-profile-responsable.php" class="btn btn-warning mt-3">
              <i class="fas fa-user-edit me-2"></i>Modifier le profil
            </a>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-bolt me-2"></i>Actions disponibles
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <a href="../add-campaign.php" class="btn btn-outline-primary action-btn">
                  <i class="fas fa-plus-circle me-2"></i>Ajouter un projet
                </a>
              </li>
              <li class="list-group-item">
                <a href="../list-projects.php" class="btn btn-outline-success action-btn">
                  <i class="fas fa-list-ul me-2"></i>Lister mes projets
                </a>
              </li>
              <li class="list-group-item">
                <a href="#" class="btn btn-outline-secondary action-btn">
                  <i class="fas fa-chart-line me-2"></i>Statistiques
                </a>
              </li>
              <li class="list-group-item">
                <a href="#" class="btn btn-outline-secondary action-btn">
                  <i class="fas fa-users me-2"></i>Gérer les membres
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>