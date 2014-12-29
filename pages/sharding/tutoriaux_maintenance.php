<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Maintenance de Cluster Partagé</li>
</ul>

<p class="titre">[ Maintenance de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#conf">I) Afficher la Configuration du Cluster</a></p>
	<p class="right"><a href="#list">- a) Lister les Bases de Données avec le Sharding Activé</a></p>
	<p class="right"><a href="#ls">- b) Lister les Shards</a></p>
	<p class="right"><a href="#deta">- c) Voir les Détails du Cluster</a></p>
	<p class="elem"><a href="#nh">II) Migrer les Serveurs de Configuration avec le même Nom d'Hôte</a></p>
	<p class="elem"><a href="#nhd">III) Migrer les Serveurs de Configuration avec des Noms d'Hôte Différents</a></p>
	<p class="elem"><a href="#remp">IV) Remplacer un Serveur de Configuration</a></p>
	<p class="elem"><a href="#hard">V) Migrer un Sharded Cluster sur du Hardware Différent</a></p>
	<p class="right"><a href="#desa">- a) Désactiver le Balanceur</a></p>
	<p class="right"><a href="#migr">- b) Migrer chaque Serveur de Configuration Séparement</a></p>
	<p class="right"><a href="#rede">- c) Redémarrer les Instances mongos</a></p>
	<p class="right"><a href="#ms">- d) Migrer les Shards</a></p>
	<p class="right"><a href="#reac">- e) Re-activer le Balanceur</a></p>
	<p class="elem"><a href="#sauv">VI) Sauvegarder les Méta-Informations du Cluster</a></p>
	<p class="elem"><a href="#comp">VII) Configurer le Comportement du Processus Balanceur dans les Sharded Clusters</a></p>
	<p class="right"><a href="#plan">- a) Planifier une Période pour le Balancement</a></p>
	<p class="right"><a href="#tail">- b) Configurer la Taille de Chunk par Défaut</a></p>
	<p class="right"><a href="#espa">- c) Changer l'Espace de Stockage Maximum pour un Shard Donné</a></p>
	<p class="right"><a href="#exig">- d) Exiger la Réplication avant la Migration de Chunk</a></p>
	<p class="elem"><a href="#gere">VIII) Gérer le Balanceur d'un Sharded Cluster</a></p>
	<p class="right"><a href="#etat">- a) Vérifier l'Etat du Balanceur</a></p>
	<p class="right"><a href="#verro">- b) Vérifier le Verrou du Balanceur<a></p>
	<p class="right"><a href="#peri">- c) Planifier la Période de Balancement<a></p>
	<p class="right"><a href="#bala">- d) Supprimer une Période de Balancement Planifiée<a></p>
	<p class="right"><a href="#desab">- e) Désactiver le Balanceur<a></p>
	<p class="right"><a href="#actib">- f) Activer le Balanceur<a></p>
	<p class="right"><a href="#balab">- g) Désactiver le Balancement Pendant une Sauvegarde<a></p>
	<p class="elem"><a href="#supp">IX) Supprimer des Shards d'un Sharded Cluster Existant</a></p>
	<p class="right"><a href="#assu">- a) S'assurer que le Processus Balanceur est Activé</a></p>
	<p class="right"><a href="#deter">- b) Déterminer le Nom du Shard à Supprimer</a></p>
	<p class="right"><a href="#suppc">- c) Supprimer les Chunks d'un Shard</a></p>
	<p class="right"><a href="#veris">- d) Verifier le Statut de la Migration</a></p>
	<p class="right"><a href="#depld">- e) Déplacer les Données Non Shardées</a></p>
	<p class="right"><a href="#fina">- f) Finaliser la Migration</a></p>
</div>

<p>Vous voilà sur la page de maintenance de Cluster Partagé, c'est ici qu'il faut venir si vous avez un remplacement de shard, un changement de serveur pour votre cluster
ou encore une migration de serveur. Bonne lecture !</p>
<a name="conf"></a>

<div class="spacer"></div>

<p class="titre">I) [ Afficher la Configuration du Cluster ]</p>

<p></p>
<a name="list"></a>

<div class="spacer"></div>

<p class="small-titre">a) Lister les Bases de Données avec le Sharding Activé</p>

<p>Pour lister les bases de données qui ont le Sharding d'activé, interrogez la collection "databases" dans la base de données de configuration.
Une base de données a le Sharding actif si la valeur du champ "partitioned" est true. Connectez-vous à une instance mongos avec un shell mongo et exécutez 
la commande suivante afin d'obtenir la liste complète des bases de données ayant le Sharding activé :</p>

<pre>
use config
db.databases.find( { "partitioned": true } ) 
</pre>

<div class="spacer"></div>

<p>Par exemple, vous pouvez utiliser la séquence de commandes suivante pour retourner la liste complète des bases de données dans le cluster :</p>

<pre>
use config
db.databases.find()
</pre>

<div class="spacer"></div>

<p>En retournant le résultat suivant, on peut voir que le Sharding est activé uniquement pour la base de données animals :</p>

<pre>
{ "_id" : "admin", "partitioned" : false, "primary" : "config" }
{ "_id" : "animals", "partitioned" : true, "primary" : "m0.example.net:30001" }
{ "_id" : "farms", "partitioned" : false, "primary" : "m1.example2.net:27017" }
</pre>
<a name="ls"></a>

<div class="spacer"></div>

<p class="small-titre">b) Lister les Shards</p>

<p>Pour lister l'ensemble actuels de shards configurés :</p>

<pre>
use admin
db.runCommand( { listShards : 1 } )
</pre>
<a name="deta"></a>

<div class="spacer"></div>

<p class="small-titre">c) Voir les Détails du Cluster</p>

<p>Pour voir les détails du cluster, utilisez la commande db.printShardingStatus() ou sh.status(). Les deux méthodes retournent le même résultat.
Par exemple, avec la commande sh.status() :
- sharding version affiche le numéro de version des méta-informations du shard
- shards affiche la liste des instances mongod utilisées comme shard au sein du cluster
- databases affiche toutes les bases de données du cluster, en incluant les bases qui n'ont pas le sharding d'activé
- L'information chunks pour la base de données "foo" affiche combien de chunks sont sur chaque shard et affiche les limites de chaque shard</p>

<pre>
--- Sharding Status ---
sharding version: { "_id" : 1, "version" : 3 }
shards:
{ "_id" : "shard0000", "host" : "m0.example.net:30001" }
{ "_id" : "shard0001", "host" : "m3.example2.net:50000" }
databases:
{ "_id" : "admin", "partitioned" : false, "primary" : "config" }
{ "_id" : "contacts", "partitioned" : true, "primary" : "shard0000" }
foo.contacts
shard key: { "zip" : 1 }
chunks:
shard0001 2
shard0002 3
shard0000 2
{ "zip" : { "$minKey" : 1 } } -->> { "zip" : 56000 } on : shard0001 { "t" : 2, "i" : 0 }
{ "zip" : 56000 } -->> { "zip" : 56800 } on : shard0002 { "t" : 3, "i" : 4 }
{ "zip" : 56800 } -->> { "zip" : 57088 } on : shard0002 { "t" : 4, "i" : 2 }
{ "zip" : 57088 } -->> { "zip" : 57500 } on : shard0002 { "t" : 4, "i" : 3 }
{ "zip" : 57500 } -->> { "zip" : 58140 } on : shard0001 { "t" : 4, "i" : 0 }
{ "zip" : 58140 } -->> { "zip" : 59000 } on : shard0000 { "t" : 4, "i" : 1 }
{ "zip" : 59000 } -->> { "zip" : { "$maxKey" : 1 } } on : shard0000 { "t" : 3, "i" : 3 }
{ "_id" : "test", "partitioned" : false, "primary" : "shard0000" }
</pre>
<a name="nh"></a>

<div class="spacer"></div>

<p class="titre">II) [ Migrer les Serveurs de Configuration avec le même Nom d'Hôte ]</p>

<p>Cette procédure va migrer un serveur de configuration d'un sharded cluster vers un nouvau système qui utilise le même nom d'hôte.
Pour migrer tous les serveurs de configuration d'un cluster, effectuez cette procédure pour chaque serveur séparement et migrez les serveurs de configuration
dans l'ordre inverse de la liste du string configdb de chaque instance mongos. Commencez par le dernier serveur de configuration listé dans le string
configdb :

1) Arrêter le serveur de configuration (ce qui rend toutes les données de configuration du sharded cluster en mode "read-only".
2) Changez le DNS qui pointe vers le système qui fournissait l'ancien serveur de confguration, afin que le même nom d'hôte pointe vers le nouveau système.
La manière dont vous aller procéder va dépendre de la façon dont vous avez organisé vos services DNS et de résolution de nom d'hôte.
3) Copiez le contenu du répertoire de données (dbpath) de l'ancien vers le nouveau serveur de configuration.</p>

<p>Par exemple, pour copier le contenu d'une machine nommée mongodb.config2.example.net, utilisez la commande suivante :</p>

<pre>rsync -az /data/configdb mongodb.config2.example.net:/data/configdb</pre>

<p>4) Démarrez l'instance du serveur de configuration sur le nouveau système :</p>

<pre>mongod --configsvr</pre>

<p>Lorsque vous démarrez le troisième serveur de configuration, votre cluster va devenir "writable" et va pouvoir créer de nouveaux chunks et les migrer.</p>
<a name="nhd"></a>

<div class="spacer"></div>

<p class="titre">III) [ Migrer les Serveurs de Configuration avec des Noms d'Hôte Différents ]</p>

<p>Cette procédure va migrer un serveur de configuration d'un sharded cluster vers un nouveau serveur qui utilise un nom d'hôte différent. Utilisez cette procédure
seulement si le serveur de configuration ne sera pas accessible via le même nom d'hôte.

Changer le nom d'hôte d'un serveur de configuration requiert un temps d'arrêt pour celui-ci mais aussi le redémarrage de tous les processus du sharded cluster.
Si possible, évitez de changer de nom d'hôte, de cette manière vous pourrez utiliser la procédure ci-dessus, de migration de server de configuration ayant
le même nom d'hôte.

Pour migrer tous les serveurs de configuration d'un cluster, effectuez cette procédure pour chaque serveur séparement et migrez les serveurs de configuration
dans l'ordre inverse de la liste du string configdb de chaque instance mongos. Démarrez avec le dernier serveur de configuration du string configdb :</p>

<p>1) Désactivez le processus balanceur du cluster temporairement.
2) Arrêter le serveur de configuration (ce qui rend toutes les données de configuration du sharded cluster en mode "read-only".
3) Copiez le contenu du répertoire de données (dbpath) de l'ancien vers le nouveau serveur de configuration.</p>

<div class="spacer"></div>

<p>Par exemple, pour copier le contenu d'une machine nommée mongodb.config2.example.net, utilisez la commande suivante :</p>

<pre>rsync -az /data/configdb mongodb.config2.example.net:/data/configdb</pre>

<p>4) Démarrez l'instance du serveur de configuration sur le nouveau système :</p>

<pre>mongod --configsvr</pre>

<p>5) Arrêtez tous les processus MongoDB (instances mongod ou replica sets, mongod des serveurs de configuration et les mongos).
6) Redémarrez tous les processus mongod fournit par les shards.
7) Mettez à jour le paramètre configdb pour chaque instance mongos.
8) Redémarrez les instances mongos.
9) Ré-activez le processus balanceur pour autoriser le cluster à reprendre ses opérations de balancements habituelles.</p>
<a name="remp"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Remplacer un Serveur de Configuration ]</p>

<p>Cette procédure va permettre de remplacer un serveur de configuration inopérant dans un sharded cluster. N'utilisez cette procédure uniquement
si un serveur ne peut plus rien faire, par exemple échec hardware.

Ce processus part du principe que le nom d'hôte ne va pas changer. Si vous devez changer le nom d'hôte de l'instance, utilisez la procédure de
migration de serveur de configuration en utilisant un nouveau nom d'hôte expliquée ci-dessus.

1) Désactivez le processus balanceur du cluster temporairement.
2) Fournissez un nouveau système ayant le même nom d'hôte que le système précédent.
Vous allez devoir vérifier que le nouveau système a la même adresse IP et le même nom d'hôte que l'ancien ou alors vous allez devoir modifier le DNS.
3) Arrêter un seul serveur de configuration (un seul uniquement). Copiez tout son répertoire de données (dbpath) vers le nouveau système. Vous pouvez utiliser la commande
suivante :</p>

<pre>rsync -az /data/configdb mongodb.config2.example.net:/data/configdb</pre>

<p>4) Redémarrez le processus du serveur de configuration que vous avez utilisez dans l'étape précédente afin de copier les données vers l'instance du nouveau
serveur de configuration.
5) Démarrez l'instance du nouveau serveur de configuration :</p>

<pre>mongod --configsvr</pre>

<p>6) Ré-activez le processus balanceur pour autoriser le cluster à reprendre ses opérations de balancements habituelles.</p>

<div class="alert alert-info">
	<u>Note</u> : Durant cette procédure, ne supprimez jamais un serveur de configuration du paramètre configdb sur n'importe laquelle
	des instances mongos. Si vous devez changer le nom d'un serveur de configuration, soyez toujours sûrs que toutes les instances mongos ont bien trois
	serveurs de configuration spécifié dans le paramètre configdb tout le temps.
</div>
<a name="hard"></a>

<div class="spacer"></div>

<p class="titre">V) [ Migrer un Sharded Cluster sur du Hardware Différent ]</p>

<p>Cette procédure permet de déplacer tous les composants d'un cluster shardé vers un autre système sans temps d'arrêt pour les opérations de lecture et d'écriture.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Pendant que la migration est en cours, n'essayez pas de modifier les méta-informations du cluster. N'utilisez aucune opération qui
	modifie les méta-informations dans tous les cas. Par exemple, ne créez pas ou ne supprimez pas de bases de données ou de collections, ni même d'utiliser des
	commandes de sharding.
</div>

<div class="spacer"></div>

<p>Si votre cluster inclus un shard qui est une instance mongod en mode standalone, vous pouvez la convertir en un Replica Set afin de simplifier la migration
et de vous permettre de garder le cluster en ligne durant de futures maintenances. Migrer un shard qui est une instance mongod standalone représente plusieurs
étapes et nécessit un temps d'arrêt pour le shard.</p>
<a name="desa"></a>

<div class="spacer"></div>

<p class="small-titre">a) Désactiver le Balanceur</p>

<p>Vous pouvez désactiver le balanceur pour stopper la migration des chunks et afin de ne permettre aucune opération d'écriture sur les méta-informations
jsqu'à ce que le processus se termine. Si la migration est en cours, le balanceur va terminer l'étape de migration en cours avant de s'arrêter.

Pour désactiver le balanceur, connectez-vous à l'une des instances mongos du cluster et utilisez la commande suivante :</p>

<pre>sh.stopBalancer()</pre>

</p>Pour vérifier l'état du balanceur :</p>

<pre>sh.getBalancerState()</pre>
<a name="migr"></a>

<div class="spacer"></div>

<p class="small-titre">b) Migrer chaque Serveur de Configuration Séparement</p>

<p>Migrez chaque serveur de configuration en commençant par le dernier serveur de configuration listé dans la chaîne de caractères "configdb". Procédez dans l'ordre
inverse de cette chaîne. Migrez et redémarrez un serveur de configuration avant de passer au serveur suivant. Ne renommez pas un serveur de configuration
pendant le processus.</p>

<div class="alert alert-info">
	<u>Note</u> : Si le nom ou l'adresse, que le cluster utilise pour se connecter à un serveur de configuration, change, alors vous devrez redémarrer
	toutes les instances mongos et mongod se trouvant dans le sharded cluster. Evitez le temps d'arrêt en utilisant les CNAMEs pour identifier les serveurs de
	configuration lors d'un déploiement MongoDB.
</div>

<div class="spacer"></div>

<p>1) Arrêter le serveur de configuration (Cela rend toutes les données de configuration du sharded cluster en mode "read-only".
2) Changez le DNS qui pointe vers le système qui hébergait l'ancien serveur de configuration, de manière à ce que le même nom d'hôte pointe sur le nouveau
système. LA façon dont vous allez procéder va dépendre de comment vous avez organisé votre DNS et la résolution du nom d'hôte.
3) Copiez le contenu du répertoire de données (dbpath) de l'ancien serveur de configuration vers le nouveau.
Par exemple, pour copier le dbpath d'une machine nommée mongodb.config2.example.net, veuillez utiliser la commande suivante :</p>

<pre>rsync -az /data/configdb mongodb.config2.example.net:/data/configdb</pre>

<p>4) Démarrez l'instance du serveur de configuration sur le nouveau système :</p>

<pre>mongod --configsvr</pre>
<a name="rede"></a>

<div class="spacer"></div>

<p class="small-titre">c) Redémarrer les Instances mongos</p>

<p>Si le string configdb va changer pendant la migration, vous devez arrêter toutes les instances mongos avant de changer ce string. Cela évite les erreurs
au sein du sharded cluster concernant le string configdb.

Si configdb reste identique, vous pouvez migrer les instances mongos séquentiellement ou toutes en une fois.

1) Arrêtez les instances mongos en utilisant la commande shutdown. Si le string configdb change, arrêtez toutes les instances mongos.

2) Si le nom d'hôte a changé pour n'importe quel serveur de configuration, mettez à jour le string configdb pour chaque instance mongos. Les instances mongos
doivent toutes utiliser le même string. Les strings doivent lister des hôtes ayant des noms similaires dans le même ordre.</p>

<div class="alert alert-success">
	Astuce : Pour éviter le temps d'arrêt, donnez à chaque serveur de configuration un DNS logique (non relaté au nom d'hôte physique ou virtuel
	du serveur). Sans DNS logique, bouger ou renommer un serveur de configuration nécessite le redémarrage de toute instance mongos et mongod du sharded cluster.
</div>

<div class="spacer"></div>

<p>3) Redémarrez les instances mongos en étant sûr d'utiliser le string configdb mis à jour si les noms d'hôtes ont changés.</p>
<a name="ms"></a>

<div class="spacer"></div>

<p class="small-titre">d) Migrer les Shards</p>

<p>Il faut migrer les shards un par un et vous devrez suivre la procédure pour chacun d'entre eux.

Migrer un Shard Replica Set : Pour migrer un sharded cluster, migrez chaque membre séparement. Premièrement, migrez les membres non-primaires, puis
le primaire en dernier.
Si le replica set a deux membres votants, ajoutez un arbitre au replica set afin de vous assurer que l'ensemble garde une majorité de ses votes disponibles
durant la migration. Vous pouvez supprimer l'arbitre après la migration complétée.</p>

<p>Migrer un membre d'un shard replica set : 

1) Arrêter le processus mongod. Pour le terminer correctement, utilisez la commande shutdown.
2) Déplacez le répertoire de données (dbpath) sur le nouveau système.
3) Redémarrez le processus mongod sur la nouvelle location.
4) Connectez-vous au primaire du replica set actuel.
5) Si le nom d'hôte du membre a changé, utilisez la commande rs.reconfig() pour mettre à jour le document de configuration du replica set avec le nouveau nom d'hôte.

Par exemple, les séquences de commandes suivantes mettent à jour le nom d'hôte pour l'instance en position 2 du tableau des membres :</p>

<pre>
cfg = rs.conf()
cfg.members[2].host = "pocatello.example.net:27017"
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>6) Pour confirmer la nouvelle configuration, utilisez rs.conf.
7) Attendez que le membre se restaure. Pour vérifier son état, utilisez rs.statut().</p>

<p>Migrer le membre primaire d'un shard replica set : Pendant que vous migrez le primaire du replica set, l'ensemble doit élire un nouveau primaire.
Ce processus de FailOver rend le replica set indisponible pour les opérations de lectures et d'écritures pendant la durée de l'élection, qui se termine rapidement
en général. Si possible, planifiez la migration pendant une période de maintenance.

1) Rétrogradez le primaire pour déclencher le processus normal de FailOver. Pour rétrograder le primaire, connectez-vous au primaire et utilisez soit la commande
replSetStepDown ou la méthode rs.stepDown(). L'exemple suivant utilise rs.stepDown() :</p>

<pre>rs.stepDown()</pre>

<p>2) Une fois que le primaire a rétrogradé, un autre membre est devenu primaire à sa place. Pour migrer le primaire rétrogradé, suivez la procédure de
Migration d'un Membre d'un Shard Replica Set ci-dessus.

Vous pouvez vérifier le changement de statut en utilisant la méthode rs.status().</p>

<p>Migrer un Shard Standalone : La procédure idéale pour migrer un shard standalone est de le convertir en un replica set et ensuite d'utiliser la procédure
de migration pour un shard replica set dans le paragraphe précédent. Dans les clusters de production, tous les shards doivent être des replica sets,
ce qui fournit une disponibilité continue pendantl les périodes de maintenance.

Migrer un shard standalone est un processus à plusieurs étapes pendant lequel une partie du shard sera indisponible. Si le shard est le shard primaire
de la base de données, le processus appelle la commande movePrimary. Pendant que la commande movePrimary est en cours d'exécution, vous devrez arrêter
de modifier les données dans la base de données. Pour migrer le shard standalone, utilisez la procédure de Suppression de Shards sur un Sharded Cluster Existant.</p>
<a name="reac"></a>

<div class="spacer"></div>

<p class="small-titre">e) Re-activer le Balanceur</p>

<p>Pour enfin terminer la migration, re-activez le balanceur pour reprendre la migration des chunks.
Connectez-vous à l'une des instances mongos et passez le paramètre true à la méthode sh.setBalancerState() :</p>

<pre>sh.setBalancerState(true)</pre>

<p>Pour vérifier l'état du balanceur, utilisez la méthode sh.getBalancerState().</p>
<a name="sauv"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Sauvegarder les Méta-Informations du Cluster ]</p>

<p>Cette procédure arrête l'instance mongod d'un serveur de configuration dans le but de créer une sauvegarde(backup) des méta-infomations d'un sharded cluster.
Les serveurs de configuration du cluster stockent toutes les méta-informations du cluster, et surtout le plus important, l'association des chunks aux shards.

Quand vous effectuez cette opération, le cluster reste opérationnel :

1) Désactivez le processus balanceur du cluster temporairement
2) Arrêtez l'une des bases de données de configuration
3) Créez une copie complète des fichiers de données (dbpath de l'instance)
4) Redémarrez le serveur de configuration original
5) Re-activez le balanceur pour autoriser le cluster à reprendre ses opérations de balancement habituelles.</p>
<a name="comp"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Configurer le Comportement du Processus Balanceur dans les Sharded Clusters ]</p>

<p>Le balanceur est un processus qui s'exécute sur l'une des instances mongos dans un cluster et s'assure que les chunks sont distribués de la même façon
à travers les shards de ce cluster. Dans la plupart des déploiements, la configuration par défaut du balanceur est suffisante pour les opérations
normales. En revanche, les administrateurs pourraient avoir besoin de modifier le comportement du balanceur en fonction des besoins de leur application.
Si vous rencontrez une situation ou vous aurez besoin de modifier le comportement du balanceur, utilisez les procédures décrites un peu plus loin.</p>
<a name="plan"></a>

<div class="spacer"></div>

<p class="small-titre">a) Planifier une Période pour le Balancement</p>

<p>Vous pouvez planifier une période de temps pendant laquelle le balanceur va migrer les chunks, comme décris dans les procédures suivantes :
- Planifier la période de balancement
- Supprimer une période de balancement planifiée

Les instances mongos utilisent leur propre timezone locale afin de respecter la planification.</p>
<a name="tail"></a>

<div class="spacer"></div>

<p class="small-titre">b) Configurer la Taille de Chunk par Défaut</p>

<p>La taille de chunk par défault est de 64mo. Dans la plupart des situations, la taille par défaut est appropriée pour la séparation et la migration de chunks.
Changer la taille par défaut des chunks affecte les chunks qui sont traités pendant un processus de migration et d'auto-séparation mais n'affecte pas les chunks
rétroactivement.</p>
<a name="espa"></a>

<div class="spacer"></div>

<p class="small-titre">c) Changer l'Espace de Stockage Maximum pour un Shard Donné</p>

<p>Le champ maxSize dans la collection shards dans le serveur de configuration définit la taille maximum pour un shard, vous permettant de contrôler si un balanceur
va migrer les chunks sur un shard. Si la taille associée est au dessus de la taille maxSize du shard, alors le balanceur ne migrera par de chunks sur celui-ci.
De plus, le balanceur ne va pas migrer de chunks sur un shard surchargé de travail. Cela doit se faire manuellement. La valeur maxSize affecte seulement
la sélection que le balanceur va effectuer pour trouver le shards de destination.

Par défaut, maxSize n'est pas spécifié, ce qui permet aux shards de consommer l'espace disque disponible su la machine si nécessaire.
Vous pouvez définir maxSize soit en ajoutant un shard soit quand un shard est en cours d'exécution.

Pour définir maxSize lorsque vous ajoutez un shard, définissez la commande addShard avec le paramètre maxSize avec la taille maximum en mo. Par exemple,
la commande suivante, au sein , d'un shell mongo, ajoute le shard avec une taille maximale de 125mo :</p>

<pre>db.runCommand( { addshard : "example.net:34008", maxSize : 125 } )</pre>

<div class="spacer"></div>

<p>Pour définir maxSize sur un shard existant, insérez ou mettez à jour le champ maxSize de la collection shards dans la base de données de configuration.
Définissez maxSize en mo.

Exemple, si vous avez ce shard sans maxSize définit :</p>

<pre>{ "_id" : "shard0000", "host" : "example.net:34001" }</pre>

<p>Effectuez la commande suivante dans un shell mongo pour ajouter un maxSize de 125mo :</p>

<pre>
use config
db.shards.update( { _id : "shard0000" }, { $set : { maxSize : 125 } } )
</pre>

<p>Si plus tard vous voudrez modifier cette taille maxSize à 250mo :</p>

<pre>
use config
db.shards.update( { _id : "shard0000" }, { $set : { maxSize : 250 } } )
</pre>
<a name="exig"></a>

<div class="spacer"></div>

<p class="small-titre">d) Exiger la Réplication avant la Migration de Chunk</p>

<p>Nouveau dans la version 2.2.1, l'option _secondaryThrottle est devenue une option pour le balanceur et pour la commande moveChunk.
_secondaryThrottle rends possible le fait de demander à ce que le balanceur attende la réplication sur les membres secondaires durant la migration.

Changé dans la version 2.4, _secondaryThrottle est devenu le mode par défaut pour toutes les opérations du balanceur et de la commande moveChunk.
La valeur par défaut de _secondaryThrottle est true. Définissez-là à false pour l'effet inverse.

Vous activez ou désactivez _secondaryThrottle directement dans la collection settings de la base de données de configuration en exécutant les commandes suivantes via
un shell mongo qui va se connecter à l'instance mongos :</p>

<pre>
use config
db.settings.update( { "_id" : "balancer" } , { $set : { "_secondaryThrottle" : true } } , { upsert :............
</pre>

<p>Vous pouvez également activer un throttle secondaire lorsque vous utilisez la commande moveChunk en définissant _secondaryThrottle à true.</p>
<a name="gere"></a>

<div class="spacer"></div>

<p class="titre">VIII) [ Gérer le Balanceur d'un Sharded Cluster ]</p>

<p>Maintenant, nous allons décrire certaines procédures administratives pour gérer le balancement.</p>
<a name="etat"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vérifier l'Etat du Balanceur</p>

<p>La commande suivante vérifie si le balanceur est activé (si le balanceur peut s'exécute). Mais elle ne vérifie pas si le balanceur est actif (si il est en train
de balancer des chunks) :</p>

<pre>sh.getBalancerState()</pre>
<a name="verro"></a>

<div class="spacer"></div>

<p class="small-titre">b) Vérifier le Verrou du Balanceur</p>

<p>Pour vérifier si le processus balanceur est actif dans votre cluster, effectuez les étapes suivantes :

1) Connectez-vous à l'un des mongos du cluster en utilisant un shell mongo.
2) Utilisez la commande suivante pour sélectionner la base de données de configuration :</p>

<pre>use config</pre>

<p>3) Utilisez la requête suivante afin de returner le verrou du balanceur :</p>

<pre>db.locks.find( { _id : "balancer" } ).pretty()</pre>

<p>Une fois que cette commande est terminée, vous obtiendrez un résultat comme celui-ci :</p>

<pre>
{ 
	"_id" : "balancer",
	"process" : "mongos0.example.net:1292810611:1804289383",
	"state" : 2,
	"ts" : ObjectId("4d0f872630c42d1978be8a2e"),
	"when" : "Mon Dec 20 2010 11:41:10 GMT-0500 (EST)",
	"who" : "mongos0.example.net:1292810611:1804289383:Balancer:846930886",
	"why" : "doing balance round"
}
</pre>

<p>Le résultat en sortie confirme que :
- Le balanceur provient de l'instance mongos exécutée sur la machine mongos0.example.net.
- La valeur dans le champ state indique qu'un mongos a le verrou. Pour la version 2.0 ou supérieure, la valeur d'un verrou actif est 2, avant la 2.0, la valeur est de 1.</p>
<a name="peri"></a>

<div class="spacer"></div>

<p class="small-titre">c) Planifier la Période de Balancement</p>

<p>Dans certaines situations, particulièrement quand votre ensemble de données grossit lentement et qu'une migration peut avoir un impacte sur les performances,
il est utile de de s'assurer que le balanceur est actif seulement à un certain temps. Utilisez la procédure suivante pour spécifier une période pendant laquelle
le balanceur va pouvoir migrer les chunks :

1) Connctez-vous à un mongos via un shell mongo.
2) Utilisez la commande suivante pour choisir la base de données de configuration :</p>

<pre>use config</pre>

<p>3) Utilisez une opération du même type que l'exemple d'update() suivant afin de modifier la période de balancement du balanceur :</p>

<pre>db.settings.update({ _id : "balancer" }, { $set : { activeWindow : { start : "start-time", stop...........</pre>

<p> Remplacez start-time et end-time avec des valeurs de temps utilisant deux nombres pour l'heure et pour les minutes (HH:MM) qui décrivent les limites
de début et de fin de la période de balancement. Ces temps vont être évalués en fonction de la timezone de chaque instances mongos individuelle du sharded cluster.
Si vos instances mongos sont physiquement localisées dans différentes timezones, utilisez une timezone commune (Par exemple : GMT) pour vous assurez que la
période de balancement est interprétée correctement. 

Par exemple, exécuter la requête suivante va forcer le balanceur à s'exécuter pendant 23h00 et 06h00, heure locale seulement :</p>

<pre>db.settings.update({ _id : "balancer" }, { $set : { activeWindow : { start : "23:00", stop : "6:............</pre>

<div class="alert alert-info">
	<u>Note</u> : La période de balancement doit être suffisante pour terminer la migration de toutes les données insérées pendant la journée.
	Comme le taux d'insertion des données peut varier selon l'activité du cluster, il est important de vous assez que la période de balancement que vous sélectionnez
	soit suffisante pour supporter les besoins de votre déploiement.
</div>
<a name="bala"></a>

<div class="spacer"></div>

<p class="small-titre">d) Supprimer une Période de Balancement Planifiée</p>

<p>Si vous avez définit la période de balancement et que vous souhaitez la supprimer pour que le balanceur soit toujours en action, utilisez les commandes suivantes :</p>

<pre>
use config
db.settings.update({ _id : "balancer" }, { $unset : { activeWindow : true } })
</pre>
<a name="desab"></a>

<div class="spacer"></div>

<p class="small-titre">e) Désactiver le Balanceur</p>

<p>Par défaut, le balanceur peut s'exécuter à n'importe quel moment et migre les chunks en temps voulu. Pour désactiver le balanceur pour une courte période
et d'empêcher toutes les migrations, utilisez la procédure suivante :

1) Connectez-vous à un mongos via un shell mongo.
2) Utilisez la commande suivante pour désactiver le balanceur :</p>

<pre>sh.setBalancerState(false)</pre>

<p>Si une migration est en cours, le système va attendre la fin de celle-ci avant de stopper.

3) Pour vérifier que votre balanceur s'est bien arrêté, utilisez la commande suivante, qui retourne false si le balanceur est désactivé :</p>

<div class="spacer"></div>

<pre>sh.getBalancerState()</pre>

<p>En option, pour vérifier qu'aucune migration est en cours après l'arrêt du balanceur, utilisez la commande suivante dans un shell mongo :</p>

<pre>
use config
while( sh.isBalancerRunning() ) {
	print("waiting...");
	sleep(1000);
}
</pre>

<div class="alert alert-success">
	<u>Astuce</u> : Pour désactiver un balanceur d'un driver qui n'a pas la commande sh.startBalancer(), utilisez la commande suivante depuis la base de 
	données de configuration :
</div>

<div class="spacer"></div>

<pre>db.settings.update( { _id: "balancer" }, { $set : { stopped: true } } , true )</pre>
<a name="actib"></a>

<div class="spacer"></div>

<p class="small-titre">f) Activer le Balanceur</p>

<p>Utilisez cette procédure si vous avez désactivé le balanceur et que vous souhaitez le ré-activer :

1) Connectez-vous à un des mongos via un shell mongo.
2) Effectuez l'une de ces opérations pour activer le balanceur :

- Depuis un shell mongo :</p>

<pre>sh.setBalancerState(true)</pre>

<p>- Depuis un driver qui n'a pas la commande sh.startBalancer(), utilisez la commande ci-dessous depuis la base de données de configuration :</p>

<pre>db.settings.update( { _id: "balancer" }, { $set : { stopped: false } } , true )</pre>
<a name="balab"></a>

<div class="spacer"></div>

<p class="small-titre">g) Désactiver le Balancement Pendant une Sauvegarde</p>

<p>Si MongoDB migre des chunks pendant une sauvegarde, vous pouvez vous retrouver avec un snapshot incomplet de votre sharded cluster.
N'effectuez jamais de sauvegarde lorsque votre balanceur est actif. Pour être sûr que votre balanceur est inactif pendant une opération de sauvegarde :

- Définissez la période de balancement de manière à ce que le balanceur soit inactif pendant la sauvegarde. Assurez-vous que votre sauvegarde se termine pendant
que le balanceur est désactivé.
- Désactivez manuellement le balanceur pendant la durée de la procédure de sauvegarde.</p>

<p>Si vous arrêtez le balanceur pendant qu'il est en plein milieu d'un tour de balancement, l'arrêt n'est pas instantanné. Le balanceur termine la migration
de chunks en cours et arrête tous les autres balancements.

Avant de commencer une opération de sauvegarde, confirmez que le balanceur n'est pas actif. Vous pouvez utiliser la commande suivante pour déterminer si le
balanceur est actif ou non :</p>

<pre>!sh.getBalancerState() && !sh.isBalancerRunning()</pre>

<p>Lorsque la sauvegarde est complète, vous pouvez réactiver le processus balanceur.</p>
<a name="supp"></a>

<div class="spacer"></div>

<p class="titre">IX) [ Supprimer des Shards d'un Sharded Cluster Existant ]</p>

<p>Pour supprimer un shard, vous devez vous assurer que ses données sont migrées sur les shards restants du cluster. Cette procédure explique comment
migrer les données et supprimer un shard de façon sécurisée.
Ici, nous allons voir comment supprimer un simple shard. N'utilisez pas cette procédure pour migrer un cluster entier sur un autre système.
Pour migrer un cluster entier sur un autre système, migrez les shards individuellement comme si ils étaient des Replia Sets indépendants.

Pour supprimer un shard, commencez par vous connecter à l'une des instances mongos du cluster en utilisant un shell mongo, puis, utilisez les procédures suivantes
pour supprimer un shard du clsuter.</p>
<a name="assu"></a>

<div class="spacer"></div>

<p class="small-titre">a) S'assurer que le Processus Balanceur est Activé</p>

<p>Pour migrer des données d'un shard, le processus balanceur doit être impérativement activé. Vérifiez l'état du balanceur en utilisant la commande 
sh.getBalancerState() dans un shell mongo.</p>
<a name="deter"></a>

<div class="spacer"></div>

<p class="small-titre">b) Déterminer le Nom du Shard à Supprimer</p>

<p>Pour déterminer le nom d'un shard, connectez-vous à une instance mongos via un shell mongo :
- Utilisez la commande listShards comme suivant :</p>

<pre>db.adminCommand( { listShards: 1 } )</pre>

<p> - Exécutez soit la commande sh.status() ou alors la commande db.printShardingStatus().
Le champ shards._id indique le nom de chaque shard.</p>	
<a name="suppc"></a>

<div class="spacer"></div>

<p class="small-titre">c) Supprimer les Chunks d'un Shard</p>

<p>Exécutez la commande removeShard. Cela commence par "vider" les chunks du shard que vous voulez supprimer :</p>

<pre>db.runCommand( { removeShard: "mongodb0" } )</pre>

<p>Dans cet exemple, nous avons supprimé le shard nommé mongodb0. Cette opération retourne la réponse :</p>

<pre>{ msg : "draining started successfully" , state: "started" , shard :"mongodb0" , ok : 1 }</pre>

<p>En fonction de la capacité de votre réseau ainsi que la quantité de données, cette opération peut prendre quelques minutes commee plusieurs jours
avant de se terminer.</p>
<a name="veris"></a>

<div class="spacer"></div>

<p class="small-titre">d) Vérifier le Statut de la Migration</p>

<p>Pour vérifier le statut de la migration à n'importe quel moment, utilisez la commande removeShard.</p>

<pre>db.runCommand( { removeShard: "mongodb0" } )</pre>

<p>Ici, nous l'avons effectué sur le shard nommé mongodb0. Cette commande retourne alors :</p>

<pre>{ msg: "draining ongoing" , state: "ongoing" , remaining: { chunks: NumberLong(42), dbs : NumberLong(...........;</pre>

<p>Dans la sortie, le document "remaining" affiche le nombre de chunks restants que MongoDB doit migrer sur les autres shards ainsi que le nombre de
bases de données MongoDB qui ont le statut "primaire" sur ce shard.

Continuez de vérifier le statut de la commande removeShard jusqu'à ce que le nombre restant de chunks soit égal à 0. Passez ensuite à l'étape suivante.</p> 
<a name="depld"></a>

<div class="spacer"></div>

<p class="small-titre">e) Déplacer les Données Non Shardées</p>

<p>Si le shard est le shard primaire pour une ou plusieurs bases de données dans le cluster, alors le shard va avoir des données non shardées. Si le shard n'est
pas le shard primaire pour aucune base de données, sautez jusqu'à l'étape suivante, Finaliser la Migration.

Dans un cluster, une base de données avec des collections non shardées va stocker ces collections dans un simple shard. Ce shard devient le shard primaire pour la base de
données. (Des bases de données différentes dans un cluster peuvent avoir des shards primaires différents.)</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : N'effectuez pas cette procédure avant d'avoir "vidé" le shard.
</div>

<p>1) Pour déterminer si le shard que vous supprimez est le shard primaire pour l'une des bases de données du cluster, utilisez l'une des deux méthodes suivantes :
- sh.status()
- db.printShardingStatus()

Dans le document retourné, le champ databases est une liste de chaque base de données et son shard primaire. Par exemple, le champ databses suivant
indique que la base de données products utilise mongodb0 en tant que shard primaire :</p>

<pre>{ "_id" : "products", "partitioned" : true, "primary" : "mongodb0" }</pre>

<p>2) Pour déplacer une base de données vers un autre shard, utilisez la commande movePrimary. Par exemple, pour migrer toutes les données non shardées restantes
depuis mongodb0 vers mongodb1 :</p>

<pre>db.runCommand( { movePrimary: "products", to: "mongodb1" })</pre>

<p>Cette commande ne retourne rien jusqu'à ce que MongoDB termine le déplacement de toutes les données ce qui devrait prendre un certain temps. Voici un exemple
de réponse de cette commande :</p>

<pre>{ "primary" : "mongodb1", "ok" : 1 }</pre>
<a name="fina"></a>

<div class="spacer"></div>

<p class="small-titre">f) Finaliser la Migration</p>

<p>Pour nettoyer toutes les méta-informations et finaliser la suppression, exécutez la commande removeShard encore une fois. Par exemple, sur un shard
nommé mongodb0 :</p>

<pre>db.runCommand( { removeShard: "mongodb0" } )</pre>

<p>Un message de confirmation apparaît :</p>

<pre>{ msg: "remove shard completed successfully" , state: "completed", host: "mongodb0", ok : 1 }</pre>

<p>Une fois que la valeur du champ "state" est "completed", vous pouvez arrêter tous les processus en rapport avec le shard mongodb0.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur la <a href="tutoriaux_gestion.php">"Gestion des Données avec un Sharded Cluster" >></a>.</p>

<?php

	include("footer.php");

?>
