<?php
session_start();
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
    <title>HairLab</title>
</head>

<body>

    <section class="hero-section">
        <header>
            <!-- MENU NAVIGATION-->
            <nav class="navbar navbar-expand-lg navbar-dark p-5">
                <div class="container d-flex justify-content-between align-items-center">
                    <a class="navbar-brand fw-bold fs-1 font-resto" href="#">HairLab</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav gap-4 fs-5">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">HOME</a>
                            </li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="profile.php">MON COMPTE</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-1"></i>DÉCONNEXION
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="login.php">CONNEXION</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- FIN MENU NAVIGATION-->
        </header>

        <section class="flex-grow-1 d-flex justify-content-center align-items-center content-section">
            <div class="flex-grow-1 d-flex justify-content-center align-items-center text-center flex-column">
                <h2 class="font-resto content-title text-white">Welcome to</h2>
                <h1 class="font-roboto content-subtitle text-white">HairLab</h1>
                <button class="p-3 p-md-3 ps-md-5 pe-md-5 ps-3 pe-3 rounded-pill book-button fs-9">
                    <a class="nav-link" href="<?php echo isset($_SESSION['user_id']) ? 'profile.php' : 'login.php'; ?>">
                        PRENDRE RENDEZ-VOUS
                    </a>
                </button>
            </div>
        </section>
    </section>

    <main></main>

    <footer></footer>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

</html>