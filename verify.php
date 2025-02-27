<?php
session_start();
require 'config/database.php'; // Connexion à la BDD

$errors = [];
$success = false;

// Vérification si un token est présent dans l'URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Recherche de l'utilisateur avec ce token
        $stmt = $pdo->prepare("SELECT * FROM users WHERE token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier si le compte n'est pas déjà activé
            if ($user['verified'] == 1) {
                $_SESSION['info'] = "Votre compte est déjà activé. Vous pouvez vous connecter.";
            } else {
                // Mettre à jour le statut de vérification et vider le token
                $update_stmt = $pdo->prepare("UPDATE users SET verified = 1, token = '' WHERE id = :id");
                $update_stmt->execute([':id' => $user['id']]);

                if ($update_stmt->rowCount() > 0) {
                    $success = true;
                    $_SESSION['success'] = "Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.";
                } else {
                    $errors[] = "Une erreur est survenue lors de l'activation de votre compte.";
                }
            }
        } else {
            $errors[] = "Token invalide ou expiré.";
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur de base de données: " . $e->getMessage();
    }
} else {
    $errors[] = "Aucun token fourni.";
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
                            <a class="nav-link" href="index.php">HOME</a>
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
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4 font-resto">Vérification du compte</h2>

                        <?php if (isset($_SESSION['info'])): ?>
                            <div class="alert alert-info" role="alert">
                                <?php
                                echo $_SESSION['info'];
                                unset($_SESSION['info']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <?php if ($success || isset($_SESSION['info'])): ?>
                                <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                                <p class="mb-4">Votre compte a été vérifié avec succès.</p>
                            <?php else: ?>
                                <i class="fas fa-times-circle text-danger fa-5x mb-4"></i>
                                <p class="mb-4">Il y a eu un problème lors de la vérification de votre compte.</p>
                            <?php endif; ?>

                            <div class="d-grid">
                                <a href="login.php" class="btn btn-dark btn-lg">Se connecter</a>
                            </div>
                        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>