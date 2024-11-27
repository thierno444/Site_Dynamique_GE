<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Récupération du nombre d'étudiants
$sql_total = "SELECT COUNT(*) AS total FROM etudiants";
$sql_archived = "SELECT COUNT(*) AS archived FROM etudiants WHERE archive=1";
$sql_active = "SELECT COUNT(*) AS active FROM etudiants WHERE archive=0";

$result_total = $conn->query($sql_total);
$result_archived = $conn->query($sql_archived);
$result_active = $conn->query($sql_active);

$total_students = $result_total->fetch_assoc()['total'];
$archived_students = $result_archived->fetch_assoc()['archived'];
$active_students = $result_active->fetch_assoc()['active'];

// Message pour l'archivage ou la désarchivage
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);

$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

// Recherche d'étudiant
$search_results = [];
if (isset($_POST['search'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);
    $sql_search = "SELECT * FROM etudiants WHERE nom LIKE '%$search_term%' OR prenom LIKE '%$search_term%' OR matricule LIKE '%$search_term%'";
    $search_results = $conn->query($sql_search);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Étudiants</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="inactivityTimer.js" defer></script>
</head>
<body>

<div class="container">
    <!-- Affichage des messages -->
    <?php if ($message): ?>
        <div id="message" class="success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div id="message" class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="header">
        <h1 class="section-title">Plateforme Étudiants</h1>
        <form method="post" style="display: inline;">
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        </form>
    </div><br>

    <!-- Formulaire de recherche -->
    <form method="post" class="search-form">
        <input type="text" name="search_term" placeholder="Rechercher un étudiant par son matricule" required>
        <button type="submit" name="search">Rechercher</button>
    </form>

    <!-- Affichage des résultats de la recherche -->
    <?php if (!empty($search_results) && $search_results->num_rows > 0): ?>
        <h2 class="section-title">Résultats de la recherche</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Niveau</th>
                        <th>État d'archivage</th>
                       
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $search_results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['matricule']); ?></td>
                            <td><?php echo htmlspecialchars($row['nom']); ?></td>
                            <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                            <td><?php echo htmlspecialchars($row['niveau']); ?></td>
                            <td><?php echo $row['archive'] ? 'Archivé' : 'Actif'; ?></td>
                           
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
             <!-- Bouton de fermeture -->
    <button class="btn-close" onclick="closeResults()">Fermer</button>
            
        </div><br>
    <?php elseif (isset($_POST['search'])): ?>
        <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($search_term); ?>".</p>
    <?php endif; ?>
    <h2 class="section-title">Administrateurs</h2>
   
    <div class="admin-actions">
       
        <div class="admin-action-card">
            <h3>Liste des administrateurs</h3>
            <p>Lister les informations d'un administrateur existant.</p>
            <button onclick="window.location.href='ad_management.php'">Lister</button>
        </div>
    </div>

    <h2 class="section-title">Étudiants</h2>
    <div class="button-group">
        <button id="show-active">Voir Étudiants Actifs (<?php echo $active_students; ?>)</button>
        <button id="show-archived">Voir Étudiants Archivés (<?php echo $archived_students; ?>)</button>
        <button id="show-total">Total Étudiants (<?php echo $total_students; ?>)</button>
    </div>

    <!-- Tableau des étudiants actifs -->
    <div id="table-active" class="table-container" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Niveau</th>
                    <th>État d'archivage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_active = "SELECT * FROM etudiants WHERE archive=0";
                $result_active = $conn->query($sql_active);

                while ($row = $result_active->fetch_assoc()) {
                    echo "<tr>
                    <td>{$row['matricule']}</td>
                    <td>{$row['nom']}</td>
                    <td>{$row['prenom']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['telephone']}</td>
                    <td>{$row['niveau']}</td>
                    <td>Actif</td>
                    
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Tableau des étudiants archivés -->
    <div id="table-archived" class="table-container" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Niveau</th>
                    <th>État d'archivage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_archived = "SELECT * FROM etudiants WHERE archive=1";
                $result_archived = $conn->query($sql_archived);

                while ($row = $result_archived->fetch_assoc()) {
                    echo "<tr>
                    <td>{$row['matricule']}</td>
                    <td>{$row['nom']}</td>
                    <td>{$row['prenom']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['telephone']}</td>
                    <td>{$row['niveau']}</td>
                    <td>Archivé</td>
                   
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Tableau des étudiants totaux -->
<div id="table-total" class="table-container" style="display: none;">
    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Niveau</th>
                <th>État d'archivage</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_total_students = "SELECT * FROM etudiants";
            $result_total_students = $conn->query($sql_total_students);

            while ($row = $result_total_students->fetch_assoc()) {
                echo "<tr>
                <td>{$row['matricule']}</td>
                <td>{$row['nom']}</td>
                <td>{$row['prenom']}</td>
                <td>{$row['email']}</td>
                <td>{$row['telephone']}</td>
                <td>{$row['niveau']}</td>
                <td>" . ($row['archive'] ? 'Archivé' : 'Actif') . "</td>
               
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>


</div>

<script>
const btnActive = document.getElementById('show-active');
const btnArchived = document.getElementById('show-archived');
const btnTotal = document.getElementById('show-total');
const tableActive = document.getElementById('table-active');
const tableArchived = document.getElementById('table-archived');
const tableTotal = document.getElementById('table-total');

// Fonction pour réinitialiser l'affichage des tableaux
function resetTableDisplay() {
    tableActive.style.display = 'none';
    tableArchived.style.display = 'none';
    tableTotal.style.display = 'none';
}

// Fonction pour vérifier si les champs de recherche sont vides
function areSearchFieldsEmpty() {
    const searchInput = document.getElementById('search-input'); // ID de votre champ de recherche
    return searchInput.value.trim() === '';
}

// Fonction pour afficher ou masquer les tableaux en fonction des champs de recherche
function handleTableDisplay() {
    if (areSearchFieldsEmpty()) {
        resetTableDisplay();
    }
}

// Écouteur pour le bouton "Actifs"
btnActive.addEventListener('click', () => {
    if (tableActive.style.display === 'block') {
        tableActive.style.display = 'none';
    } else {
        resetTableDisplay(); // Masquer les autres tableaux
        tableActive.style.display = 'block';
    }
    handleTableDisplay(); // Vérifier les champs de recherche
});

// Écouteur pour le bouton "Archivés"
btnArchived.addEventListener('click', () => {
    if (tableArchived.style.display === 'block') {
        tableArchived.style.display = 'none';
    } else {
        resetTableDisplay(); // Masquer les autres tableaux
        tableArchived.style.display = 'block';
    }
    handleTableDisplay(); // Vérifier les champs de recherche
});

// Écouteur pour le bouton "Total"
btnTotal.addEventListener('click', () => {
    if (tableTotal.style.display === 'block') {
        tableTotal.style.display = 'none';
    } else {
        resetTableDisplay(); // Masquer les autres tableaux
        tableTotal.style.display = 'block';
    }
    handleTableDisplay(); // Vérifier les champs de recherche
});

// Écouteur pour le champ de recherche (si applicable)
document.getElementById('search-input').addEventListener('input', handleTableDisplay);
// Fonction pour masquer le tableau de recherche
function closeResults() {
    // Sélectionne le conteneur du tableau de recherche
    const tableContainer = document.querySelector('.table-container');
    // Masque le conteneur
    tableContainer.style.display = 'none';
}

// Écouteur d'événement pour le bouton "Fermer"
document.querySelector('.btn-close').addEventListener('click', closeResults);


</script>

</body>
</html>
