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

    // Fetch existing donations by donor
    $stmt = $dbco->prepare("SELECT dp.*, p.titre FROM donateur_projet dp JOIN projet p ON dp.id_projet = p.id_projet WHERE dp.id_donateur = :id_donateur");
    $stmt->execute([':id_donateur' => $_SESSION['id_donateur']]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle donation update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'], $_POST['new_donation_amount'])) {
        $donationId = $_POST['donation_id'];
        $newDonationAmount = $_POST['new_donation_amount'];

        // Fetch old donation details
        $stmt = $dbco->prepare("SELECT id_projet, montant_participation FROM donateur_projet WHERE id = :id");
        $stmt->execute([':id' => $donationId]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donation) {
            // Update donation amount
            $stmt = $dbco->prepare("UPDATE donateur_projet SET montant_participation = :new_amount WHERE id = :id");
            $stmt->execute([':new_amount' => $newDonationAmount, ':id' => $donationId]);

            // Update project total collected amount
            $stmt = $dbco->prepare("UPDATE projet SET montant_total_collecte = montant_total_collecte + :amount_diff WHERE id_projet = :id_projet");
            $stmt->execute([
                ':amount_diff' => $newDonationAmount - $donation['montant_participation'],
                ':id_projet' => $donation['id_projet']
            ]);

            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Donation updated successfully!'];
        } else {
            $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Donation not found.'];
        }
        header("Location: ".$_SERVER['PHP_SELF']);
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
    <title>Modifier un Don - TuniDons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/update-d.css">
    
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
    <div class="card shadow-sm">
        <h2 class="mb-4">
            <i class="fas fa-edit me-2"></i>Modifier un don
        </h2>
        
        <form method="POST">
            <div class="mb-4">
                <label for="donation" class="form-label">Sélectionner un don à modifier</label>
                <select name="donation_id" id="donation" class="form-select" required>
                    <?php foreach ($donations as $donation): ?>
                        <option value="<?= $donation['id'] ?>">
                            <?= htmlspecialchars($donation['titre']) ?> - 
                            <?= number_format($donation['montant_participation'], 2) ?> TND
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="new_donation_amount" class="form-label">Nouveau montant</label>
                <div class="input-group">
                    <span class="input-group-text">TND</span>
                    <input type="number" name="new_donation_amount" id="new_donation_amount" 
                           class="form-control" min="1" step="0.01" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Mettre à jour
                </button>
            </div>
        </form>
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