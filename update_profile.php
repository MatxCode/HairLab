<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    $sql = "UPDATE users SET nom = ?, prenom = ?, email = ?, date_naissance = ?, adresse = ?, telephone = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $date_naissance, $adresse, $telephone, $user_id]);

    header("Location: profile.php?success=1");
    exit;
}
?>