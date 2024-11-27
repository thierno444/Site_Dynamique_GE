<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fonction pour vérifier si un champ ne contient que des lettres sans espaces ni caractères spéciaux
function validateName($name) {
    return preg_match('/^[A-Za-z]+$/', $name);
}

// Fonction pour mettre en majuscule la première lettre du nom
function capitalizeFirstLetter($string) {
    return ucfirst(strtolower($string));
}

// Fonction pour mettre en majuscule la première lettre de chaque mot du prénom
function capitalizeEachWord($string) {
    return ucwords(strtolower($string));
}

// Fonction pour vérifier si l'email est au format nom.prenom@gmail.com et qu'il n'existe pas déjà
function verifierEmail($conn, $email, $id = null) {
    // Vérifier si l'email est au format nom.prenom@gmail.com
    if (!preg_match('/^[a-zA-Z]+(\.[a-zA-Z]+)+@gmail\.com$/', $email)) {
        return "L'email doit être au format nom.prenom@gmail.com et provenir de Gmail.";
    }

    // Vérifier si l'email existe déjà dans la base de données
    $sql = "SELECT id FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Si l'ID trouvé est différent de celui fourni, l'email est déjà utilisé
        if ($row['id'] != $id) {
            return "Cet email est déjà utilisé par un autre administrateur.";
        }
    }

    return null;
}

// Vérification de l'existence de l'ID dans la requête GET
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    $sql = "SELECT * FROM admins WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $role = isset($_POST['role']) ? trim($_POST['role']) : '';
            $password = isset($_POST['mot_de_passe']) ? trim($_POST['mot_de_passe']) : '';

            // Validation de l'email
            $error = verifierEmail($conn, $email, $id);
            if ($error) {
                echo "<div class='error-message'>$error</div>";
            } else {
                // Continue with other validations and processing
                if (!validateName($nom)) {
                    $error = "Le nom doit contenir uniquement des lettres, sans espaces ni caractères spéciaux.";
                } elseif (!preg_match("/^([A-Z][a-zA-Z]*)(\s[A-Z][a-zA-Z]*){0,2}$/", $prenom)) {
                    $error = "Le prénom peut contenir jusqu'à 3 mots, chaque mot commençant par une majuscule.";
                } elseif (!in_array($role, ['admin', 'super admin'])) {
                    $error = "Le rôle sélectionné est invalide.";
                } elseif (!empty($password) && (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password))) {
                    $error = "Le mot de passe doit comporter au moins 8 caractères, avec au moins une majuscule, une minuscule et un chiffre.";
                } else {
                    // Sécurisation des données pour éviter les injections SQL
                    $nom = $conn->real_escape_string(capitalizeFirstLetter($nom));
                    $prenom = $conn->real_escape_string(capitalizeEachWord($prenom));
                    $email = $conn->real_escape_string($email);
                    $role = $conn->real_escape_string($role);

                    // Mise à jour du mot de passe uniquement s'il a été modifié
                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                        $sql = "UPDATE admins SET nom='$nom', prenom='$prenom', email='$email', role='$role', mot_de_passe='$hashed_password' WHERE id='$id'";
                    } else {
                        $sql = "UPDATE admins SET nom='$nom', prenom='$prenom', email='$email', role='$role' WHERE id='$id'";
                    }

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['success'] = "L'administrateur a été mis à jour avec succès.";
                        header('Location: admin_management.php');
                        exit();
                    } else {
                        $error = "Erreur: " . $conn->error;
                    }
                }
            }
        }
    } else {
        $error = "Aucun administrateur trouvé avec cet identifiant.";
    }
} else {
    $error = "Identifiant d'administrateur manquant.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Administrateur</title>
    <link rel="stylesheet" href="admin_edit.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Modifier Administrateur</h1>

        <!-- Affichage des messages d'erreur -->
        <?php if (isset($error)) { ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <?php if (isset($row)) { ?>
        <form method="post" class="admin-form">
            <label>Nom:</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($row['nom']); ?>" required><br>

            <label>Prénom:</label>
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($row['prenom']); ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br>

            <label>Rôle:</label>
            <select name="role" required>
                <option value="admin" <?php echo ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="super admin" <?php echo ($row['role'] == 'super admin') ? 'selected' : ''; ?>>Super Admin</option>
            </select><br>

            <label>Mot de passe:</label>
            <input type="password" name="mot_de_passe"><br>

            <input type="checkbox" onclick="togglePassword()"> Voir le mot de passe<br>

            <input type="submit" value="Modifier"><br><br><br>
            <a href="dashboard.php" class="btn-back">Retour au Dashboard</a>
        </form>
        <?php } ?>
    </div>
    <footer>
        <p>&copy; 2024 Gestion des Étudiants. Tous droits réservés.</p>
    </footer>
    <div id="timer"></div>

    <script>
        // Fonction pour afficher/masquer le mot de passe
        function togglePassword() {
            var passwordField = document.querySelector('input[name="mot_de_passe"]');
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
