<?php
// Démarre la session pour pouvoir utiliser les variables de session
session_start();

// Inclusion du fichier db.php qui gère la connexion à la base de données
include 'db.php';

// Vérifie si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fonction pour vérifier si un champ contient uniquement des lettres avec une première lettre majuscule
function validateName($name) {
    return preg_match('/^[A-Z][a-zA-Z]+$/', $name);
}

// Fonction pour valider le prénom (jusqu'à 3 mots, chaque mot commence par une majuscule)
function validatePrenom($prenom) {
    return preg_match('/^([A-Z][a-z]+)( [A-Z][a-z]+){0,2}$/', $prenom);
}

// Fonction pour valider l'email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Fonction pour valider le mot de passe (minimum 8 caractères, 1 lettre majuscule, 1 chiffre, 1 caractère spécial)
function validatePassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Vérifie si le formulaire d'inscription a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Convertir nom et prénom en minuscules et mettre la première lettre de chaque mot en majuscule
    $nom = isset($_POST['nom']) ? ucwords(strtolower($conn->real_escape_string(trim($_POST['nom'])))) : '';
    $prenom = isset($_POST['prenom']) ? ucwords(strtolower($conn->real_escape_string(trim($_POST['prenom'])))) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $conn->real_escape_string(trim($_POST['mot_de_passe'])) : '';
    $role = isset($_POST['role']) ? $conn->real_escape_string(trim($_POST['role'])) : '';

    // VALIDATIONS
    // Vérification des noms
    if (!validateName($nom)) {
        $_SESSION['error'] = "Le nom doit commencer par une majuscule et ne contenir que des lettres.";
        header('Location: register_admin.php');
        exit();
    }
    
    // Vérification des prénoms
    if (!validatePrenom($prenom)) {
        $_SESSION['error'] = "Le prénom doit commencer par une majuscule, contenir jusqu'à trois mots séparés par des espaces, et ne contenir que des lettres.";
        header('Location: register_admin.php');
        exit();
    }
    
    // Vérification de l'email
    if (!validateEmail($email)) {
        $_SESSION['error'] = "Veuillez fournir un email valide.";
        header('Location: register_admin.php');
        exit();
    }
    
    // Vérification du mot de passe
    if (!validatePassword($mot_de_passe)) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères, avec une lettre majuscule, un chiffre et un caractère spécial.";
        header('Location: register_admin.php');
        exit();
    }

    // Vérification de l'existence de l'email
    $sql = "SELECT id FROM admins WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "L'email est déjà enregistré. Veuillez en utiliser un autre.";
        header('Location: register_admin.php');
        exit();
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    // Insère les informations de l'administrateur dans la table 'administrateurs'
    $sql = "INSERT INTO admins (nom, prenom, email, mot_de_passe, role) 
            VALUES ('$nom', '$prenom', '$email', '$hashed_password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Administrateur inscrit avec succès.";
    } else {
        $_SESSION['error'] = "Erreur: " . $conn->error;
    }

    header('Location: register_admin.php');
    exit();
}

// Ferme la connexion à la base de données
$conn->close();
?>
