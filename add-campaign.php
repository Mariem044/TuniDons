<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un responsable
if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=donation;charset=utf8';
$username = 'root';
$password = '';
$msg = '';

try {
    $dbco = new PDO($dsn, $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'ID du responsable connecté
    $stmt = $dbco->prepare("SELECT id_responsable FROM responsable_association WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$responsable) {
        throw new Exception("Responsable non trouvé.");
    }

    // Gestion du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les valeurs du formulaire
        $titre = trim($_POST['titre']);
        $description = trim($_POST['description']);
        $montant = floatval($_POST['montant']);
        $date_limite = $_POST['date_limite'];

        // Validation
        if (empty($titre) || empty($description) || empty($montant) || empty($date_limite)) {
            $msg = ['type' => 'danger', 'message' => 'Veuillez remplir tous les champs.'];
        } elseif ($montant <= 0) {
            $msg = ['type' => 'danger', 'message' => 'Le montant doit être supérieur à zéro.'];
        } elseif (strtotime($date_limite) < strtotime(date('Y-m-d'))) {
            $msg = ['type' => 'danger', 'message' => 'La date limite doit être dans le futur.'];
        } else {
            // Insertion dans la base
            $stmt = $dbco->prepare("INSERT INTO projet (titre, description, date_limite, montant_total_a_collecter, montant_total_collecte, id_responsable_association) 
                                    VALUES (:titre, :description, :date_limite, :montant, 0, :id_responsable)");
            $stmt->execute([
                ':titre' => $titre,
                ':description' => $description,
                ':date_limite' => $date_limite,
                ':montant' => $montant,
                ':id_responsable' => $responsable['id_responsable']
            ]);

            $msg = ['type' => 'success', 'message' => 'Projet ajouté avec succès !'];
        }
    }

} catch (PDOException $e) {
    $msg = ['type' => 'danger', 'message' => 'Erreur de base de données : ' . $e->getMessage()];
} catch (Exception $e) {
    $msg = ['type' => 'danger', 'message' => 'Erreur : ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Projet - TuniDons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/add-campaign.css">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="dashboard/responsable-dashboard.php">
                <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['pseudo']); ?></span>
                <a href="../logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>Se Déconnecter
                </a>
            </div>
        </div>
    </nav>

    <div class="project-container">
        <div class="project-card">
            <div class="project-header">
                <h2><i class="fas fa-plus-circle me-2"></i>Ajouter un Nouveau Projet</h2>
            </div>
            
            <div class="project-body">
                <form method="POST">
                    <div class="mb-4">
                        <label for="titre" class="form-label">Titre du projet</label>
                        <div class="input-icon">
                            <i class="fas fa-heading"></i>
                            <input type="text" name="titre" id="titre" class="form-control" required maxlength="255" placeholder="Nommez votre projet">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Description détaillée</label>
                        <textarea name="description" id="description" rows="4" class="form-control" required placeholder="Décrivez votre projet en détail..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="montant" class="form-label">Montant à collecter (TND)</label>
                            <div class="input-icon">
                                <i class="fas fa-money-bill-wave"></i>
                                <input type="number" name="montant" id="montant" class="form-control" step="0.01" min="1" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="date_limite" class="form-label">Date limite</label>
                            <div class="input-icon">
                                <i class="fas fa-calendar-alt"></i>
                                <input type="date" name="date_limite" id="date_limite" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between pt-3">
                        <a href="dashboard/responsable-dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Ajouter le projet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($msg)): ?>
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-<?= $msg['type'] ?> border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas <?= $msg['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                    <?= htmlspecialchars($msg['message']) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum date for date picker (today)
        document.getElementById('date_limite').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>