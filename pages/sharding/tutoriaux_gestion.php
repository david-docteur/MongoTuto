<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Gestion des Données avec un Cluster Partagé</li>
</ul>

<p class="titre">[ Gestion des Données avec un Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#cree">I) Créer des Chunks dans un Sharded Cluster</a></p>
	<p class="elem"><a href="#sepa">II) Séparer des Chunks dans un Sharded Cluster</a></p>
	<p class="elem"><a href="#migr">III) Migrer des Chunks dans un Sharded Cluster</a></p>
	<p class="elem"><a href="#modi">IV) Modifier la Taille de Chunk dans un Sharded Cluster</a></p>
	<p class="elem"><a href="#tag">V) Sharding par Tag</a></p>
	<p class="right"><a href="#comp">- a) Comportement et Opérations</a></p>
	<p class="right"><a href="#chun">- b) Les Chunks qui Enjambent plusieures Intervales de Tags</a></p>
	<p class="elem"><a href="#tags">VI) Gérer les Tags de Shard</a></p>
	<p class="right"><a href="#tsha">- a) Tagguer un Shard</a></p>
	<p class="right"><a href="#inter">- b) Tagguer une Intervalle de Clé de Shard</a></p>
	<p class="right"><a href="#supp">- c) Supprimer un Tag d'une Intervalle de Clé de Shard</a></p>
	<p class="right"><a href="#voir">- d) Voir les Shards Existants</a></p>
	<p class="elem"><a href="#forc">VII) Forcer les Clés Uniques pour les Sharded Collections</a></p>
	<p class="right"><a href="#ve">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#conts">- b) Contraintes d'Unicité pour la Clé de Shard</a></p>
	<p class="right"><a href="#conta">- c) Contraintes d'Unicité sur des Champs Arbitraires</a></p>
	<p class="elem"><a href="#grid">VIII) Sharder un Stockage de Données GridFS</a></p>
	<p class="right"><a href="#files">- a) La Collection files</a></p>
	<p class="right"><a href="#chunks">- b) La Collection chunks</a></p>
</div>

<p></p>
<a name="cree"></a>

<div class="spacer"></div>

<p class="titre">I) [ Créer des Chunks dans un Sharded Cluster ]</p>

<p>Créer des chunks, ou pré-séparer une collection vide pour garantir une distribution égale des chunks pendant l'ingestion des données.
Le but de pré-séparer les limites de chunks d'une sharded collection vide est de permettre aux clients d'insérer des données dans une collection
déjà partitionnée. Dans la plupart des cas, un sharded cluster va créer et distribuer les chunks automatiquement sans l'intervention de l'utilisateur.
En revanche, dans un nombre limité de cas, MongoDB ne peut pas créer assez de chunks ou distribuer les données assez rapidement afin de supporter
un certain débit. Par exemple :

- Si vous voulez partitionner une collection déjà existante qui est un seul shard.
- Si vous souhaitez insérer un large volume de données dans un cluster qui n'est pas balancé, ou alors une imbalance liée à l'insertion de ces données.
  Par exemple, les clés de shard s'incrémentant ou se décrémentant de façon monotone insèrent les données dans un seul chunk.</p>
  
<p>Ces opérations sont des ressources intensives pour plusieurs raisons :
  
- La migration de chunks a besoin de copier toutes les données d'un chunk depuis un shard vers un autre.
- MongoDB ne peut migrer qu'un seul chunk à la fois.
- MongoDB ne créer une séparation qu'après une opération d'insertion.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Ne pré-séparez qu'une collection vide. Si une collection a déjà des données, MongoDB sépare automatiquement les données
	de la collection lorsque vous activez le sharding pour la collection.
</div>

<div class="spacer"></div>

<p>Pour créer des chunks manuellement, utilisez la procédure suivante :

1) Séparez des chunks vides dans votre collection en effectuant manuellement la commande split sur les chunks.
Par exemple, pour créer des chunks pour les documents dans la collection myapp.users en utilisant le champ email comme clé de shard, utilisez l'opération suivante
dans un shell mongo ( en supposant que cette collection comporte 100 millions de documents) :</p>

<pre>
for ( var x=97; x<97+26; x++ ){
	for( var y=97; y<97+26; y+=6 ) {
		var prefix = String.fromCharCode(x) + String.fromCharCode(y);
		db.runCommand( { split : "myapp.users" , middle : { email : prefix } } );
	}
}
</pre>
<a name="sepa"></a>

<div class="spacer"></div>

<p class="titre">II) [ Séparer des Chunks dans un Sharded Cluster ]</p>

<p>Créer des chunks manuellement dans une sharded collection. Normalement, MongoDB sépare un chunk après une insertion si celui-ci excède la taille maximale
de chunk. Par contre, vous voudrez peut-être séparer un chunk manuellement si :

- vous avez énormement de données dans votre cluster et peu de chunks, ce qui est le cas après le déploiement d'un cluster utilisant des données existantes.
- vous allez probablement ajouter une grosse quantité de données qui devrait résider initialement dans un seul chunk ou shard. Par exemple, vous prévoyez
d'insérer une grosse quantité de données avec des valeurs de clé de shard entre 300 et 400, mais toutes les valeurs de votre clé de shard sont
entre 250 et 500 sur un seul chunk.</p>

<div class="alert alert-info">
	<u>Note</u> : Les chuns ne peuvent pas être assemblés ou combinés une fois qu'ils ont été séparés.
</div>

<p>Le balanceur va migrer les chunks séparés récement vers un nouveau shard immédiatement si l'instance mongos prévoit le fait que les futures insertions
pourraient bénéficier de ce déplacement. Le balanceur ne distingue pas les chunks séparés manuellement de ce qui ont été créés automatiquement par le système.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Soyez vigilants lorsque vous allez séparer des données dans une sharded collection pour créer de nouveaux chunks.
	Quand vous shardez une collection qui a déjà des données existantes, MongoDB crée des chunks automatiquement pour distribuer de manière égale la collection.
	Pour séparer des données de façon efficace dans un sharded cluster, vous devez considérer le nombre de documents dans un chunk ainsi que la taille moyenne d'un
	document afin de créer une taille de chunk uniforme. Quand les chunks ont des tailles irrégulières, les shardes vont avoir un nombre égal de chunks mais 
	vont contenir des tailles de données très différentes. Evitez de créer des séparations qui mèneront à une collection avec des chunks de tailles très différentes.
</div>

<div class="spacer"></div>

<p>Utilisez la commande sh.status() pour déterminer les limites du chunk actuel à travers le cluster.
Afin de séparer un chunk manuellement, utilisez la commande split avec soit les champs middle ou find. Le shell mongo fournit les méthodes sh.splitFind()
et sh.splitAt().

La méthode splitFind() sépare le chunk qui contient le premier document retourné qui correspond à cette requête en deux chunks de tailles égales.
Vous devez spécifier le namespace complet (par exemple database.collection) dans la collection shardée pour la méthode splitFind(). La requête dans 
splitFind() n'a pas besoin d'utiliser une clé de shard, même si il est toujours bon d'en utiliser une.

Par exemple, la commande suivante sépare le chunk qui contient la valeur 63109 pour le champ zipcode de la collection people dans la base de données records :</p>

<pre>sh.splitFind( "records.people", { "zipcode": 63109 } )</pre>

<p>Utilisez la méthode splitAt() afin de séparer un chunk en deux, en utilisant le document de la requête en tant que la plus basse limite dans le nouveau
shard. Par exemple, la commande suivante sépare le chunk qui contient la valeur 63109 pour le champ zipcode de la collection people dans la base de données records :</p>

<pre>sh.splitAt( "records.people", { "zipcode": 63109 } )</pre>

<div class="alert alert-info">
	<u>Note</u> : La méthode splitAt() ne sépare par forcément le chun en deux chunks à tailles égales. La séparation s'effectue
	à l'endroit du document qui correspond à la requête, sans regarder ou ce document se situe dans le chunk.
</div>

<a name="migr"></a>

<div class="spacer"></div>

<p class="titre">III) [ Migrer des Chunks dans un Sharded Cluster ]</p>

<p>Migrer les chunks manuellement sans utiliser le processus de balancement automatique. Dans la plupart des cas, il vous sera conseillé de laisser
le balanceur automatique migrer les chunks entre les shards. Mais, vous voudrez peut-être migrer les shards manuellement dans certains cas :

- Lorsque vous pré-séparez une collection vide, migrez les chunks manuellement afin de les distribuer à proportions égales sur les shards.
Utilisez la pré-séparation dans des situations limitées afin de supporter l'insertion de données de masses.
- Si le balanceur dans un cluster actif ne peut pas distribuer les chunks pendant la période de balancement, alors vous devrez migrer les chunks manuellement.

Pour migrer les chuns manuellement, utilisez la commande moveChunk. Par exemple, pour migrer un seul chunk, l'exemple suivant considère que le champ
username est la clé de shard pour la collection nommée users dans la base de données myapp, et que la valeur smith existe dans le chunk à migrer. Migrez
le chunk en utilisant la commande suivante dans un shell mongo :</p>

<pre>
db.adminCommand(
	{ 
		moveChunk : "myapp.users",
		find : {username : "smith"},
		to : "mongodb-shard3.example.net"
	}
)
</pre>

<div class="spacer"></div>

<p>Cette commande va déplacer le chunk qui inclut la valeur de clé de shard "smith" vers le shard nommé mongodb-shard3.example.net. Cette commande va bloquer
jusqu'à ce que la migration soit complètement terminée.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Pour retourner la liste des shards, utilisez la commande listShards.
</div>

<p>Un autre exemple, pour migrer les chunks de façon égale pour la collection myapp.users, mettez chaque préfixe de chunk sur le shard suivant depuis l'autre
et exécutez la commande suivante via un shell mongo :</p>

<pre>
var shServer = [ "sh0.example.net", "sh1.example.net", "sh2.example.net", "sh3.example.net", "sh4.example.for ( var x=97; x&inf;97+26; x++ ){
	for( var y=97; y&inf;97+26; y+=6 ) {
		var prefix = String.fromCharCode(x) + String.fromCharCode(y);
		db.adminCommand({moveChunk : "myapp.users", find : {email : prefix}, to : shServer[(y-97)/6]})
	}
}
................
</pre>

<div class="spacer"></div>

<p>Depuis la version 2.2, la commande moveChunk a le paramètre _secondaryThrottle. Lorsqu'il est définit à true, MongoDB va s'assurer que tous les changements
sur les shards, qui font partie de la migration de chunks, soient répliqués sur les secondaires tout au long de l'opération de migration.
Depuis la version 2.4, _secondaryThrottle est définit à true par défaut.</p>

<div class="alert alert-danger">
	<u>Attention</u> : La commande moveChunk pourrait produire le message d'erreur suivant : The collection's metadata lock is already taken.
	Cela signifie que les clients ont trop de curseurs ouverts qui tentent d'accéder au chunk en cours de migration. Vous voudrez alors soit
	attendre que les curseurs terminent leurs opérations ou fermer les curseurs manuellement.
</div>
<a name="modi"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Modifier la Taille de Chunk dans un Sharded Cluster ]</p>

<p>Modifier la taille de chunk par défaut dans une sharded collection. Quand le premier processus mongos se connecte à un ensemble de serveurs de configuration,
il initialise le shardec cluster avec une taille de chunk par défaut de 64mo. Cette taille de chunk par défaut fonctionne bien pour la plupart des déploiements.
Toutefois, si vous remarquer que les migrations automatiques ont plus d'I/O que ce que votre hardware peut supporter, vous voudrez sûrement réduire la taille
de chunk. Pour les séparations et migrations automatiques, une petite taille de chunk mène à des migrations plus rapides et plus fréquentes.

Pour modifier la taille de chunk, utilisez la procédure suivante :

1) Connectez-vous à l'un des mongos du cluster via un shell mongo.
2) Exécutez la commande suivante pour basculer sur la base de données de configuration :</p>

<pre>use config</pre>

<p>Effectuez l'opération save() suivante afin de stocker la valeur de la taille de chunk :</p>

<pre>db.settings.save( { _id:"chunksize", value: <size> } )</pre>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Les options chunkSize et --chunkSize, passées en paramètre lors de l'exécution des mongos, n'affectent pas la taille de chunk
	après que vous ayez initialisé le cluster.
	Pour éviter toute confusion, définissez toujours la taille de chunk en utilisant toujours la procédure ci-dessus plutôt que les options en paramètres.
</div>

<p>Modifiez la taille de chunk à quelques limitations :

- La séparation automatique s'effectue uniquement durant un insert ou une update
- Si vous réduisez la taille de chunk, cela devrait prendre du temps à tous les chunks pour se séparer à la nouvelle taille
- Les séparations ne peuvent pas être annulées
- Si vous augmentez la taille de chunk, les chunks existants grossissent uniquement lors d'un insert ou d'une update jusqu'à ce qu'ils atteignent la nouvelle taille.</p>
<a name="tag"></a>

<div class="spacer"></div>

<p class="titre">V) [ Sharding par Tag ]</p>

<p>Les tags associent des limites de valeurs spécifiques de clé de shard avec des shards spécifiques pour la gestion de patrons de déploiements.
MongoDB supporte le tagging de limites de valeurs de clé de shard pour associer ces limites à un shard ou groupe de shard. Ces shards reçoivent tous les inserts
sous les limites tagguées.

Le balanceur obéït aux associations des limites tagguées, qui activent les patrons de déploiement suivants :

- isole un sous-ensemble de données spécifique sur chaque ensemble de shards spécifique
- s'assure que les données les plus pertinentes résident sur les shards qui sont le plus proche géographiquement des serveurs d'application

Ce document décrit le comportement, les opérations et l'utilisation des tags avec le sharding dans les déploiements MongoDB.</p>

<div class="alert alert-info">
	<u>Note</u> : Les tags de limites de clé de shard sont différents des Tags de membres de Replica Sets.
</div>

<div class="alert alert-danger">
	<u>Attention</u> : Le sharding basé hash ne supporte pas les tags.
</div>
<a name="comp"></a>

<div class="spacer"></div>

<p class="small-titre">a) Comportement et Opérations</p>

<p>Le balanceur migre les chunks de documents dans des collections shardées aux shards associés à un tag qui a une intervale de clé de shard avec une limite supérieure
plus haute que la limite inférieure du chunk.</p>

<div class="alert alert-info">
	<u>Note</u> : Si les chunks d'une collection shardée sont déjà balancés correctement, alors le balanceur ne va migrer aucun chunk.
	Par contre, ci ceux-ci ne sont pas balancés, le balanceur migre les chunks dans les intervales tagguées des shards associés avec ces tags.
</div>

<div class="spacer"></div>

<p>Après avoir configuré les tags avec une intervale de clé de shard, et l'avoir associée avec un ou plusieurs shards, le cluster devrait prendre un peu de temps
pour balancer les données sur les différents shards. Cela dépend de la division des chunks et sur la distribution des données actuelle dans le cluster.

Une fois configuré, le balanceur respecte les intervalles de tags pendant les prochains tours de balancement.</p>
<a name="chun"></a>

<div class="spacer"></div>

<p class="small-titre">b) Les Chunks qui Enjambent plusieures Intervales de Tags</p>

<p>Un simple chunk peut contenir des données avec des valeurs de clé de shard qui ont des intervalles associées avec plus qu'un seul tag. Pour ce genre de situations,
le balanceur va migrer les chunks sur les shards qui contiennent les valeurs de clé de shard qui excèdent la limite supérieure de l'intervale de tag choisie.

Par exemple, en prenant une sharded collection avec deux intervalles de tags configurées :
- valeurs de clé de shard entre 100 et 200 ont des tags pour les chunks correspondants aux shards taggués NYC.
- valeurs de clé de shard entre 200 et 300 ont des tags pour les chunks correspondants aux shards taggués SFO.

Pour cette collection, le balanceur va migrer les chunks ayant des valeurs de clé de shard entre 150 et 220 vers un shard taggué NYC, vu que 150 est plus proche
de 200 que de 300.

Pour vérifier que votre collection n'a aucun chunk taggué avec ambiguité, créez des séparations sur les limites de votre tag. Vous pouvez ensuite migrer les chunks
manuellement aux shards appropriés, ou alors attendre que le balanceur migre ces chunks automatiquement.</p>
<a name="tags"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Gérer les Tags de Shard ]</p>

<p>Les tags associent des intervalles de valeurs spécifiques de clé de shard avec des shards spécifiques.</p>
<a name="tsha"></a>

<div class="spacer"></div>

<p class="small-titre">a) Tagguer un Shard</p>

<p>Vous pouvez associer des tags à un shard particulier en utilisant la méthode sh.addShardTag() lorsque vous êtes connecté à une instance mongos.
Un simple shard peut contenir plusieurs tags, tout comme plusieurs shards peuvent contenir le même tag.

Par exemple, nous allons ajouter le tag NYC à deux shards, et les tags SFO et NRT à un troisième shard :</p>

<pre>
sh.addShardTag("shard0000", "NYC")
sh.addShardTag("shard0001", "NYC")
sh.addShardTag("shard0002", "SFO")
sh.addShardTag("shard0002", "NRT")
</pre>

<p>Vous voudrez probablement supprimer des tags d'un shard particulier en utilisant la méthode sh.removeShardTag() lorsque vous êtes connecté à une instance mongos, comme
l'exemple suivant qui supprime le tag NRT d'un shard :</p>

<pre>sh.removeShardTag("shard0002", "NRT")</pre>
<a name="inter"></a>

<div class="spacer"></div>

<p class="small-titre">b) Tagguer une Intervalle de Clé de Shard</p>

<p>Pour assigner un tag à une intervalle de valeurs de clés de shard, utilisez la méthode sh.addTagRange() une fois connecté à une instance mongos.
Toute intervalle de clé de shard doit avoir un seul tag associé uniquement. Vous ne pouvez pas chevaucher des intervalles définient ou alors tagguer la même
intervalle plus d'une fois.

Par exemple, prenons la collection users de la base de données records, shardée par le champ zipcode. Les opérations suivantes vont assigner :

- deux intervalles de zip codes dans Manhattan et Brooklyn avec le tag NYC
- une intervalle de zip codes dans San-Francisco avec le tag SFO</p>

<pre>
sh.addTagRange("records.users", { zipcode: "10001" }, { zipcode: "10281" }, "NYC")
sh.addTagRange("records.users", { zipcode: "11201" }, { zipcode: "11240" }, "NYC")
sh.addTagRange("records.users", { zipcode: "94102" }, { zipcode: "94135" }, "SFO")
</pre>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Les intevalles de shard sont toujours inclusives au niveau de la limite inférieure et exclusives pour la limite supérieure.
</div>
<a name="supp"></a>

<div class="spacer"></div>

<p class="small-titre">c) Supprimer un Tag d'une Intervalle de Clé de Shard</p>

<p>Le processus mongod ne fournit pas de méthode pour supprimer une intervalle de tag. Vous  devrez alors supprimer un tag d'une intervalle de clé de shard
en supprimant le document relatif dans la collection tags dans la base de données de configuration. Chaque document dans la base de données Tags détient
l'espace de nom (namespace) de la sharded collection et une valeur minimum de clé de shard.</p>

Par exemple, nous allons maintenant supprimer le tag NYC pour l'intervalle de zip codes pour Mahattan :</p>

<pre>
use config
db.tags.remove({ _id: { ns: "records.users", min: { zipcode: "10001" }}, tag: "NYC" })
</pre>
<a name="voir"></a>

<div class="spacer"></div>

<p class="small-titre">d) Voir les Shards Existants</p>

<p>La sortie de la commande sh.status() liste les tags associés à un shard, si plusieurs, pour chaque shard. Les tags d'un shard existent dans le document
du shard dans la collection shards de la base de données de configuration. Pour retourner tous les shards ayant un tag spécifique, utilisz la séquence d'opérations
qui va suivre, qui va retourner uniquement les shards taggués avec NYC :</p>

<pre>
use config
db.shards.find({ tags: "NYC" })
</pre>

<p>Vous pouvez trouver des intervalles de shards pour tous les espaces de noms dans la collection tags de la base de données de configuration. La sortie
de sh.status() affiche toutes les intervalles de tags. Pour retourner toutes les intervalles de clés de shard tagguées avec NYC, utilisez la séquence suivante :</p>

<pre>
use config
db.tags.find({ tags: "NYC" })
</pre>
<a name="forc"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Forcer les Clés Uniques pour les Sharded Collections ]</p>

<p>S'assurez qu'un champ est toujours unique dans toutes les collections d'un cluster.</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>La contrainte "unique" sur les indexes s'assure que seulement un seul document peut avoir une valeur pour un champ donné dans une collection. Pour les collections
shardées, ces indexes uniques ne peuvent pas forcer l'unicité car les opérations d'insertion et d'indexage sont locales pour chaque shard. En bref, si vous spécifiez
un indexe unique sur un shard, celui-ci sera unique seulement sur ce shard lui-même, et non à travers tout le cluster.

Si vous avez besoin d'être sûr q'une champ soit unique sur touts les collections à travers un environnement shardé, il y a pour cela deux options :

1) Forcer l'unicité sur la clé de shard. MongoDB peut forcer l'unicité sur une clé de shard. Pour les clés de shard composées, MongoDB va forcer l'unicité
sur l'entièrement combinaison de la clé, et pas seulement pour un composant spécifique de la clé de shard.
De plus, vous ne pouvez pas spécifier de contrainte d'unicité pour un indexe hashé.

2) Utiliser une collection secondaire pour forcer l'unicité. Vous pouvez créer une collection minimale qui contient uniquement le champ unique et une référence
à un document dans la collection principale. Si vous insérez toujours dans une collectio secondaire avant d'insérer dans la collection principale, MongoDB
va produire une erreur si vous tentez d'utiliser une clé dupliquée.</p>

<div class="alert alert-info">
	<u>Note</u> : Si vous avez un petit ensemble de données, vous n'aurez probablement pas à sharder cette collection et vous pourrez créer de multiples
	indexes uniques. Sinon, vous pouvez toujours sharder sur une seul clé unique.
</div>

<p>Utilisez toujours le Write Concern aknoweledge en conjonction avec un driver MongoDB récent.</p>
<a name="conts"></a>

<div class="spacer"></div>

<p class="small-titre">b) Contraintes d'Unicité pour la Clé de Shard</p>

<p>Pour sharder une collection en utilisant la contrainte "unique", spécifiez la commande shardCollection de cette façon :</p>

<pre>db.runCommand( { shardCollection : "test.users" , key : { email : 1 } , unique : true } );</pre>

<p>Rapellez-vous que le champ _id est toujours unique. Par défaut, MongoDB insère un ObjectID dans le champ _id. En revanche, vous pouvez insérer manuellement
votre propre valeur dans le champ _id et l'utiliser en tant que clé de shard. Pour utiliser le champ _id en tant que clé de shard :</p>

<pre>db.runCommand( { shardCollection : "test.users" } )</pre>

<div class="alert alert-danger">
	<u>Attention</u> : Dans toute collection shardée ou vous ne shardez pas avec le champ _id, vous devez vous assurer de l'unicité du champ _id.
	La meilleure façon de vous assurer que ce champ est toujours unique est d'utiliser un ObjectId, ou tout autre identifiant universel et unique ( Universally unique identifier - UUID).
</div>

<div class="spacer"></div>

<p>Limitations :

- vous pouvez forcer l'unicité uniquement sur un seul champ d'une collection en utilisant cette méthode.
- si vous utilisez une clé de shard composée, vous pouvez uniquement forcer l'unicité sur la combinaison des composants de la clé dans la clé de shard.

Dans la plupart des cas, les meilleures clés de shard sont des clés de shards combinées qui incluent des éléments qui permettent la scalabilité et l'isolation de
requête, aussi bien que la haute cardinalité. Ces clés de shard idéales ne sont pas toujours les mêmes clés qui requièrent l'unicité et une approche différente.</p>
<a name="conta"></a>

<div class="spacer"></div>

<p class="small-titre">c) Contraintes d'Unicité sur des Champs Arbitraires</p>

<p>Si vous ne pouvez pas utiliser un champ unique en tant que clé de shard ou alors si vous avez besoin de forcer l'unicité sur plusieurs champs, vous devez
alors créer une autre collection qui se comporte comme un collection "proxy". Cette collection doit contenir à la fois une référence vers le document original
(par exemple son ObjectId) et la clé unqiue.

Si vous devez sharder cette collection "proxy", alors shardez sur la clé unique en utilisant la procédure vue ci-dessus, sinon, vous pouvez simplement créer
plusieurs indexes uniques sur la collection.</p>

<p>Considérons l'exemple suivant pour la collection "proxy" :</p>

<pre>
{
	"_id" : ObjectId("...")
	"email" ": "..."
}
</pre>

<div class="spacer"></div>

<p>Le champ _id détient l'ObjectId du document auquel il fait référence, et le champ email est le champ sur lequel vous voulez assurer l'unicité.
Pour sharder cette collection, utilisez l'opération suivante en utilisant le champ email comme clé de shard :</p>

<pre>db.runCommand( { shardCollection : "records.proxy" , key : { email : 1 } , unique : true } );</pre>

<p>Si vous n'avez pas besoin de sharder la collection "proxy", utilisez la commande suivante pour créer un indexe unique sur le champ email :</p>

<pre>db.proxy.ensureIndex( { "email" : 1 }, {unique : true} )</pre>

<p>Vous devrez donc créer plusieurs indexes uniques sur cette collection si vous n'envisagez pas de sharder cette collection "proxy".
Pour insérer des documents, utilisez la procédure suivante dans le shell Javascript mongo :</p>

<pre>
use records;	
	
var primary_id = ObjectId();
	
db.proxy.insert(
	{
		"_id" : primary_id
		"email" : "example@example.net"
	}
)

// if: the above operation returns successfully,
// then continue:
db.information.insert(
	{
		"_id" : primary_id
		"email": "example@example.net"
		// additional information...
	}
)
</pre>

<div class="spacer"></div>

<p>Vous devez insérer un document dans la collection proxy en premier lieu. Si l'opération réussit, le champ email est unique, et vous pouvez continuer à insérer
le document actuel dans la collection "information".

Considérations :

- Votre application doit récupérer les erreurs lorsque vous insérez les documents dans la collection "proxy" et forcer la consistance entre les deux collections.
- Si la collection proxy nécessite d'être shardée, vous devez sharder sur le seul champ sur lequel vous souhaitez forcer l'unicité.
- Pour forcer l'unicité sur plus d'un champ en utilisant des collections proxy shardées, vous devez impérativement avoir une collection proxy pour chaque
champ sur lequel on force l'unicité. Si vous créez plusieurs indexes uniques, sur une seule collection proxy, vous ne pourre pas sharder de collections proxy.</p> 
<a name="grid"></a>

<div class="spacer"></div>

<p class="titre">VIII) [ Sharder un Stockage de Données GridFS ]</p>

<p>Choisir si l'on doit sharder des données GridFS dans une collection shardée.</p>
<a name="files"></a>

<div class="spacer"></div>

<p class="small-titre">a) La Collection files</p>

<p>La plupart des déploiements n'auront pas besoin de sharder la collection "files". La collection "files" est générallement petite et ne contient uniquement
des méta-informations. Si vous devez sharder la collection "files", utilisez le champ _id si possible en combinaison avec un champ d'application.

Laisser la collection "files" non shardée signifie que tous les documents de méta-informations se situent sur un seul shard. Pour un stockage GridFS de production,
vous devez obligatoirement stocker la collection "files" sur un Replica Set.</p>
<a name="chunks"></a>

<div class="spacer"></div>

<p class="small-titre">b) La Collection chunks</p>

<p>Pour sharder la collection "chunks" par { files_id : 1 , n : 1 }, utilisez les commandes suivantes :</p>

<pre>
db.fs.chunks.ensureIndex( { files_id : 1 , n : 1 } )
db.runCommand( { shardCollection : "test.fs.chunks" , key : { files_id : 1 , n : 1 } } )
</pre>

<p>Vous souhaiterez peut-être aussi sharder juste en utilisant le champ file_id :</p>

<pre>db.runCommand( { shardCollection : "test.fs.chunks" , key : { files_id : 1 } } )</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : { files_id : 1 , n : 1 } et { files_id : 1 } sont les seules et uniques clés de shard supportées pour la collection "chunks"
	d'un stockage GridFS.
</div>

<p>La valeur files_id par défaut est un ObjectId, donc les valeurs de files_id sont toujours croissantes et les applications vont insérer toutes les nouvelles
données GridFS dans un simple chunk et shard. Si votre charge d'écriture est trop importante à contrôler pour un simple serveur, considérez une clé de shard
différente ou alors utilisez une valeur différente pour le champ _id de la collection "files".</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur le <a href="tutoriaux_diagnostique.php">Diagnostique de Sharded Cluster >></a>.</p>


<?php

	include("footer.php");

?>
