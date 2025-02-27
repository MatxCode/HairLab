<?php
session_start();
require_once 'config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérification que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérification que les nouveaux mots de passe correspondent
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas.";
        header("Location: profile.php");
        exit;
    }

    // Vérification de la complexité du mot de passe
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        header("Location: profile.php");
        exit;
    }

    // Récupération du mot de passe actuel hashé depuis la base de données
    $sql = "SELECT password FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification que le mot de passe actuel est correct
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
        header("Location: profile.php");
        exit;
    }

    // Hashage du nouveau mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Mise à jour du mot de passe dans la base de données
    $update_sql = "UPDATE users SET password = :password WHERE id = :user_id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        // Succès
        $_SESSION['success'] = "Votre mot de passe a été modifié avec succès.";
    } else {
        // Erreur
        $_SESSION['error'] = "Une erreur est survenue lors de la modification du mot de passe.";
    }

    // Redirection vers la page de profil
    header("Location: profile.php");
    exit;
} else {
    // Si accès direct à la page sans soumission de formulaire
    header("Location: profile.php");
    exit;
}
