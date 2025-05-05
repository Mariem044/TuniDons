<?php
session_start();

if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Projet non spécifié.");
}

$projectId = intval($_GET['id']);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get project
    $stmt = $dbco->prepare("SELECT * FROM projet WHERE id_projet = :id");
    $stmt->execute([':id' => $projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        throw new Exception("Projet introuvable.");
    }

    // Get donations
    $stmt = $dbco->prepare("
        SELECT d.nom, d.prenom, dp.montant_participation, dp.date_participation
        FROM donateur_projet dp
        JOIN donateur d ON d.id_donateur = dp.id_donateur
        WHERE dp.id_projet = :id
        ORDER BY dp.date_participation DESC
    ");
    $stmt->execute([':id' => $projectId]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals
    $totalCollected = $project['montant_total_collecte'];
    $totalNeeded = $project['montant_total_a_collecter'];
    $remaining = $totalNeeded - $totalCollected;
    $progress = ($totalCollected / $totalNeeded) * 100;

} catch (PDOException $e) {
    die("Erreur DB : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détails du projet - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/details.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard_responsable.php">
      <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
    </a>
    <div>
      <span class="me-3 d-none d-md-inline"><?= htmlspecialchars($_SESSION['pseudo']) ?></span>
      <a href="logout.php" class="btn btn-outline-danger">
        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
      </a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card">
    <div class="card-body">
      <h2>
        <i class="fas fa-project-diagram me-2"></i><?= htmlspecialchars($project['titre']) ?>
      </h2>
      
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%" 
             aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      
      <div class="project-meta">
        <div class="meta-item">
          <h5><i class="fas fa-bullseye me-2"></i>Objectif</h5>
          <p><?= number_format($totalNeeded, 2) ?> TND</p>
        </div>
        <div class="meta-item">
          <h5><i class="fas fa-hand-holding-usd me-2"></i>Collecté</h5>
          <p><?= number_format($totalCollected, 2) ?> TND</p>
        </div>
        <div class="meta-item">
          <h5><i class="fas fa-piggy-bank me-2"></i>Restant</h5>
          <p><?= number_format($remaining, 2) ?> TND</p>
        </div>
        <div class="meta-item">
          <h5><i class="far fa-calendar-alt me-2"></i>Date limite</h5>
          <p><?= date('d/m/Y', strtotime($project['date_limite'])) ?></p>
        </div>
      </div>
      
      <h5 class="mb-3"><i class="fas fa-align-left me-2"></i>Description</h5>
      <p class="mb-4"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
      
      <hr>
      
      <h4><i class="fas fa-users me-2"></i>Liste des Donateurs</h4>
      <?php if (empty($donations)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-circle me-2"></i>Aucun donateur pour ce projet.
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><i class="fas fa-user me-1"></i>Nom</th>
                <th><i class="fas fa-user me-1"></i>Prénom</th>
                <th><i class="fas fa-money-bill-wave me-1"></i>Montant</th>
                <th><i class="far fa-calendar-alt me-1"></i>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($donations as $don): ?>
                <tr>
                  <td><?= htmlspecialchars($don['nom']) ?></td>
                  <td><?= htmlspecialchars($don['prenom']) ?></td>
                  <td><?= number_format($don['montant_participation'], 2) ?> TND</td>
                  <td><?= date('d/m/Y H:i', strtotime($don['date_participation'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>