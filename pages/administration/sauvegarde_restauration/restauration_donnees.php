<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Restaurer les Données après une Interruption Inattendue</li>
</ul>

<p class="titre">[ Restaurer les Données après une Interruption Inattendue ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#proc">I) Processus</a></p>
	<p class="right"><a href="#indi">- a) Indications</a></p>
	<p class="right"><a href="#ve">- b) Vue d'Ensemble</a></p>
	<p class="right"><a href="#pr">- c) Procédures</a></p>
	<p class="elem"><a href="#lock">II) mongod.lock</a></p>
</div>

<p>Si MongoDB ne s'arrête pas correctement (avec db.shutdownServer()  ou mongod --shutdown), la représentation des fichiers de données sur le disque ne reflettera pas un état intègre des données, ce qui
pourrait amener à des données corrompus.
Pour éviter la corruption des données, il vous faudra toujours arrêter la base de données normalement et proprement et utiliser le journaling.
MongoDB écrit des données au journal, par défaut, toutes les 100 millisecondes, de manière à ce que MongoDB puisse toujours récupérer les données
intègres dans le cas d'un arrêt interrompu ou anormal, peut-être à cause d'une coupe de courant ou d'une erreur système.
Si vous n'exécutez pas votre base de données sous un Replica Set et que le journaling n'est pas activé, utilisez la procédure suivante afin de récupérer
les données qui seraient corrompues. Si vous avez un Replica Set, vous devrez toujours restaurer à partir d'une sauvegarde ou redémarrer l'instance mongod
avec un dbpath vide et autoriser MongoDB a effectuer une synchronisation initiale pour restaurer les données.</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="titre">I) [ Processus ]</p>

<p></p>
<a name="indi"></a>

<div class="spacer"></div>

<p class="small-titre">a) Indications</p>

<p>Lorsque vous êtes conscient du fait qu'une instance mongod est exécutée sans journaling, que celle-ci s'arrête de façon innappropriée et que la réplication
n'est pas activée, vous devrez toujours exécuter l'opération de réparation avant de démarrer MongoDB à nouveau. Si vous utilisez la réplication,
alors restaurez depuis une sauvegarde et autorisez la réplication à effectuer une sycnhronisation initiale pour restaurer les données.

Si le fichier mongod.lock est dans le répertoire de données (spécifié par le dbpath) , /data/db par défaut, n'est pas un fichier de 0 bytes, alors mongodb
refusera de démarrer et vous trouverez un message qui contiendra la ligne suivante dans votre log MongoDB :</p>

<pre>Unclean shutdown detected.</pre>

<p>Cela indique que vous devez exécuter MongoDB avec l'option --repair. Si vous exécutez --repair lorsque le fichier mongodb.lock existe dans votre dbpath,
ou alors le --repairpath optionnel, vous verrez un message qui contient la ligne suivante :</p>

<pre>old lock file: /data/db/mongod.lock. probably means unclean shutdown</pre>

<p>Si vous voyez ce message, vous devrez en dernier recours supprimer le fichier lock et exécuter une opération de réparation avant de démarrer la base de données
normallement comme dans la procédure suivante :</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">b) Vue d'Ensemble</p>

<div class="alert alert-danger">
	<u>Attention</u> : N'utilisez pas cette procédure pour récupérer un membre d'un Replica Set. Vous devriez, à la place, restaurer avec une sauvegarde
	ou effectuer une synchronisation initiale en utilisant les données depuis un membre intacte du replica set.
</div>

<div class="spacer"></div>

<p>Il y a deux processus pour réparer les fichiers de données qui résultent d'un arrêt interrompu :

1) Utilisez l'option --repair en conjonction avec l'option --repairpath. mongod va lire les fichiers de données et écrire les données dans les nouveaux
fichiers de données. Cela ne modifie pas les fichiers de données existants.
Vous n'avez pas besoin de supprimer le fichier mongod.lock avant d'utiliser cette procédure.

2) Utilisez l'option --repair. mongod va lire les fichiers de données existants, écrire les données existantes sur les nouveaux fichiers de données et remplacer
ceux qui existent, probablement corrompus, fichiers avec les nouveaux fichiers.
Vous devez impérativement supprimer le fichier mongod.lock avant d'utiliser cette procédure.</p>

<div class="alert alert-success">
	<u>Astuce</u> : L'option --repair est aussi disponible dans le shell mongo avec la méthode db.repairDatabase(), représentant la commande
	repairDatabase.
</div>
<a name="pr"></a>

<div class="spacer"></div>

<p class="small-titre">c) Procédures</p>

<p>Pour réparer vos fichiers de données avec l'option --repairpath, pour préserver les fichirs de données originaux non modifiés :

1) Démarrez mongod en utilisant l'option --repair pour lire les fichiers de données existants.</p>

<pre>mongod --dbpath /data/db --repair --repairpath /data/db0</pre>

<p>Une fois cela terminé, les nouveaux fichiers de données réparés vont se situer dans le répertoire /data/db0.
Démarrez mongod en utilisant l'invocation suivante pour pointr le dbpath vers /data/db0 :</p>

<pre>mongod --dbpath /data/db0</pre>

<p>Une fois que vous aurez confirmé que les fichiers de données sont opérationnels, vous devrez supprimer ou archiver les fichiers de données du répertoire /data/db.

Pour réparer vos fichiers de données sans préserver les fichiers originaux, n'utilisez pas l'option -repairpath comme dans la procédure suivante :

1) Supprimez le fichier lock :</p>

<pre>rm /data/db/mongod.lock</pre>

<p>Remplacez /data/db avec votre dbpath ou les fichiers de données de votre instance MongoDB réside.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Après avoir supprimé le fichier mongod.lock, vous devez exécuter le processus --repair avant d'utiliser votre base de données.
</div>

<p>2) Démarrez mongod en utilisant l'option --repair pour lire les fichiers de données existants :</p>

<pre>mongod --dbpath /data/db --repair</pre>

<p>Une fois cela terminé, les fichiers de données réparés vont remplacer les fichiers de données originaux dans le répertoire /data/db.

3) Démarrez mongod en utilisant l'invocation suivante pour pointer le dbpath à /data/db :</p>

<pre>mongod --dbpath /data/db</pre>
<a name="lock"></a>

<div class="spacer"></div>

<p class="titre">II) [ mongod.lock ]</p>

<p>En temps normal, vous ne devriez jamais supprimer le fichier mongod.lock et démarrer mongod. Considérez plutôt l'une des méthodes du paragraphe précédent pour
restaurer la base de données et supprimer les fichiers verrous. Dans certaines situations, vous pouvez supprimer le fichier verrou et démarrer la base de données
en utilisant les fichiers probablement corrompus, et tenter de récupérer les données depuis votre base de données, en revanche, il est impossible de prédire
l'état de la base de données dans ces situations. Si vous n'exécutez pas avec le journaling, et que votre base de données s'arrête de façon innattendue, vous devriez
toujours procéder comme si votre base de données est dans un état corrompu et non-intègre. Si il vous est possible de restaurer avec une sauvegarde,
ou , si vous avec un replica set, restaurez en effectuant une synchronisation initiale en utilisant les données depuis un membre intacte de l'ensemble.</p>

<div class="spacer"></div>

<p>La suite va concerner le dernier bloc du tutoriel sur l'administration MongoDB, le Scripting.
Passons a la page suivante qui va correspondre au <a href="../scripting/js_serveur.php">"Javascript Côté Serveur" >></a>.</p>

<?php

	include("footer.php");

?>
