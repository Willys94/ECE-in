<?php
session_start();
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$nom_base = "ecein";
$connexion = new mysqli($serveur, $utilisateur, $mot_de_passe, $nom_base);
if ($connexion->connect_error) {
    die("Connexion échouée : " . $connexion->connect_error);
}
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit();
}
$id_utilisateur = $_SESSION['id_utilisateur'];
$id_publication = intval($_POST['id_publication']);
$id_ami = intval($_POST['id_ami']);
// Récupérer le contenu de la publication
$pub = $connexion->query("SELECT texte, media FROM publication WHERE id_publication = $id_publication")->fetch_assoc();
if ($pub) {
    $texte = "[Partage de publication] " . $pub['texte'];
    if (!empty($pub['media'])) {
        $texte .= "\n(Média : " . $pub['media'] . ")";
    }
    $texte = $connexion->real_escape_string($texte);
    $connexion->query("INSERT INTO message (expediteur_id, destinataire_id, contenu, date_envoi) VALUES ($id_utilisateur, $id_ami, '$texte', NOW())");
}
header("Location: messagerie.php?conversation=$id_ami");
exit(); 