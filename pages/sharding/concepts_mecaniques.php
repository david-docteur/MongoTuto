<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Mécaniques de Cluster Patagé</li>
</ul>

<p class="titre">[ Mécaniques de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#bcs">I) Balancing de Collection Shardée</a></p>
	<p class="right"><a href="#bc">- a) Balanceur de Cluster</a></p>
	<p class="right"><a href="#sm">- b) Le Seuil de Migration</a></p>
	<p class="right"><a href="#ts">- c) Taille de Shard</a></p>
	<p class="elem"><a href="#chun">II) Migration de Chunks sur les Shards</a></p>
	<p class="right"><a href="#migr">- a) Migration de Chunk</a></p>
	<p class="elem"><a href="#sepa">III) Séparation de Chunk dans un Sharded Cluster</a></p>
	<p class="right"><a href="#tcc">- a) Taille de Chunk</a></p>
	<p class="right"><a href="#limi">- b) Limitations</a></p>
	<p class="elem"><a href="#inde">IV) Indexes de Clés de Shard</a></p>
	<p class="elem"><a href="#meta">V) Méta-Informations de Sharded Cluster</a></p>
</div>

<p>[ schéma mongodb ]</p>
<a name="bcs"></a>

<div class="spacer"></div>

<p class="titre">I) [ Balancing de Collection Shardée ]</p>

<p>On appelle Balancing le processus qui va distribuer les données de la collection shardée, de manière équilibrée, sur tous les shards du Cluster.
Quand un shard détient plus de chunks de la collection shardé que les autres, MongoDB balance les chuns automatiquement sur les différents shards. 
La procédure de Balancing pour les Clusters Sharded est entièrement transparente à l'utilisateur et à la couche applicative.</p>
<a name="bc"></a>

<div class="spacer"></div>

<p class="small-titre">a) Balanceur de Cluster</p>

<p>Le Balanceur est responsable de la redistribution des chunks d'une collection shardée de manière équitable sur les shards pour chaque collection shardée.
Par défaut, ce processus est toujours activé.
Toute instance mongo du Cluster peut démarrer un tour de balancement. Quand un processus de balancement est actif, le mongos responsable obtient un verrou (lock)
en modifiant un document dans la collection "lock" dans la base de données de configuration.</p>

<p>Afin de réguler une distribution inégale de chunk pour une collection shardée, le Balanceur migre les chunks des shards en contenant le plus vers les shards
qui en contiennent le moins. Le Balanceur migre les chunks, un par un, jusqu'à ce qu'il y ai une dispersion équilibrée de chunks pour la collection
à travers les shards.
La migration de chunks nécessite beaucoup de ressources et de bande passante, ce qui peut avoir un impacte majeur sur les performances de la base de données.
Le Balanceur tente de minimiser l'impacte en :
- Migrant un chunk à la fois
- Démarrer un tour de balancement seulement si la différence du nombre de chunks entre le shard en ayant le plus et le shard en ayant le moins atteint
le seuil de migration.

Vous aurez probablement à désactiver le Balanceur en cas de maintenance. </p>
<a name="sm"></a>

<div class="spacer"></div>

<p class="small-titre">b) Le Seuil de Migration</p>

<p>Pour minimiser l'impacte du Balancing sur le cluster, le Balanceur ne va pas commencer à balancer les chunks jusqu'à ce que la distribution des chunks
d'une sharded collection a atteint un certain seuil. Les seuils correspondent à la différence du nombre de chunks entre le shard de la collection qui en a le plus
et le shard qui en a le moins. Le Balanceur a les seuils suivants :</p>

<table>
	<tr>
		<th>Nombre de Chunks </th><th>Seuil de Migration</th>
	</tr>
	<tr>
		<td>Moins de 20</td><td>2</td>
	</tr>
	<tr>
		<td>Entre 21 et 80</td><td>4</td>
	</tr>
	<tr>
		<td>Plus de 80</td><td>8</td>
	</tr>
</table>

<div class="spacer"></div>

<p>Les seuils précédents apparaissent dans la version 2.2. Dans les versions précédentes, un tour de balancement démarrerait seulement si le shard ayant le plus
de chunks aurait plus de 8 chunks que celui ayant le moins de chunks.
Quand un tour de balancement débute, le balanceur ne s'arrêtera pas avant que la différence entre le nombre de chunks sur deux shards quelconques de cette
collection est inférieur à 2 ou alors si une migration de chunk échoue.</p>
<a name="ts"></a>

<div class="spacer"></div>

<p class="small-titre">c) Taille de Shard</p>

<p>Par défaut, MongoDB va tenter de remplir tout l'espace disque disponible avec les données sur tous les shards tant que l'ensemble de données grossit.
Par s'assurer que le cluster a la capacité de gérer l'augmentation des données, inspectez l'utilistion du disque et tout autres métriques.
Quand vous ajoute un shard, vous allez vouloir définir une taille maximale pour ce shard. Cela empêche le balanceur de migrer des chunks 
sur le shard quand la valeur de "mapped" excède la taille maximale. Utilisez le paramètre maxSize de la commade addShard() pour définir la taille maximale
du shard.</p>
<a name="chun"></a>

<div class="spacer"></div>

<p class="titre">II) [ Migration de Chunks sur les Shards ]</p>

<p>En tant que partie du processus de Balancing, nous allons voir comment MongoDB distribue les chunks sur les différents shards.</p>

<p>[ schéma mongodb ]</p>
<a name="migr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Migration de Chunk</p>

<p>MongoDB migre les chunks dans un sharded cluster pour distribuer les chunks d'une sharded collection de façon équitable sur les différents shards. La migration
peut être soit :

- Manuelle : Utilisez la migration manuellement dans certaines situations limitées, telles que la distribution pendant des insertions de masse. 
- Automatique : Le processus du Balanceur migre les chunsk automatiquement quand il y a une distribution de chunks non équilibrée de votre sharded collection
sur les shards. </p>

<p>Toutes les migrations de chunks utilisent les procédures suivantes :

1) Le Balanceur envoie la commande moveChunk au shard source.
2) La source commence à bouger les données avec une commande moveChunk interne. Pendant le processus de migration, les opérations sur le chunk sont routées
vers le shard source. Le shard source est responsable des opérations d'écritures arrivant pour ce chunk.
3) Le shard de destination commence à envoyer une requête pour les documents du chunk et commence à recevoir les copies des données.
4) Après avoir reçu le document final du chunk, le shard de destination commence un processus de synchronisation pour s'assurer qu'il a tous les changements
des documents migrés qui se sont déroulés pendant le processus de migration.
5) Quand tout est synchronisé, le shard de destination se connecte à la base de données de configuration et met à jour les méta-informations du cluster avec
la nouvelle location du chunk en question.
6) Une fois que le shard destination termine la mise à jour des méta-informations, et qu'il n'y a aucun curseurs d'ouverts sur le chunk, le shard source supprime
sa copie des documents.

Depuis la version 2.4, si le balanceur a besoin d'effectuer des migrations de chunk additionnelles depuis le shard source, le balanceur peut commencer
la migration de chunk suivante sans attendre que le processus de migration actuellement en cours d'exécution, termine son étape de suppression.

Le processus de migration s'assure de la consistence des données et maximalise la disponibilité des chunks pendant le balancement.</p>

<div class="spacer"></div>

<p>Queuing de Migration de Chunk : Depuis la version 2.4 également, afin de migrer plusieurs chunks depuis un shard source, le balanceur migre les chunks un par
un. En revanche, le balanceur n'attend pas que la phase actuellement de suppression se termine, avant de continuer la migration du chunk suivant.
Ce comportement de queuing permet aux shards de se décharger des chunks plus rapidement en cas de cluster lourdement déséquilibré, comme lorsque l'on exécute
un chargement initial des données sans pré-séparer les chunks et quand l'on ajoute un nouveau shard au cluster.

Ce comportement affecte aussi la commande moveChunk(), et les scripts qui utilisent la commande moveChunk devraient être beaucoup plus rapides.
Dans certains cas, la phase de suppression devrait persister plus longtemps. Si plusieurs phases de suppression sont queued mais pas encore complétée, un crash
du membre primaire du Replica Set peut empêcher de multiples données de migrer.</p>

<p>Write Concern de Migration de Chunk : Depuis la version 2.4, pendant la copie et le suppression de données durant des migrations, le balanceur attend
que les membres secondaires aient répliqué les données. Cela réduit la vitesse de migration de chunk mais améliore la fiabilité et s'assure que les migrations d'un
large nombre de chunks ne peuvent pas altérer la disponibilité d'un sharded cluster.</p>
<a name="sepa"></a>

<div class="spacer"></div>

<p class="titre">III) [ Séparation de Chunk dans un Sharded Cluster ]</p>

<p>Quand un chunk devient plus lourd que la taille autorisée, une instance mongos le sépare en deux. La séparation devrait résulter en une distribution des chunks
déséquilibrée à travers les différents shards du cluster. Dans ce genre de situation, les instances mongos vont initialiser un tour de migrations
pour redistribuer les données équitablement sur les différents shards.</p>

<p>[ schéma mongodb ]</p>
<a name="tcc"></a>

<div class="spacer"></div>

<p class="small-titre">a) Taille de Chunk</p>

<p>La tailel par défaut d'un chunk dans MongoDB est de 64mo. Vous pouvez bien sûr augmenter ou réduire cette taille en gardant à l'esprit des effets possibles
sur les performances de votre cluster.

1) Des chunks de petite taille conduisent à une distribution plus équitable sur les différents shards du cluster au frais de migrations plus fréquentes. 
Cela créer de la surcharge au niveau de la couche du routage de requêtes (mongos).
2) Des plus gros chunks résultent en des migrations plus moindres. Cela est plus efficace d'un point de vue réseau mais aussi au niveau de la couche de routage
de requêtes. Par contre, ces avantages mènent au fait que les données seront moins équitablement distribuées.</p>
<a name="limi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Limitations</p>

<p>Changer la taille de chunk a un impacte lors de la séparation mais il y a quelques limitations par rapport à ça.
- La séparation automatique se déclenche uniquement pendant un INSERT ou un UPDATE. Si vous réduisez la taille de chunk, cela devrait prendre du temps à tous les chunks
de se séparer selon la nouvelle taille.
- Il n'y a pas de retour possible après une séparation. Si vous augmentez la taille de chunk, les chunks existant devront grossir au travers des inserts et updates
jusqu'à ce qu'il atteigne la nouvelle taille.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Les limites de la taille d'un chunk sont inclusives pour la valeur la plus basse limite et exclusive pour la valeur la plus haute.
</div>
<a name="inde"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Indexes de Clés de Shard ]</p>

<p>Toutes les Collections shardées doivent obligatoirement avoir un indexe qui commence par la clé de shard. Si vous shardez une collection sans documents
et sans ce genre d'indexe, la commande shardCollection va créer l'indexe sur la clé de shard. Si la collection a déjà des documents, vous devez créer l'indexe
avant d'utiliser la commande shardCollection().
Depuis la version 2.2, l'indexe sur la clé de shard n'a plus besoin d'être uniquement sur la clé de shard. Cet indexe peut-être sur la clé de shard celle-même,
ou sur un indexe composé ou la clé de shard est un préfixe de l'indexe.</p>

<div class="alert alert-danger">
	<u>Attention</u> : L'indexe sur la clé de shard ne peut être un indexe multi-clés.
</div>

<div class="spacer"></div>

<p>Une collection shardée nommée people a pour clé de shard le champ zipcode. Ce champ a l'indexe { zipcode : 1 }. Vous pouvez remplacer cet indexe
avec un indexe composé tel que { zipcode : 1, username : 1 } :

1) Créer un indexe sur { zipcode : 1, username : 1 } :</p>

<pre>db.people.ensureIndex( { zipcode: 1, username: 1 } );</pre>

2) Quand MongoDB finit de créer l'indexe, vous pouvez facilement supprimer l'indexe existant sur { zipcode : 1 } comme ceci :</p>

<pre>db.people.dropIndex( { zipcode: 1 } );</pre>

<div class="spacer"></div>

<p>Depuis que l'indexe sur la clé de shard ne peut pas être un indexe multi-clés, l'indexe { zipcode: 1, username: 1 } peut uniquement remplacer l'indexe 
{ zipcode : 1 }, s'il n'y a pas de tableau de valeurs sur le champ username.
Si vous supprimez le dernier indexe valide pour la clé de shard, restaurez en re-créant un indexe sur la clé de shard uniquement.</p>
<a name="meta"></a>

<div class="spacer"></div>

<p class="titre">V) [ Méta-Informations de Sharded Cluster ]</p>

<p>Les seveurs de configuration stockent les méta-informations de votre sharded cluster. Celles-ci reflettent la location et l'état des ensembles de données
shardés et du système. Les méta-informations contiennent la liste des chunks sur chaque shard ainsi que les limites de chaque chunk. Les instances mongos
mettent en cache ces données et s'en servent afin de router les opérations de lectures et d'écitures aux shards spécifiques.
Les seveurs de configuration stockent les informations dans la base de données de configuration.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Faîtes toujours un sauvegarde de la base de données de configuration "config" avant d'effectuer quelconque maintenant
	sur un serveur de configuration.
</div>

<div class="spacer"></div>

<p>Pour accéder à la base de données config, effectuez la commande suivante dans un shell mongo : </p>

<pre>use config</pre>

<p>En général, vous ne devez pas éditer directement le contenu de la base de données "config". Celle-ci contient les collections suivantes :
changelog, chunks, collections, databases, lockpings, locks, mongos, settings, shards et version.</p>

<div class="spacer"></div>

<p>Voilà, c'est terminé pour la parite théorique sur le Sharding, maintenant vous êtes à passer la partie la plus intéressante, la pratique !
En espéran que ça vous a plus et que vous maîtrisez enfin les principes du Sharding avec MongoDB, passons à la suite : <a href="tutoriaux_deploiement.php">Déploiement de Sharded Cluster >></a> Bon courage !</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>