<?php

// Vos fonctions (token, traitement des fichiers etc...)

/**
 * Génère un token sécurisé et l'enregistre dans la session et un cookie.
 *
 * @return string Le token généré.
 */
function generateToken() {
	// Vérifiez si la session est démarrée
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	// Chaîne de caractères pour le token
	$chaine = "azertyuiopqsdfghjklmwxcvbnAZERTYUIOPMLKJHGFDSQWXCVBN0123456789&:;+[]()#/%*éèà";
	$tableau = mb_str_split($chaine);
	$longueurTableau = count($tableau);
	$token = "";

	for ($i = 0; $i < rand(16, 30); $i++) {
		$token .= $tableau[rand(0, $longueurTableau - 1)];
	}

	// Hasher le token
	$token = md5(sha1($token));

	// Enregistrer le token en session
	$_SESSION['token'] = $token;

	// Créer un cookie pour la session avec une durée de vie de 15 minutes
	setcookie('session_token', session_id(), time() + 900, "/", "", false, true);

	return $token;
}

/**
 * Vérifie la validité de la session, du cookie et du token.
 *
 * @return void Redirige vers la page de connexion si la vérification échoue.
 */
function verifyToken() {
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	// Vérifiez si l'utilisateur est connecté
	if (!isset($_SESSION['user_id'])) {
		header('Location: ?route=login');
		exit;
	}

	// Vérifiez si le cookie de session est valide
	if (!isset($_COOKIE['session_token']) || $_COOKIE['session_token'] !== session_id()) {
		session_unset();
		session_destroy();
		setcookie('session_token', '', time() - 3600, "/");
		header('Location: ?route=login');
		exit;
	}

	// Vérifiez si le token est présent dans l'URL et correspond à celui de la session
	if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['token']) {
		header('Location: ?route=login');
		exit;
	}
}

/**
 * Génère le HTML pour afficher la liste des fichiers appartenant à un utilisateur.
 *
 * @param int $userId L'ID de l'utilisateur connecté.
 * @return array Un tableau associatif contenant les fichiers de l'utilisateur.
 */
function genereListeHtmlUtilisateur($userId) {
	global $pdo;

	// Prépare une requête pour récupérer les fichiers de l'utilisateur
	$stmt = $pdo->prepare("SELECT fichier FROM fichiers WHERE id_utilisateur = ?");
	$stmt->execute([$userId]);
	
	// Retourne un tableau associatif avec les fichiers
	return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

/**
 * Traite les fichiers uploadés par un utilisateur.
 *
 * @param array $files Tableau des fichiers envoyés via $_FILES.
 * @param int $userId L'ID de l'utilisateur connecté.
 * @param PDO $pdo Connexion à la base de données PDO.
 * @param array $extensions Extensions autorisées pour les fichiers. (Je sais que c'était pas demandé mais bon...)
 * @param string $uploadDir Dossier où stocker les fichiers. (Pareil...)
 * @return array Tableau des erreurs ou messages de réussite.
 */
function handleFileUpload($files, $userId, $pdo, $extensions = ['.jpg', '.png', '.gif', '.pdf', '.docx', '.xlsx', '.html', '.css', '.ico'], $uploadDir = 'uploads/') {
	$messages = []; // Stocke les erreurs ou succès pour chaque fichier

	// Vérifier si le dossier d'upload existe, sinon le créer
	if (!is_dir($uploadDir)) {
		if (!mkdir($uploadDir, 0755, true)) {
			// Si la création du dossier échoue, renvoyer une erreur
			$messages[] = "Erreur : Impossible de créer le dossier d'upload.";
			return $messages; // Arrêter la fonction car les fichiers ne peuvent pas être traités
		}
	}

	// Parcourir chaque fichier envoyé
	for ($i = 0; $i < count($files['name']); $i++) {
		// Vérifier s'il y a une erreur pour ce fichier
		if ($files['error'][$i] !== UPLOAD_ERR_OK) {
			$messages[] = "Erreur avec le fichier " . strip_tags($files['name'][$i]);
			continue;
		}

		// Récupérer l'extension du fichier
		$verif_ext = strrchr($files['name'][$i], '.');

		// Vérifier si l'extension est autorisée
		if (in_array($verif_ext, $extensions)) {
			// Générer un nom unique pour le fichier
			$nouveau_nom = uniqid() . $verif_ext;

			// Déplacer le fichier vers le dossier "uploads/"
			if (move_uploaded_file($files['tmp_name'][$i], $uploadDir . $nouveau_nom)) {
				// Enregistrer le fichier dans la base de données
				$stmt = $pdo->prepare("INSERT INTO fichiers (id_utilisateur, fichier) VALUES (?, ?)");
				$stmt->execute([$userId, $nouveau_nom]);

				$messages[] = "Fichier " . strip_tags($files['name'][$i]) . " uploadé avec succès.";
			} else {
				$messages[] = "Erreur lors du déplacement du fichier " . strip_tags($files['name'][$i]);
			}
		} else {
			$messages[] = "Extension non autorisée pour le fichier " . strip_tags($files['name'][$i]) . ". Extensions acceptées : " . implode(', ', $extensions);
		}
	}

	return $messages;
}

?>