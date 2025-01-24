<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
	// Si l'utilisateur n'est pas connecté, arrêter le script avec un message d'erreur
	die("Vous devez être connecté pour envoyer des fichiers.");
}

// Vérifier la validité du token
verifyToken();

// Traitement du formulaire d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
	$messages = handleFileUpload($_FILES['files'], $_SESSION['user_id'], $pdo);

	foreach ($messages as $message) {
		echo "<p>$message</p>";
	}

	header('Location: ?route=index');
	exit;
}

$token = $_SESSION['token'];
$fichier = file_get_contents('template/upload.html');
$fichier = str_replace('[TOKEN]', strip_tags($token), $fichier);
echo $fichier;
?>