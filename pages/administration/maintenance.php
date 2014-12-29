<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Configuration, Maintenance et Analyse</li>
</ul>
<p class="titre">[ Configuration, Maintenance et Analyse ]</p>

<p>- <a href="maintenance/cmd_bdd.php">[ Utiliser les Commandes de Base de Données ]</a></p>
<p>- <a href="maintenance/gerer_mongod.php">[ Gérer les Processus mongod ]</a></p>
<p>- <a href="maintenance/analyse_performances.php">[ Analyser les Performances des Opérations ]</a></p>
<p>- <a href="maintenance/monitoring_snmp.php">[ Monitoring MongoDB avec SNMP ]</a></p>
<p>- <a href="maintenance/fichiers_logs.php">[ Fichiers Logs ]</a></p>
<p>- <a href="maintenance/gerer_journaling.php">[ Gérer le Journaling ]</a></p>
<p>- <a href="maintenance/stocker_js.php">[ Stocker une Fonction JavaScript sur le Serveur ]</a></p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>