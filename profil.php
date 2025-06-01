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
$requete = "SELECT * FROM utilisateur WHERE id_utilisateur = $id_utilisateur";
$resultat = $connexion->query($requete);
$utilisateur = $resultat->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - ECE In</title>
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
                    <li class="nav-item"><a class="nav-link active" href="profil.php">Vous</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="messagerie.php">Messagerie</a></li>
                    <li class="nav-item"><a class="nav-link" href="emplois.php">Emplois</a></li>
                    <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- En-tête du profil -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <?php if (!empty($utilisateur['image_fond'])): ?>
                            <img src="<?php echo $utilisateur['image_fond']; ?>" alt="Image de fond" class="w-100" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="text-center" style="margin-top: -50px;">
                            <?php if (!empty($utilisateur['photo_profil'])): ?>
                                <img src="<?php echo $utilisateur['photo_profil']; ?>" alt="Photo de profil" class="rounded-circle border border-4 border-white" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-inline-block" style="width: 100px; height: 100px;"></div>
                            <?php endif; ?>
                            <h2 class="mt-3 mb-1"><?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></h2>
                            <p class="text-muted"><?php echo htmlspecialchars($utilisateur['email']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-3">
                            <a href="modifier_profil.php" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Modifier le profil
                            </a>
                            <a href="uploads/mon_cv.pdf" target="_blank" class="btn btn-outline-success">
                                <i class="fas fa-file-pdf me-2"></i>Voir mon CV
                            </a>
                            <a href="albums.php" class="btn btn-outline-info">
                                <i class="fas fa-images me-2"></i>Mes albums photos
                            </a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin.php" class="btn btn-outline-danger">
                                <i class="fas fa-cog me-2"></i>Administration
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Formations -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title h4 mb-4">Formations</h3>
                        <?php
                        $formations = file_exists('formations.xml') ? simplexml_load_file('formations.xml') : null;
                        if ($formations) {
                            $formations_array = [];
                            foreach ($formations->formation as $f) {
                                $formations_array[] = $f;
                            }
                            usort($formations_array, function($a, $b) {
                                return intval($b->annee_fin) - intval($a->annee_fin);
                            });
                            echo '<div class="list-group">';
                            foreach ($formations_array as $formation) {
                                echo '<div class="list-group-item">';
                                echo '<h5 class="mb-1">' . htmlspecialchars($formation->titre) . '</h5>';
                                echo '<p class="mb-1">' . htmlspecialchars($formation->ecole) . ' (' . $formation->annee_debut . ' - ' . $formation->annee_fin . ')</p>';
                                echo '<small class="text-muted">' . htmlspecialchars($formation->description) . '</small>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                        <hr>
                        <h4 class="h5 mb-3">Ajouter une formation</h4>
                        <form method="post" action="ajouter_xml.php" class="needs-validation" novalidate>
                            <input type="hidden" name="type" value="formation">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="titre" class="form-control" placeholder="Titre" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="ecole" class="form-control" placeholder="École" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="annee_debut" class="form-control" placeholder="Année de début" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="annee_fin" class="form-control" placeholder="Année de fin" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="description" class="form-control" placeholder="Description" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Ajouter la formation</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Projets -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title h4 mb-4">Projets</h3>
                        <?php
                        $projets = file_exists('projets.xml') ? simplexml_load_file('projets.xml') : null;
                        if ($projets) {
                            $projets_array = [];
                            foreach ($projets->projet as $p) {
                                $projets_array[] = $p;
                            }
                            usort($projets_array, function($a, $b) {
                                return intval($b->annee) - intval($a->annee);
                            });
                            echo '<div class="list-group">';
                            foreach ($projets_array as $projet) {
                                echo '<div class="list-group-item">';
                                echo '<h5 class="mb-1">' . htmlspecialchars($projet->titre) . '</h5>';
                                echo '<p class="mb-1">' . htmlspecialchars($projet->lieu) . ' (' . $projet->annee . ')</p>';
                                echo '<small class="text-muted">' . htmlspecialchars($projet->description) . '</small>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                        <hr>
                        <h4 class="h5 mb-3">Ajouter un projet</h4>
                        <form method="post" action="ajouter_xml.php" class="needs-validation" novalidate>
                            <input type="hidden" name="type" value="projet">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="titre" class="form-control" placeholder="Titre" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="lieu" class="form-control" placeholder="Lieu" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="annee" class="form-control" placeholder="Année" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="description" class="form-control" placeholder="Description" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Ajouter le projet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Générer CV -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <form method="post" action="generer_cv.php">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-file-download me-2"></i>Générer mon CV
                            </button>
                        </form>
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
    <script>
    // Validation des formulaires Bootstrap
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</body>
</html>
