<?php

// Fichier PHP pour charger les étudiants

include 'db.php';

$archived = $_GET['archived'];
$sql = "SELECT * FROM etudiants WHERE archive='$archived'";
$result = $conn->query($sql);

echo "<table border='1'>
<tr>
<th>Nom</th>
<th>Prénom</th>
<th>Email</th>
<th>Téléphone</th>
<th>Matricule</th>
<th>Actions</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$row['nom']}</td>
    <td>{$row['prenom']}</td>
    <td>{$row['email']}</td>
    <td>{$row['telephone']}</td>
    <td>{$row['matricule']}</td>
    <td>
    <a href='edit_student.php?id={$row['id']}'>Modifier</a> |
    <a href='archive_student.php?id={$row['id']}&archive=" . ($archived ? 0 : 1) . "'>" . ($archived ? 'Désarchiver' : 'Archiver') . "</a>
    </td>
    </tr>";
}
echo "</table>";

$conn->close();
?>
