<?php
// Lister, modifier, et supprimer des administrateurs
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM admins";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Administrateurs</title>
    <link rel="stylesheet" href="admin_management.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>
    <div class="container">
        <h2>Gestion des Administrateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['nom']) ?></td>
                    <td><?= htmlspecialchars($row['prenom']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <a href="edit_admin.php?id=<?= $row['id'] ?>" class="btn btn-edit">Modifier</a>
                        <a href="delete_admin.php?id=<?= $row['id'] ?>" class="btn btn-delete">Supprimer</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        
        <a href="dashboard.php" class="btn btn-back">Retour au Dashboard</a>
    </div>
    <div id="timer"></div>
</body>
</html>

<?php
$conn->close();
?>
