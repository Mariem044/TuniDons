<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=donation", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM projet ORDER BY date_limite DESC LIMIT 6");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TuniDons - Plateforme de financement participatif</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/index.css">
  
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
      <ul class="navbar-nav nav-center mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="#">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
      </ul>
      <div class="nav-buttons">
        <a id="login-button" href="login.php" class="btn btn-outline-dark me-2">Se connecter</a>
        <a href="inscri.php" class="btn btn-success">S'inscrire</a>
      </div>
    </div>  
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section text-center">
  <div class="container">
    <p class="hero-subtitle">Plateforme N°1 en Tunisie</p>
    <h1 class="hero-title">Les collectes de fonds réussies<br>commencent ici</h1>
    <a href="inscri.php" class="btn btn-success btn-lg mt-3">
      <i class="fas fa-rocket me-2"></i>Lancer un TuniDons
    </a>
  </div>

  <!-- Floating Images -->
  <div class="img-container container">
    <div class="floating-img img-1">
      <img src="images/cause.jpg" class="circle-img" alt="Votre cause">
      <div class="label">Votre cause</div>
    </div>
    <div class="floating-img img-2">
      <img src="images/medicale.jpg" class="circle-img" alt="Médical">
      <div class="label">Médical</div>
    </div>
    <div class="floating-img img-3">
      <img src="images/urgence.jpg" class="circle-img" alt="Urgence">
      <div class="label">Urgence</div>
    </div>
    <div class="floating-img img-4">
      <img src="images/305.jpg" class="circle-img" alt="Éducation">
      <div class="label">Éducation</div>
    </div>
    <div class="floating-img img-5">
      <img src="images/business.jpg" class="circle-img" alt="Business">
      <div class="label">Business</div>
    </div>
  </div>
</section>

<!-- Highlight Bar -->
<section class="highlight-bar">
  <div class="container highlight-content">
    <div class="highlight-item">⚡ <span><strong>Pas de frais pour commencer</strong></span></div>
    <div class="separator"></div>
    <div class="highlight-item">❤️ <span><strong>Financement sécurisé</strong></span></div>
    <div class="separator"></div>
    <div class="highlight-item">🏆 <span><strong>Plateforme N°1</strong></span></div>
  </div>
</section>

<!-- Projects Section -->
<section class="container my-5 py-5">
  <h2 class="text-center section-title">Projets en vedette</h2>
  <div class="row g-4">
    <?php foreach ($projects as $project): 
      $remaining = $project['montant_total_a_collecter'] - $project['montant_total_collecte'];
      $percentage = ($project['montant_total_a_collecter'] > 0) 
                    ? min(100, round(($project['montant_total_collecte'] / $project['montant_total_a_collecter']) * 100)) 
                    : 0;
    ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo htmlspecialchars($project['titre']); ?></h5>
            <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($project['description'], 0, 100))) . '...'; ?></p>
            
            <div class="mt-auto">
              <div class="d-flex justify-content-between mb-2">
                <span><strong>Objectif:</strong> <?= number_format($project['montant_total_a_collecter'], 2); ?> TND</span>
                <span><strong><?= $percentage; ?>%</strong></span>
              </div>
              
              <div class="progress-container">
                <div class="progress-bar">
                  <span style="width: <?= $percentage; ?>%;"></span>
                </div>
              </div>
              
              <div class="d-flex justify-content-between mt-2">
                <span><i class="far fa-calendar-alt me-1"></i> <?= $project['date_limite']; ?></span>
                <span><strong><?= number_format($project['montant_total_collecte'], 2); ?> TND</strong></span>
              </div>
              
              <a href="project-details.php?id=<?= $project['id_projet']; ?>" class="btn btn-success w-100 mt-3">
                <i class="fas fa-hand-holding-heart me-2"></i>Contribuer
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="row justify-content-between align-items-center">
      <div class="col-md-6 mb-3 mb-md-0">
        <div class="language-btn mb-3">
          <img src="https://flagcdn.com/tn.svg" width="20" alt="Drapeau Tunisie">
          <span>Tunisie · Français</span>
        </div>
        <div class="small mb-3">
          © 2025 TuniDons
          <a href="#">Conditions</a>
          <a href="#">Politique de confidentialité</a>
          <a href="#">Mentions légales</a>
        </div>
      </div>
      <div class="col-md-6 text-md-end">
        <div class="social-icons mb-3">
          <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>