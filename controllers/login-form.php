<?php

// Page inaccessible si la personne est connecté

session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
	// Rediriger vers la page d'accueil avec le token si l'utilisateur est déjà connecté
	header('Location: ?route=index&token=' . $_SESSION['token']);
	exit;
}

// Vérifier si le formulaire a été soumis via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Récupérer les données du formulaire
	$email = strip_tags($_POST['username']);
	$password = strip_tags($_POST['password']);

	// Vérifier que les champs ne sont pas vides
	if (empty($email) || empty($password)) {
		die("Veuillez remplir tous les champs."); // Afficher un message d'erreur si des champs sont vides
	}

	// Rechercher l'utilisateur dans la base
	$stmt = $pdo->prepare("SELECT id, password FROM utilisateurs WHERE email = ?");
	$stmt->execute([$email]);
	$user = $stmt->fetch();

	// Vérifier si l'utilisateur existe et si le mot de passe est correct
	if ($user && password_verify($password, $user['password'])) {
		// Connexion réussie : Initialiser la session
		$_SESSION['user_id'] = $user['id'];

		// Générer un token unique avec une durée de vie de 15 minutes
		$token = generateToken(); // Appelle une fonction pour générer un token sécurisé
		$_SESSION['token_expiry'] = time() + 900; // Enregistrer l'expiration du token dans la session (15 minutes)

		// Créer un cookie pour prolonger la session
		setcookie('user', $user['id'], time() + 3600, '/', '', false, true);

		// Rediriger vers la page d'accueil avec le token dans l'URL
		header('Location: ?route=index&token=' . urlencode($token));
		exit;
	} else {
		// Afficher un message d'erreur si les identifiants sont incorrects
		echo "Email ou mot de passe incorrect.";
	}
}

// Charger le contenu HTML du formulaire de connexion
$fichier = file_get_contents('template/login.html');
echo $fichier;

?>