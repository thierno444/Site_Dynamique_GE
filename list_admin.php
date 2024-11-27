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
                    
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="dashboard_admin.php" class="btn-back">Retour au Dashboard</a>
    </div>
   
</body>
</html>

<?php
$conn->close();
?>
