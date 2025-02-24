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
    <title>HairLab</title>
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
        <div class="row justify-content-center mb-5">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-5 font-resto">Connexion</h2>

                        <form action="" method="POST">
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
                                        required>
                                </div>
                            </div>
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
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="login" class="btn btn-dark btn-lg">Se connecter</button>
                            </div>
                            <div class="text-center mt-4">
                                <a href="#" class="text-muted">Mot de passe oublié ?</a>
                            </div>
                            <div class="text-center mt-3">
                                <p class="mb-0">Vous n'avez pas de compte ?
                                    <a href="register.php" class="text-decoration-none">S'inscrire</a>
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