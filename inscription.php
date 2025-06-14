<?php
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$nom_base = "ecein";

$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $nom_base);
if ($connexion->connect_error) {
    die("Connexion échouée : " . $connexion->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $connexion->real_escape_string($_POST['nom']);
    $prenom = $connexion->real_escape_string($_POST['prenom']);
    $email = $connexion->real_escape_string($_POST['email']);
    $mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $requete = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES ('$nom', '$prenom', '$email', '$mdp', 'auteur')";
    if ($connexion->query($requete)) {
        $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
    } else {
        $message = "Erreur : " . $connexion->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - ECE In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><strong>ECE In</strong></a>
        </div>
    </nav>
    <main class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="card shadow w-100" style="max-width: 420px;">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Inscription à ECE In</h2>
                <?php if (!empty($message)) echo '<div class="alert alert-info">' . $message . '</div>'; ?>
                <form action="inscription.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" name="nom" id="nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                </form>
                <p class="mt-3 text-center">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
            </div>
        </div>
    </main>
    <footer class="bg-dark text-light py-4 mt-4 mt-auto">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 ECE In - Tous droits réservés</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Validation Bootstrap
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
