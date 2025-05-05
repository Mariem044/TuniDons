<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['pseudo'])) {
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

    // Fetch donor information
    $stmt = $dbco->prepare("SELECT * FROM donateur WHERE pseudo = :pseudo");
    $stmt->execute([':pseudo' => $_SESSION['pseudo']]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get updated data from the form
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $CIN = $_POST['CIN'];

        // Check if the CIN already exists in the database
        $checkCINStmt = $dbco->prepare("SELECT COUNT(*) FROM donateur WHERE CIN = :CIN AND pseudo != :pseudo");
        $checkCINStmt->execute([':CIN' => $CIN, ':pseudo' => $_SESSION['pseudo']]);
        $cinExists = $checkCINStmt->fetchColumn();

        if ($cinExists > 0) {
            // CIN already exists, show an error message
            $error_message = "Le CIN existe déjà dans la base de données. Veuillez entrer un CIN unique.";
        } else {
            // Update donor information in the database
            $updateStmt = $dbco->prepare("UPDATE donateur SET prenom = :prenom, nom = :nom, email = :email, CIN = :CIN WHERE pseudo = :pseudo");
            $updateStmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':CIN' => $CIN,
                ':pseudo' => $_SESSION['pseudo']
            ]);

            // Redirect to dashboard after updating
            header('Location: donor-dashboard.php');
            exit();
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil - TuniDons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="edit-d.css">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hand-holding-heart me-2"></i>TuniDons
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['pseudo']); ?></span>
                <a href="../logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i>Se Déconnecter
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="profile-container">
            <div class="profile-header">
                <h1><i class="fas fa-user-edit me-2"></i>Modifier votre profil</h1>
                <p class="text-muted">Mettez à jour vos informations personnelles</p>
            </div>
            
            <?php if ($donor): ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label for="prenom" class="form-label">Prénom</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($donor['prenom']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="nom" class="form-label">Nom</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($donor['nom']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($donor['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="CIN" class="form-label">CIN</label>
                        <div class="input-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" class="form-control" id="CIN" name="CIN" value="<?php echo htmlspecialchars($donor['CIN']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-5">
                        <a href="donor-dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Sauvegarder
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Impossible de récupérer vos informations. Veuillez réessayer plus tard.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>