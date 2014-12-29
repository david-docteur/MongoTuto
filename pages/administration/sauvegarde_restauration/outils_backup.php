<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Sauvegarder et Restaurer avec les Outils MongoDB</li>
</ul>

<p class="titre">[ Sauvegarder et Restaurer avec les Outils MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#sauv">I) Sauvegarder une Base de Données avec mongodump</a></p>
	<p class="right"><a href="#oper">- a) Opérations Basiques avec mongodump</a></p>
	<p class="right"><a href="#oplo">- b) Opération Pointant dans le Temps en Utilisant les Oplogs</a></p>
	<p class="right"><a href="#cree">- c) Créer des Sauvegardes sans Instance mongod en cours d'Exécution</a></p>
	<p class="right"><a href="#inst">- d) Créer des Sauvegardes avec des Instances mongod Non Locales</a></p>
	<p class="elem"><a href="#rest">II) Restaurer une Base de Données avec mongorestore</a></p>
	<p class="right"><a href="#temp">- a) Restaurer une Sauvegarde d'Oplog Pointant dans le Temps</a></p>
	<p class="right"><a href="#bina">- b) Restaurer un Sous-Ensemble de Données Depuis une Sauvegarde Binaire</a></p>
	<p class="right"><a href="#exec">- c) Restaurer Sans mongod en Cours d'Exécution</a></p>
	<p class="right"><a href="#nl">- d) Restaurer des Sauvegardes pour des Instances mongod Non-Locales</a></p>
</div>

<p>Dans cette partie du tutoriel, nous allons voir comment transformer le contenu entier de votre instance MongoDB dans un fichier au format binaire.
Si les snapshots de disque ne sont pas disponibles, cette approche offre la meilleure option pour des sauvegardes système complètes.
Si votre système peut effectuer des snapshots de disque, veuillez vous reporter à la section suivante.</p>
<a name="sauv"></a>

<div class="spacer"></div>

<p class="titre">I) [ Sauvegarder une Base de Données avec mongodump ]</p>

<div class="alert alert-danger">
	<u>Attention</u> : mongodump ne créé pas de sauvegarde pour la base de données 'local'.
</div>
<a name="oper"></a>

<div class="spacer"></div>

<p class="small-titre">a) Opérations Basiques avec mongodump</p>

<p>L'outil mongodump peut effectuer des sauvegardes des données soit :
- se connecter à une instance mongod ou mongos en cours d'exécution, ou
- accéder aux fichiers de données sans instance active.

L'outil peut créer une sauvegarde pour un serveur entier, base de données ou collection, ou peut utiliser une requête pour sauvegarder juste une partie
de la collection. Lorsque vous exécutez mongodump sans arguments, la commande se connecte à l'instance locale de MongoDB (par exemple : 127.0.0.1 ou localhost)
sur le port 27017 et créé une base de données de sauvegarde appelée dump/ dans le répertoire courant.
Pour sauvegarder des données depuis une instance mongod ou mongos en cours d'exécution sur la même machine et sur le port par défaut 27017, utilisez la commande
suivante :</p>

<pre>mongodump</pre>

<div class="alert alert-info">
	<u>Note</u> : Les données crééent avec l'outil mongodump depuis la version 2.2 est incompatible avec les versions de mongorestore depuis la version 2.0
	et plus jeunes.
</div>

<div class="spacer"></div>

<p>Pour limiter la quantité de données inclue dans la sauvegarde de la base de données, spécifiez les options --db et --collection :</p>

<pre>
mongodump --dbpath /data/db/ --out /data/backup/

mongodump --host mongodb.example.net --port 27017
</pre>

<p>mongodump va écrire des fichiers BSON qui contiennent des copies des données accessibles via l'instance mongod écoutant sur le port 27017 sur l'hôte
mongodb.example.net :</p>

<pre>mongodump --collection collection --db test</pre>

<p>Cette commande va créer une sauvegarde de la collection nommée "collection" de la base de données "test" dans le sous-répertoire dump/ du répertoire
de travail courant.</p>
<a name="oplo"></a>

<div class="spacer"></div>

<p class="small-titre">b) Opération Pointant dans le Temps en Utilisant les Oplogs</p>

<p>Utilisez l'option --oplog avec mongodump pour collecter les entrées d'oplog pour construire un snapshot de la base de données pointant dans le temps sous un
Replica Set. Avec l'option --oplog, mongodump va copier toutes les données de la base de données source ainsi que toutes les entrées d'oplog depuis le début
de la procédure de sauvegarde jusqu'à ce que la procédure de sauvegarde se termine. Cette procédure de sauvegarde, en conjonction avec mongorestore --oplogReplay,
vous permet de restaurer les données qui reflettent un momen particulier dans le temps.</p>
<a name="cree"></a>

<div class="spacer"></div>

<p class="small-titre">c) Créer des Sauvegardes sans Instance mongod en cours d'Exécution</p>

<p>Si votre instance MongoDB n'est pas exécutée, vous pouvez utiliser l'option --dbpath pour spécifier la location des fichiers de données de votre
instance. Le programme mongodump lit directement les fichiers de données avec cette opération. Cela va verrouiller le répertoire des données pour éviter
les conflits d'écritures. Le processus mongod ne doit absolument pas être exécuté ou attaché à ces fichiers de données lorsque vous exécutez mongodump 
dans ce contexte.
Par exemple, prenons une instance MongoDB qui contient les bases de données customers, products et suppliers, l'opération mongodump suivante va
sauvegarde la base de données en utilisant l'option --dbpath, ce qui spécifie la location des fichiers de données :</p>

<pre>mongodump --dbpath /data -o dataout</pre>

<p>L'option --out (ou -o) vous permet de spécifier le répertoire ou mongodump va stocker sa sauvegarde, mongodump créé un dossier séparé de sauvegarde
pour chaque base de données sauvegardée : dataout/customers, dataout/products et dataout/suppliers.</p>
<a name="inst"></a>

<div class="spacer"></div>

<p class="small-titre">d) Créer des Sauvegardes avec des Instances mongod Non Locales</p>

<p>Les options --host et --port d mongodump vont vous permettre de vous connecter et de sauvegarder depuis un hôte distant, comme dans l'exemple suivant :</p>

<pre>mongodump --host mongodb1.example.net --port 3017 --username user --password pass --out /opt/backup/m.............</pre>

<p>Pour toute commande mongodump que vous rencontrerez, comme l'exemple ci-dessus, spécifiez un nom d'utilisateur et un mot de passe pour
spécifier l'identification pour la base de données souhaitée.</p>
<a name="rest"></a>

<div class="spacer"></div>

<p class="titre">II) [ Restaurer une Base de Données avec mongorestore ]</p>

<p>L'outil mongorestore récupère et restaure une sauvegarde binaire créée par le programme mongodump. Par défaut, mongorestore cherche un fichier
de sauvegarde d'une base de données dans le répertoire dump/.
Le programme mongorestore peut restaurer les données soit :
- En se connectant directement à une instance mongod ou mongos en cours d'exécution, ou
- Ecrire à un ensemble de fichier de données MongoDB sans l'utilisation d'une instance MongoDB en cours d'exécution

mongorestore peut restaurer soit une sauvegarde entière d'une base de données, soit un sous-ensemble de cette sauvegarde. 
Pour utiliser mongorestore en vous connectant à une instance mongod ou mongos active, utilisez la commande suivante :</p>

<pre>mongorestore --port "port number" "path to the backup"</pre>

<p>Pour utiliser mongorestore en écrivant sur les fichiers de données sans utiliser d'instance mongod en cours d'exécution :</p>

<pre>mongorestore --dbpath "database path" "path to the backup"</pre>

<p>Considérons l'exemple suivant :</p>

<pre>mongorestore dump-2012-10-25/</pre>

<p>Ici, mongorestore importe la sauvegarde de base de données depuis le répertoire dump-2012-10-25/ dans l'instance mongod exécutée sur l'interface localhost.</p>
<a name="temp"></a>

<div class="spacer"></div>

<p class="small-titre">a) Restaurer une Sauvegarde d'Oplog Pointant dans le Temps</p>

<p>Si vous avez créé votre sauvegarde de votre base de données en utilsiant l'option --oplog pour utiliser un snapshot pointant dans le temps, appelez
mongorestore avec l'option --oplogReplay comme dans l'exemple qui va suivre :</p>

<pre>mongorestore --oplogReplay</pre>

<p>Vous pourriez aussi utiliser la commande mongorestore --objcheck pour vérifier l'intégrité des objets lors de l'insertion de ceux-ci dans la base
de données, ou alors, vous pourriez considérer l'option mongorestore --drop afin de supprimer chaque collection de la base de données
avant de restaurer des sauvegardes.</p>
<a name="bina"></a>

<div class="spacer"></div>

<p class="small-titre">b) Restaurer un Sous-Ensemble de Données Depuis une Sauvegarde Binaire</p>

<p>mongorestore inclut aussi la capacité de filtrer toutes les données avant de les insérer dans la nouvelle base de données :</p>

<pre>mongorestore --filter '{"field": 1}'</pre>

<p>Ici, mongorestore va ajouter des documents à la base de données depuis une sauvegarde importée depuis le répertoire dump/ si ces documents
on un champ 'field' ayant pour valeur 1. Entourez le filtre de simple guillemets pour éviter que le filtre intéragisse avec votre environnement shell.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="small-titre">c) Restaurer Sans mongod en Cours d'Exécution</p>

<p>mongorestore peut écrire des données vers des fichiers de données MongoDB sans avoir besoin de se connecter directement à une instance mongod.
Par exemple, prenons un ensemble de bases de données sauvegardées dans le répertoire /data/backup :

- /data/backup/customers
- /data/backup/products
- /data/backup/suppliers

La commande mongorestore qui va suivre va restaurer la base de données products :</p>

<pre>mongorestore --dbpath /data/db --journal /data/backup/products</pre>

<p>L'option --dbpath va spécifier le chemin des fichiers de données MongoDB.
Le programme mongorestore importe la base de données sauvegardée dans le répertoire /data/backup/products vers l'instance mongod qui s'exécute sur l'interface
localhost. L'opération mongorestore va importer la sauvegarde même si mongod n'est pas en cours d'exécution.
L'option --journal s'assure que mongorestore enregistre toutes les opérations dans le journal. Le journal évite la corruption des fichiers de données
si une raison particulière interromp la procédure de restauration (par exemple : coupure de courant, échec de disque dur etc ...).</p>
<a name="nl"></a>

<div class="spacer"></div>

<p class="small-titre">d) Restaurer des Sauvegardes pour des Instances mongod Non-Locales</p>

<p>Par défaut, mongorestore se connecte à une instance MongoDB exécuté en local (127.0.0.1 par exemple) et sur le port par défaut (27017).
Si vous souhaitez restaurer sur un hôte distant, utilisez l'exemple suivant :</p>

<pre>mongorestore --host mongodb1.example.net --port 3017 --username user --password pass /opt/backup/mong</pre>

<p>Comme vous pouvez le voir, vous devrez spécifier un nom d'utilisateur et un mot de passe si l'instance mongod nécessite l'identification.</p>

<div class="spacer"></div>

<p>La suite va concerner les <a href="snapshots_filesystem.php">"Snapshots Filesystem" >></a>.</p>

<?php

	include("footer.php");

?>
