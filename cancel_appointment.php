<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si le rendez-vous appartient à l'utilisateur
    $sql_check = "SELECT id FROM rendez_vous WHERE id = :appointment_id AND user_id = :user_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        // Supprimer le rendez-vous
        $sql_delete = "DELETE FROM rendez_vous WHERE id = :appointment_id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
        $stmt_delete->execute();

        $_SESSION['success'] = "Rendez-vous annulé avec succès.";
    } else {
        $_SESSION['error'] = "Rendez-vous introuvable ou non autorisé.";
    }
}

header("Location: profile.php");
exit;
?>