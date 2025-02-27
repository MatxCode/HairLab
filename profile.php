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
$sql_appointments = "SELECT id, date_heure FROM rendez_vous WHERE user_id = :user_id ORDER BY date_heure ASC";
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
$sql_taken_slots = "SELECT date_heure FROM rendez_vous";
$stmt_taken_slots = $pdo->query($sql_taken_slots);
$takenSlots = [];
while ($row = $stmt_taken_slots->fetch(PDO::FETCH_ASSOC)) {
    $takenSlots[] = date("H:i", strtotime($row['date_heure']));
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>HairLab - Profil</title>
</head>

<body>
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
                            <a class="nav-link" href="logout.php">DÉCONNEXION</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- FIN MENU NAVIGATION-->
    </header>

    <main class="container py-5">
        <h2 class="mb-4">Tableau de bord</h2>
        <div class="card mb-4">
            <div class="card-body">
                <h3>Informations personnelles</h3>
                <form action="update_profile.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo $user['date_naissance']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo htmlspecialchars($user['adresse']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h3>Rendez-vous</h3>
                <form action="book_appointment.php" method="POST">
                    <div class="mb-3">
                        <label for="date" class="form-label">Choisir une date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Choisir une heure</label>
                        <select class="form-control" id="time" name="time" required>
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?php echo $slot; ?>" <?php echo in_array($slot, $takenSlots) ? 'disabled' : ''; ?>><?php echo $slot; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Prendre rendez-vous</button>
                </form>
                <hr>
                <ul class="list-group">
                    <?php if (empty($appointments)): ?>
                        <li class="list-group-item">Aucun rendez-vous programmé.</li>
                    <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo date("d/m/Y H:i", strtotime($appointment['date_heure'])); ?>
                                <a href="cancel_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-danger btn-sm">Annuler</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body text-center">
                <h3>Suppression du compte</h3>
                <p>Cette action est irréversible.</p>
                <a href="delete_account.php" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">Supprimer mon compte</a>
            </div>
        </div>
    </main>

    <script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>