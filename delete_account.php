<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Supprimer les rendez-vous de l'utilisateur
$sql_delete_appointments = "DELETE FROM rendez_vous WHERE user_id = ?";
$stmt = $pdo->prepare($sql_delete_appointments);
$stmt->execute([$user_id]);

// Supprimer l'utilisateur
$sql_delete_user = "DELETE FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql_delete_user);
$stmt->execute([$user_id]);

session_destroy();
header("Location: login.php?account_deleted=1");
exit;
?>