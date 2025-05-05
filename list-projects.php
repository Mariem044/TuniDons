<?php
session_start();

// Check if user is logged in and is a responsable
if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
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

    // Get responsable ID
    $stmt = $dbco->prepare("SELECT id_responsable FROM responsable_association WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$responsable) {
        throw new Exception("Responsable non trouvé.");
    }

    // Fetch projects created by this responsable
    $stmt = $dbco->prepare("SELECT * FROM projet WHERE id_responsable_association = :id ORDER BY date_limite DESC");
    $stmt->execute([':id' => $responsable['id_responsable']]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <title>Mes Projets - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #4e73df;
      --secondary-color: #2c3e50;
      --success-color: #1cc88a;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --light-gray: #f8f9fc;
      --dark-gray: #5a5c69;
      --border-radius: 0.35rem;
    }
    
    body {
      font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background-color: var(--light-gray);
      color: var(--dark-gray);
    }
    
    .navbar {
      background-color: white;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
      padding: 0.75rem 1rem;
    }
    
    .navbar-brand {
      font-weight: 800;
      font-size: 1.25rem;
      color: var(--secondary-color);
    }
    
    .container {
      max-width: 1200px;
      padding-top: 2rem;
      padding-bottom: 3rem;
    }
    
    h2 {
      color: var(--secondary-color);
      font-weight: 700;
      margin-bottom: 2rem;
      position: relative;
      padding-bottom: 10px;
    }
    
    h2:after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 60px;
      height: 3px;
      background-color: var(--success-color);
    }
    
    .card {
      border: none;
      border-radius: var(--border-radius);
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.75rem 0 rgba(58, 59, 69, 0.2);
    }
    
    .card-title {
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1rem;
    }
    
    .card-text {
      margin-bottom: 0.75rem;
      font-size: 0.95rem;
    }
    
    .card-text strong {
      color: var(--secondary-color);
    }
    
    .btn {
      border-radius: var(--border-radius);
      font-weight: 600;
      padding: 0.5rem 1rem;
      transition: all 0.3s;
    }
    
    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-color);
      color: white;
    }
    
    .btn-outline-warning {
      color: var(--warning-color);
      border-color: var(--warning-color);
    }
    
    .btn-outline-warning:hover {
      background-color: var(--warning-color);
      color: white;
    }
    
    .btn-outline-danger {
      color: var(--danger-color);
      border-color: var(--danger-color);
    }
    
    .btn-outline-danger:hover {
      background-color: var(--danger-color);
      color: white;
    }
    
    .alert-info {
      background-color: #d1ecf1;
      color: #0c5460;
      border-color: #bee5eb;
      border-radius: var(--border-radius);
    }
    
    .progress {
      height: 0.5rem;
      border-radius: 0.25rem;
      margin-bottom: 1rem;
    }
    
    .progress-bar {
      background-color: var(--success-color);
    }
    
    @media (max-width: 768px) {
      .container {
        padding-top: 1.5rem;
        padding-bottom: 2rem;
      }
      
      h2 {
        font-size: 1.75rem;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard_responsable.php">
      <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
    </a>
    <div>
      <span class="me-3 d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['pseudo']); ?></span>
      <a href="logout.php" class="btn btn-outline-danger">
        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
      </a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-project-diagram me-2"></i>Mes Projets</h2>
    <a href="add-campaign.php" class="btn btn-success">
      <i class="fas fa-plus me-1"></i> Nouveau Projet
    </a>
  </div>

  <?php if (count($projects) === 0): ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>Vous n'avez pas encore ajouté de projets.
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($projects as $projet): 
        $progress = ($projet['montant_total_collecte'] / $projet['montant_total_a_collecter']) * 100;
        $progress = min(100, $progress); // Ensure progress doesn't exceed 100%
      ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?php echo htmlspecialchars($projet['titre']); ?></h5>
              <p class="card-text text-muted"><?php echo nl2br(htmlspecialchars(substr($projet['description'], 0, 100))) . '...'; ?></p>
              
              <div class="progress mt-2">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%" 
                  aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              
              <div class="d-flex justify-content-between mb-2">
                <small><strong>Collecté:</strong> <?php echo number_format($projet['montant_total_collecte'], 2); ?> TND</small>
                <small><strong>Objectif:</strong> <?php echo number_format($projet['montant_total_a_collecter'], 2); ?> TND</small>
              </div>
              
              <p class="card-text"><i class="far fa-calendar-alt me-2"></i><strong>Date limite:</strong> <?php echo date('d/m/Y', strtotime($projet['date_limite'])); ?></p>
              
              <div class="mt-auto pt-3">
                <div class="d-grid gap-2">
                  <a href="project-details.php?id=<?php echo $projet['id_projet']; ?>" class="btn btn-outline-primary">
                    <i class="far fa-eye me-1"></i> Voir détails
                  </a>
                  <a href="update_project.php?id=<?php echo $projet['id_projet']; ?>" class="btn btn-outline-warning">
                    <i class="far fa-edit me-1"></i> Modifier
                  </a>
                  <a href="delete_project.php?id=<?php echo $projet['id_projet']; ?>" class="btn btn-outline-danger" 
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                    <i class="far fa-trash-alt me-1"></i> Supprimer
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>