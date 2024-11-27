<?php
// Inclure le fichier de connexion
include 'db.php';

// Récupération des étudiants et leurs notes pour les trier par moyenne
$sql = "SELECT e.matricule, e.nom, e.prenom, n.moyenne
        FROM etudiants e
        LEFT JOIN licence3 n ON e.id = n.etudiant_id
        WHERE e.niveau = 'L3'
        ORDER BY n.moyenne DESC"; // Trier par moyenne décroissante

if ($result = $conn->query($sql)) {
    // Si la requête réussit, on traite les résultats
    if ($result->num_rows > 0) {
        $rows = [];
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    } else {
        $rows = [];
    }
} else {
    // Gestion des erreurs SQL
    echo "Erreur de la requête : " . $conn->error;
    $rows = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Étudiants par Ordre de Mérite - Licence 3</title>
    <link rel="stylesheet" href="lister_etudiants_merite.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Étudiants par Ordre de Mérite - Licence 3</h1>
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Moyenne</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($rows) && !empty($rows)) {
                    foreach($rows as $row) {
                        echo "<tr>
                                 <td>" . htmlspecialchars($row["matricule"]) . "</td>
                                <td>" . htmlspecialchars($row["nom"]) . "</td>
                                <td>" . htmlspecialchars($row["prenom"]) . "</td>
                                <td>" . htmlspecialchars($row["moyenne"]) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun étudiant trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="buttons">
            <a href="licence3.php" class="button">Retour à la liste des étudiants Licence 3</a>
             
        </div>
    </div>
</body>
</html>
