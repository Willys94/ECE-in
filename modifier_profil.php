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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $photo = "";
    $image_fond = "";
    $cv = "";

    if (!empty($_FILES['photo_profil']['name'])) {
        $nom_photo = 'uploads/photo_' . $id_utilisateur . '_' . basename($_FILES['photo_profil']['name']);
        move_uploaded_file($_FILES['photo_profil']['tmp_name'], $nom_photo);
        $photo = $nom_photo;
    }

    if (!empty($_FILES['image_fond']['name'])) {
        $nom_fond = 'uploads/fond_' . $id_utilisateur . '_' . basename($_FILES['image_fond']['name']);
        move_uploaded_file($_FILES['image_fond']['tmp_name'], $nom_fond);
        $image_fond = $nom_fond;
    }

    if (!empty($_FILES['cv']['name']) && pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION) === 'pdf') {
        $nom_cv = 'uploads/cv_utilisateur' . $id_utilisateur . '.pdf';
        move_uploaded_file($_FILES['cv']['tmp_name'], $nom_cv);
        $cv = $nom_cv;
    }

    $requete = "UPDATE utilisateur SET ";
    if ($photo) $requete .= "photo_profil = '$photo', ";
    if ($image_fond) $requete .= "image_fond = '$image_fond', ";
    $requete = rtrim($requete, ', ');
    $requete .= " WHERE id_utilisateur = $id_utilisateur";

    if ($connexion->query($requete)) {
        $message = "Profil mis à jour avec succès.";
    } else {
        $message = "Erreur : " . $connexion->error;
    }
}

$chemin_cv = 'uploads/cv_utilisateur' . $id_utilisateur . '.pdf';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - ECE In</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main style="max-width: 600px; margin: auto; padding: 20px;">
        <h2>Modifier mon profil</h2>
        <?php if (!empty($message)) echo '<p>' . $message . '</p>'; ?>
        <form action="modifier_profil.php" method="POST" enctype="multipart/form-data">
            <label for="photo_profil">Photo de profil :</label><br>
            <input type="file" name="photo_profil" accept="image/*"><br><br>

            <label for="image_fond">Image de fond :</label><br>
            <input type="file" name="image_fond" accept="image/*"><br><br>

            <label for="cv">CV (PDF uniquement) :</label><br>
            <input type="file" name="cv" accept="application/pdf"><br><br>

            <input type="submit" value="Mettre à jour">
        </form>

        <?php if (file_exists($chemin_cv)): ?>
            <p><a href="<?php echo $chemin_cv; ?>" download>Télécharger mon CV</a></p>
        <?php endif; ?>

        <p><a href="profil.php">Retour au profil</a></p>
    </main>
</body>
</html>
