<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fonction pour vérifier si un champ ne contient que des lettres sans espaces ni caractères spéciaux
function validateName($name) {
    return preg_match('/^[A-Za-z]+$/', $name);
}

// Fonction pour valider le téléphone (que des chiffres, sans espaces, et doit être de 9 chiffres)
function validatePhone($phone) {
    return preg_match('/^\d{9}$/', $phone);
}

// Fonction pour mettre en majuscule la première lettre du nom
function capitalizeFirstLetter($string) {
    return ucfirst(strtolower($string));
}

// Fonction pour mettre en majuscule la première lettre de chaque mot du prénom
function capitalizeEachWord($string) {
    return ucwords(strtolower($string));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? $conn->real_escape_string(trim($_POST['nom'])) : '';
    $prenom = isset($_POST['prenom']) ? $conn->real_escape_string(trim($_POST['prenom'])) : '';
    $date_naissance = isset($_POST['date_naissance']) ? $conn->real_escape_string(trim($_POST['date_naissance'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $telephone = isset($_POST['telephone']) ? $conn->real_escape_string(trim($_POST['telephone'])) : '';
    $niveau = isset($_POST['niveau']) ? $conn->real_escape_string(trim($_POST['niveau'])) : '';

    // Vérification du contenu des champs "nom" et "prénom"
    if (strcasecmp($nom, 'nom') === 0 || strcasecmp($prenom, 'prenom') === 0) {
        $_SESSION['error'] = "Veuillez saisir un nom ou un prénom valide.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification des noms
    if (!validateName($nom)) {
        $_SESSION['error'] = "Le nom ne doit contenir que des lettres, sans espaces ni caractères spéciaux.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification des prénoms
    if (!validateName($prenom)) {
        $_SESSION['error'] = "Le prénom ne doit contenir que des lettres, sans espaces ni caractères spéciaux.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification du téléphone
    if (!validatePhone($telephone)) {
        $_SESSION['error'] = "Le numéro de téléphone doit contenir exactement 9 chiffres, sans espaces.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification de la date de naissance
    $birthDate = new DateTime($date_naissance);
    $maxDate = new DateTime('2007-12-31');
    if ($birthDate > $maxDate) {
        $_SESSION['error'] = "La date de naissance ne peut pas être postérieure au 31 décembre 2007.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Veuillez fournir un email valide.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification de l'existence de l'email
    $sql = "SELECT id FROM etudiants WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "L'email est déjà enregistré. Veuillez en utiliser un autre.";
        header('Location: register_student.php');
        exit();
    }

    // Vérification de l'existence du numéro de téléphone
    $sql = "SELECT id FROM etudiants WHERE telephone = '$telephone'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Le numéro de téléphone est déjà enregistré. Veuillez en utiliser un autre.";
        header('Location: register_student.php');
        exit();
    }

    // Mise en majuscule de la première lettre du nom et de chaque mot du prénom
    $nom = capitalizeFirstLetter($nom);
    $prenom = capitalizeEachWord($prenom);
    function generateMatricule($conn) {
        $sql = "SELECT matricule FROM etudiants ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastMatricule = $row['matricule'];
            preg_match('/(\d+)$/', $lastMatricule, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newNumber = $lastNumber + 1;
            $newMatricule = 'MAT2024-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $newMatricule = 'MAT2024-001';
        }
        
        return $newMatricule;
    }
    

    // Génération du matricule unique pour l'étudiant
    $matricule = generateMatricule($conn);

    // Insertion des informations de l'étudiant dans la table 'etudiants'
    $sql = "INSERT INTO etudiants (nom, prenom, date_naissance, email, telephone, niveau, matricule) 
            VALUES ('$nom', '$prenom', '$date_naissance', '$email', '$telephone', '$niveau', '$matricule')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Étudiant inscrit avec succès. Matricule: $matricule";
    } else {
        $_SESSION['error'] = "Erreur: " . $conn->error;
    }

    header('Location: register_student.php');
    exit();
}

$conn->close();
?>
