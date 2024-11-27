<?php
include 'db.php';

$id = $_GET['id'];

$sql = "DELETE FROM admins WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header('Location: admin_management.php');
} else {
    echo "Erreur: " . $conn->error;
}

$conn->close();
?>
