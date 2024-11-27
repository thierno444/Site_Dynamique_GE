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
               COALESCE(n.Programmation_Objet, 0) AS Programmation_Objet, 
               COALESCE(n.Base_de_Données, 0) AS Base_de_Données, 
               COALESCE(n.Réseaux, 0) AS Réseaux, 
               COALESCE(n.Systèmes_Exploitation, 0) AS Systèmes_Exploitation, 
               COALESCE(n.statut, 'en cours') AS statut, 
               COALESCE(n.moyenne, 0) AS moyenne
        FROM etudiants e
        LEFT JOIN notes_licence3 n ON e.id = n.etudiant_id
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
                <p>Niveau : Licence 3</p>
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
                    <td>Programmation Objet</td>
                    <td><?php echo htmlspecialchars($etudiant['Programmation_Objet']); ?></td>
                    <td><?php
                        if ($etudiant['Programmation_Objet'] >= 16) {
                            echo "Excellente performance en Programmation Objet.";
                        } elseif ($etudiant['Programmation_Objet'] >= 14) {
                            echo "Bon travail en Programmation Objet.";
                        } elseif ($etudiant['Programmation_Objet'] >= 12) {
                            echo "Travail satisfaisant en Programmation Objet.";
                        } elseif ($etudiant['Programmation_Objet'] >= 10) {
                            echo "Résultats justes en Programmation Objet.";
                        } else {
                            echo "Insuffisant en Programmation Objet.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Base de Données</td>
                    <td><?php echo htmlspecialchars($etudiant['Base_de_Données']); ?></td>
                    <td><?php
                        if ($etudiant['Base_de_Données'] >= 16) {
                            echo "Excellente performance en Base de Données.";
                        } elseif ($etudiant['Base_de_Données'] >= 14) {
                            echo "Bon travail en Base de Données.";
                        } elseif ($etudiant['Base_de_Données'] >= 12) {
                            echo "Travail satisfaisant en Base de Données.";
                        } elseif ($etudiant['Base_de_Données'] >= 10) {
                            echo "Résultats justes en Base de Données.";
                        } else {
                            echo "Insuffisant en Base de Données.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Réseaux</td>
                    <td><?php echo htmlspecialchars($etudiant['Réseaux']); ?></td>
                    <td><?php
                        if ($etudiant['Réseaux'] >= 16) {
                            echo "Excellente performance en Réseaux.";
                        } elseif ($etudiant['Réseaux'] >= 14) {
                            echo "Bon travail en Réseaux.";
                        } elseif ($etudiant['Réseaux'] >= 12) {
                            echo "Travail satisfaisant en Réseaux.";
                        } elseif ($etudiant['Réseaux'] >= 10) {
                            echo "Résultats justes en Réseaux.";
                        } else {
                            echo "Insuffisant en Réseaux.";
                        }
                    ?></td>
                </tr>
                <tr>
                    <td>Systèmes d'Exploitation</td>
                    <td><?php echo htmlspecialchars($etudiant['Systèmes_Exploitation']); ?></td>
                    <td><?php
                        if ($etudiant['Systèmes_Exploitation'] >= 16) {
                            echo "Excellente performance en Systèmes d'Exploitation.";
                        } elseif ($etudiant['Systèmes_Exploitation'] >= 14) {
                            echo "Bon travail en Systèmes d'Exploitation.";
                        } elseif ($etudiant['Systèmes_Exploitation'] >= 12) {
                            echo "Travail satisfaisant en Systèmes d'Exploitation.";
                        } elseif ($etudiant['Systèmes_Exploitation'] >= 10) {
                            echo "Résultats justes en Systèmes d'Exploitation.";
                        } else {
                            echo "Insuffisant en Systèmes d'Exploitation.";
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
