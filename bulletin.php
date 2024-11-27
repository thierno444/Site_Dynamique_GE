<?php
// Inclure le fichier de connexion à la base de données
include 'db.php';

// Récupérer l'ID de l'étudiant à partir de l'URL
$etudiant_id = $_GET['id'];

// Requête SQL pour récupérer les informations de l'étudiant et ses notes
$sql = "SELECT e.nom, e.prenom, e.matricule, 
               n.Algébre, n.Analyse, n.Algo, n.HTML_CSS, n.moyenne,
               n.statut
        FROM etudiants e
        LEFT JOIN licence1 n ON e.id = n.etudiant_id
        WHERE e.id = ?";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $etudiant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $etudiant = $result->fetch_assoc();
} else {
    echo "Aucune donnée trouvée pour cet étudiant.";
    exit;
}

$conn->close();

// Définir les appréciations et la décision du conseil de classe en fonction de la moyenne générale
$moyenne_generale = $etudiant['moyenne'];

if ($moyenne_generale >= 16) {
    $appreciation = "Excellente performance académique. Continuez ainsi !";
    $decision_conseil = "Félicitations. Admis avec mention.";
} elseif ($moyenne_generale >= 14) {
    $appreciation = "Bon travail, mais vous pouvez encore vous améliorer.";
    $decision_conseil = "Admis. Encouragez à maintenir cet effort.";
} elseif ($moyenne_generale >= 12) {
    $appreciation = "Travail satisfaisant, mais peut faire mieux.";
    $decision_conseil = "Admis sous réserve d'amélioration.";
} elseif ($moyenne_generale >= 10) {
    $appreciation = "Résultats justes, un travail plus soutenu est nécessaire.";
    $decision_conseil = "Admis sous réserve d'amélioration significative.";
} else {
    $appreciation = "Insuffisant, des efforts supplémentaires sont nécessaires.";
    $decision_conseil = "Ajourné. Doit faire preuve de plus d'assiduité.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de Notes</title>
    <link rel="stylesheet" href="bulletin.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="school-info">
                <img src="images.png" alt="Logo Simplon Sénégal" class="logo">
                <h1>Simplon Sénégal</h1>
                <p>Institut Privé de Formation - Dakar</p>
                <p>Adresse : 123 Rue de la Formation, Dakar</p>
                <p>Tel : +221 33 123 45 67</p>
            </div>
            <div class="student-info">
                <h2>BULLETIN DE NOTES</h2>
                <p>Année scolaire : 2023-2024</p>
                <p><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></p>
                <p>Niveau : Licence 1</p>
                <p>Matricule : <?php echo htmlspecialchars($etudiant['matricule']); ?></p>
            </div>
        </header>

        <table class="grades-table">
            <thead>
                <tr>
                    <th>Matières</th>
                    <th>Note /20</th>
                    <th>Appréciations</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Algèbre</td>
                    <td><?php echo htmlspecialchars($etudiant['Algébre']); ?></td>
                    <td><?php
                        if ($etudiant['Algébre'] >= 16) {
                            echo "Excellente performance en algèbre.";
                        } elseif ($etudiant['Algébre'] >= 14) {
                            echo "Bon travail en algèbre.";
                        } elseif ($etudiant['Algébre'] >= 12) {
                            echo "Travail satisfaisant en algèbre.";
                        } elseif ($etudiant['Algébre'] >= 10) {
                            echo "Résultats justes en algèbre.";
                        } else {
                            echo "Insuffisant en algèbre.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Analyse</td>
                    <td><?php echo htmlspecialchars($etudiant['Analyse']); ?></td>
                    <td><?php
                        if ($etudiant['Analyse'] >= 16) {
                            echo "Excellente performance en analyse.";
                        } elseif ($etudiant['Analyse'] >= 14) {
                            echo "Bon travail en analyse.";
                        } elseif ($etudiant['Analyse'] >= 12) {
                            echo "Travail satisfaisant en analyse.";
                        } elseif ($etudiant['Analyse'] >= 10) {
                            echo "Résultats justes en analyse.";
                        } else {
                            echo "Insuffisant en analyse.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Algorithmique</td>
                    <td><?php echo htmlspecialchars($etudiant['Algo']); ?></td>
                    <td><?php
                        if ($etudiant['Algo'] >= 16) {
                            echo "Excellente performance";
                        } elseif ($etudiant['Algo'] >= 14) {
                            echo "Bon travail";
                        } elseif ($etudiant['Algo'] >= 12) {
                            echo "Travail satisfaisant";
                        } elseif ($etudiant['Algo'] >= 10) {
                            echo "Résultats justes ";
                        } else {
                            echo "Insuffisant en algorithmique.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>HTML/CSS</td>
                    <td><?php echo htmlspecialchars($etudiant['HTML_CSS']); ?></td>
                    <td><?php
                        if ($etudiant['HTML_CSS'] >= 16) {
                            echo "Excellent travail en développement web.";
                        } elseif ($etudiant['HTML_CSS'] >= 14) {
                            echo "Bon travail en développement web.";
                        } elseif ($etudiant['HTML_CSS'] >= 12) {
                            echo "Travail satisfaisant en développement web.";
                        } elseif ($etudiant['HTML_CSS'] >= 10) {
                            echo "Résultats justes en développement web.";
                        } else {
                            echo "Insuffisant en développement web.";
                        }
                    ?></td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <p><strong>Moyenne générale :</strong> <?php echo htmlspecialchars($moyenne_generale); ?>/20</p>
            <p><strong>Statut :</strong> <?php echo htmlspecialchars($etudiant['statut']); ?></p>
        </div>

        <footer>
            <p><strong>Appréciations du conseil de classe :</strong></p>
            <p><?php echo htmlspecialchars($appreciation); ?></p>
            <p><strong>Décision du conseil de classe :</strong></p>
            <p><?php echo htmlspecialchars($decision_conseil); ?></p>
            <div class="signature">
                <img src="sign.png" alt="Signature de la directrice">
                <p>La Directrice</p>
            </div>
            <div class="footer-logo">
                <img src="tamp-removebg-preview.png" alt="Logo Simplon Sénégal">
            </div>
        </footer>

        <div class="buttons">
            <button onclick="window.print()">Imprimer</button>
            <button onclick="downloadPDF()">Télécharger</button>
            <button onclick="window.history.back()">Retour</button>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.querySelector('.container');
            html2pdf()
                .from(element)
                .save('bulletin.pdf');
        }
    </script>
    <script src="https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
</body>
</html>
