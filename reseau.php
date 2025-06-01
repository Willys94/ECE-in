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
$message = "";

// Traitement des demandes reçues : accepter ou refuser
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['source'])) {
    $source = intval($_POST['source']);
    if ($_POST['action'] === 'accepter') {
        $connexion->query("UPDATE connexion SET statut = 'accepte' WHERE id_utilisateur_source = $source AND id_utilisateur_cible = $id_utilisateur");
        $message = "Demande acceptée.";
    } elseif ($_POST['action'] === 'refuser') {
        $connexion->query("DELETE FROM connexion WHERE id_utilisateur_source = $source AND id_utilisateur_cible = $id_utilisateur");
        $message = "Demande refusée.";
    }
}

// Traitement de la demande de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cible'])) {
    $cible = intval($_POST['cible']);
    $verif = $connexion->query("SELECT * FROM connexion WHERE id_utilisateur_source = $id_utilisateur AND id_utilisateur_cible = $cible");
    if ($verif->num_rows === 0) {
        $connexion->query("INSERT INTO connexion (id_utilisateur_source, id_utilisateur_cible, statut) VALUES ($id_utilisateur, $cible, 'en_attente')");
        $message = "Demande de connexion envoyée.";
    } else {
        $message = "Demande déjà envoyée ou utilisateur déjà connecté.";
    }
}

$utilisateurs = $connexion->query("SELECT * FROM utilisateur WHERE id_utilisateur != $id_utilisateur");
$mes_demandes = $connexion->query("SELECT u.prenom, u.nom FROM connexion c JOIN utilisateur u ON c.id_utilisateur_cible = u.id_utilisateur WHERE c.id_utilisateur_source = $id_utilisateur AND c.statut = 'en_attente'");
$demandes_recues = $connexion->query("SELECT u.id_utilisateur, u.prenom, u.nom FROM connexion c JOIN utilisateur u ON c.id_utilisateur_source = u.id_utilisateur WHERE c.id_utilisateur_cible = $id_utilisateur AND c.statut = 'en_attente'");
$connexions = $connexion->query("SELECT u.prenom, u.nom FROM utilisateur u WHERE u.id_utilisateur IN (
    SELECT id_utilisateur_source FROM connexion WHERE id_utilisateur_cible = $id_utilisateur AND statut = 'accepte'
    UNION
    SELECT id_utilisateur_cible FROM connexion WHERE id_utilisateur_source = $id_utilisateur AND statut = 'accepte'
)");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Réseau - ECE In</title>
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
                    <li class="nav-item"><a class="nav-link active" href="reseau.php">Mon Réseau</a></li>
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
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Utilisateurs disponibles -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4">Utilisateurs disponibles</h2>
                        <?php while ($user = $utilisateurs->fetch_assoc()): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                                <?php if (!empty($user['photo_profil'])): ?>
                                    <img src="<?php echo $user['photo_profil']; ?>" alt="Photo de profil" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                </div>
                                <form action="reseau.php" method="POST">
                                    <input type="hidden" name="cible" value="<?php echo $user['id_utilisateur']; ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Ajouter</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Demandes et connexions -->
            <div class="col-md-6">
                <!-- Demandes en attente -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4">Demandes en attente</h2>
                        <?php while ($demande = $mes_demandes->fetch_assoc()): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <span><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></span>
                                <span class="badge bg-warning text-dark ms-2">En attente</span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Demandes reçues -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4">Demandes reçues</h2>
                        <?php while ($recu = $demandes_recues->fetch_assoc()): ?>
                            <div class="d-flex align-items-center justify-content-between mb-3 p-2 border-bottom">
                                <div>
                                    <strong><?php echo htmlspecialchars($recu['prenom'] . ' ' . $recu['nom']); ?></strong>
                                    <small class="d-block text-muted">Vous a envoyé une demande</small>
                                </div>
                                <div class="btn-group">
                                    <form action="reseau.php" method="POST" class="d-inline">
                                        <input type="hidden" name="source" value="<?php echo $recu['id_utilisateur']; ?>">
                                        <input type="hidden" name="action" value="accepter">
                                        <button type="submit" class="btn btn-success btn-sm">Accepter</button>
                                    </form>
                                    <form action="reseau.php" method="POST" class="d-inline ms-1">
                                        <input type="hidden" name="source" value="<?php echo $recu['id_utilisateur']; ?>">
                                        <input type="hidden" name="action" value="refuser">
                                        <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Mes connexions -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4">Mes connexions</h2>
                        <?php while ($ami = $connexions->fetch_assoc()): ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-check text-success me-2"></i>
                                <span><?php echo htmlspecialchars($ami['prenom'] . ' ' . $ami['nom']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4 mt-4">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 ECE In - Tous droits réservés</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
