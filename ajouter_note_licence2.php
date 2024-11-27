<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'MonProjet_Etudiants');

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si l'ID de l'étudiant est passé en paramètre
if (isset($_GET['id'])) {
    $etudiant_id = $_GET['id'];

    // Vérifier si l'étudiant a déjà des notes enregistrées
    $sql = "SELECT Processeur, Robotique, IA, `IOT` FROM licence2 WHERE etudiant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si les notes existent, récupérer les données
        $row = $result->fetch_assoc();
    } else {
        // Si les notes n'existent pas, les initialiser à 0
        $row = [
            'Processeur' => 0.00,
            'Robotique' => 0.00,
            'IA' => 0.00,
            'IOT' => 0.00
        ];

        // Insérer les notes initiales dans la base de données
        $stmt = $conn->prepare("INSERT INTO licence2 (etudiant_id, Processeur, Robotique, IA, `IOT`) VALUES (?, 0.00, 0.00, 0.00, 0.00)");
        $stmt->bind_param("i", $etudiant_id);
        $stmt->execute();
    }
} else {
    // Rediriger si aucun ID n'est passé
    header("Location: licence2.php");
    exit();
}

// Traitement du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des champs
    $Processeur = filter_var($_POST['Processeur'], FILTER_VALIDATE_FLOAT);
    $Robotique = filter_var($_POST['Robotique'], FILTER_VALIDATE_FLOAT);
    $IA = filter_var($_POST['IA'], FILTER_VALIDATE_FLOAT);
    $IOT = filter_var($_POST['IOT'], FILTER_VALIDATE_FLOAT);

    // Vérifier si les valeurs sont valides et comprises entre 0 et 20
    if ($Processeur !== false && $Robotique !== false && $IA !== false && $IOT !== false &&
        $Processeur >= 0 && $Processeur <= 20 && $Robotique >= 0 && $Robotique <= 20 &&
        $IA >= 0 && $IA <= 20 && $IOT >= 0 && $IOT <= 20) {
        
        // Mettre à jour les notes dans la base de données
        $stmt = $conn->prepare("UPDATE licence2 SET Processeur=?, Robotique=?, IA=?, `IOT`=? WHERE etudiant_id=?");
        $stmt->bind_param("ddddi", $Processeur, $Robotique, $IA, $IOT, $etudiant_id);

        if ($stmt->execute()) {
            // Redirection après la mise à jour
            header("Location: licence2.php");
            exit();
        } else {
            $error_message = "Erreur lors de la mise à jour des notes : " . $stmt->error;
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
    <title>Ajouter/Modifier des Notes - Licence 2</title>
    <link rel="stylesheet" href="ajouter_note_licence1.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter/Modifier des Notes - Licence 2</h1>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="Processeur">Processeur :</label>
            <input type="text" name="Processeur" id="Processeur" value="<?= htmlspecialchars($row['Processeur']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Robotique">Robotique :</label>
            <input type="text" name="Robotique" id="Robotique" value="<?= htmlspecialchars($row['Robotique']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="IA">IA :</label>
            <input type="text" name="IA" id="IA" value="<?= htmlspecialchars($row['IA']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="IOT">IOT :</label>
            <input type="text" name="IOT" id="IOT" value="<?= htmlspecialchars($row['IOT']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <button type="submit">Ajouter / Mettre à jour</button>
        </form><br>
        <a href="licence2.php" class="btn-retour">Retour</a>
    </div>
</body>
</html>
