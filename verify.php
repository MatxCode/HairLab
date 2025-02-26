<?php
session_start();
require 'config/database.php'; // Connexion à la BDD

$token = isset($_GET['token']) ? $_GET['token'] : '';
$verified = false;
$message = '';

if (!empty($token)) {
    try {
        // Vérification de l'existence du token
        $stmt = $pdo->prepare("SELECT * FROM users WHERE token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si le compte n'est pas déjà vérifié
            if ($user['verified'] == 0) {
                // Mise à jour du statut de vérification
                $update = $pdo->prepare("UPDATE users SET verified = 1, token = NULL WHERE id = :id");
                $update->execute([':id' => $user['id']]);

                $verified = true;
                $message = "Votre compte a été vérifié avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $verified = true;
                $message = "Votre compte a déjà été vérifié. Vous pouvez vous connecter.";
            }
        } else {
            $message = "Lien de vérification invalide ou expiré.";
        }
    } catch (PDOException $e) {
        $message = "Une erreur est survenue lors de la vérification de votre compte.";
    }
} else {
    $message = "Aucun token de vérification n'a été fourni.";
}
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="assets/js/script.js"></script>
    <title>HairLab - Vérification du compte</title>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <!-- MENU NAVIGATION-->
        <nav class="navbar navbar-expand-lg navbar-light p-5">
            <div class="container d-flex justify-content-between align-items-center">
                <a class="navbar-brand fw-bold fs-1 font-resto" href="#">HairLab</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav justify-content-center gap-4 fs-5">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">HOME</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">MON COMPTE</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- FIN MENU NAVIGATION-->
    </header>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-5 font-resto">Vérification du compte</h2>

                        <div class="text-center mb-4">
                            <?php if ($verified): ?>
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success fa-5x"></i>
                                </div>
                            <?php else: ?>
                                <div class="mb-4">
                                    <i class="fas fa-exclamation-circle text-danger fa-5x"></i>
                                </div>
                            <?php endif; ?>

                            <div class="alert <?php echo $verified ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        </div>

                        <div class="d-grid">
                            <?php if ($verified): ?>
                                <a href="login.php" class="btn btn-dark btn-lg">Se connecter</a>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-dark btn-lg">Retour à l'inscription</a>
                            <?php endif; ?>
                        </div>

                        <?php if (!$verified): ?>
                            <div class="text-center mt-4">
                                <p>Si vous rencontrez des problèmes lors de la vérification de votre compte, veuillez
                                    <a href="contact.php" class="text-decoration-none">contacter notre support</a>.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- Section Contact -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <h3 class="font-resto text-brown mb-4 mx-md-4">Contact</h3>
                    <ul>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2 text-brown"></i>
                            4 Rue de la Coiffure, 75000 Paris
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-phone me-2 text-brown"></i>
                            +33 1 23 45 67 89
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fas fa-envelope me-2 text-brown"></i>
                            contact@hairlab.fr
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-brown"></i>
                            Mardi - Dimanche : 9h00 - 19h00
                        </li>
                    </ul>
                </div>

                <!-- Section Suivez-nous -->
                <div class="col-md-6">
                    <h3 class="font-resto text-brown mb-4">Suivez-nous</h3>
                    <div class="d-flex gap-4 social-icons">
                        <a href="#" class="text-white social-link">
                            <i class="fab fa-facebook-f fa-2x"></i>
                        </a>
                        <a href="#" class="text-white social-link">
                            <i class="fab fa-instagram fa-2x"></i>
                        </a>
                        <a href="#" class="text-white social-link">
                            <i class="fab fa-twitter fa-2x"></i>
                        </a>
                        <a href="#" class="text-white social-link">
                            <i class="fab fa-linkedin-in fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-4 pt-4 border-top">
                <p class="mb-0">&copy; 2024 HairLab. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous">
</script>

</html>