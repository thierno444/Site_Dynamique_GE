Gestion des Notes des Étudiants

Ce projet est une application permettant à un administrateur de gérer les notes des étudiants et leur statut d'admission en fonction de leurs performances académiques.
Fonctionnalités

    Gestion par niveau académique :
        L'administrateur peut sélectionner la liste des étudiants d'un niveau donné (exemple : L1).
        Les étudiants sont regroupés par niveau pour une gestion simplifiée.

    Saisie et calcul des notes :
        Chaque étudiant possède 4 notes correspondant à 4 modules.
        Le statut d'admission est initialisé à "en cours".

    Mise à jour du statut d'admission :
        Si l'étudiant atteint la moyenne requise : le statut passe à "admis".
        Si l'étudiant n'atteint pas la moyenne : le statut passe à "recalé".

    Affichage des résultats :
        Liste des étudiants triée par ordre de mérite.
        Statistiques globales :
            Nombre total d'étudiants.
            Nombre d'étudiants admis.
            Nombre d'étudiants recalés.

Installation

    Clonez le dépôt sur votre machine locale :

    git clone https://github.com/thierno444/Site_Dynamique_GE.git

    Assurez-vous d'avoir installé un serveur web local (comme XAMPP, WAMP, ou MAMP) pour exécuter le projet.

    Placez le dossier du projet dans le répertoire htdocs ou le dossier correspondant à votre serveur.

    Importez le fichier SQL pour créer la base de données :
        Accédez à phpMyAdmin.
        Créez une nouvelle base de données (ex. : gestion_etudiants).
        Importez le fichier database.sql inclus dans le projet.

Utilisation

    Lancez le serveur local et ouvrez le projet dans votre navigateur :

    http://localhost/Site_Dynamique_GE

    Connectez-vous en tant qu'administrateur pour accéder aux fonctionnalités :
        Ajout des notes.
        Mise à jour des statuts.
        Consultation des résultats.

Technologies utilisées

    Frontend : HTML, CSS, JavaScript et Bootstrap.
    Backend : PHP.
    Base de données : MySQL.

Améliorations possibles

    Ajout d'une fonctionnalité d'export des résultats au format Excel ou PDF.
    Notifications automatiques aux étudiants concernant leur statut d'admission.
    Intégration d'un système de gestion des utilisateurs (administrateurs multiples avec rôles).
