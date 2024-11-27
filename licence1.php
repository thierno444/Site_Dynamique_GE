<?php
// Inclure le fichier de connexion
include 'db.php';

// Mettre à jour les statuts des étudiants en fonction des notes
$update_sql = "UPDATE licence1 
               SET statut = CASE 
                   WHEN Algébre IS NULL OR Analyse IS NULL OR Algo IS NULL OR HTML_CSS IS NULL THEN 'en cours'
                   WHEN moyenne >= 9.50 THEN 'admis'
                   ELSE 'recalé'
               END";

// Exécuter la requête de mise à jour des statuts
if ($conn->query($update_sql) === FALSE) {
    echo "Erreur de mise à jour des statuts : " . $conn->error;
}

// Récupérer le nombre d'étudiants par statut
$sql_admis = "SELECT COUNT(*) as total_admis FROM licence1  WHERE statut = 'admis'";
$sql_recales = "SELECT COUNT(*) as total_recales FROM licence1  WHERE statut = 'recalé'";
$sql_en_cours = "SELECT COUNT(*) as total_en_cours FROM licence1  WHERE statut = 'en cours'";

$total_admis = $conn->query($sql_admis)->fetch_assoc()['total_admis'];
$total_recales = $conn->query($sql_recales)->fetch_assoc()['total_recales'];
$total_en_cours = $conn->query($sql_en_cours)->fetch_assoc()['total_en_cours'];

// Récupérer les étudiants et leurs notes pour le niveau L1
$sql = "SELECT e.id, e.nom, e.prenom, e.matricule, 
                COALESCE(n.Algébre, 0) AS Algébre, 
                COALESCE(n.Analyse, 0) AS Analyse, 
                COALESCE(n.Algo, 0) AS Algo, 
                COALESCE(n.HTML_CSS, 0) AS HTML_CSS, 
                COALESCE(n.statut, 'en cours') AS statut, 
                COALESCE(n.moyenne, 0) AS moyenne
        FROM etudiants e
        LEFT JOIN licence1  n ON e.id = n.etudiant_id
        WHERE e.niveau = 'L1'";


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
    <title>Licence 1 - Liste des Étudiants</title>
    <link rel="stylesheet" href="licence1.css">
</head>
<body>
    <div class="container">
        <h1>Licence 1 - Liste des Étudiants</h1>
        
        <table>
            <thead>
                <tr>
                <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    
                    <th>Algébre</th>
                    <th>Analyse</th>
                    <th>Algo</th>
                    <th>HTML/CSS</th>
                    
                    <th>Moyenne</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($rows) && !empty($rows)) {
                    foreach ($rows as $row) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["matricule"]) . "</td>
                                <td>" . htmlspecialchars($row["nom"]) . "</td>
                                <td>" . htmlspecialchars($row["prenom"]) . "</td>
                                 
                                <td>" . htmlspecialchars($row["Algébre"]) . "</td>
                                <td>" . htmlspecialchars($row["Analyse"]) . "</td>
                                <td>" . htmlspecialchars($row["Algo"]) . "</td>
                                <td>" . htmlspecialchars($row["HTML_CSS"]) . "</td>
                               
                                <td>" . htmlspecialchars($row["moyenne"]) . "</td>
                                 <td>" . htmlspecialchars($row["statut"]) . "</td>
                                <td>
                                    <a href='ajouter_note_licence1.php?id=" . htmlspecialchars($row["id"]) . "'>modifier</a>
                                    <a href='bulletin.php?id=" . htmlspecialchars($row["id"]) . "'>Mon Bulletin</a>
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
            <a href="lister_etudiants_merite.php" class="button">Lister les étudiants par ordre de mérite</a>
            <a href="note_etudiant.php" class="button">Retour</a>
        </div>
    </div>
</body>
</html>
