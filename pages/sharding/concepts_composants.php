<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Composants d'un Cluster Partagé</li>
</ul>

<p class="titre">[ Composants d'un Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#shar">I) Shards</a></p>
	<p class="right"><a href="#shap">- a) Shard Primaire</a></p>
	<p class="right"><a href="#stat">- b) Statut de Shard</a></p>
	<p class="elem"><a href="#srv">II) Serveurs de Configuration</a></p>
	<p class="right"><a href="#bdd">- a) Base de Donnnées de Configuration</a></p>
	<p class="right"><a href="#ope">- b) Opérations de Lecture et d'Ecriture sur les Serveurs de Configuration</a></p>
	<p class="right"><a href="#disp">- c) Disponibilité de Serveur de Configuration</a></p>
</div>

<p>Les Sharded Clusters implémentent le Sharding et consiste en les composants suivants :

_ Shards : Un Shard est une instance mongod qui détient un sous-ensemble des données de votre collection. Chaque shard est soit une instance mongod ou alors,
dans la plupart des cas, un Replica Set. En production, tous les shards sont des Replica Sets. 
_ Serveurs de Configuration : Chaque serveur de configuration est une instance mongod qui détient les méta-informations du Cluster. Les méta-informations
vont associer les chunks aux shards spécifiques.
_ Instances de Routage : Chaque routeur est une instance mongos qui route/dirige toutes les opérations de lectures et d'écritures de vos applications
jusqu'aux différents shards. Les applications n'accèdent pas aux Shards directement.

Il faut activer le Sharding dans MongoDB par Collection. Pour chaque collection que vous shardez, vous allez devoir spécifier une clé de shard pour cette collection.</p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="titre">I) [ Shards ]</p>

<p>Un shard est un Replica Set ou une simple instance mongod qui contient un sous-ensemble des données sur le Sharded Cluster. Ensemble, les shards que contient
le cluster contiennent l'ensemble complet des données. Typiquement, chaque chaque shard est un Replica Set. Le Replica Set fournit la redondance des informations
et la haute-disponibilité des données pour chaque shard.</p>

<div class="alert alert-danger">
	<u>Attention</u> : MongoDB sharde les données par Collection. Vous allez obligatoirement devoir accéder aux données du Cluster via les instances
	mongos. Si vous vous connectez directement à un shard, vous allez voir seulement sa fraction des informations. Il n'y a pas d'ordre particulier
	sur l'ensemble des données sur un shard spécifique. MongoDB ne garantit pas le fait que deux chunks consécutifs vont être stockés sur le même shard.
</div>

<div class="spacer"></div>

[ schéma mongodb ]
<a name="shap"></a>

<div class="spacer"></div>

<p class="small-titre">a) Shard Primaire</p> 

<p>Chaque base de données à un shard primaire qui contient toutes les collections non-shardées dans cette même base de données. Le terme
primaire n'a absolument rien à voir avec le terme de membre primaire des Replica Sets.</p>

[ schéma mongodb ]

<div class="spacer"></div>

<p>Afin de changer le shard primaire d'une base de données, utilisez la commande movePrimary().</p>

<div class="alert alert-danger">
	<u>Attention</u> : La commande movePrimary() peut être gourmande en ressources car elle copie toutes les collections non shardées 
	sur le nouveau shard. Pendant ce temps, ces donnés ne seront pas disponibles pour d'autres opérations.
</div>

<div class="spacer"></div>

<p>Quand vous déployez un nouveau sharded cluster, le premier shard devient le shard primaire pour toutes les bases de données existantes avant d'activer le sharding.</p>
<a name="stat"></a>

<div class="spacer"></div>

<p class="small-titre">b) Statut de Shard</p>

<p>Utilisez la commande sh.statut() dans le shell mongo afin d'avoir une vue d'ensemble du Cluster. Ce rapport inclut quel shard est le primaire de la base de
données ainsi que la distribution des chunks à travers les shards.</p>
<a name="srv"></a>

<div class="spacer"></div>

<p class="titre">II) [ Serveurs de Configuration ]</p>

<p>Les serveurs de configurations sont des intances mongod spéciales qui stockent les méta-informations d'un sharded Cluster. Les serveurs de configurations
utilisent un commit à deux phases afin d'assurer la fiabilité et l'efficacité des opérations. Les serveurs de configuration ne s'exécutent pas comme des
Replica Sets et doivent être disponibles afin de déployer un sharded cluster ou alors afin d'effectuer le moindre changement aux méta-informations
du Cluster.
Un sharded cluster de production a exactement trois serveurs de configuration. Pour un environnement de test, vous pouvez en avoir un seul uniquement.
Mais afin d'assurer la redondance des données de manière efficace, vous devriez toujours en utiliser trois.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Si votre cluster a un seul serveur de configuration, alors le serveur de configuration est le seul point d'échec.
	Si celui-ci est inaccessible, le cluster ne l'est plus également. Si vous ne pouvez pas restaurer les données sur un serveur de configuration, le cluster
	sera inutile. Utilisez systématiquement trois serveurs de configuration pour vos déploiments de production.
</div>

<p>Les serveurs de configuration stockent les méta-informations pour un seul cluster. Chaque cluster doit impérativement avoir ses propres serveurs de configuration.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Utilisez CNAMEs pour identifier vos serveurs de configuration au sein du cluster. De cett façon, vous allez pouvoir renommer
	vos serveurs de configuration sans devoir les arrêter.
</div>
<a name="bdd"></a>

<div class="spacer"></div>

<p class="small-titre">a) Base de Donnnées de Configuration</p>

<p>Les serveurs de configuration stockent les méta-données dans la base de données de configuration. Les instances mongos mettent en cache ces informations
et les utilisent afin de router/rediriger les opérations de lectures et écritures aux shards.</p>
<a name="ope"></a>

<div class="spacer"></div>

<p class="small-titre">b) Opérations de Lecture et d'Ecriture sur les Serveurs de Configuration</p>

<p>MongoDB écrit sur les serveurs de configuration dans les cas suivants :

- Pour créer des séparations dans les chunks existants.
- Pour migrer un chunk entre les shards.

MongoDB lit depuis les serveurs de configuration dans les cas suivants :

- Une nouvelle instance mongos démarre pour la première fois, ou un mongos existant redémarre.
- Après une migration de chunk, les instances mongos se mettent à jour avec les méta-informations du nouveau Cluster.

MongodDB utilise aussi les serveurs de configuration afin de gérer les verrous.</p>
<a name="disp"></a>

<div class="spacer"></div>

<p class="small-titre">c) Disponibilité de Serveur de Configuration</p>

<p>Si un ou deux serveurs de configuration deviennent indisponibles, les méta-informations du cluster deviennent read-only, vous ne pourrez pas les modifier.
Vous pourrez toujours lire et écrire des données depuis les shards, mais pas de migration ou de séparation de chunks jusqu'à ce que ces trois serveurs
deviennent disponibles à nouveau.
Si les trois serveurs de configuration sont indisponibles, vous pouvez toujours utiliser le cluster si vous ne redémarrez pas les instances mongos jusqu'à ce que
les serveurs deviennent disponibles. Sinon, si vous redémarrez quand ceux-ci sont hors-service, les clusters ne pourront pas router les lectures et écritures.</p>

<p>Les clusters deviennent inutilisable sans leur méta-informations respectives, c'est pour cela qu'il vous faudra toujours vérifier que les serveurs de
configuration soient toujours disponibl et en bon état de fonctionnement. On en déduit donc que les backups de serveurs de configuration sont indispensables.
Les données sur les serveurs de configuration sont très petites contrairement aux données stockées sur les clusters, ce qui signifique qu'un serveur de configuration
a relativement toujours une activité minimale et que le serveur de configuration n'a pas besoin d'être toujours disponible afin de supporter un sharded cluster.
En conséquences, il est facile de faire une sauvegarde des serveurs de configuration.
Si le nom ou l'adresse qu'un sharded Cluster utilise pour se connecter à un serveur de configuration change, vous devrez alors redémarrer toutes les instances
mongod et mongos du cluster. Evitez les temps d'arrêt en utilisant les CNAMEs afin d'identifier les serveurs de configuration à l'intérieur de votre déploiment
MongoDB.</p>
 
<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur les <a href="concepts_architectures.php">Architectures de Sharded Cluster >></a>.</p>

<?php

	include("footer.php");

?>