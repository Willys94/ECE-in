<?php
session_start();
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit();
}
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$nom_base = "ecein";
$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $nom_base);
if ($connexion->connect_error) {
    die("Connexion échouée : " . $connexion->connect_error);
}
$id_utilisateur = $_SESSION['id_utilisateur'];

// Traitement ajout notification officielle (admin)
if (isset($_POST['ajouter_notification_officielle']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $titre = $connexion->real_escape_string($_POST['titre']);
    $description = $connexion->real_escape_string($_POST['description']);
    $date_evenement = !empty($_POST['date_evenement']) ? $connexion->real_escape_string($_POST['date_evenement']) : null;
    $sql = "INSERT INTO notification_officielle (titre, description, date_evenement) VALUES ('$titre', '$description', " . ($date_evenement ? "'$date_evenement'" : "NULL") . ")";
    if ($connexion->query($sql)) {
        echo '<div class="alert alert-success">Notification officielle créée !</div>';
    } else {
        echo '<div class="alert alert-danger">Erreur : ' . $connexion->error . '</div>';
    }
}

// Notifications officielles (événements)
$notifs_officielles = $connexion->query("SELECT * FROM notification_officielle ORDER BY date_evenement DESC");

// Notifications personnelles (réseau)
$notifs_perso = $connexion->query("SELECT * FROM notification WHERE id_utilisateur = $id_utilisateur ORDER BY date_notif DESC");

// Marquer une notification comme lue (supprimer)
if (isset($_POST['lire_notif']) && isset($_POST['id_notification'])) {
    $id_notif = intval($_POST['id_notification']);
    $connexion->query("DELETE FROM notification WHERE id_notification = $id_notif AND id_utilisateur = $id_utilisateur");
    header("Location: notifications.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications - ECE In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><strong>ECE In</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="reseau.php">Mon Réseau</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Vous</a></li>
                    <li class="nav-item"><a class="nav-link active" href="notifications.php">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="messagerie.php">Messagerie</a></li>
                    <li class="nav-item"><a class="nav-link" href="emplois.php">Emplois</a></li>
                    <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Formulaire admin pour notification officielle -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Créer une notification officielle</h2>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Titre</label>
                                <input type="text" name="titre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date de l'événement (optionnel)</label>
                                <input type="datetime-local" name="date_evenement" class="form-control">
                            </div>
                            <button type="submit" name="ajouter_notification_officielle" class="btn btn-primary">Publier</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Notifications officielles -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0">Notifications officielles</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($notifs_officielles->num_rows > 0): ?>
                            <?php while ($notif = $notifs_officielles->fetch_assoc()): ?>
                                <div class="border-start border-primary ps-3 mb-3">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($notif['titre']); ?></h5>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($notif['description'])); ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i><?php echo date("d/m/Y H:i", strtotime($notif['date_evenement'])); ?>
                                    </small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aucune notification officielle</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notifications personnelles -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0">Notifications personnelles</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($notifs_perso->num_rows > 0): ?>
                            <?php while ($notif = $notifs_perso->fetch_assoc()): ?>
                                <div class="border-start border-primary ps-3 mb-3">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($notif['titre']); ?></h5>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($notif['message'])); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?php echo date("d/m/Y H:i", strtotime($notif['date_notif'])); ?>
                                        </small>
                                        <form method="POST" action="notifications.php" class="d-inline">
                                            <input type="hidden" name="id_notification" value="<?php echo $notif['id_notification']; ?>">
                                            <button type="submit" name="lire_notif" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-check me-1"></i>Marquer comme lue
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Aucune notification personnelle</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4 mt-4 mt-auto">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 ECE In - Tous droits réservés</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 