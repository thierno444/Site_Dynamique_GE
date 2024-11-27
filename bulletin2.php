<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
include 'db.php';

// Récupérer l'ID de l'étudiant à partir de l'URL
$etudiant_id = $_GET['id'];

// Requête SQL pour récupérer les informations de l'étudiant et ses notes
$sql = "SELECT e.nom, e.prenom, e.matricule, 
               COALESCE(n.Processeur, 0) AS Processeur, 
               COALESCE(n.Robotique, 0) AS Robotique, 
               COALESCE(n.IA, 0) AS IA, 
               COALESCE(n.`IOT`, 0) AS `IOT`, 
               COALESCE(n.statut, 'en cours') AS statut, 
               COALESCE(n.moyenne, 0) AS moyenne
        FROM etudiants e
        LEFT JOIN licence2 n ON e.id = n.etudiant_id
        WHERE e.id = ?";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erreur de préparation de la requête SQL : " . $conn->error);
}

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
                <p>Niveau : Licence 2</p>
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
                    <td>Processeur</td>
                    <td><?php echo htmlspecialchars($etudiant['Processeur']); ?></td>
                    <td><?php
                        if ($etudiant['Processeur'] >= 16) {
                            echo "Excellente performance en Processeur.";
                        } elseif ($etudiant['Processeur'] >= 14) {
                            echo "Bon travail en Processeur.";
                        } elseif ($etudiant['Processeur'] >= 12) {
                            echo "Travail satisfaisant en Processeur.";
                        } elseif ($etudiant['Processeur'] >= 10) {
                            echo "Résultats justes en Processeur.";
                        } else {
                            echo "Insuffisant en Processeur.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Robotique</td>
                    <td><?php echo htmlspecialchars($etudiant['Robotique']); ?></td>
                    <td><?php
                        if ($etudiant['Robotique'] >= 16) {
                            echo "Excellente performance en Robotique.";
                        } elseif ($etudiant['Robotique'] >= 14) {
                            echo "Bon travail en Robotique.";
                        } elseif ($etudiant['Robotique'] >= 12) {
                            echo "Travail satisfaisant en Robotique.";
                        } elseif ($etudiant['Robotique'] >= 10) {
                            echo "Résultats justes en Robotique.";
                        } else {
                            echo "Insuffisant en Robotique.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>IA</td>
                    <td><?php echo htmlspecialchars($etudiant['IA']); ?></td>
                    <td><?php
                        if ($etudiant['IA'] >= 16) {
                            echo "Excellente performance en IA.";
                        } elseif ($etudiant['IA'] >= 14) {
                            echo "Bon travail en IA.";
                        } elseif ($etudiant['IA'] >= 12) {
                            echo "Travail satisfaisant en IA.";
                        } elseif ($etudiant['IA'] >= 10) {
                            echo "Résultats justes en IA.";
                        } else {
                            echo "Insuffisant en IA.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>IOT</td>
                    <td><?php echo htmlspecialchars($etudiant['IOT']); ?></td>
                    <td><?php
                        if ($etudiant['IOT'] >= 16) {
                            echo "Excellente performance en IOT.";
                        } elseif ($etudiant['IOT'] >= 14) {
                            echo "Bon travail en IOT.";
                        } elseif ($etudiant['IOT'] >= 12) {
                            echo "Travail satisfaisant en IOT.";
                        } elseif ($etudiant['IOT'] >= 10) {
                            echo "Résultats justes en IOT.";
                        } else {
                            echo "Insuffisant en IOT.";
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.container');
            html2pdf().from(element).save('bulletin.pdf');
        }
    </script>
</body>
</html>
