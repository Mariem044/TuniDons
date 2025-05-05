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

        // Delete the project
        $stmt = $dbco->prepare("DELETE FROM projet WHERE id_projet = :id_projet");
        $stmt->execute([':id_projet' => $id_projet]);

        // Redirect after deletion
        header('Location: list-projects.php');
        exit();
    } catch (PDOException $e) {
        die("Erreur DB : " . $e->getMessage());
    }
} else {
    echo "Aucun projet trouvé à supprimer.";
}
?>
