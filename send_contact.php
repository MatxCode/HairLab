<?php
session_start();
require_once 'config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Protection CSRF : Vérification du token
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Échec de vérification CSRF. Veuillez réessayer.";
        header("Location: profile.php#contact");
        exit;
    }

    // Récupération des données du formulaire en les sécurisant contre XSS
    $user_id = $_SESSION['user_id'];
    $subject = htmlspecialchars($_POST['contact_subject'] ?? '', ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($_POST['contact_prenom'] ?? '', ENT_QUOTES, 'UTF-8');
    $nom = htmlspecialchars($_POST['contact_nom'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['contact_email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars($_POST['contact_telephone'] ?? '', ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($_POST['contact_message'] ?? '', ENT_QUOTES, 'UTF-8');
    $consent = isset($_POST['contact_consent']) ? 1 : 0;

    // Validation des données
    $errors = [];

    if (empty($subject)) {
        $errors[] = "Le sujet est requis.";
    }

    if (empty($prenom) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $prenom)) {
        $errors[] = "Le prénom est invalide.";
    }

    if (empty($nom) || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/", $nom)) {
        $errors[] = "Le nom est invalide.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (!empty($telephone) && !preg_match("/^\+?[0-9\s\-]+$/", $telephone)) {
        $errors[] = "Le numéro de téléphone est invalide.";
    }

    if (empty($message)) {
        $errors[] = "Le message est requis.";
    }

    if ($consent !== 1) {
        $errors[] = "Vous devez accepter la politique de confidentialité.";
    }

    // Si pas d'erreurs, enregistrement du message dans la base de données
    if (empty($errors)) {
        try {
            // Mapper les valeurs du sujet à des descriptions plus claires
            $subject_map = [
                'info_services' => 'Informations sur nos services',
                'prices' => 'Demande de tarifs',
                'appointment' => 'Question sur les rendez-vous',
                'complaint' => 'Réclamation',
                'other' => 'Autre demande'
            ];

            $subject_text = $subject_map[$subject] ?? 'Autre demande';

            // Préparation de la requête SQL avec requête préparée (déjà sécurisée contre SQL Injection)
            $sql = "INSERT INTO contact_messages (user_id, sujet, prenom, nom, email, telephone, message, consentement, date_envoi, statut) 
                    VALUES (:user_id, :sujet, :prenom, :nom, :email, :telephone, :message, :consentement, NOW(), 'non_traite')";

            $stmt = $pdo->prepare($sql);

            // Exécution de la requête avec les paramètres sécurisés
            $stmt->execute([
                ':user_id' => $user_id,
                ':sujet' => $subject_text,
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':message' => $message,
                ':consentement' => $consent
            ]);

            // Suppression du token CSRF après usage
            unset($_SESSION['csrf_token']);

            // Message de succès
            $_SESSION['success'] = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
        } catch (PDOException $e) {
            // En cas d'erreur lors de l'insertion
            $_SESSION['error'] = "Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer.";
            error_log("Erreur d'envoi de contact: " . $e->getMessage());
        }
    } else {
        // Stockage des erreurs dans la session
        $_SESSION['error'] = "Votre message n'a pas pu être envoyé : " . implode(", ", $errors);
    }

    // Redirection vers la page de profil
    header("Location: profile.php#contact");
    exit;
} else {
    // Si le formulaire n'a pas été soumis, redirection vers la page de profil
    header("Location: profile.php");
    exit;
}
?>
