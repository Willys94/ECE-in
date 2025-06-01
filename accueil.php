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

// Traitement de suppression
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['supprimer_id'])) {
    $id_pub = intval($_POST['supprimer_id']);
    $verif = $connexion->query("SELECT * FROM publication WHERE id_publication = $id_pub");
    $pub = $verif->fetch_assoc();
    if ($pub && ($pub['id_utilisateur'] == $_SESSION['id_utilisateur'] || $_SESSION['role'] == 'admin')) {
        if (!empty($pub['media']) && file_exists($pub['media'])) {
            unlink($pub['media']);
        }
        $connexion->query("DELETE FROM publication WHERE id_publication = $id_pub");
    }
    header("Location: accueil.php");
    exit();
}

// Traitement de modification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['modifier_id'])) {
    $id_pub = intval($_POST['modifier_id']);
    $texte_modifie = $connexion->real_escape_string($_POST['texte_modifie']);
    $visibilite_modifiee = $connexion->real_escape_string($_POST['visibilite_modifiee']);
    $media_modifie = '';

    $verif = $connexion->query("SELECT * FROM publication WHERE id_publication = $id_pub");
    $pub = $verif->fetch_assoc();
    if ($pub && ($pub['id_utilisateur'] == $_SESSION['id_utilisateur'] || $_SESSION['role'] == 'admin')) {
        // Gestion du média
        if (!empty($_FILES['media_modifie']['name'])) {
            // Supprimer l'ancien fichier si existant
            if (!empty($pub['media']) && file_exists($pub['media'])) {
                unlink($pub['media']);
            }
            $dossier_upload = "uploads/";
            $fichier_temp = $_FILES['media_modifie']['tmp_name'];
            $nom_fichier = basename($_FILES['media_modifie']['name']);
            $media_modifie = $dossier_upload . time() . "_" . $nom_fichier;
            move_uploaded_file($fichier_temp, $media_modifie);
        } else {
            $media_modifie = $pub['media'];
        }
        $connexion->query("UPDATE publication SET texte = '$texte_modifie', visibilite = '$visibilite_modifiee', media = '$media_modifie' WHERE id_publication = $id_pub");
    }
    header("Location: accueil.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["texte_publication"])) {
    $texte = $connexion->real_escape_string($_POST['texte_publication']);
    $visibilite = $connexion->real_escape_string($_POST['visibilite']);
    $chemin_media = "";

    if (!empty($_FILES['media']['name'])) {
        $dossier_upload = "uploads/";
        $fichier_temp = $_FILES['media']['tmp_name'];
        $nom_fichier = basename($_FILES['media']['name']);
        $chemin_media = $dossier_upload . time() . "_" . $nom_fichier;
        move_uploaded_file($fichier_temp, $chemin_media);
    }

    $id_utilisateur = $_SESSION['id_utilisateur'];
    $requete = "INSERT INTO publication (id_utilisateur, texte, media, date_pub, visibilite) VALUES ($id_utilisateur, '$texte', '$chemin_media', NOW(), '$visibilite')";
    if ($connexion->query($requete)) {
        header("Location: accueil.php");
        exit();
    } else {
        echo "Erreur lors de la publication : " . $connexion->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $id_publication = intval($_POST['id_publication']);
    $id_utilisateur = $_SESSION['id_utilisateur'];
    
    switch ($_POST['action']) {
        case 'reaction':
            $type_reaction = $connexion->real_escape_string($_POST['type_reaction']);
            $verif = $connexion->query("SELECT * FROM reaction WHERE id_publication = $id_publication AND id_utilisateur = $id_utilisateur");
            if ($verif->num_rows === 0) {
                $connexion->query("INSERT INTO reaction (id_publication, id_utilisateur, type_reaction) VALUES ($id_publication, $id_utilisateur, '$type_reaction')");
            } else {
                $reaction = $verif->fetch_assoc();
                if ($reaction['type_reaction'] === $type_reaction) {
                    // Si l'utilisateur clique sur la même réaction, on la retire
                    $connexion->query("DELETE FROM reaction WHERE id_publication = $id_publication AND id_utilisateur = $id_utilisateur");
                } else {
                    // Sinon on met à jour le type de réaction
                    $connexion->query("UPDATE reaction SET type_reaction = '$type_reaction' WHERE id_publication = $id_publication AND id_utilisateur = $id_utilisateur");
                }
            }
            break;
            
        case 'commenter':
            $commentaire = $connexion->real_escape_string($_POST['commentaire']);
            $connexion->query("INSERT INTO commentaire (id_publication, id_utilisateur, texte, date_commentaire) VALUES ($id_publication, $id_utilisateur, '$commentaire', NOW())");
            break;
            
        case 'partager':
            $connexion->query("INSERT INTO partage (id_publication, id_utilisateur, date_partage) VALUES ($id_publication, $id_utilisateur, NOW())");
            break;
    }
    header("Location: accueil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ECE In - Accueil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="accueil.php"><strong>ECE In</strong></a>
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
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cog me-1"></i>Administration</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white rounded shadow-sm p-4 mb-4">
                    <h1 class="mb-1">Bienvenue, <?php echo $_SESSION['prenom']; ?> !</h1>
                    <p class="text-muted mb-0">Le réseau social professionnel de l'ECE Paris</p>
                </div>
            </div>
        </div>
        <section class="evenement-semaine mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Évènement de la semaine</h2>
                    <div class="carrousel">
                        <img src="images/evenement1.jpg" alt="Séminaire ECE" class="img-fluid rounded mb-2">
                        <p class="mb-0">Séminaire sur l'IA organisé par ECE Paris - 30 mai 2025</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="fil-actualite mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Fil d'actualité</h2>
                    <form action="accueil.php" method="POST" enctype="multipart/form-data" class="mb-4">
                        <div class="mb-3">
                            <textarea name="texte_publication" class="form-control" placeholder="Exprimez-vous..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="media" class="form-label">Ajouter une photo/vidéo :</label>
                            <input type="file" name="media" accept="image/*,video/*" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="visibilite" class="form-label">Visibilité :</label>
                            <select name="visibilite" class="form-select" required>
                                <option value="public">Public</option>
                                <option value="amis">Amis uniquement</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Publier</button>
                    </form>
                    <div class="publications">
                        <?php
                        $id_utilisateur = $_SESSION['id_utilisateur'];
                        $requete = "SELECT p.*, u.nom, u.prenom, u.photo_profil 
                                    FROM publication p 
                                    JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur 
                                    WHERE p.visibilite = 'public' 
                                    OR p.id_utilisateur = $id_utilisateur 
                                    OR (p.visibilite = 'amis' AND (
                                        p.id_utilisateur IN (
                                            SELECT id_utilisateur_source FROM connexion 
                                            WHERE id_utilisateur_cible = $id_utilisateur AND statut = 'accepte'
                                            UNION
                                            SELECT id_utilisateur_cible FROM connexion 
                                            WHERE id_utilisateur_source = $id_utilisateur AND statut = 'accepte'
                                        )
                                    ))
                                    ORDER BY p.date_pub DESC";
                        $resultat = $connexion->query($requete);
                        while ($ligne = $resultat->fetch_assoc()) {
                            echo '<div class="card mb-4">';
                            echo '<div class="card-body">';
                            
                            // En-tête de la publication
                            echo '<div class="d-flex align-items-center mb-3">';
                            if (!empty($ligne['photo_profil'])) {
                                echo '<img src="' . $ligne['photo_profil'] . '" alt="Photo de profil" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;">';
                            }
                            echo '<div>';
                            echo '<h5 class="card-title mb-0">' . htmlspecialchars($ligne['prenom'] . ' ' . $ligne['nom']) . '</h5>';
                            echo '<small class="text-muted">' . ($ligne['visibilite'] == 'public' ? 'Public' : 'Amis uniquement') . ' • ' . $ligne['date_pub'] . '</small>';
                            echo '</div>';
                            echo '</div>';
                            
                            // Contenu de la publication
                            echo '<p class="card-text">' . htmlspecialchars($ligne['texte']) . '</p>';
                            
                            // Média
                            if (!empty($ligne['media'])) {
                                if (str_contains($ligne['media'], '.mp4')) {
                                    echo '<video controls class="img-fluid rounded mb-3" style="max-width: 300px;"><source src="' . $ligne['media'] . '" type="video/mp4"></video>';
                                } else {
                                    echo '<div class="text-center mb-3">';
                                    echo '<img src="' . $ligne['media'] . '" alt="media" class="img-fluid rounded" style="max-width: 300px; max-height: 300px; object-fit: contain;">';
                                    echo '</div>';
                                    // Bouton Ajouter à un album
                                    echo '<form method="POST" action="ajouter_photo_album.php" class="mb-3">';
                                    echo '<input type="hidden" name="id_publication" value="' . $ligne['id_publication'] . '">';
                                    echo '<div class="input-group">';
                                    echo '<select name="id_album" class="form-select" required>';
                                    echo '<option value="">Ajouter à un album...</option>';
                                    $albums = $connexion->query("SELECT * FROM album WHERE id_utilisateur = " . $_SESSION['id_utilisateur']);
                                    while ($alb = $albums->fetch_assoc()) {
                                        echo '<option value="' . $alb['id_album'] . '">' . htmlspecialchars($alb['nom']) . '</option>';
                                    }
                                    echo '</select>';
                                    echo '<button type="submit" class="btn btn-outline-primary">Ajouter</button>';
                                    echo '</div>';
                                    echo '</form>';
                                }
                            }
                            
                            // Compteurs de réactions
                            $reactions = $connexion->query("SELECT type_reaction, COUNT(*) as total FROM reaction WHERE id_publication = " . $ligne['id_publication'] . " GROUP BY type_reaction");
                            $nb_reactions = ['like' => 0];
                            while ($reaction = $reactions->fetch_assoc()) {
                                if ($reaction['type_reaction'] === 'like') {
                                    $nb_reactions['like'] = $reaction['total'];
                                }
                            }
                            
                            $nb_commentaires = $connexion->query("SELECT COUNT(*) as total FROM commentaire WHERE id_publication = " . $ligne['id_publication'])->fetch_assoc()['total'];
                            $nb_partages = $connexion->query("SELECT COUNT(*) as total FROM partage WHERE id_publication = " . $ligne['id_publication'])->fetch_assoc()['total'];
                            
                            // Vérifier la réaction de l'utilisateur
                            $ma_reaction = $connexion->query("SELECT type_reaction FROM reaction WHERE id_publication = " . $ligne['id_publication'] . " AND id_utilisateur = " . $_SESSION['id_utilisateur'])->fetch_assoc();
                            $mon_type_reaction = $ma_reaction ? $ma_reaction['type_reaction'] : null;
                            
                            // Boutons d'interaction
                            echo '<div class="d-flex gap-2 mb-3">';
                            
                            // Bouton J\'aime
                            echo '<form method="POST" action="accueil.php" class="d-inline">';
                            echo '<input type="hidden" name="id_publication" value="' . $ligne['id_publication'] . '">';
                            echo '<input type="hidden" name="action" value="reaction">';
                            echo '<input type="hidden" name="type_reaction" value="like">';
                            echo '<button type="submit" class="btn btn-outline-primary ' . ($mon_type_reaction === 'like' ? 'active' : '') . '">';
                            echo '<i class="fas fa-thumbs-up"></i> J\'aime (' . $nb_reactions['like'] . ')';
                            echo '</button>';
                            echo '</form>';
                            
                            // Bouton Commenter
                            echo '<button onclick="toggleCommentaire(' . $ligne['id_publication'] . ')" class="btn btn-outline-secondary">';
                            echo '<i class="fas fa-comment"></i> Commenter (' . $nb_commentaires . ')';
                            echo '</button>';
                            
                            // Bouton Partager (affiche le formulaire)
                            echo '<button onclick="togglePartage(' . $ligne['id_publication'] . ')" class="btn btn-outline-success">';
                            echo '<i class="fas fa-share"></i> Partager (' . $nb_partages . ')';
                            echo '</button>';
                            
                            // Formulaire de partage dans un div caché
                            echo '<div id="partageform_' . $ligne['id_publication'] . '" style="display:none; margin-top:8px;">';
                            echo '<form method="POST" action="partager_message.php" class="d-flex align-items-center">';
                            echo '<input type="hidden" name="id_publication" value="' . $ligne['id_publication'] . '">';
                            echo '<select name="id_ami" class="form-select form-select-sm me-2" required style="min-width:150px;">';
                            echo '<option value="">Partager avec...</option>';
                            $mes_amis = $connexion->query("SELECT u.id_utilisateur, u.prenom, u.nom FROM utilisateur u WHERE u.id_utilisateur IN (\n        SELECT id_utilisateur_source FROM connexion WHERE id_utilisateur_cible = $id_utilisateur AND statut = 'accepte'\n        UNION\n        SELECT id_utilisateur_cible FROM connexion WHERE id_utilisateur_source = $id_utilisateur AND statut = 'accepte'\n    )");
                            while ($ami = $mes_amis->fetch_assoc()) {
                                echo '<option value="' . $ami['id_utilisateur'] . '">' . htmlspecialchars($ami['prenom'] . ' ' . $ami['nom']) . '</option>';
                            }
                            echo '</select>';
                            echo '<button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-paper-plane"></i> Envoyer</button>';
                            echo '</form>';
                            echo '</div>';
                            
                            // Formulaire de commentaire (caché par défaut)
                            echo '<div id="commentaire_' . $ligne['id_publication'] . '" class="mb-3" style="display:none;">';
                            echo '<form method="POST" action="accueil.php">';
                            echo '<input type="hidden" name="id_publication" value="' . $ligne['id_publication'] . '">';
                            echo '<input type="hidden" name="action" value="commenter">';
                            echo '<div class="input-group">';
                            echo '<textarea name="commentaire" class="form-control" placeholder="Écrivez votre commentaire..." required></textarea>';
                            echo '<button type="submit" class="btn btn-primary">Publier</button>';
                            echo '</div>';
                            echo '</form>';
                            echo '</div>';
                            
                            // Affichage des commentaires
                            $commentaires = $connexion->query("SELECT c.*, u.prenom, u.nom, u.photo_profil 
                                                             FROM commentaire c 
                                                             JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
                                                             WHERE c.id_publication = " . $ligne['id_publication'] . " 
                                                             ORDER BY c.date_commentaire ASC");
                            
                            echo '<div class="commentaires mt-3">';
                            while ($commentaire = $commentaires->fetch_assoc()) {
                                echo '<div class="d-flex align-items-start mb-2">';
                                if (!empty($commentaire['photo_profil'])) {
                                    echo '<img src="' . $commentaire['photo_profil'] . '" alt="Photo de profil" class="rounded-circle me-2" style="width:32px; height:32px; object-fit:cover;">';
                                }
                                echo '<div class="flex-grow-1">';
                                echo '<div class="bg-light rounded p-2">';
                                echo '<strong class="d-block">' . htmlspecialchars($commentaire['prenom'] . ' ' . $commentaire['nom']) . '</strong>';
                                echo '<p class="mb-0">' . htmlspecialchars($commentaire['texte']) . '</p>';
                                echo '<small class="text-muted">' . $commentaire['date_commentaire'] . '</small>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                            
                            // Boutons Modifier/Supprimer pour l'auteur ou l'admin
                            if ($_SESSION['id_utilisateur'] == $ligne['id_utilisateur'] || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin')) {
                                echo '<div class="mt-3">';
                                echo '<button onclick="toggleEditForm(\'editform_' . $ligne['id_publication'] . '\')" class="btn btn-outline-primary btn-sm me-2">Modifier</button>';
                                
                                echo '<form method="POST" action="accueil.php" onsubmit="return confirm(\'Supprimer cette publication ?\');" class="d-inline">';
                                echo '<input type="hidden" name="supprimer_id" value="' . $ligne['id_publication'] . '">';
                                echo '<button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>';
                                echo '</form>';
                                
                                // Formulaire de modification caché
                                echo '<form id="editform_' . $ligne['id_publication'] . '" method="POST" action="accueil.php" enctype="multipart/form-data" class="mt-3" style="display:none;">';
                                echo '<input type="hidden" name="modifier_id" value="' . $ligne['id_publication'] . '">';
                                echo '<div class="mb-3">';
                                echo '<textarea name="texte_modifie" class="form-control" required>' . htmlspecialchars($ligne['texte']) . '</textarea>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label class="form-label">Visibilité :</label>';
                                echo '<select name="visibilite_modifiee" class="form-select">';
                                echo '<option value="public"' . ($ligne['visibilite'] == 'public' ? ' selected' : '') . '>Public</option>';
                                echo '<option value="amis"' . ($ligne['visibilite'] == 'amis' ? ' selected' : '') . '>Amis uniquement</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label class="form-label">Remplacer la photo/vidéo :</label>';
                                echo '<input type="file" name="media_modifie" class="form-control" accept="image/*,video/*">';
                                echo '</div>';
                                echo '<button type="submit" class="btn btn-primary">Enregistrer</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                            
                            echo '</div>'; // Fin card-body
                            echo '</div>'; // Fin card
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact mb-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Contact Administrateur</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope me-2"></i>Email : admin@ecein.fr</p>
                            <p><i class="fas fa-phone me-2"></i>Téléphone : 01 23 45 67 89</p>
                            <p><i class="fas fa-map-marker-alt me-2"></i>Adresse : 10 Rue Sextius Michel, 75015 Paris</p>
                        </div>
                        <div class="col-md-6">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18..." class="w-100 rounded" height="300" style="border:0;" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="bg-dark text-light py-4 mt-4">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 ECE In - Tous droits réservés</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleCommentaire(id) {
        var form = document.getElementById('commentaire_' + id);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function toggleEditForm(id) {
        var form = document.getElementById(id);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function togglePartage(id) {
        var form = document.getElementById('partageform_' + id);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'inline-block';
        } else {
            form.style.display = 'none';
        }
    }
    </script>
</body>
</html>
