<?php
require 'config/database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE token = :token AND verified = 0");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        $update = $pdo->prepare("UPDATE users SET verified = 1 WHERE id = :id");
        $update->execute([':id' => $user['id']]);
        echo "Votre compte a été activé avec succès ! <a href='login.php'>Se connecter</a>";
    } else {
        echo "Lien invalide ou compte déjà activé.";
    }
} else {
    echo "Token non fourni.";
}
?>