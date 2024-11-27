<?php
// Archiver/Désarchiver un étudiant
include 'db.php';
session_start(); // Démarrer la session

$id = $_GET['id'];
$archive = $_GET['archive'];
$archived = isset($_GET['archived']) ? $_GET['archived'] : 0;

// Récupérer le matricule de l'étudiant
$sql = "SELECT matricule FROM etudiants WHERE id='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$matricule = $row['matricule'];

$sql = "UPDATE etudiants SET archive='$archive' WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    $_SESSION['message'] = "Action réussie : L'étudiant avec le matricule $matricule a été " . ($archive ? "archivé" : "désarchivé") . " avec succès.";
} else {
    $_SESSION['error'] = "Erreur: " . $sql . "<br>" . $conn->error;
}

// Rediriger en conservant le paramètre 'archived'
header("Location: dashboard.php?archived=$archived");
$conn->close();
?>
