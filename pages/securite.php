<?php

	set_include_path("../");
	include("../header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Sécurité</li>
</ul>

<p class="titre">[ Sécurité ]</p>

<p>Comme tout le monde, je l'espère, <b>vous êtes soucieux de la sécurité de vos données et des risques de les exposer au web</b>.
Vous allez apprendre à gérer cet aspect de MongoDB de manière à <b>vous sécuriser le plus possible</b>.
Pour ceux qui seraient <b>très avancés et qui trouveraient une ou même plusieurs vulnérabilités</b> au sein de MongoDB (hé oui, le code est entièrement
accessible car open-source), vous allez apprendre à <b>créer votre propre rapport de vulnérabilité</b> pour que l'équipe de <b>10Gen</b> se charge de résoudre les problèmes rapidement.
La sécurité avec MongoDB est prise très au sérieux.</p>

<div class="spacer"></div>

<p><a href="securite/introduction.php">[ Introduction ]</a></p>
<p><a href="securite/controle_acces.php">[ Contrôle d'Accès ]</a></p>
<p><a href="securite/auth_processus.php">[ Authentification Inter-Processus ]</a></p>
<p><a href="securite/exposition_reseau.php">[ Sécurité et Exposition du Réseau ]</a></p>
<p><a href="securite/interfaces_api.php">[ Sécurité et Interfaces de l'API MongoDB ]</a></p>
<p><a href="securite/sharded_cluster.php">[ Sécurité de Sharded Cluster ]</a></p>
<p><a href="securite/tutoriel_securite_reseau.php">[ Tutoriel de Sécurité Réseau ]</a></p>
<p><a href="securite/tutoriel_controle_acces.php">[ Tutoriel de Gestion du Contrôle d'Accès ]</a></p>
<p><a href="securite/rapport_vulnerabilite.php">[ Créer un Rapport de Vulnérabilité ]</a></p>

<div class="spacer"></div>

<p>Et voilà, vous êtes arrivés <b>au dernier chapitre de MongoTuto.com</b> ! En espérant vous avoir appris <b>quelques astuces sur MongoDB et la sécurité des informations</b>.
Maintenant, vous pouvez soit <b>consulter les exemples de codes</b>, <b>découvrir les différents outils MongoDB</b> ou même <b>revenir sur des points</b> que vous n'avez pas compris
ou même lus. <b>Merci d'en être arrivés jusqu'à là</b>, et surtout, si vous avez <b>des remarques</b>, même qui vous semblent <b>inutiles ou bêtes</b>, on ne sait jamais, <a href="contact.php">"contactez-moi"</a>.</p>
	
<?php

	include("../footer.php");

?>
