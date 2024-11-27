<?php
// Informations de connexion à la base de données
$servername = "localhost";
$username = "niassy";
$password = "1903";  // Laisser vide si aucun mot de passe n'est défini
$dbname = "Etudiant";

// Connexion à la base de données MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// Initialiser les variables de message
$message = "";
$messageType = "";

// Récupérer les données du formulaire (via méthode POST ou autre)
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$date_naissance = $_POST['date_naissance'];
$email = $_POST['email'];
$telephone = $_POST['telephone'];
$niveau = $_POST['niveau'];
$id = $_POST['id'];

// Validation du numéro de téléphone
if ($telephone >= 750000000 && $telephone <= 789999999) {
    // Préparer la requête SQL avec des paramètres
    $stmt = $conn->prepare("UPDATE etudiants SET nom=?, prenom=?, date_naissance=?, email=?, telephone=?, niveau=? WHERE id=?");

    // Assigner les valeurs aux paramètres
    $stmt->bind_param("ssssisi", $nom, $prenom, $date_naissance, $email, $telephone, $niveau, $id);

    // Exécuter la requête
    if ($stmt->execute()) {
        $message = "Mise à jour réussie.";
        $messageType = "success";
    } else {
        $message = "Erreur lors de la mise à jour : " . $stmt->error;
        $messageType = "error";
    }

    // Fermer la requête préparée
    $stmt->close();
} else {
    $message = "Le numéro de téléphone doit être compris entre 750000000 et 789999999.";
    $messageType = "error";
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message de mise à jour</title>
    <link rel="stylesheet" href="edit_student_action.css"> <!-- Inclure votre fichier CSS -->
</head>
<body>

<!-- Modale pour afficher le message -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p class="<?php echo $messageType; ?>"><?php echo $message; ?></p>
        <button id="modalButton">OK</button>
    </div>
</div>

<script>
// Afficher la modale
document.getElementById("messageModal").style.display = "block";

// Fermer la modale lorsque l'utilisateur clique sur <span> (x)
document.querySelector(".close").onclick = function() {
    document.getElementById("messageModal").style.display = "none";
}

// Gérer le clic sur le bouton OK
document.getElementById("modalButton").onclick = function() {
    if ("<?php echo $messageType; ?>" === "success") {
        window.location.href = "dashboard_super_admin.php";
    } else {
        window.location.href = "edit_student.php?id=<?php echo $id; ?>";
    }
}

// Fermer la modale lorsque l'utilisateur clique en dehors de la modale
window.onclick = function(event) {
    if (event.target == document.getElementById("messageModal")) {
        document.getElementById("messageModal").style.display = "none";
    }
}
</script>

</body>
</html>

