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
    $sql = "SELECT Algébre, Analyse, Algo, HTML_CSS FROM licence1 WHERE etudiant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // Si aucune note n'est trouvée, initialiser les valeurs à 0
        $row = ['Algébre' => 0, 'Analyse' => 0, 'Algo' => 0, 'HTML_CSS' => 0];
    }
} else {
    // Rediriger si aucun ID n'est passé
    header("Location: licence1.php");
    exit();
}

// Traitement du formulaire après soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation des champs
    $algebre = filter_var($_POST['algebre'], FILTER_VALIDATE_FLOAT);
    $analyse = filter_var($_POST['analyse'], FILTER_VALIDATE_FLOAT);
    $algo = filter_var($_POST['algo'], FILTER_VALIDATE_FLOAT);
    $html_css = filter_var($_POST['html_css'], FILTER_VALIDATE_FLOAT);

    // Vérifier si les valeurs sont valides et comprises entre 0 et 20
    if ($algebre !== false && $analyse !== false && $algo !== false && $html_css !== false &&
        $algebre >= 0 && $algebre <= 20 && $analyse >= 0 && $analyse <= 20 &&
        $algo >= 0 && $algo <= 20 && $html_css >= 0 && $html_css <= 20) {
        
        if ($result->num_rows > 0) {
            // Mise à jour des notes si elles existent déjà
            $stmt = $conn->prepare("UPDATE licence1 SET Algébre=?, Analyse=?, Algo=?, HTML_CSS=? WHERE etudiant_id=?");
            $stmt->bind_param("ddddi", $algebre, $analyse, $algo, $html_css, $etudiant_id);
        } else {
            // Ajout des notes si elles n'existent pas encore
            $stmt = $conn->prepare("INSERT INTO licence1 (etudiant_id, Algébre, Analyse, Algo, HTML_CSS) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idddd", $etudiant_id, $algebre, $analyse, $algo, $html_css);
        }

        if ($stmt->execute()) {
            // Redirection après l'ajout ou la mise à jour
            header("Location: licence1.php");
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
    <title>Ajouter/Modifier des Notes - Licence 1</title>
    <link rel="stylesheet" href="ajouter_note_licence1.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter/Modifier des Notes - Licence 1</h1>
        <?php if (isset($error_message)) : ?>
            <div class="error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="algebre">Algébre :</label>
            <input type="text" name="algebre" id="algebre" value="<?= htmlspecialchars($row['Algébre']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="analyse">Analyse :</label>
            <input type="text" name="analyse" id="analyse" value="<?= htmlspecialchars($row['Analyse']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="algo">Algo :</label>
            <input type="text" name="algo" id="algo" value="<?= htmlspecialchars($row['Algo']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <label for="html_css">HTML/CSS :</label>
            <input type="text" name="html_css" id="html_css" value="<?= htmlspecialchars($row['HTML_CSS']); ?>" required pattern="\d+(\.\d{1,2})?" title="Veuillez entrer un nombre décimal valide entre 0 et 20." min="0" max="20">
            
            <button type="submit">Ajouter/Mettre à jour</button>
        </form>
        <br>
        <a href="licence1.php" class="btn-retour">Retour</a>
    </div>
</body>
</html>
