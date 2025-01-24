<?php
// Page inaccessible si la personne est connecté
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
	// Redirection vers la page principale si l'utilisateur est déjà connecté
	header('Location: ?route=index&token=' . $_SESSION['token']);
	exit;
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Récupérer les données du formulaire
	$email = strip_tags($_POST['username']);
	$password = strip_tags($_POST['password']);
	$confirmPassword = strip_tags($_POST['confirm-password']);
	
	// Vérification que les mots de passe correspondent
	if ($password !== $confirmPassword) {
		die("Les mots de passe ne correspondent pas.");
	}
	
	// Vérification de l'existence de l'email dans la base de données
	$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
	$stmt->execute([$email]);
	if ($stmt->rowCount() > 0) {
		die("Cet email est déjà utilisé."); // Si l'email existe déjà, afficher une erreur
	}
	
	// Hashage du mot de passe
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	
	// Insertion dans la base de données
	$stmt = $pdo->prepare("INSERT INTO utilisateurs (email, password) VALUES (?, ?)");
	$success = $stmt->execute([$email, $hashedPassword]);
	
	if ($success) {
		// Charger PhpMailer pour envoyer un email de confirmation
		require './vendor/autoload.php';
		$mail = new PHPMailer(true);
		try {
			// Configuration du serveur
			$mail->isSMTP();
			$mail->Host = 'dwwm2425.fr';
			$mail->SMTPAuth = true;
			$mail->Username = 'contact@dwwm2425.fr';
			$mail->Password = '!cci18000Bourges!';
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;
			
			// Expéditeur et destinataire
			$mail->setFrom('contact@dwwm2425.fr', 'Support DWWM'); // Expéditeur
			$mail->addAddress($email); // Destinataire
			
			// Contenu de l'email
			$mail->isHTML(true);
			$mail->Subject = 'Confirmation de votre inscription';
			$mail->Body = '<h1>Bienvenue !</h1><p>Merci de vous être inscrit sur notre plateforme.</p>';
			
			$mail->send();
			echo "Inscription réussie ! Un email de confirmation a été envoyé.";
		} catch (Exception $e) {
			// Si l'email échoue, afficher une erreur
			echo "L'inscription a réussi, mais l'email n'a pas pu être envoyé : {$mail->ErrorInfo}";
		}
	} else {
		// Si l'insertion échoue, afficher une erreur
		echo "Erreur lors de l'inscription. Veuillez réessayer.";
	}
}

$file = file_get_contents('template/register.html');
echo $file;

?>