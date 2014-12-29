<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Sauvegarder et Restaurer un Sharded Cluster</li>
</ul>

<p class="titre">[ Sauvegarder et Restaurer un Sharded Cluster ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#peti">I) Sauvegarder un Petit Sharded Cluster avec mongodump</a></p>
	<p class="right"><a href="#veun">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prun">- b) Procédure</a></p>
	<p class="elem"><a href="#snap">II) Sauvegarder un Sharded Cluster avec des Snapshots FileSystem</a></p>
	<p class="right"><a href="#vede">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prde">- b) Procédure</a></p>
	<p class="elem"><a href="#bdd">III) Sauvegarder un Sharded Cluster avec des Dumps de Base de Données</a></p>
	<p class="right"><a href="#vetr">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prtr">- b) Procédure</a></p>
	<p class="elem"><a href="#plan">IV) Plannifier une Période Maintenance pour les Sharded Clusters</a></p>
	<p class="right"><a href="#vequ">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prqu">- b) Procédure</a></p>
	<p class="elem"><a href="#rest">V) Restaurer un Simple Shard</a></p>
	<p class="right"><a href="#veci">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prci">- b) Procédure</a></p>
	<p class="elem"><a href="#shar">VI) Restaurer un Sharded Cluster</a></p>
	<p class="right"><a href="#vesi">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#prsi">- b) Procédure</a></p>
</div>

<p></p>
<a name="peti"></a>

<div class="spacer"></div>

<p class="titre">I) [ Sauvegarder un Petit Sharded Cluster avec mongodump ]</p>

<p></p>
<a name="veun"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Si votre Sharded Cluster ne contient qu'un petit ensemble de données, vous pouvez vous connecter à l'instance mongos en utilisant mongodump.
Vous pouvez créer des sauvegardes pour votre cluster MongoDB, si votre infrastructure de sauvegarde peut capturer une sauvegarde entière dans un
laps de temps raisonnable et si vous avez un système de stockage qui peut détenir l'ensemble de données MongoDB en entier.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Par défaut, mongodump envoi ses requêtes aux noeuds non-primaires.
</div>
<a name="prun"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<div class="alert alert-info">
	<u>Note</u> : Si vous utilisez mongodump sans spécifier de base de données ou de collection, mongodump va capturer les données des collections
	ainsi que les méta-informations du cluster depuis les serveurs de configuration.
	Vous ne pouvez pas utiliser l'option --oplog pour mongodump lorsque vous capturez des données depuis mongos. Cette option est seulement disponible
	lorsque vous l'exécutez directement sur un membre du Replica Set.
</div>

<p>Vous pouvez effectuer une sauvegarde d'un sharded cluster en connectant mongodump à un mongos. Utilisez l'opération suivante :</p>

<pre>mongodump --host mongos3.example.net --port 27017</pre>

<p>mongodump va écrire des fichiers BSON qui détiennent une copie des données stockées dans le sharded cluster accessible via mongos écoutant sur le port 27017
de l'hôte mongos3.exemple.net.

Restaurer les données : Les sauvegardes crééent avec le programme mongodump ne reflettent pas les chunks ou la distribution des données dans la ou les sharded collection(s).
Comme toute sortie de mongodump, ces sauvegardes contiennent des répertoires séparés pour chaque base de données ainsi que des fichiers BSON
pour chaque collection dans une base de données.
Vous pouvez restaurer les sorties mongodump pour toute instance MongoDB, incluant les instances standalones, un Replica Set, ou un nouveau sharded cluster.
Lorsque vous restaurez les données d'un sharded cluster, vous devez déployer et configurer le sharding avant de restaurer les données avec la sauvegarde.</p>
<a name="snap"></a>

<div class="spacer"></div>

<p class="titre">II) [ Sauvegarder un Sharded Cluster avec des Snapshots FileSystem ]</p>

<p></p>
<a name="vede"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Ici, nous allons voir comment effectuer une sauvegarde de tous les composants d'un sharded cluster. Cette procédure va utiliser les snapshots filesystem
en capturant une copie d'une instance mongod. Une autre procédure utilise mongodump pour créer une sauvegarde binaire lorsque le snapshot filesystem
n'est pas disponible.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Pour capturer une sauvegarde pointant dans le temps depuis un sharded cluster, vous devez impérativement arrêter toutes les écritures
	sur le cluster. Sur un système de production en cours d'exécution, vous pouvez uniquement capturer une approximation du snapshot pointant dans le temps.
</div>
<a name="prde"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>Dans cette procédure, vous allez devoir arrêter le balanceur du cluster et effectuer une sauvegarde de la base de données de configuration, puis ensuite,
effectuer une sauvegarde de chaque shard du cluster avec un outil de capture de snapshot. Si vous avez besoin d'un snapshot d'un moment exact dans le temps,
vous aurez besoin d'arrêter toutes les opérations d'écritures de votre application avant de prendre des snapshots filesystem, sinon, le snapshot 
va seulement prendre une approximation dans le temps.
Pour des snapshots pointant un moment approximatif dans le temps, vous pouvez améliorer la qualité de votre sauvegarde en minimisant l'impacte sur le cluster
en effectuant une sauvegarde depuis un membre secondaire du Replica Set que chaque shard fournit.

1) Désactivez le processus balanceur qui égalise la distribution à travers les shards du cluster. Pour désactiver le balanceur, utilisez la méthode
sh.stopBalancer() dans un shell mongo.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Il est essentiel que vous arrêtiez le balanceur avant de créer des sauvegardes. Si le balanceur reste actif, vos sauvegardes
	pourraient avoir des données dupliquées ou manquantes, vu que certains chunks peuvent migrer lors d'une sauvegarde en cours.
</div>

<div class="spacer"></div>

<p>2) Verrouillez un membre de chaque Replica Set dans chaque shard de manière à ce que vos sauvegardes reflettent l'état de votre base de données
au moment approximatif d'un moment dans le temps. Verrouillez ces instances mongod dans une intervalle aussi courte que possible.
Pour verrouiller ou geler un sharded cluster, vous devez :

- utiliser la méthode db.fsyncLock() dans un shell mongo connecté à un membre secondaire du Replica Set fournit par l'instance mongod du shard.
- Arrêtez un des serveurs de configuration pour éviter tout changement des méta-informations pendant le processus de sauvegarde

3) utilisez mongodump pour sauvegarder un des serveurs de configuration. Cela va sauvegarder les méta-informations du cluster. Vous avez besoin de
sauvegarder qu'un seul serveur de configuration vu qu'ils détiennent tous les même informations.
Utilisez cette commande sur l'une des instances mongod de configuration ou via mongos :</p>

<pre>mongodump --db config</pre>

<p>4) Sauvegardez les membres du replica set des shards que vous avezz verrouillés. Vous devriez sauvegarder les shards en parrallèle et, pour chaque shard,
créer un snapshot.

5) Déverouillez tous les membres verrouillés de replica set de chaque shard en utilisant la méthode db.fsyncUnlock() dans le shell mongo.

6) Re-activez le balanceur avec la méthode sh.setBalanceurState().
Utilisez la séquence de commandes suivante lorsque vous êtes connecté à mongos avec un shell mongo :</p>

<pre>
use config
sh.setBalancerState(true)
</pre>
<a name="bdd"></a>

<div class="spacer"></div>

<p class="titre">III) [ Sauvegarder un Sharded Cluster avec des Dumps de Base de Données ]</p>

<p></p>
<a name="vetr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Idem ici, nous allons voir comment créer une sauvegarde de tous les composants d'un sharded cluster en créeant dans dumps d'une instance mongod
avec mongodump. Une autre méthode utilise les snapshots de filesystem pour sauvegarder les données et serait plus efficace dans certaines situations
si la configuration de votre système d'exploitation autorise les sauvegardes de filesystem.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Pour capturer une sauvegarde pointant dans le temps depuis un sharded cluster, vous devez impérativement arrêter toutes les écritures
	sur le cluster. Sur un système de production en cours d'exécution, vous pouvez uniquement capturer une approximation du snapshot pointant dans le temps.
</div>
<a name="prtr"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>Dans cette procédure, vous allez arrêter le balanceur du cluster et effectuer une sauvegarde de la base de données de configuration, et ensuite,
effectuer une sauvegarde de chaque shard en utilisant un outil de snapshot filesystem. Si vous avez besoin de capturer un moment précis dans le temps du système,
vous aurez besoin dans un premier temps de stopper toutes les écritures de votre application avant d'effectuer des snapshots filesystem, sinon,
le snapshot ne supportera qu'un moment approximatif dans le temps.

Pour des snapshots approximatifs dans le temps, vous pouvez améliorer la qualité de votre sauvegarde en minimisant l'impacte sur le cluster en
effectuant une sauvegarde depuis un membre secondaire du replica set que fournit chaque shard.

1) Désactivez le processus balanceur qui égalise la distribution à travers les shards du cluster. Pour désactiver le balanceur, utilisez la méthode
sh.stopBalancer() dans un shell mongo.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Il est essentiel que vous arrêtiez le balanceur avant de créer des sauvegardes. Si le balanceur reste actif, vos sauvegardes
	pourraient avoir des données dupliquées ou manquantes, vu que certains chunks peuvent migrer lors d'une sauvegarde en cours.
</div>

<div class="spacer"></div>

<p>2) Verrouillez un membre de chaque Replica Set dans chaque shard de manière à ce que vos sauvegardes reflettent l'état de votre base de données
au moment approximatif d'un moment dans le temps. Verrouillez ces instances mongod dans une intervalle aussi courte que possible.
Pour verrouiller ou geler un sharded cluster, vous devez :

- Arrêtez un membre de chaque Replica Set
Assurez-vous que l'oplog a une capacité suffisante pour accepter ces secondaires de se remettre à niveau avec les membres primaires une fois le processus de sauvegarde
terminé.
- Arrêtez un des serveurs de configuration pour éviter tout changement des méta-informations pendant le processus de sauvegarde

3) utilisez mongodump pour sauvegarder un des serveurs de configuration. Cela va sauvegarder les méta-informations du cluster. Vous avez besoin de
sauvegarder qu'un seul serveur de configuration vu qu'ils détiennent tous les même informations.
Utilisez cette commande sur l'une des instances mongod de configuration ou via mongos :</p>

<pre>mongodump --journal --db config</pre>

<p>4) Sauvegardez les membres du replica set des shards qui s'arrêtent en utilisant mongodump et en spécifiant l'option --dbpath. Vous devriez sauvegarder les shards en parrallèle :</p>

<pre>mongodump --journal --dbpath /data/db/ --out /data/backup/</pre>

<div class="spacer"></div>

<p>Vous devez exécuter cette commande sur le système ou mongod s'est exécuté. Cette opération va utiliser le journaling et créer un dump de l'instance
mongod entière avec les fichiers de données situés dans /data/db. mongodump va générer un log de ce dump dans le répertoire /data/backup.</p>

5) Redémarrez tous les membres stoppés de replica set de chaque shard normalement, ce qui leur permettra de se mettre à niveau avec le primaire concerné.

6) Re-activez le balanceur avec la méthode sh.setBalanceurState().
Utilisez la séquence de commandes suivante lorsque vous êtes connecté à mongos avec un shell mongo :</p>

<pre>
use config
sh.setBalancerState(true)
</pre>
<a name="plan"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Plannifier une Période Maintenance pour les Sharded Clusters ]</p>

<p></p>
<a name="vequ"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Dans un sharded cluster, le processus balanceur est responsable de la distrbution des données (chunks) à travers ce cluster, de manière à ce que chaque shard
a approximativement la même proportion de données que les autres.
Par contre, lorsque vous créez des sauvegardes d'un sharded cluster, il est important de désactiver le balanceur lorsque vous prenez des sauvegardes
pour vous assurer qu'aucune migration de chunks affecte le contenu de la sauvegarde capturé par la procédure de sauvegarde.</p>
<a name="prqu"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>Si vous avez une sauvegarde automatique planifiée, vous pouvez désactiver toutes les opérations de balancing pour une période de temps.
Considérons la commande suivante par exemple :</p>

<pre>
use config
db.settings.update( { _id : "balancer" }, { $set : { activeWindow : { start : "6:00", stop : "23:00" ............
</pre>

<p>Cette opération configure le balanceur pour qu'il s'exécute entre 6h00 et 23h basé sur l'heure du serveur. Planifiez votre opération de sauvegarde
pour qu'elle s'exécute et se termine à l'extérieur de ce temps. Assurez-vous que la sauvegarde peut se compléter à l'extérieur de cette période 
lorsque le balanceur est en cours d'exécution et qu le balanceur balancer la collection correctement à travers les shards durant la période
qui leur ai attribuée à chacun.</p>
<a name="rest"></a>

<div class="spacer"></div>

<p class="titre">V) [ Restaurer un Simple Shard ]</p>

<p></p>
<a name="veci"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Restaurer un simple shard avec une sauvegarde avec d'autre shards non affectés nécessite un nombre d'opérations et de considérations particulières.
Nous allons donc voir comment procéder dans ce paragraphe. </p>
<a name="prci"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>Restaurez toujours les sharded clusters en entiers. Lorsque vous restaurez un simple shard, gardez en tête que le balanceur aura peut-être déplacé des chunks
depuis ou vers ce shard depuis la dernière sauvegarde. Si c'est le cas, vous devez déplacer manuellement ces chunks comme décrit dans la procédure suivante :

1) Restaurez le shard comme vous le feriez pour toute instance mongod. N'hésitez pas à revenir sur les chapitres précédents pour retrouver ces méthodes en détails.

2 Pour chaque chunk qui migre hors de ce shard, vous n'avez pas besoin de faire quelque chose. Vous n'avez pas besoin de supprimer ces documents
du shard car les chunks sont automatiquement filtrés depuis les requêtes par mongos. Vous pouvez supprimer ces documents du shard si vous le désirez.

3) Pour les chunks qui migrent vers ce shard après la dernière sauvegarde la plus récente, vous devez restaurer manuellement les chunks en utilisant les sauvegardes
des autres shards, ou d'autres sources. Pour déterminer quels chunks ont migrés, jettez-un oeil à la collection changelog dans la base de données de configuration.</p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Restaurer un Sharded Cluster ]</p>

<p></p>
<a name="vesi"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>La procédure ici va décrire comment restaurer un sharded cluster entier. La procédure exacte utilisée pour restaurer une base de données dépend de la méhode
utilisée pour capturer une sauvegarde.</p>
<a name="prsi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>1) Stoppez tous les processus mongod et mongos.

2) Si les noms d'hôtes de shard ont changés, vous devez mettre à jour manuellement la collection shards dans la base de données de configuration pour utiliser
les nouveaux noms d'hôtes. Effectuez les étapes suivantes :
	a) démarrez les trois serveurs de configuration en utilisant une commande similaire :</p>
	
<pre>mongod --configsvr --dbpath /data/configdb --port 27019</pre>

<p>	b) Restaurez la base de données de configuration sur chaque serveur de configuration.

	c) Démarrez une instance mongos.
	
	d) Mettez à jour la collection shards de la base de données de configuration pour appliquer les nouveaux noms d'hôtes.

3) Restaurez les suivants :
	- les fichiers de données pour chaque serveur de chaque shard. Car les replica sets fournissent chaque shard de production, restaurez tous les membres
	du replica set ou utilisez l'autre approche standart pour restaurer un replica set avec une sauvegarde. 
	- Les fichiers de données pour chaque serveur de configuration, si vous ne l'avez pas déjà fait dans l'étape précédente.

4) Redémarrez toutes les instances mongos.

5) Redémarrez toutes les instances mongod.

6) connectez-vous à une instance mongos depuis un shell mongo et utilisez la méthode db.printShardingStatus() pour vous assurer que le cluster est
complètement opérationnel, comme suivant :</p>

<pre>
db.printShardingStatus()
show collections
</pre>

<div class="spacer"></div>

<p>La suite va concerner <a href="copier_bdd.php">"Copier des Bases de Données entre des Instances" >></a>.</p>

<?php

	include("footer.php");

?>
