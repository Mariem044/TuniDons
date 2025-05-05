<?php
session_start();

// Vérifier si le donateur est connecté
if (!isset($_SESSION['pseudo'])) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'ID du donateur connecté
    $stmt = $dbco->prepare("SELECT id_donateur FROM donateur WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $donateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$donateur) {
        throw new Exception("Donateur introuvable.");
    }

    // Récupérer les participations aux projets du donateur
    $stmt = $dbco->prepare("
        SELECT dp.date_participation, dp.montant_participation, p.titre
        FROM donateur_projet dp
        JOIN projet p ON dp.id_projet = p.id_projet
        WHERE dp.id_donateur = :id_donateur
        ORDER BY dp.date_participation DESC
    ");
    $stmt->execute([':id_donateur' => $donateur['id_donateur']]);
    $dons = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $errorMsg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Dons - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="don.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand" href="dashboard-donateur.php">
        <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['pseudo']); ?></span>
        <a href="../logout.php" class="btn btn-danger">
          <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="dashboard-container">
    <h1 class="page-header">
      <i class="fas fa-history me-2"></i>Historique de mes dons
    </h1>

    <?php if (isset($errorMsg)): ?>
      <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMsg); ?>
      </div>
    <?php elseif (empty($dons)): ?>
      <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>Vous n'avez pas encore effectué de dons.
      </div>
    <?php else: ?>
      <div class="table-container">
        <table class="table table-hover">
          <thead>
            <tr>
              <th><i class="fas fa-project-diagram me-2"></i>Projet</th>
              <th><i class="fas fa-money-bill-wave me-2"></i>Montant (TND)</th>
              <th><i class="fas fa-calendar-alt me-2"></i>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dons as $don): ?>
              <tr>
                <td><?php echo htmlspecialchars($don['titre']); ?></td>
                <td class="donation-amount"><?php echo number_format($don['montant_participation'], 2); ?></td>
                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($don['date_participation']))); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mt-4">
      <a href="donor-dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>