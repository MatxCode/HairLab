<?php
session_start();
require_once 'config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupération des informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT nom, prenom, date_naissance, adresse, telephone, email FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupération des rendez-vous de l'utilisateur
$sql_appointments = "SELECT id, date_jour, heure_rdv FROM rendez_vous WHERE user_id = :user_id ORDER BY date_jour ASC, heure_rdv ASC";
$stmt_appointments = $pdo->prepare($sql_appointments);
$stmt_appointments->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_appointments->execute();
$appointments = $stmt_appointments->fetchAll(PDO::FETCH_ASSOC);

function generateTimeSlots()
{
    $slots = [];
    $start = strtotime("09:00");
    $end = strtotime("17:00");
    while ($start <= $end) {
        $slots[] = date("H:i", $start);
        $start = strtotime("+30 minutes", $start);
    }
    return $slots;
}
$timeSlots = generateTimeSlots();

// Récupération des créneaux déjà réservés
$sql_taken_slots = "SELECT date_jour, heure_rdv FROM rendez_vous";
$stmt_taken_slots = $pdo->query($sql_taken_slots);
$takenSlots = [];
while ($row = $stmt_taken_slots->fetch(PDO::FETCH_ASSOC)) {
    $takenSlots[$row['date_jour']][] = $row['heure_rdv'];
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
    <title>HairLab - Mon Profil</title>
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
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>DÉCONNEXION
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- FIN MENU NAVIGATION-->
    </header>

    <main class="container py-5 flex-grow-1">
        <h2 class="text-center mb-5 font-resto">Mon Profil</h2>

        <!-- Messages d'erreur/succès PHP -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
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

        <!-- Onglets -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-black" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                    <i class="fas fa-user me-2"></i>Informations personnelles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-black" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                    <i class="fas fa-calendar-alt me-2"></i>Mes rendez-vous
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-black" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">
                    <i class="fas fa-cog me-2"></i>Paramètres du compte
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Onglet informations personnelles -->
            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h3 class="card-title mb-4 font-resto">Mes informations</h3>
                        <form action="update_profile.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <div class="row">
                                <!-- Prénom -->
                                <div class="col-md-6 mb-4">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="prenom" name="prenom"
                                            value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                                    </div>
                                </div>

                                <!-- Nom -->
                                <div class="col-md-6 mb-4">
                                    <label for="nom" class="form-label">Nom</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="nom" name="nom"
                                            value="<?php echo htmlspecialchars($user['nom']); ?>" required>
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
                                    <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                                        value="<?php echo $user['date_naissance']; ?>">
                                </div>
                            </div>

                            <!-- Adresse postale -->
                            <div class="mb-4">
                                <label for="adresse" class="form-label">Adresse postale</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" class="form-control" id="adresse" name="adresse"
                                        value="<?php echo htmlspecialchars($user['adresse']); ?>">
                                </div>
                            </div>

                            <!-- Numéro de téléphone -->
                            <div class="mb-4">
                                <label for="telephone" class="form-label">Numéro de téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="telephone" name="telephone"
                                        pattern="[0-9]{10}" placeholder="0123456789"
                                        value="<?php echo htmlspecialchars($user['telephone']); ?>">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-lg">
                                    <i class="fas fa-save me-2"></i>Mettre à jour mes informations
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onglet rendez-vous -->
            <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                <div class="row">
                    <!-- Prendre rendez-vous -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body p-5">
                                <h3 class="card-title mb-4 font-resto">Prendre rendez-vous</h3>
                                <form action="book_appointment.php" method="POST">
                                    <div class="mb-4">
                                        <label for="date" class="form-label">Choisir une date</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-day"></i>
                                            </span>
                                            <input type="date" class="form-control" id="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="time" class="form-label">Choisir une heure</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <select class="form-control" id="time" name="time" required>
                                                <?php foreach ($timeSlots as $slot): ?>
                                                    <option value="<?php echo $slot; ?>" data-date="<?php echo $date ?? ''; ?>">
                                                        <?php echo $slot; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-dark btn-lg">
                                            <i class="fas fa-calendar-plus me-2"></i>Réserver ce créneau
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mes rendez-vous -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body p-5">
                                <h3 class="card-title mb-4 font-resto">Mes rendez-vous</h3>
                                <?php if (empty($appointments)): ?>
                                    <div class="alert alert-info text-center py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <p class="mb-0">Vous n'avez aucun rendez-vous programmé.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($appointments as $appointment): ?>
                                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center mb-2 border rounded">
                                                <div>
                                                    <i class="fas fa-calendar-check me-2 text-success"></i>
                                                    <strong><?php echo date("d/m/Y", strtotime($appointment['date_jour'])); ?></strong>
                                                    à
                                                    <span><?php echo $appointment['heure_rdv']; ?></span>
                                                </div>
                                                <a href="cancel_appointment.php?id=<?php echo $appointment['id']; ?>"
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                                                    <i class="fas fa-times me-1"></i>Annuler
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet paramètres du compte -->
            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h3 class="card-title mb-4 font-resto">Paramètres du compte</h3>

                        <!-- Changer le mot de passe -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-lock me-2"></i>Changer le mot de passe
                                </h4>
                                <form action="change_password.php" method="POST">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>Mettre à jour le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Suppression du compte -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h4 class="card-title text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Zone dangereuse
                                </h4>
                                <p class="card-text">La suppression de votre compte est une action définitive. Toutes vos données personnelles et vos rendez-vous seront supprimés de façon permanente.</p>
                                <a href="delete_account.php" class="btn btn-outline-danger"
                                    onclick="return confirm('Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est irréversible et toutes vos données seront perdues.');">
                                    <i class="fas fa-trash-alt me-2"></i>Supprimer mon compte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
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