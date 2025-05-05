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

if (isset($_GET['id'])) {
    $id_projet = $_GET['id'];

    try {
        $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch project data
        $stmt = $dbco->prepare("SELECT * FROM projet WHERE id_projet = :id_projet");
        $stmt->execute([':id_projet' => $id_projet]);
        $projet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$projet) {
            throw new Exception("Projet non trouvé.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $montant_total_a_collecter = $_POST['montant_total_a_collecter'];
            $date_limite = $_POST['date_limite'];

            // Update the project
            $stmt = $dbco->prepare("UPDATE projet SET titre = :titre, description = :description, montant_total_a_collecter = :montant_total_a_collecter, date_limite = :date_limite WHERE id_projet = :id_projet");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':montant_total_a_collecter' => $montant_total_a_collecter,
                ':date_limite' => $date_limite,
                ':id_projet' => $id_projet
            ]);

            // Redirect after update
            header('Location: list-projects.php');
            exit();
        }

    } catch (PDOException $e) {
        die("Erreur DB : " . $e->getMessage());
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo "Aucun projet trouvé à modifier.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mettre à jour le projet - TuniDons</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/update-p.css">
  
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
  <div class="card shadow-sm">
    <h2 class="mb-4">
      <i class="fas fa-edit me-2"></i>Mettre à jour le projet
    </h2>
    
    <form action="update_project.php?id=<?= $id_projet ?>" method="POST">
      <div class="mb-4">
        <label for="titre" class="form-label">Titre du projet</label>
        <input type="text" class="form-control" id="titre" name="titre" 
               value="<?= htmlspecialchars($projet['titre']) ?>" required>
      </div>
      
      <div class="mb-4">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" 
                  rows="6" required><?= htmlspecialchars($projet['description']) ?></textarea>
      </div>
      
      <div class="mb-4">
        <label for="montant_total_a_collecter" class="form-label">Montant à collecter</label>
        <div class="input-group">
          <span class="input-group-text">TND</span>
          <input type="number" class="form-control" id="montant_total_a_collecter" 
                 name="montant_total_a_collecter" step="0.01" min="1"
                 value="<?= htmlspecialchars($projet['montant_total_a_collecter']) ?>" required>
        </div>
      </div>
      
      <div class="mb-4">
        <label for="date_limite" class="form-label">Date limite</label>
        <input type="date" class="form-control" id="date_limite" name="date_limite" 
               value="<?= htmlspecialchars($projet['date_limite']) ?>" required>
      </div>
      
      <div class="d-flex justify-content-end mt-4">
        <a href="list-projects.php" class="btn btn-outline-secondary me-3">
          <i class="fas fa-times me-1"></i> Annuler
        </a>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save me-1"></i> Mettre à jour
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>