<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Vérification de l'existence de l'ID dans la requête GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Sécurisation de la requête pour éviter les injections SQL
    $id = $conn->real_escape_string($id);

    // Requête de suppression
    $sql = "DELETE FROM etudiants WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: dashboard.php'); // Redirection après suppression
        exit();
    } else {
        echo "Erreur: " . $conn->error;
    }
} else {
    echo "ID d'étudiant manquant.";
}

$conn->close();
?>
