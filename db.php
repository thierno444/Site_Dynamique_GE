<?php
// 2.1. Connexion à la Base de Données
// Créez un fichier db.php pour gérer la connexion à la base de données.
$servername = "localhost";
$username = "root";
$password = "";
$database = "MonProjet_Etudiants";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
