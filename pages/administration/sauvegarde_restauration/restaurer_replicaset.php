<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Restaurer un Replica Set avec une Sauvegarde MongoDB</li>
</ul>

<p class="titre">[ Restaurer un Replica Set avec une Sauvegarde MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#rs">I) Restaurer une Base de Données dans un Replica Set Simple</a></p>
	<p class="elem"><a href="#mbr">II) Ajouter des Membres au Replica Set</a></p>
	<p class="right"><a href="#cop">- a) Copier les Fichiers de Base de Données et Redémarrer l'Instance mongod</a></p>
	<p class="right"><a href="#maj">- b) Mettez à Jour les Membres Secondaires en Utilisant la Sycnhronisation Initiale</a></p>
</div>

<p>Ici nous allons voir comment restaurer les données d'un Replica Set dans un nouveau Replica Set. Cela peut-être très utile en cas de catastrophe et de remise en route
de votre ensemble. Vous ne pouvez pas simplement restaurer un seul ensemble de données sur trois nouvelles instances mongod et ensuite créer un Replica Set.
Dans cette situation, MongoDB va forcer les secondaires à effectuer un synchronisation initiale. Nous allons voir comment déployer un Replica Set
normalement et proprement.</p>
<a name="rs"></a>

<div class="spacer"></div>

<p class="titre">I) [ Restaurer une Base de Données dans un Replica Set Simple ]</p>

<p>1) Obtenez une sauvegarde des fichiers de données MongoDB. Ces fichiers peuvent venir d'un snapshot. Le MMS Backup Service produit
des fichiers de base de données MongoDB pour les snapshots stockés et les snapshots pointant dans le temps. Vous pouvez aussi utiliser mongorestore pour
restaurer les fichiers de base de données en utilisant les données crééent avec mongodump.

2) Démarrez mongod en utilisant les fichiers de données depuis la sauvegarde en tant que dbpath. Dans l'exemple suivant, /data/db est le dbpath :</p>

<pre>mongod --dbpath /data/db</pre>

<p>3) Convertissez votre instance mongod standalone en un Replica Set à un noeud en arrêtant l'instance mongod, et en redémarrant avec l'option --replSet :</p>

<pre>mongod --dbpath /data/db --replSet "replName"</pre>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Définissez explicitement le paramètre oplogSize pour contrôlr la taille de votre oplog créé pour ce membre du Replica Set.
</div>

<p>4) Connectez-vous à l'instance mongod.

5) Utilisez la méthode rs.initiate() pour démarrer le nouveau Replica Set.</p>
<a name="mbr"></a>

<div class="spacer"></div>

<p class="titre">II) [ Ajouter des Membres au Replica Set ]</p>

<p>MongoDB fournit deux options pour restaurer les membres secondaires d'un Replica Set :

1) Copiez manuellement les fichiers de bases de données dans chaque répertoire de données.
2) Autoriser la synchronisation initiale afin de distribuer les données automatiquement.

Nous allons aborder les deux options dans la partie qui va suivre.</p>

<div class="alert alert-info">
	<u>Note</u> : Si votre base de données est large, une synchronisation initiale peut prendre du temps avant de se terminer.
	Pour les bases de données larges, il serait peut-être préférable de copier les fichiers de bases de données sur chaque hôte directement.</p>
</div>
<a name="cop"></a>

<div class="spacer"></div>

<p class="small-titre">a) Copier les Fichiers de Base de Données et Redémarrer l'Instance mongod</p>

<p>Utilisez la séquence d'opérations suivante pour attribuer aux autres membres du Replica Set les données restaurées en copiant les fichiers de données
directement :

1) Arrêter l'instance mongod que vous venez de restaurer. Utilisez --shutdown ou db.shutdownServer() pour être sûr de stopper l'instance correctement.

2) Copiez le répertoire de données du membre primaire dans le dbpath des autres membres du Replica Set. Le dbpath est /data/db par défaut.

3) Démarrez l'instance mongod que vous venez de restaurer.

4) Dans un shell mongo connecté au primaire, ajoutez les secondaires au Replica Set en utilisant la commande rs.add().</p>
<a name="maj"></a>

<div class="spacer"></div>

<p class="small-titre">b) Mettez à Jour les Membres Secondaires en Utilisant la Sycnhronisation Initiale</p>

<p>Utilisez la séquence d'opérations suivante pour attribuer les données restaurées aux autres membres du Replica Set en utilisant la synchronisation initiale
par défaut :

1) Assurez-vous que les répertoires de données se situant sur chaque membre sont vides.

2) Ajoutez chaque membre au Replica Set. La synchronisation initiale va copier les données du membre primaire vers les autres membres du Replica Set.</p>

<div class="spacer"></div>

<p>La suite va concerner <a href="sauvegarder_cluster.php">"Sauvegarder et Restaurer un Sharded Cluster" >></a>.</p>

<?php

	include("footer.php");

?>
