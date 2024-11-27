<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Administrateur</title>
    <link rel="stylesheet" href="register_admin.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>
    <form id="adminForm" action="register_admin_action.php" method="post">
        <div class="container">
            <h1>Inscription Administrateur</h1>

            <!-- Affichage des messages d'erreur ou de succès -->
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p style="color:green;">' . $_SESSION['success'] . '</p>';
                unset($_SESSION['success']);
            }
            ?>

            <label>Nom:</label>
            <input type="text" name="nom" id="nom" required><br>
            <label>Prénom:</label>
            <input type="text" name="prenom" id="prenom" required><br>
            <label>Email:</label>
            <input type="email" name="email" id="email" required><br>
            <label>Mot de passe:</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>
            <input type="checkbox" onclick="togglePassword()"> Afficher le mot de passe<br>
            <label>Rôle:</label>
            <select name="role" id="role" required>
                <option value="admin">Admin</option>
                <option value="super admin">Super Admin</option>
            </select><br>
            <input type="submit" value="Créer">
            <a href="dashboard.php" class="btn-back">Retour au Dashboard</a>
        </div>
    </form>

    <script src="inactivityTimer.js" defer></script>
   
    <div id="timer"></div>

    <script>
        // Sauvegarde les valeurs du formulaire dans le sessionStorage
        document.getElementById('adminForm').addEventListener('submit', function() {
            sessionStorage.setItem('nom', document.getElementById('nom').value);
            sessionStorage.setItem('prenom', document.getElementById('prenom').value);
            sessionStorage.setItem('email', document.getElementById('email').value);
            sessionStorage.setItem('role', document.getElementById('role').value);
        });

        // Restaure les valeurs sauvegardées après un rechargement de la page
        window.addEventListener('DOMContentLoaded', function() {
            if (sessionStorage.getItem('nom')) {
                document.getElementById('nom').value = sessionStorage.getItem('nom');
            }
            if (sessionStorage.getItem('prenom')) {
                document.getElementById('prenom').value = sessionStorage.getItem('prenom');
            }
            if (sessionStorage.getItem('email')) {
                document.getElementById('email').value = sessionStorage.getItem('email');
            }
            if (sessionStorage.getItem('role')) {
                document.getElementById('role').value = sessionStorage.getItem('role');
            }
        });

        // Réinitialise le formulaire si l'inscription est réussie
        <?php if (isset($_SESSION['success'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                // Réinitialise le formulaire
                document.getElementById('adminForm').reset();
                // Vide le sessionStorage
                sessionStorage.clear();
            });
        <?php endif; ?>

        // Afficher/Masquer le mot de passe
        function togglePassword() {
            var passwordField = document.getElementById("mot_de_passe");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
