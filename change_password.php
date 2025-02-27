<?php
session_start();
require_once 'config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Protection CSRF : Génération du token s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérification que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Échec de vérification CSRF. Veuillez réessayer.";
        header("Location: profile.php");
        exit;
    }

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

    // Vérification de la complexité du mot de passe (8 caractères minimum avec au moins une majuscule, une minuscule et un chiffre)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $new_password)) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.";
        header("Location: profile.php");
        exit;
    }

    try {
        // Récupération du mot de passe actuel hashé depuis la base de données
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
            header("Location: profile.php");
            exit;
        }

        // Vérification que le mot de passe actuel est correct
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
            header("Location: profile.php");
            exit;
        }

        // Vérification que le nouveau mot de passe est différent de l'ancien
        if (password_verify($new_password, $user['password'])) {
            $_SESSION['error'] = "Le nouveau mot de passe doit être différent de l'ancien.";
            header("Location: profile.php");
            exit;
        }

        // Hashage du nouveau mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Mise à jour du mot de passe dans la base de données
        $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = "Votre mot de passe a été modifié avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la modification du mot de passe.";
        }

        // Redirection vers la page de profil
        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        error_log("Erreur SQL : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
        header("Location: profile.php");
        exit;
    }
} else {
    // Si accès direct à la page sans soumission de formulaire
    header("Location: profile.php");
    exit;
}
