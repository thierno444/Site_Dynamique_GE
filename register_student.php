<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Étudiant</title>
    <link rel="stylesheet" href="register_student.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>
    <!-- Formulaire d'inscription étudiant -->
    <form action="register_student_action.php" method="post">
        <h1>Inscription Étudiant</h1>

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
        <input type="text" name="nom" required><br>
        <label>Prénom:</label>
        <input type="text" name="prenom" required><br>
        <label>Date de naissance:</label>
        <input type="date" name="date_naissance" required max="2007-12-31"><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Téléphone:</label>
        <input type="text" name="telephone"><br>
        <label>Niveau:</label>
        <select name="niveau">
            <option value="L1">L1</option>
            <option value="L2">L2</option>
            <option value="L3">L3</option>
            <option value="M1">M1</option>
            <option value="M2">M2</option>
        </select><br>
        <input type="submit" value="Inscrire">
        <button type="button" onclick="window.location.href='dashboard.php';" class="btn-retour">Retour au Dashboard</button>
        <div id="timer"></div>
    </form>

    <script src="register_student.js"></script>
</body>
</html>
