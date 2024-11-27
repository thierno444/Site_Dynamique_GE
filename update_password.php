<?php
include 'db.php';

// Email de l'utilisateur dont tu veux changer le mot de passe
$email = 'manonpauw@gmail.com';

// Nouveau mot de passe à définir
$newPassword = 'manonpauw';

// Générer le hash du nouveau mot de passe
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Préparer la requête SQL pour mettre à jour le mot de passe
$sql = "UPDATE admins SET mot_de_passe=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $email);

// Exécuter la requête
if ($stmt->execute()) {
    echo "Mot de passe mis à jour avec succès pour l'utilisateur $email !";
} else {
    echo "Erreur lors de la mise à jour : " . $stmt->error;
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>
