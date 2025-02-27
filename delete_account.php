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

// Récupération de l'ID utilisateur
$user_id = $_SESSION['user_id'];

try {
    // Supprimer les rendez-vous de l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Supprimer l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Vérifier si l'utilisateur a bien été supprimé
    if ($stmt->rowCount() > 0) {
        // Nettoyage de session après suppression
        session_unset();
        session_destroy();

        // Redirection après suppression réussie
        header("Location: login.php?account_deleted=1");
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du compte.";
        header("Location: profile.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur SQL : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: profile.php");
    exit;
}
