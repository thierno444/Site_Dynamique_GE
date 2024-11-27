<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT * FROM administrateurs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Administrateurs</title>
    <link rel="stylesheet" href="admin_list.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Administrateurs</h1>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="edit_admin.php?id=<?php echo $row['id']; ?>" class="btn-action">Modifier</a>
                        <a href="delete_admin.php?id=<?php echo $row['id']; ?>" class="btn-action">Supprimer</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="dashboard_super_admin.php" class="btn-back">Retour au Dashboard</a>
    </div>
    <footer>
        <p>&copy; 2024 Gestion des Étudiants. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
