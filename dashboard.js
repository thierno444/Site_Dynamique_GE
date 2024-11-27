
// Confirmation de Suppression

// Pour éviter les suppressions accidentelles,  

document.addEventListener('DOMContentLoaded', () => {
    const deleteLinks = document.querySelectorAll('a[data-confirm]');

    deleteLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')) {
                event.preventDefault();
            }
        });
    });
});


// Interface Dynamique

//  charger les données dynamiquement, ce qui évite de recharger la page pour chaque action.

// Exemple : Chargement des étudiants non archivés avec AJAX (dashboard.js):

document.addEventListener('DOMContentLoaded', () => {
    const loadStudents = (archived = 0) => {
        fetch(`load_students.php?archived=${archived}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('students-list').innerHTML = data;
            });
    };

    loadStudents(); // Charge les étudiants non archivés par défaut

    document.getElementById('show-archived').addEventListener('click', () => {
        loadStudents(1); // Charge les étudiants archivés
    });

    document.getElementById('show-active').addEventListener('click', () => {
        loadStudents(0); // Charge les étudiants non archivés
    });
});
