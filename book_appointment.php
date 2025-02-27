<?php
session_start();
require_once 'config/database.php';

// Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Vérifier la présence et la validité du token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Échec de vérification CSRF. Opération annulée.";
    header("Location: profile.php");
    exit;
}

// Vérifier si la requête est bien de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$date = trim($_POST['date']);
$time = trim($_POST['time']);
$datetime = "$date $time";

// Validation de la date et de l'heure
if (empty($date) || empty($time)) {
    $_SESSION['error'] = "Veuillez sélectionner une date et une heure.";
    header("Location: profile.php");
    exit;
}

// Vérifier si la date et l'heure sont valides et dans le futur
$current_datetime = new DateTime();
$selected_datetime = DateTime::createFromFormat('Y-m-d H:i', "$date $time");

if (!$selected_datetime || $selected_datetime < $current_datetime) {
    $_SESSION['error'] = "La date et l'heure doivent être valides et dans le futur.";
    header("Location: profile.php");
    exit;
}

// Vérifier si le créneau est déjà réservé
$sql_check = "SELECT COUNT(*) FROM rendez_vous WHERE date_jour = :date AND heure_rdv = :time";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->bindParam(':date', $date, PDO::PARAM_STR);
$stmt_check->bindParam(':time', $time, PDO::PARAM_STR);
$stmt_check->execute();
$count = $stmt_check->fetchColumn();

if ($count > 0) {
    $_SESSION['error'] = "Ce créneau est déjà réservé. Veuillez choisir un autre horaire.";
    header("Location: profile.php");
    exit;
}

// Insérer le rendez-vous
try {
    $sql_insert = "INSERT INTO rendez_vous (user_id, date_jour, heure_rdv) VALUES (:user_id, :date, :time)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt_insert->bindParam(':time', $time, PDO::PARAM_STR);

    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "Rendez-vous pris avec succès !";
    } else {
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
    }
} catch (PDOException $e) {
    error_log("Erreur SQL (Prise de rendez-vous) : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur technique est survenue. Veuillez réessayer plus tard.";
}

// Redirection vers la page de profil
header("Location: profile.php");
exit;
?>