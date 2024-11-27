<?php
session_start();
include 'db.php';

$error = "";

// Traitement du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM admins WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
            $_SESSION['admin_id'] = $row['id'];
            if ($row['role'] === 'super admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: dashboard_admin.php');
            }
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Email non trouvé.";
    }

    // Stocker l'erreur dans la session
    $_SESSION['error'] = $error;
    header("Location: login.php");
    exit();
}

// Affichage de l'erreur si elle est définie
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Efface l'erreur après l'avoir affichée
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Connexion</title>
</head>
<body>
    <div class="overlay">
        <h1>Connexion Administrateur</h1>
        <form action="login.php" method="post">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            
            <label for="mot_de_passe">Mot de passe :</label>
            <div class="input-container">
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span><br><br>
            </div>
            
            <input type="submit" value="Se connecter">
        </form>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("mot_de_passe");
            var toggleIcon = document.querySelector(".toggle-password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "🙈"; // Icône indiquant que le mot de passe est visible
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️"; // Icône indiquant que le mot de passe est caché
            }
        }
    </script>
</body>
</html>
