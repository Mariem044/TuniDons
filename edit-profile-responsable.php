<?php
session_start();

// Redirect if not connected as "responsable"
if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
    header("Location: login.php");
    exit();
}

$dsn = "mysql:host=localhost;dbname=donation;charset=utf8";
$user = "root";
$pass = "";

$msg = "";
$type = ""; // 'success' or 'danger'

try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get responsable info
    $stmt = $db->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new Exception("Utilisateur non trouvé.");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $update = $db->prepare("
            UPDATE responsable_association SET
                nom = :nom,
                prenom = :prenom,
                email = :email,
                CIN = :cin,
                nom_association = :nom_association,
                adresse_association = :adresse_association,
                matricule_fiscal = :matricule_fiscal
            WHERE id_responsable = :id
        ");

        $update->execute([
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':email' => $_POST['email'],
            ':cin' => $_POST['cin'],
            ':nom_association' => $_POST['nom_association'],
            ':adresse_association' => $_POST['adresse_association'],
            ':matricule_fiscal' => $_POST['matricule_fiscal'],
            ':id' => $data['id_responsable']
        ]);

        $msg = "Profil mis à jour avec succès.";
        $type = "success";

        // Refresh data
        $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    $msg = $e->getMessage();
    $type = "danger";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/edit-res.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard/responsable-dashboard.php">
            <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
        </a>
        <div>
            <span class="me-3 d-none d-md-inline"><?= htmlspecialchars($data['prenom']) . ' ' . htmlspecialchars($data['nom']) ?>    </span>
            <a href="logout.php" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Modifier votre profil</h2>
    </div>
    
    <form method="post" class="card p-4 mb-5">
        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" value="<?= htmlspecialchars($data['nom']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" name="prenom" id="prenom" class="form-control" value="<?= htmlspecialchars($data['prenom']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="cin" class="form-label">CIN</label>
            <input type="text" name="cin" id="cin" class="form-control" maxlength="8" pattern="\d{8}" value="<?= htmlspecialchars($data['CIN']) ?>" required>
        </div>

        <hr>
        <h5><i class="fas fa-building me-2"></i>Informations de l'association</h5>

        <div class="mb-3">
            <label for="nom_association" class="form-label">Nom de l'association</label>
            <input type="text" name="nom_association" id="nom_association" class="form-control" value="<?= htmlspecialchars($data['nom_association']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="adresse_association" class="form-label">Adresse</label>
            <input type="text" name="adresse_association" id="adresse_association" class="form-control" value="<?= htmlspecialchars($data['adresse_association']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="matricule_fiscal" class="form-label">Matricule Fiscal</label>
            <input type="text" name="matricule_fiscal" id="matricule_fiscal" class="form-control" pattern="\$[A-Z]{3}[0-9]{2}" value="<?= htmlspecialchars($data['matricule_fiscal']) ?>" required>
            <small class="text-muted">Format: $ABC12</small>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <a href="dashboard/responsable-dashboard.php" class="btn btn-secondary me-2">
                <i class="fas fa-times me-1"></i> Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>

<?php if ($msg): ?>
<div class="toast-container">
    <div class="toast align-items-center text-white bg-<?= $type ?> show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas <?= $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
            <button type="button" class="btn-close btn-close-white m-2" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide toast after 5 seconds
    const toastEl = document.querySelector('.toast');
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }
</script>
</body>
</html>