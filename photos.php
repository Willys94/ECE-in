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

// Liste de tous les albums
$albums = $connexion->query("SELECT a.*, u.prenom, u.nom FROM album a JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur ORDER BY a.date_creation DESC");

// Affichage du contenu d'un album
$photos = null;
$album_info = null;
if (isset($_GET['id_album'])) {
    $id_album = intval($_GET['id_album']);
    $album_info = $connexion->query("SELECT a.*, u.prenom, u.nom FROM album a JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur WHERE a.id_album = $id_album")->fetch_assoc();
    $photos = $connexion->query("SELECT p.* FROM album_photo ap JOIN publication p ON ap.id_publication = p.id_publication WHERE ap.id_album = $id_album");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tous les albums photos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
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
                    <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="messagerie.php">Messagerie</a></li>
                    <li class="nav-item"><a class="nav-link" href="emplois.php">Emplois</a></li>
                    <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Tous les albums photos</h1>
                        <div class="row">
                            <?php while ($album = $albums->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($album['nom']); ?></h5>
                                            <p class="card-text">par <?php echo htmlspecialchars($album['prenom'] . ' ' . $album['nom']); ?></p>
                                            <a href="photos.php?id_album=<?php echo $album['id_album']; ?>" class="btn btn-outline-primary">Voir l'album</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($album_info): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4"><?php echo htmlspecialchars($album_info['nom']); ?></h2>
                        <p class="text-muted mb-4">par <?php echo htmlspecialchars($album_info['prenom'] . ' ' . $album_info['nom']); ?></p>
                        <div class="photo-grid">
                            <?php while ($photo = $photos->fetch_assoc()): ?>
                                <div class="photo-item">
                                    <img src="<?php echo $photo['media']; ?>" alt="Photo" class="img-fluid">
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <a href="photos.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-2"></i>Retour à tous les albums</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-light py-4 mt-4">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 ECE In - Tous droits réservés</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 