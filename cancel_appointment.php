<?php
session_start();
require_once 'config/database.php';

// Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérifier la présence et la validité du token CSRF
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Échec de vérification CSRF. Suppression annulée.";
    header("Location: profile.php");
    exit;
}

// Vérifier la présence de l'ID du rendez-vous
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Rendez-vous invalide.";
    header("Location: profile.php");
    exit;
}

$appointment_id = (int)$_GET['id']; // Convertir en entier pour éviter les injections
$user_id = $_SESSION['user_id'];

try {
    // Vérifier si le rendez-vous appartient bien à l'utilisateur
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
} catch (PDOException $e) {
    error_log("Erreur SQL (Suppression rendez-vous) : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
}

// Redirection vers la page de profil
header("Location: profile.php");
exit;
