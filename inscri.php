<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription - TuniDons</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="css/inscri.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
      </a>
    </div>
  </nav>

  <!-- Registration Options -->
  <section class="hero-section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">Créer un compte</h2>
        <p class="section-subtitle">Choisissez votre type de compte pour continuer</p>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6">
          <div class="card h-100">
            <div class="card-body p-4">
              <div class="icon-container">
                <i class="fas fa-heart icon"></i>
              </div>
              <h5 class="card-title">Je suis un Donateur</h5>
              <p class="card-text">Faire des dons, suivre les projets et contribuer aux causes importantes.</p>
              <a href="signup/register-donor.php" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>S'inscrire comme Donateur
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-md-6 mt-4 mt-md-0">
          <div class="card h-100">
            <div class="card-body p-4">
              <div class="icon-container">
                <i class="fas fa-users icon"></i>
              </div>
              <h5 class="card-title">Je suis un Responsable d'Association</h5>
              <p class="card-text">Créer et gérer des projets de collecte de fonds pour votre association.</p>
              <a href="signup/register-association.php" class="btn btn-outline-primary">
                <i class="fas fa-building me-2"></i>S'inscrire comme Responsable
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark">
    <div class="container">
      <div class="text-center py-3">
        <p class="mb-0">© 2025 TuniDons. Tous droits réservés.</p>
        <div class="mt-2">
          <a href="#" class="text-white mx-2"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-white mx-2"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>