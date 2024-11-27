<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];

$sql = "SELECT * FROM admins WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
        $_SESSION['admin_id'] = $row['id'];
        if ($row['role'] === 'super admin') {
            header('Location: dashboard.php');
        } else {
            header('Location: dashboard_admin.php');
        }
        exit();
    } else {
        echo "Mot de passe incorrect.";
    }
} else {
    echo "Email non trouve.";
}

$conn->close();
?>