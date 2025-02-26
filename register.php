<?php
session_start();
require 'config/database.php'; // Connexion à la BDD
require 'send_mail.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $date_naissance = $_POST['date_naissance'];
    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($adresse) || empty($telephone) || empty($email) || empty($password) || empty($password_confirm)) {
        $errors[] = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    } elseif ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    } elseif (!preg_match('/^\d{10}$/', $telephone)) {
        $errors[] = "Le numéro de téléphone doit contenir 10 chiffres.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(50)); // Génération d'un token unique

        try {
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, date_naissance, adresse, telephone, email, password, token) VALUES (:nom, :prenom, :date_naissance, :adresse, :telephone, :email, :password, :token)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':date_naissance' => $date_naissance,
                ':adresse' => $adresse,
                ':telephone' => $telephone,
                ':email' => $email,
                ':password' => $hashed_password,
                ':token' => $token
            ]);

            // Envoi de l'email de confirmation
            $subject = "Activation de votre compte";
            $message = "Cliquez sur ce lien pour activer votre compte : http://localhost/TP-03/verify.php?token=$token";
            sendEmail($email, $subject, $message);

            $_SESSION['success'] = "Un email de confirmation a été envoyé.";
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Cet email est déjà utilisé.";
            } else {
                $errors[] = "Erreur lors de l'inscription.";
            }
        }
    }
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
    <title>HairLab - Inscription</title>
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
                        <h2 class="text-center mb-5 font-resto">Inscription</h2>

                        <!-- Message d'erreur PHP -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <!-- Message de succès PHP -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="row">
                                <!-- Prénom -->
                                <div class="col-md-6 mb-4">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            id="prenom"
                                            name="prenom"
                                            value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>"
                                            required>
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div class="col-md-6 mb-4">
                                    <label for="nom" class="form-label">Nom</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            id="nom"
                                            name="nom"
                                            value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Date de naissance -->
                            <div class="mb-4">
                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <input type="date"
                                        class="form-control"
                                        id="date_naissance"
                                        name="date_naissance"
                                        value="<?php echo isset($_POST['date_naissance']) ? htmlspecialchars($_POST['date_naissance']) : ''; ?>"
                                        required>
                                </div>
                            </div>

                            <!-- Adresse postale -->
                            <div class="mb-4">
                                <label for="adresse" class="form-label">Adresse postale</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        id="adresse"
                                        name="adresse"
                                        value="<?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?>"
                                        required>
                                </div>
                            </div>

                            <!-- Numéro de téléphone -->
                            <div class="mb-4">
                                <label for="telephone" class="form-label">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel"
                                        class="form-control"
                                        id="telephone"
                                        name="telephone"
                                        value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>"
                                        pattern="[0-9]{10}"
                                        placeholder="0123456789"
                                        required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                        required>
                                </div>
                            </div>

                            <!-- Mot de passe -->
                            <div class="mb-4">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        required>
                                </div>
                                <div class="form-text">
                                    Le mot de passe doit contenir au moins 8 caractères, incluant des lettres majuscules, minuscules et des chiffres.
                                </div>
                            </div>

                            <!-- Confirmation du mot de passe -->
                            <div class="mb-4">
                                <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control"
                                        id="password_confirm"
                                        name="password_confirm"
                                        required>
                                </div>
                            </div>

                            <!-- Conditions d'utilisation -->
                            <div class="mb-4 form-check">
                                <input type="checkbox"
                                    class="form-check-input"
                                    id="terms"
                                    name="terms"
                                    required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a> et la <a href="#" class="text-decoration-none">politique de confidentialité</a>
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-dark btn-lg">S'inscrire</button>
                            </div>

                            <div class="text-center mt-4">
                                <p class="mb-0">Vous avez déjà un compte ?
                                    <a href="login.php" class="text-decoration-none">Se connecter</a>
                                </p>
                            </div>
                        </form>
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