<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Sauvegarde et Restauration</li>
</ul>
<p class="titre">[ Sauvegarde et Restauration ]</p>

<p>- <a href="sauvegarde_restauration/outils_backup.php">[ Sauvegarder et Restaurer avec les Outils MongoDB ]</a></p>
<p>- <a href="sauvegarde_restauration/snapshots_filesystem.php">[ Sauvegarder et Restaurer avec des Snapshots FileSystem ]</a></p>
<p>- <a href="sauvegarde_restauration/restaurer_replicaset.php">[ Restaurer un Replica Set avec une Sauvegarde MongoDB ]</a></p>
<p>- <a href="sauvegarde_restauration/sauvegarder_cluster.php">[ Sauvegarder et Restaurer un Sharded Cluster ]</a></p>
<p>- <a href="sauvegarde_restauration/copier_bdd.php">[ Copier des Bases de Données entre des Instances ]</a></p>
<p>- <a href="sauvegarde_restauration/restauration_donnees.php">[ Restaurer les Données après une Interruption Inattendue ]</a></p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>