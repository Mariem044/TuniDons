<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetching available projects
    $stmt = $dbco->prepare("SELECT * FROM projet WHERE date_limite >= CURDATE() AND montant_total_a_collecter > montant_total_collecte");
    $stmt->execute();
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle donation form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_amount'], $_POST['projet_id'])) {
        $donationAmount = (float) $_POST['donation_amount'];
        $projetId = (int) $_POST['projet_id'];

        if ($donationAmount > 0) {
            $stmt = $dbco->prepare("SELECT montant_total_a_collecter, montant_total_collecte FROM projet WHERE id_projet = :id_projet");
            $stmt->execute([':id_projet' => $projetId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            $remainingAmount = $project['montant_total_a_collecter'] - $project['montant_total_collecte'];

            if ($donationAmount <= $remainingAmount) {
                // Insert donation
                $stmt = $dbco->prepare("INSERT INTO donateur_projet (id_projet, id_donateur, montant_participation) VALUES (:id_projet, :id_donateur, :montant)");
                $stmt->execute([
                    ':id_projet' => $projetId,
                    ':id_donateur' => $_SESSION['id_donateur'],
                    ':montant' => $donationAmount
                ]);

                // Update collected amount
                $stmt = $dbco->prepare("UPDATE projet SET montant_total_collecte = montant_total_collecte + :donation WHERE id_projet = :id_projet");
                $stmt->execute([
                    ':donation' => $donationAmount,
                    ':id_projet' => $projetId
                ]);

                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Donation successful!'];
            } else {
                $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Donation exceeds the remaining required amount.'];
            }
        } else {
            $_SESSION['toast'] = ['type' => 'warning', 'message' => 'Invalid donation amount.'];
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Erreur : ' . $e->getMessage()];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Don - TuniDons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/make-donation.css">
   
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard/donor-dashboard.php">
            <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
        </a>
        <div>
            <span class="me-3 d-none d-md-inline"><?= htmlspecialchars($_SESSION['pseudo']) ?></span>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm p-4 mb-5">
                <h2 class="text-center mb-4">
                    <i class="fas fa-donate me-2"></i>Faire un don
                </h2>
                
                <form method="POST">
                    <div class="mb-4">
                        <label for="projet" class="form-label">Projet à soutenir</label>
                        <select name="projet_id" id="projet" class="form-select form-control-lg" required>
                            <?php foreach ($projets as $projet): ?>
                                <option value="<?= $projet['id_projet'] ?>">
                                    <?= htmlspecialchars($projet['titre']) ?> - 
                                    <?= number_format($projet['montant_total_collecte'], 2) ?> TND / 
                                    <?= number_format($projet['montant_total_a_collecter'], 2) ?> TND
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="donation_amount" class="form-label">Montant du don (TND)</label>
                        <div class="input-group">
                            <span class="input-group-text">TND</span>
                            <input type="number" name="donation_amount" id="donation_amount" 
                                   class="form-control form-control-lg" 
                                   min="1" step="0.01" required>
                        </div>
                        <small class="text-muted">Veuillez entrer un montant positif</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le don
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if (!empty($projets)): ?>
            <div class="card shadow-sm p-4">
                <h3 class="mb-4">
                    <i class="fas fa-list-ul me-2"></i>Projets disponibles
                </h3>
                
                <div class="row g-4">
                    <?php foreach ($projets as $projet): 
                        $progress = ($projet['montant_total_collecte'] / $projet['montant_total_a_collecter']) * 100;
                        $progress = min(100, $progress);
                    ?>
                    <div class="col-md-6">
                        <div class="card project-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($projet['titre']) ?></h5>
                                <p class="card-text text-muted"><?= nl2br(htmlspecialchars(substr($projet['description'], 0, 120))) ?>...</p>
                                
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $progress ?>%" 
                                         aria-valuenow="<?= $progress ?>" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <small><strong>Collecté:</strong> <?= number_format($projet['montant_total_collecte'], 2) ?> TND</small>
                                    <small><strong>Objectif:</strong> <?= number_format($projet['montant_total_a_collecter'], 2) ?> TND</small>
                                </div>
                                
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        Date limite: <?= date('d/m/Y', strtotime($projet['date_limite'])) ?>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['toast'])): ?>
    <div class="toast-container">
        <div class="toast align-items-center text-white bg-<?= $_SESSION['toast']['type'] ?> show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas <?= $_SESSION['toast']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                    <?= $_SESSION['toast']['message'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php $_SESSION['toast'] = null; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide toast after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.querySelector('.toast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();
        }
    });
</script>
</body>
</html>