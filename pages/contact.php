<?php

	set_include_path("../");
	include("header.php");
	require_once('recaptchalib.php');
	error_reporting(E_ALL);
	// Clé publique pour le captcha
	$publickey = "fake";
	// Clé privée pour le captcha
	$privatekey = "fake";
	
	// Notre futur message d'erreur (s'il y en a un)
	$erreur = null;
	
	

	// Vérifier si le captcha est valide
	if(isset($_POST['sendEmail'])) {
	  $resp = recaptcha_check_answer ($privatekey,
									$_SERVER["REMOTE_ADDR"],
									$_POST["recaptcha_challenge_field"],
									$_POST["recaptcha_response_field"]);
	  // Erreur - captcha non valide
	  if (!$resp->is_valid) {
		$erreur = "Le captcha saisit n'est pas valide !";
	  } else {
			// Vérifier si un utilisateur envoi un email
			if(isset($_POST['sendEmail']) && isset($_POST['nomForm']) && isset($_POST['emailForm']) && isset($_POST['messageForm'])
				&& !empty($_POST['nomForm']) && !empty($_POST['emailForm']) && !empty($_POST['messageForm'])) {
				
				$destinataire = "admin@mongotuto.com";
				$sujet = "Requête de " . $_POST['nomForm'] . " venant de MongoTuto.com";
				$message = $_POST['nomForm'] . " (" . $_POST['emailForm'] . ") a écrit le message suivant " . $_POST['messageForm'];
				
				try {
					// Nouvelle connexion à MongoDB
					// On s'identifie, on ne vas pas laisser libre accès comme certains :)
					$connexion = new MongoClient("fake")) or Die("Impossible de se connecter à la base de données MongoDB.");
					// Obtenir la base de données
					$collection = $connexion->mongotuto->emails;
					$documents = $collection->insert(array('sujet' => $sujet, 'message' => $message));
					$erreur = "Votre e-mail a été envoyé avec succès. L'administrateur va bientôt vous contacter.";
				} catch(Exception $ex) {
					$erreur = "L'envoi de l'e-mail a échoué. Problème avec le serveur SMTP ?" . $ex;
				}
				
			} else {
					$erreur = "Au moins un des champs obligatoires est vide !";
			}
	  }
	}


?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Contact</li>
</ul>

<p class="titre">[ Contact ]</p>

<p><b>Vous souhaitez me contacter ?</b> Remplissez le formulaire et je vous répondrais <b>dès que possible</b>.</p>
<p>Vous pouvez utiliser ce formulaire pour <b>effectuer des demandes concernant MongoDB</b>, <b>les fonctionnalités et tutoriaux en général</b>,
mais aussi <b>les bugs</b>, <b>liens morts</b>, <b>manque de mise à jour</b> etc ... Je ne réponds pas aux <b>critiques non justifiées</b> ou tout autre message <b>non approprié</b>
mais <b>j'accepte totalement</b> les remarques constructives, <b>au contraire</b>, c'est comme ça que la communauté <b>progressera</b>.
Ce formulaire redirige les e-mails vers l'adresse : <b>admin@mongotuto.com</b>.</p>

<div class="spacer"></div>

<p style="color: red; font-weight: bold;"><?php echo $erreur; ?></p>

<form id="send" name="send" method="POST" action="contact.php">

	<table id="contactTable">
		<tr>
			<td><b>Votre nom*</b>:</td><td><input type="text" id="nomForm" name="nomForm" placeholder="Votre nom" size="30"></td>
		</tr>
		<tr>
			<td><b>Votre adresse e-mail*</b>:</td><td><input type="text" id="emailForm" name="emailForm" placeholder="Votre e-mail" size="30"></td>
		</tr>
		<tr>
			<td><b>Votre message*</b>:</td><td><textarea id="messageForm" name="messageForm" rows="10" cols="80"></textarea></td>
		</tr>
		<tr>
			<td><td><?php echo recaptcha_get_html($publickey); ?></td>
		</tr>
		<tr>
			<td></td><td><input class="btn btn-primary" type="submit" id="endEmail" name="sendEmail" value="Envoyer"></td>
		</tr>
	</table>
	
	<p style="margin-left: 140px;"><b>* Tous les champs sont obligatoires.</b></p>

</form>

<?php

	include("footer.php"); 

?>
