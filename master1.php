<?php
// Inclure le fichier de connexion
include 'db.php';

// Mettre à jour les statuts des étudiants en fonction des notes
$update_sql = "UPDATE master1 
               SET statut = CASE 
                   WHEN Comptabilite IS NULL OR Analyse IS NULL OR Statistique IS NULL OR Physique IS NULL THEN 'en cours'
                   WHEN moyenne >= 10 THEN 'admis'
                   ELSE 'recalé'
               END";

// Exécuter la requête de mise à jour des statuts
if ($conn->query($update_sql) === FALSE) {
    echo "Erreur de mise à jour des statuts : " . $conn->error;
}

// Récupérer le nombre d'étudiants par statut
$sql_admis = "SELECT COUNT(*) as total_admis FROM master1 WHERE statut = 'admis'";
$sql_recales = "SELECT COUNT(*) as total_recales FROM master1 WHERE statut = 'recalé'";
$sql_en_cours = "SELECT COUNT(*) as total_en_cours FROM master1 WHERE statut = 'en cours'";

$total_admis = $conn->query($sql_admis)->fetch_assoc()['total_admis'];
$total_recales = $conn->query($sql_recales)->fetch_assoc()['total_recales'];
$total_en_cours = $conn->query($sql_en_cours)->fetch_assoc()['total_en_cours'];


// Récupérer les étudiants et leurs notes pour le niveau M1
$sql = "SELECT e.id, e.nom, e.prenom, 
                COALESCE(n.Comptabilite, 0) AS Comptabilite, 
                COALESCE(n.Analyse, 0) AS Analyse, 
                COALESCE(n.Statistique, 0) AS Statistique, 
                COALESCE(n.Physique, 0) AS Physique, 
                COALESCE(n.statut, 'en cours') AS statut, 
                COALESCE(n.moyenne, 0) AS moyenne
        FROM etudiants e
        LEFT JOIN master1 n ON e.id = n.etudiant_id
        WHERE e.niveau = 'M1'";

if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    } else {
        $rows = [];
    }
} else {
    echo "Erreur de la requête : " . $conn->error;
    $rows = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Master 1 - Liste des Étudiants</title>
    <link rel="stylesheet" href="licence1.css">
</head>
<body>
    <div class="container">
        <h1>Master 1 - Liste des Étudiants</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Comptabilite</th>
                    <th>Analyse</th>
                    <th>Statistique</th>
                    <th>Physique</th>
                    <th>Statut</th>
                    <th>Moyenne</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($rows) && !empty($rows)) {
                    foreach ($rows as $row) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["nom"]) . "</td>
                                <td>" . htmlspecialchars($row["prenom"]) . "</td>
                                <td>" . htmlspecialchars($row["Comptabilite"]) . "</td>
                                <td>" . htmlspecialchars($row["Analyse"]) . "</td>
                                <td>" . htmlspecialchars($row["Statistique"]) . "</td>
                                <td>" . htmlspecialchars($row["Physique"]) . "</td>
                                <td>" . htmlspecialchars($row["statut"]) . "</td>
                                <td>" . htmlspecialchars($row["moyenne"]) . "</td>
                                <td>
                                    <a href='ajouter_note_master1.php?id=" . htmlspecialchars($row["id"]) . "'>Modifier</a>
                                    <a href='bulletinmaster1.php?id=" . htmlspecialchars($row["id"]) . "'>Mon Bulletin</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Aucun étudiant trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>

         <!-- Affichage du nombre d'étudiants en cours, admis, et recalés -->
         <div class="statistiques">
            <p><strong>Nombre d'étudiants admis:</strong> <?php echo $total_admis; ?></p>
            <p><strong>Nombre d'étudiants recalés:</strong> <?php echo $total_recales; ?></p>
        </div>


        <!-- Ajout des boutons -->
        <div class="buttons">
            <a href="lister_etudiants_merite_master1.php" class="button">Lister les étudiants par ordre de mérite</a>
            <a href="note_etudiant.php" class="button">Retour</a>
        </div>
    </div>
</body>
</html>