<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'MonProjet_Etudiants');

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si l'ID de l'étudiant est passé en paramètre
if (isset($_GET['id'])) {
    $etudiant_id = $_GET['id'];

    // Récupérer les notes actuelles, si elles existent
    $sql = "SELECT Comptabilite, Analyse, Statistique, Physique FROM master1 WHERE etudiant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Si aucune note n'est trouvée, initialiser les valeurs à 0
        $row = ['Comptabilite' => 0, 'Analyse' => 0, 'Statistique' => 0, 'Physique' => 0];
    }
} else {
    // Rediriger si aucun ID n'est passé
    header("Location: master1.php");
    exit();
}

// Traitement du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des champs
    $Comptabilite = filter_var($_POST['Comptabilite'], FILTER_VALIDATE_FLOAT);
    $Analyse = filter_var($_POST['Analyse'], FILTER_VALIDATE_FLOAT);
    $Statistique = filter_var($_POST['Statistique'], FILTER_VALIDATE_FLOAT);
    $Physique = filter_var($_POST['Physique'], FILTER_VALIDATE_FLOAT);

    // Vérifier si les valeurs sont valides et comprises entre 0 et 20
    if ($Comptabilite !== false && $Analyse !== false && $Statistique !== false && $Physique !== false &&
        $Comptabilite >= 0 && $Comptabilite <= 20 && $Analyse >= 0 && $Analyse <= 20 &&
        $Statistique >= 0 && $Statistique <= 20 && $Physique >= 0 && $Physique <= 20) {
        
        if ($result->num_rows > 0) {
            // Mise à jour des notes si elles existent déjà
            $stmt = $conn->prepare("UPDATE master1 SET Comptabilite=?, Analyse=?, Statistique=?, Physique=? WHERE etudiant_id=?");
            $stmt->bind_param("ddddi", $Comptabilite, $Analyse, $Statistique, $Physique, $etudiant_id);
        } else {
            // Ajout des notes si elles n'existent pas encore
            $stmt = $conn->prepare("INSERT INTO master1 (etudiant_id, Comptabilite, Analyse, Statistique, Physique) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idddd", $etudiant_id, $Comptabilite, $Analyse, $Statistique, $Physique);
        }

        if ($stmt->execute()) {
            // Redirection après l'ajout ou la mise à jour
            header("Location: master1.php");
            exit();
        } else {
            $error_message = "Erreur lors de l'enregistrement des notes : " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Veuillez entrer des notes valides entre 0 et 20.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter/Modifier des Notes - Master 1</title>
    <link rel="stylesheet" href="ajouter_note_licence1.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter/Modifier des Notes - Master 1</h1>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="Comptabilite">Comptabilité :</label>
            <input type="text" name="Comptabilite" id="Comptabilite" value="<?= htmlspecialchars($row['Comptabilite']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Analyse">Analyse :</label>
            <input type="text" name="Analyse" id="Analyse" value="<?= htmlspecialchars($row['Analyse']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Statistique">Statistique :</label>
            <input type="text" name="Statistique" id="Statistique" value="<?= htmlspecialchars($row['Statistique']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Physique">Physique :</label>
            <input type="text" name="Physique" id="Physique" value="<?= htmlspecialchars($row['Physique']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <button type="submit">Ajouter/Mettre à jour</button>
        </form>
        <br>
        <a href="master1.php" class="btn-retour">Retour</a>
    </div>
</body>
</html>
