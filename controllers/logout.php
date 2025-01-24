<?php
session_start();

// Supprimer toutes les variables de session
$_SESSION = [];  // Réinitialiser le tableau de session pour supprimer toutes les données stockées

// Détruire la session
session_destroy(); // Détruire complètement la session sur le serveur

// Supprimer le cookie de session, si il existe
if (isset($_COOKIE['user'])) {
    setcookie('user', '', time() - 3600, '/'); // Définit un cookie expiré pour supprimer l'entrée sur le navigateur
}

// Rediriger l'utilisateur vers la page de connexion ou la page d'accueil
header('Location: ?route=login');
exit;
?>