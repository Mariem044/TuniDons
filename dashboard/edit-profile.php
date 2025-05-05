<?php
session_start();

// Check if user is logged in and is a responsable
if (!isset($_SESSION['pseudo']) || $_SESSION['user_type'] !== 'responsable') {
    header('Location: ../login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation";

try {
    $dbco = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get user data
    $stmt = $dbco->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$responsable) {
        throw new Exception("Responsable introuvable.");
    }

    $success = false;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $CIN = $_POST['CIN'];
        $email = $_POST['email'];
        $nom_association = $_POST['nom_association'];
        $adresse_association = $_POST['adresse_association'];
        $matricule_fiscal = $_POST['matricule_fiscal'];

        $update = $dbco->prepare("UPDATE responsable_association SET
            nom = :nom,
            prenom = :prenom,
            CIN = :CIN,
            email = :email,
            nom_association = :nom_association,
            adresse_association = :adresse_association,
            matricule_fiscal = :matricule_fiscal
            WHERE pseudo = :pseudo");

        $update->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':CIN' => $CIN,
            ':email' => $email,
            ':nom_association' => $nom_association,
            ':adresse_association' => $adresse_association,
            ':matricule_fiscal' => $matricule_fiscal,
            ':pseudo' => $_SESSION['pseudo']
        ]);

        // Refresh data after update
        $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
        $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

        $success = true;
    }

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
    <title>Modifier le profil - TuniDons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="edit-p.css">
    
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h2><i class="fas fa-user-edit me-2"></i>Modifier le profil</h2>
            </div>
            
            <div class="profile-body">
                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>Profil mis à jour avec succès
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="nom" class="form-label">Nom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="nom" id="nom" class="form-control" value="<?= htmlspecialchars($responsable['nom']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="prenom" class="form-label">Prénom</label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="prenom" id="prenom" class="form-control" value="<?= htmlspecialchars($responsable['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="CIN" class="form-label">CIN</label>
                            <div class="input-icon">
                                <i class="fas fa-id-card"></i>
                                <input type="text" name="CIN" id="CIN" class="form-control" value="<?= htmlspecialchars($responsable['CIN']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($responsable['email']) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="nom_association" class="form-label">Nom de l'association</label>
                        <div class="input-icon">
                            <i class="fas fa-building"></i>
                            <input type="text" name="nom_association" id="nom_association" class="form-control" value="<?= htmlspecialchars($responsable['nom_association']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="adresse_association" class="form-label">Adresse de l'association</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="adresse_association" id="adresse_association" class="form-control" value="<?= htmlspecialchars($responsable['adresse_association']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="matricule_fiscal" class="form-label">Matricule fiscal</label>
                        <div class="input-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <input type="text" name="matricule_fiscal" id="matricule_fiscal" class="form-control" value="<?= htmlspecialchars($responsable['matricule_fiscal']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between pt-3">
                        <a href="responsable-dashboard.php" class="btn btn-secondary" onclick="return confirm('Voulez-vous vraiment annuler les modifications ?')">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>