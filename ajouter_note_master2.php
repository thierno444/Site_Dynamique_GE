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
    $sql = "SELECT Python, Angular, Lavarel, React FROM master2 WHERE etudiant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Si aucune note n'est trouvée, initialiser les valeurs à 0
        $row = ['Python' => 0, 'Angular' => 0, 'Lavarel' => 0, 'React' => 0];
    }
} else {
    // Rediriger si aucun ID n'est passé
    header("Location: master2.php");
    exit();
}

// Traitement du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des champs
    $Python = filter_var($_POST['Python'], FILTER_VALIDATE_FLOAT);
    $Angular = filter_var($_POST['Angular'], FILTER_VALIDATE_FLOAT);
    $Lavarel = filter_var($_POST['Lavarel'], FILTER_VALIDATE_FLOAT);
    $React = filter_var($_POST['React'], FILTER_VALIDATE_FLOAT);

    // Vérifier si les valeurs sont valides et comprises entre 0 et 20
    if ($Python !== false && $Angular !== false && $Lavarel !== false && $React !== false &&
        $Python >= 0 && $Python <= 20 && $Angular >= 0 && $Angular <= 20 &&
        $Lavarel >= 0 && $Lavarel <= 20 && $React >= 0 && $React <= 20) {
        
        if ($result->num_rows > 0) {
            // Mise à jour des notes si elles existent déjà
            $stmt = $conn->prepare("UPDATE master2 SET Python=?, Angular=?, Lavarel=?, React=? WHERE etudiant_id=?");
            $stmt->bind_param("ddddi", $Python, $Angular, $Lavarel, $React, $etudiant_id);
        } else {
            // Ajout des notes si elles n'existent pas encore
            $stmt = $conn->prepare("INSERT INTO master2 (etudiant_id, Python, Angular, Lavarel, React) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idddd", $etudiant_id, $Python, $Angular, $Lavarel, $React);
        }

        if ($stmt->execute()) {
            // Redirection après l'ajout ou la mise à jour
            header("Location: master2.php");
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
    <title>Ajouter/Modifier des Notes - Master 2</title>
    <link rel="stylesheet" href="ajouter_note_licence1.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter/Modifier des Notes - Master 2</h1>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="Python">Python :</label>
            <input type="text" name="Python" id="Python" value="<?= htmlspecialchars($row['Python']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Angular">Angular :</label>
            <input type="text" name="Angular" id="Angular" value="<?= htmlspecialchars($row['Angular']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Lavarel">Lavarel :</label>
            <input type="text" name="Lavarel" id="Lavarel" value="<?= htmlspecialchars($row['Lavarel']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="React">React :</label>
            <input type="text" name="React" id="React" value="<?= htmlspecialchars($row['React']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <button type="submit">Ajouter/Mettre à jour</button>
        </form>
        <br>
        <a href="master2.php" class="btn-retour">Retour</a>
    </div>
</body>
</html>
