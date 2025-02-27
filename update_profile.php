<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];

    // Récupérer et sécuriser les données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars(trim($_POST['prenom']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $date_naissance = $_POST['date_naissance'];
    $adresse = htmlspecialchars(trim($_POST['adresse']), ENT_QUOTES, 'UTF-8');
    $telephone = htmlspecialchars(trim($_POST['telephone']), ENT_QUOTES, 'UTF-8');

    if (!$email) {
        $_SESSION['error'] = "L'adresse email n'est pas valide.";
        header("Location: profile.php");
        exit;
    }

    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $sql_check = "SELECT id FROM users WHERE email = :email AND id != :user_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $_SESSION['error'] = "Cet email est déjà utilisé par un autre compte.";
        header("Location: profile.php");
        exit;
    }

    // Mettre à jour les informations de l'utilisateur
    $sql = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email, date_naissance = :date_naissance, adresse = :adresse, telephone = :telephone WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':date_naissance', $date_naissance, PDO::PARAM_STR);
    $stmt->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Vos informations ont été mises à jour avec succès.";
    } else {
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
    }

    header("Location: profile.php");
    exit;
}
