<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
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

    // Handle donation deletion
    if (isset($_GET['donation_id'])) {
        $donationId = $_GET['donation_id'];

        // Fetch donation details
        $stmt = $dbco->prepare("SELECT id_projet, montant_participation FROM donateur_projet WHERE id = :id");
        $stmt->execute([':id' => $donationId]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donation) {
            // Delete donation record
            $stmt = $dbco->prepare("DELETE FROM donateur_projet WHERE id = :id");
            $stmt->execute([':id' => $donationId]);

            // Update project total collected amount
            $stmt = $dbco->prepare("UPDATE projet SET montant_total_collecte = montant_total_collecte - :amount WHERE id_projet = :id_projet");
            $stmt->execute([
                ':amount' => $donation['mont
