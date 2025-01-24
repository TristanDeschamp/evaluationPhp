<?php
// Page accessible uniquement aux personnes connectées
require_once('autoload.php');

// Démarrer la session pour gérer les utilisateurs connectés
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
	// Si l'utilisateur n'est pas connecté, redirigez-le vers la page d'inscription
   header('Location: ?route=register');
   exit;
}

// Vérifier la validité du token
verifyToken();

// Récupérer l'ID de l'utilisateur depuis la session
$userId = $_SESSION['user_id'];

// Récupérer les fichiers associés à l'utilisateur
$files = genereListeHtmlUtilisateur($userId);

// Initialiser la liste HTML des fichiers
$fileListHtml = '';

// Générer le HTML pour chaque fichier si des fichiers sont disponibles
if (!empty($files) && is_array($files)) {
	foreach ($files as $file) {
		// Créez une liste avec des liens pour télécharger chaque fichier
		$fileListHtml .= '<li><a href="uploads/' . htmlspecialchars($file['fichier']) . '" download>' . htmlspecialchars($file['fichier']) . '</a></li>';
	}
} else {
	 // Si aucun fichier n'est disponible, afficher un message
	$fileListHtml = '<li>Aucun fichier disponible.</li>';
}

// Charger le modèle HTML
$token = $_SESSION['token']; // Récupérer le token depuis la session
$fichier = file_get_contents('template/index.html');

// Remplacer le placeholder [LISTEFICHIERS] par la liste des fichiers
$fichier = str_replace('[LISTEFICHIERS]', $fileListHtml, $fichier);

// Remplacer le placeholder [TOKEN] par le token actuel
$fichier = str_replace('[TOKEN]', $token, $fichier);

// Afficher la page
echo $fichier;
?>