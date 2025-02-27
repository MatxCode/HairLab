<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $datetime = $date . ' ' . $time;

    // Vérifier si le créneau est déjà réservé
    $sql_check = "SELECT COUNT(*) FROM rendez_vous WHERE date_heure = :datetime";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':datetime', $datetime, PDO::PARAM_STR);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        $_SESSION['error'] = "Ce créneau est déjà réservé. Veuillez choisir un autre horaire.";
        header("Location: profile.php");
        exit;
    }

    // Insérer le rendez-vous
    $sql_insert = "INSERT INTO rendez_vous (user_id, date_heure) VALUES (:user_id, :datetime)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':datetime', $datetime, PDO::PARAM_STR);
    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "Rendez-vous pris avec succès !";
    } else {
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
    }

    header("Location: profile.php");
    exit;
} else {
    header("Location: profile.php");
    exit;
}
