<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Outils</li>
</ul>
<p class="titre">[ Outils ]</p>

<p>Vous pouvez trouver sur cette page <b>différents outils</b>, systèmes d'exploitation <b>confondus</b>, tous <b>relatifs à MongoDB</b>.
Si vous souhaitez <b>ajouter un outil</b> que vous connaissez, ou même que vous développez, <b>aucun problème</b> ! <a href="contact.php">"Contactez-moi"</a>,
écrivez <b>le nom</b> de votre software/service, ajoutez <b>une petite description</b> que vous aimeriez voir et je l'ajouterai si celui-ci est <b>approprié</b>.</p>

<div class="spacer"></div>

<table>
	<tr>
		<th>Nom</th>
		<th>Lien</th>
		<th>Description</th>
		<th>Gratuit ?</th>
	</tr>
	<tr>
		<td><b>RoboMongo</b></td>
		<td><a href="http://www.RoboMongo.org" target="_blank">http://www.RoboMongo.org</a></td>
		<td>Mon client MongoDB préféré. Très complet, gratuit et activement amélioré.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>MongoVUE</b></td>
		<td><a href="http://www.MongoVue.com" target="_blank">http://www.MongoVue.com</a></td>
		<td>Très bon client MongoDB aussi, probablement le plus populaire mais il est payant.</td>
		<td>Non</td>
	</tr>
	<tr>
		<td><b>MongoDB Management Service (MMS)</b></td>
		<td><a href="https://mms.mongodb.com/" target="_blank">https://mms.mongodb.com/</a></td>
		<td>Une interface officielle de MongoDB permettant le monitoring gratuit de vos déploiements
		ainsi qu'un service de sauvegarde payant.</td>
		<td>Oui|Non</td>
	</tr>
	<tr>
		<td><b>UMongo</b></td>
		<td><a href="http://www.edgytech.com/umongo" target="_blank">http://www.edgytech.com/umongo</a></td>
		<td>Ce client vous permet de gérer et administrer vos clusters MongoDB.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>JsonLint</b></td>
		<td><a href="http://www.JsonLint.com" target="_blank">http://www.JsonLint.com</a></td>
		<td>Analyse et vérifie la syntaxe de vos documents JSON et BSON. Très utile si vous n'êtes pas sûr de la structure d'un de vos documents.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>JsonGenerator</b></td>
		<td><a href="http://www.json-generator.com" target="_blank">http://www.Json-Generator.com</a></td>
		<td>Générateur de données JSON si vous voulez créer des données de test.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>PhpMoAdmin</b></td>
		<td><a href="http://www.PhpMoAdmin.com" target="_blank">http://www.PhpMoAdmin.com</a></td>
		<td>Exactement comme PhpMyAdmin, cette interface web vous permet de gérer votre MongoDB avec votre navigateur web préféré.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>RockMongo</b></td>
		<td><a href="http://RockMongo.com" target="_blank">http://RockMongo.com</a></td>
		<td>Encore un PhpMyAdmin like pour administrer votre déploiement MongoDB. Jamais testé mais a l'air pas mal !</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>Jongo</b></td>
		<td><a href="http://www.Jongo.org" target="_blank">http://www.Jongo.org/<a></td>
		<td>Un outil que je trouve particulièrement intéressant, qui est basé sur le driver Java de MongoDB. Celui-ci vous permet
		d'effectuer vos requêtes en langage Java comme si vous les effectuez dans un shell habituel.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>MongoDirector</b></td>
		<td><a href="http://www.MongoDirector.com" target="_blank">http://www.MongoDirector.com<a></td>
		<td>Service d'hébergement cloud et management de serveurs, ensembles de répliques et shards.</td>
		<td>Non</td>
	</tr>
	<tr>
		<td><b>Jeu du Pendu</b></td>
		<td><a href="http://www.hangmanfun.com/game/mongodb" target="_blank">http://www.HangmanFun.com/game/mongodb/<a></td>
		<td>Juste un jeu du pendu réalisé avec MongoDB, histoire de montrer un peu ce que l'on peut faire avec.</td>
		<td>Oui</td>
	</tr>
	<tr>
		<td><b>Le reste ...</b></td>
		<td><a href="http://MongoDB-Tools.com/" target="_blank">http://MongoDB-Tools.com/<a></td>
		<td>Un site web très complet comportant d'autres outils. On y trouve surtout des drivers et outils compatibles avec MongoDB.</td>
		<td>Oui</td>
	</tr>
</table>

<?php

	include("footer.php");

?>
