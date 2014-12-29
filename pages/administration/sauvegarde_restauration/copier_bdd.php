<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Copier des Bases de Données entre des Instances</li>
</ul>

<p class="titre">[ Copier des Bases de Données entre des Instances ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#syno">I) Synopsis</a></p>
	<p class="elem"><a href="#cons">II) Considérations</a></p>
	<p class="elem"><a href="#proc">III) Processus</a></p>
	<p class="right"><a href="#copi">- a) Copier et Renommer une Base de Données</a></p>
	<p class="right"><a href="#reno">- b) Renommer une Base de Données</a></p>
	<p class="right"><a href="#auth">- c) Copier une Base de Données avec L'Authentification</a></p>
	<p class="right"><a href="#clon">- d) Clôner une Base de Données</a></p>
</div>

<p></p>
<a name="syno"></a>

<div class="spacer"></div>

<p class="titre">I) [ Synopsis ]</p>

<p>MongoDB fournit les commandes copydb et clone pour supporter la migration de bases de données logiques entières entre des instances mongod.
Avec ces commandes, vous pouvez copier des données entre des instances avec une simple interface sans besoin d'un stage intermédiaire.
Les méthodes db.cloneDatabase() et db.copyDatabase() permettent d'exécuter ces commandes respectives. Ces méthodes permettent d'exécuter les commandes
sur le serveur de destination et prennent les données du serveur source.

Les migrations de données qui nécessitent un stage intermédiaire ou qui ont besoin de plus d'une instance de base de données sont en dehors de ce tutoriel.
copydb et clone sont plus idéales pour les cas d'utilisation suivants :

- migrations de données
- data warehousing
- environnements de tests</p>

<div class="alert alert-info">
	<u>Note</u> : copydb et clone ne produisent pas de snapshots pointant dans le temps de la base de données source. Ecrire des données
	à la base de données source ou à la base de données de destination, pendant la copie, donnera plusieurs ensembles de données.
</div>
<a name="cons"></a>

<div class="spacer"></div>

<p class="titre">II) [ Considérations ]</p>

<p>- Vous devez exécuter copydb ou clone sur le serveur de destination
- Vous ne pouvez pas utiliser copydb ou clone avec des bases de données ayant une collection shardée dans un sharded cluster, ou toute base de données
via une instance mongos.
- Vous pouvez utiliser copydb ou clone avec des bases de données qui n'ont pas de collections shardées dans un cluster, lorsque vous êtes directement connecté
à l'instance mongod.
- Vous pouvez exécuter copydb ou clone sur des membres secondaires d'un Replica Set, avec une préférence de lecture proprement configurée.
- Chaque instance mongod de destination doit avoir assez d'espace disque libre sur le serveur de destination pour la base de données que vous êtes
en train de copier. Utilisez l'opération db.stats() pour vérifier la taille de la base de données sur l'instance mongod source.</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="titre">III) [ Processus ]</p>

<p></p>
<a name="copi"></a>

<div class="spacer"></div>

<p class="small-titre">a) Copier et Renommer une Base de Données</p>

<p>Pour copier une base de données depuis une instance MongoDB vers une autre ainsi que de renommer cette base de données durant le processus, 
utilisez la commande copydb ou la méthode correspondant, db.copyDatabase() dans un shell mongo.
Utilisez la procédure suivante pour copier une base de donnés nommée test se trouvant sur le serveur db0.example.net vers le serveur nommé 
db1.example.net et la renommer "records" dans le processus :

- vérifiez que la base de données "test" existe sur l'instance mongod source exécutée sur l'hôte db0.example.net
- connectez-vous au serveur de destination, exécuté sur l'hôte db1.example.net en utilisant le shell mongo.
- modelez votre commande sur le modèle suivant :</p>

<pre>db.copyDatabase( "test", "records", "db0.example.net" )</pre>
<a name="reno"></a>

<div class="spacer"></div>

<p class="small-titre">b) Renommer une Base de Données</p>

<p>Vous pouvez aussi utiliser copydb et db.copyDatabase() pour :
- renommer une base de données sous une simple instance MongoDB
- créer une base de données dupliquée pour des mesures de test

Utilisez la procédure suivante pour renommer la base de données test en records sur une simple instance mongod :
	- connectez-vous à l'instance mongod en utilisant un shell mongo
	- Modelez votre opération sur la suivante :</p>

<pre>db.copyDatabase( "test", "records" )</pre>
<a name="auth"></a>

<div class="spacer"></div>

<p class="small-titre">c) Copier une Base de Données avec L'Authentification</p>

<p>Pour copier une base de données depuis une instance MongoDB source qui a l'authentification activée, vous pouvez spécifier des identifiants 
à la commande copydb ou la méthode db.copyDatabase() dans un shell mongo.
Dans l'opération suivante, vous allez copier la base de données test de l'instance mongod exécutée sur le serveur db0.example.net vers la base de données
records sur l'instance locale (db1.example.net). Vu que l'instance mongod exécutée sur le serveur db0.example.net nécessite l'authentification
pour toutes les connexions, vous aurez besoin de passer des identifiants à la méthode db.copyDatabase() comme dans la procédure suivante :

- connectez-vous à l'instance mongod de destination exécutée sur l'hôte db1.example.net en utilsiant un shell mongo
- utilisez la commande suivante :</p>

<pre>db.copyDatabase( "test", "records", db0.example.net, "username", "password")</pre>

<p>Remplacez bien évidement username et password par vos propres identifiants.</p>
<a name="clon"></a>

<div class="spacer"></div>

<p class="small-titre">d) Clôner une Base de Données</p>

<p>La commande clone copie une base de données entre des instances mongod comme copydb, par contre, clone préserve le nom de la base de données de l'instance
source vers l'instance mongod de destination.
Pour toute opération, clone est équivalent à copydb, mais est plus facile à utiliser et a une syntaxe plus basique. Le shell mongo fournit 
la méthode db.cloneDatabase() pour la commande clone.
Vous pouvez utiliser la procédure suivante pour clôner une base de données depuis l'instance mongod exécutée sur db0.example.net vers le mongod exécuté
sur db1.example.net :

- connectez-vous à l'instance mongod de destination exécutée sur db1.example.net en utilisant un shell mongo
- utilisez la commande suivante pour spécifier le nom de la base de données qui vous souhaitez clôner :</p>

<pre>use records</pre>

<p>- utilisez l'opération suivante pour démarrer l'opération de clônage :</p>

<pre>db.cloneDatabase( "db0.example.net" )</pre>

<div class="spacer"></div>

<p>La suite va concerner <a href="restauration_donnees.php">"Restaurer les Données après une Interruption Inattendue" >></a>.</p>

<?php

	include("footer.php");

?>
