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
    $sql = "SELECT Geographie, SIG, Economie, Anglais FROM licence3 WHERE etudiant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Si aucune note n'est trouvée, initialiser les valeurs à 0
        $row = ['Geographie' => 0, 'SIG' => 0, 'Economie' => 0, 'Anglais' => 0];
    }
} else {
    // Rediriger si aucun ID n'est passé
    header("Location: licence3.php");
    exit();
}

// Traitement du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des champs
    $Geographie = filter_var($_POST['Geographie'], FILTER_VALIDATE_FLOAT);
    $SIG = filter_var($_POST['SIG'], FILTER_VALIDATE_FLOAT);
    $Economie = filter_var($_POST['Economie'], FILTER_VALIDATE_FLOAT);
    $Anglais = filter_var($_POST['Anglais'], FILTER_VALIDATE_FLOAT);

    // Vérifier si les valeurs sont valides et comprises entre 0 et 20
    if ($Geographie !== false && $SIG !== false && $Economie !== false && $Anglais !== false &&
        $Geographie >= 0 && $Geographie <= 20 && $SIG >= 0 && $SIG <= 20 &&
        $Economie >= 0 && $Economie <= 20 && $Anglais >= 0 && $Anglais <= 20) {
        
        if ($result->num_rows > 0) {
            // Mise à jour des notes si elles existent déjà
            $stmt = $conn->prepare("UPDATE licence3 SET Geographie=?, SIG=?, Economie=?, Anglais=? WHERE etudiant_id=?");
            $stmt->bind_param("ddddi", $Geographie, $SIG, $Economie, $Anglais, $etudiant_id);
        } else {
            // Ajout des notes si elles n'existent pas encore
            $stmt = $conn->prepare("INSERT INTO licence3 (etudiant_id, Geographie, SIG, Economie, Anglais) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idddd", $etudiant_id, $Geographie, $SIG, $Economie, $Anglais);
        }

        if ($stmt->execute()) {
            // Redirection après l'ajout ou la mise à jour
            header("Location: licence3.php");
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
    <title>Ajouter/Modifier des Notes - Licence 3</title>
    <link rel="stylesheet" href="ajouter_note_licence1.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter/Modifier des Notes - Licence 3</h1>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="Geographie">Geographie :</label>
            <input type="text" name="Geographie" id="Geographie" value="<?= htmlspecialchars($row['Geographie']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="SIG">SIG :</label>
            <input type="text" name="SIG" id="SIG" value="<?= htmlspecialchars($row['SIG']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Economie">Economie :</label>
            <input type="text" name="Economie" id="Economie" value="<?= htmlspecialchars($row['Economie']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="Anglais">Anglais :</label>
            <input type="text" name="Anglais" id="Anglais" value="<?= htmlspecialchars($row['Anglais']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <button type="submit">Ajouter/Mettre à jour</button>
        </form>
        <br>
        <a href="licence3.php" class="btn-retour">Retour</a>
    </div>
</body>
</html>
