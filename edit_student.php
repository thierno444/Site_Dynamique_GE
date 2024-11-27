<?php
include 'db.php';
session_start();

// Initialisation des messages d'erreur spécifiques à chaque champ
$errors = [
    'nom' => '',
    'prenom' => '',
    'date_naissance' => '',
    'email' => '',
    'telephone' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string(trim($_POST['id']));
    $nom = ucfirst(strtolower(trim($_POST['nom'])));
    $prenom = ucwords(strtolower(trim($_POST['prenom'])));
    $date_naissance = trim($_POST['date_naissance']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $niveau = trim($_POST['niveau']);

    // Validation du nom
    if (empty($nom)) {
        $errors['nom'] = "Le nom est requis.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', $nom)) {
        $errors['nom'] = "Le nom ne doit contenir que des lettres.";
    }

    // Validation du prénom
    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est requis.";
    } elseif (!preg_match('/^([A-Za-z]+)( [A-Za-z]+){0,2}$/', $prenom)) {
        $errors['prenom'] = "Le prénom doit contenir jusqu'à trois mots séparés par des espaces.";
    }

    // Validation de la date de naissance
    if (empty($date_naissance)) {
        $errors['date_naissance'] = "La date de naissance est requise.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_naissance) || (int)substr($date_naissance, 0, 4) > 2007) {
        $errors['date_naissance'] = "La date de naissance doit être avant 2007 et au format YYYY-MM-DD.";
    }

    // Validation de l'email
    if (empty($email)) {
        $errors['email'] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
        $errors['email'] = "Veuillez fournir un email valide au format nom@gmail.com.";
    }

    // Validation du téléphone
    if (empty($telephone)) {
        $errors['telephone'] = "Le numéro de téléphone est requis.";
    } elseif (!preg_match('/^[7][5-8][0-9]{7}$/', $telephone)) {
        $errors['telephone'] = "Le numéro de téléphone doit commencer par 75, 76, 77 ou 78 et contenir uniquement des chiffres.";
    }

    // Si aucune erreur, mise à jour dans la base de données
    if (!array_filter($errors)) {
        $sql = "UPDATE etudiants 
                SET nom='$nom', prenom='$prenom', date_naissance='$date_naissance', email='$email', telephone='$telephone', niveau='$niveau' 
                WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "L'étudiant a été modifié avec succès.";
            // Réinitialiser les valeurs après la mise à jour
            $nom = $prenom = $date_naissance = $email = $telephone = $niveau = '';
        } else {
            $errorMessage = "Erreur lors de la modification: " . $conn->error;
        }
    }
}

// Récupération des informations de l'étudiant
$id = $_GET['id'];
$sql = "SELECT * FROM etudiants WHERE id='$id'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Étudiant</title>
    <link rel="stylesheet" href="edit_student.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Modifier Étudiant</h1>

        <form action="edit_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">

            <label>Nom:</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($nom ?? $student['nom']); ?>" required>
            <div class="error"><?php echo $errors['nom']; ?></div>

            <label>Prénom:</label>
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($prenom ?? $student['prenom']); ?>" required>
            <div class="error"><?php echo $errors['prenom']; ?></div>

            <label>Date de naissance:</label>
            <input type="date" name="date_naissance" value="<?php echo htmlspecialchars($date_naissance ?? $student['date_naissance']); ?>" required>
            <div class="error"><?php echo $errors['date_naissance']; ?></div>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? $student['email']); ?>" required>
            <div class="error"><?php echo $errors['email']; ?></div>

            <label>Téléphone:</label>
            <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone ?? $student['telephone']); ?>" required>
            <div class="error"><?php echo $errors['telephone']; ?></div>

            <label>Niveau:</label>
            <select name="niveau">
                <option value="L1" <?php echo ($niveau ?? $student['niveau']) == 'L1' ? 'selected' : ''; ?>>L1</option>
                <option value="L2" <?php echo ($niveau ?? $student['niveau']) == 'L2' ? 'selected' : ''; ?>>L2</option>
                <option value="L3" <?php echo ($niveau ?? $student['niveau']) == 'L3' ? 'selected' : ''; ?>>L3</option>
                <option value="M1" <?php echo ($niveau ?? $student['niveau']) == 'M1' ? 'selected' : ''; ?>>M1</option>
                <option value="M2" <?php echo ($niveau ?? $student['niveau']) == 'M2' ? 'selected' : ''; ?>>M2</option>
            </select><br>

            <input type="submit" value="Modifier">
        </form>
        <a href="dashboard.php" class="btn-back">Retour au Dashboard</a>
    </div>
    <div id="timer"></div>
</body>
</html>
