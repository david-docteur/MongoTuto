<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Administration</li>
</ul>
<p class="titre">[ Administration ]</p>

<p>Vous voici dans la partie <b>la plus complète de MongoTuto.com</b>, la partie <b>Administration</b>. Celle-ci va se diviser en <b>deux parties</b> : <b>La Théorie</b> et <b>la Pratique</b>,
tout comme la replication et le sharding. Nous allons d'abord voir <b>les différents aspects pour apprendre à gérer</b> MongoDB, <b>les options disponibles</b> ainsi que <b>les
principales fonctionnalités</b> comme <b>la sauvegarde ou la restauration des données</b>. Puis, <b>la partie Pratique</b> va vous montrer comment cela se passe
dans le feu de l'action. Allez, commençons par regarder <b>le plan</b> :</p> 

<div class="spacer"></div>

<p class="small-titre">[ I - La Théorie ]</p>

<p>- <a href="administration/strategies_sauvegarde.php">[ Stratégies de Sauvegarde pour Systèmes MongoDB ]</a></p>
<p>- <a href="administration/monitoring.php">[ Monitoring avec MongoDB ]</a></p>
<p>- <a href="administration/configuration_bdd.php">[ Configuration d'Exécution de Base de Données ]</a></p>
<p>- <a href="administration/import_export.php">[ Importer/Exporter les Données MongoDB ]</a></p>
<p>- <a href="administration/management_donnees.php">[ Management des Données ]</a></p>
<p>- <a href="administration/optimisation.php">[ Stratégies d'Optimisation pour MongoDB ]</a></p>

<div class="spacer"></div>

<p class="small-titre">[ II - La Pratique ]</p>

<p><a href="administration/maintenance.php">[ Configuration, Maintenance et Analyse ]</a></p>
<p><a href="administration/sauvegarde_restauration.php">[ Sauvegarde et Restauration ]</a></p>
<p><a href="administration/scripting.php">[ Scripting avec MongoDB ]</a></p>

<div class="spacer"></div>

<p>Je ne pense pas qu'il vous sera nécessaire de <b>tout lire</b> dans cette section sur l'administration avec MongoDB, mais lorsque
vous en aurez terminé avec ce chapitre, pourquoi ne pas <b>ajouter des notions de sécurité</b> avec MongoDB ?
Votre déploiement doit être <b>à l'abris</b> et être <b>le moins vulnérable possible</b>, je vous invite donc à passer au chapitre suivant : <a href="securite.php">"Sécurité" >></a>.</p>

<?php

	include("footer.php");

?>
