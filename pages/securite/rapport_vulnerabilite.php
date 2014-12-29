<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Créer un Rapport de Vulnérabilité</li>
</ul>

<p class="titre">[ Créer un Rapport de Vulnérabilité ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#tick">I) Créer un Ticket dans JIRA</a></p>
	<p class="elem"><a href="#info">II) Les Informations à Fournir</a></p>
	<p class="elem"><a href="#envo">III) Envoyez le Rapport via Email</a></p>
	<p class="elem"><a href="#eval">IV) Evaluation du Rapport de Vulnérabilité</a></p>
	<p class="elem"><a href="#divu">V) Divulgation</a></p>
</div>

<p>Si vous pensez avoir découvert une vulnérabilité dans MongoDB ou alors si vous avez malheureusement rencontré un problème de sécurité, vous pouvez
envoyer cet incident afin que cela ne se reproduise plus.

Pour reporter un incident, MongoDB recommande fortement de remplir un ticket dans leur JIRA relaté au projet de sécurité <a href="https://jira.mongodb.org/browse/SECURITY/" target="_blank">JIRA - MongoDB Security</a>.
MongoDB s'engage à répondre aux tickets de sécurité en moins de 48h.</p>
<a name="tick"></a>

<div class="spacer"></div>

<p class="titre">I) [ Créer un Ticket dans JIRA ]</p>

<p>Veuillez repporter vos incidents à cette adresse : <a href="https://jira.mongodb.org/browse/SECURITY/" target="_blank">https://jira.mongodb.org/browse/SECURITY/</a>.
Le numéro du ticket va devenir une référence d'identification pour votre incident. Vous pourrez alors utiliser cet identifiant pour vérifier le statut de votre
requête.</p>
<a name="info"></a>

<div class="spacer"></div>

<p class="titre">II) [ Les Informations à Fournir ]</p>

<p>Tous les rapports de vulnérabilité doivent contenir autant d'information que possible de manière à ce que les développeurs de MongoDB puissent
résoudre votre incident le plus vite possible. Veuillez bien inclure ces informations suivantes :

- Le nom du produit
- Des informations sur les vulnérabilités récurrentes, si possible, en incluant :
	- le score CVSS (Common Vulnerability Scoring System)
	- l'identifier CVE (Common Vulnerability and Exposures)
	- Des informations de contact, incluant une adresse e-mail et/ou un numéro de téléphone si possible.</p>
<a name="envo"></a>

<div class="spacer"></div>
	
<p class="titre">III) [ Envoyez le Rapport via Email ]</p>

<p>Alors que JIRA est la méthode de rapport préférée, vous voudrez probablement envoyer vos rapports de vulnérabilité par e-mail à l'adresse
security[at]mongodb[dot]com.
Si vous souhaitez crypter votre e-mail en utilisant <a href="http://docs.mongodb.org/10gen-gpg-key.asc" target="_blank">la clé publique de MongoDB</a>.

MongoDB Inc. répond aux rapports de vulnérabilités envoyés par e-mail avec une réponse par e-mail qui contient un numéro de référence pour le ticket JIRA
posté dans le projet SECURITY.</p>
<a name="eval"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Evaluation du Rapport de Vulnérabilité ]</p>

<p>MongoDB Inc. valide toutes les vulnérabilités transmisent et utilise JIRA pour garder toutes les communications concernant les vulnérabilités, en incluant les
requêtes de demande de clarification ou d'informations additionnelles. Si besoin, des représentants de MongoDB peuvent organiser une conférence (Skype ou autre ...)
pour échanger des informations en fonction de votre vulnérabilité.</p>
<a name="divu"></a>

<div class="spacer"></div>

<p class="titre">V) [ Divulgation ]</p>

<p>MongoDB Inc. demande à ce que vous ne divulguez pas publiquement toute information regardant cette vulnérabilité ou même que vous exploitiez cette faille
jusqu'à ce que MongoDB Inc. ai eu l'opportunité d'analyser la vulnérabilité, de répondre à votre demande ainsi que de prévenir les utilisateurs clé, les clients
et aussi les partenaires.

Le temps requis pour valider une vulnérabilité rapportée dépend de la complexité de celle-ci. MongoDB Inc. considère toute vulnérabilité très au sérieux
et va toujours s'assurer qu'il y aura toujours un moyen clair et très simple de communiquer avec le reporteur.

Après avoir validé la vulnérabilité, MongoDB Inc. divulgue publiquement la vulnérabilité avec un laps de temps négocié avec le rapporteur. Si vous le demandez,
va pouvoir recevoir des crédits dans le prochain bulletin de sécurité MongoDB.</p>

<div class="spacer"></div>

<p>Bon hé bien ... voila, ceci est la fin de la toute derniere page du tutoriel sur MongoDB. Maintenant appart
la relecture de certains chapitres que vous auriez esquivé voire mal compris, je vous invite a acceder a la section <a href="../exemples_code.php">"Exemples de Code" >></a>.</p>
	
<?php

	include("footer.php");

?>