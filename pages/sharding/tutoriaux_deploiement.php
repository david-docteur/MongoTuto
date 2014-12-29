<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Déploiement de Cluster Partagé</li>
</ul>

<p class="titre">[ Déploiement de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#depl">I) Déployer un Sharded Cluster</a></p>
	<p class="right"><a href="#dema">- a) Démarrer les Instances des Serveurs de Configuation</a></p>
	<p class="right"><a href="#mong">- b) Démarrer les Instances mongos</a></p>
	<p class="right"><a href="#ajou">- c) Ajouter des Shards au Cluster</a></p>
	<p class="right"><a href="#abdd">- d) Activer le Sharding pour une Base de Données</a></p>
	<p class="right"><a href="#acol">- e) Activer le Sharding Pour une Collection</a></p>
	<p class="elem"><a href="#consi">II) Considérations de Sélection de Clé de Shard</a></p>
	<p class="right"><a href="#choi">- a) Choisir une Clé de Shard</a></p>
	<p class="right"><a href="#divi">- b) Créer une Clé de Shard Facilement Divisible</a></p>
	<p class="right"><a href="#alea">- c) Créer une Clé de Shard Fortement Aléatoire</a></p>
	<p class="right"><a href="#cibl">- d) Créer une Clé de Shard qui ne Cible qu'un seul Shard</a></p>
	<p class="right"><a href="#comp">- e) Utiliser une Clé de Shard Composée</a></p>
	<p class="right"><a href="#card">- f) Cardinalité</a></p>
	<p class="elem"><a href="#hash">III) Sharder une Collection avec une Clé de Shard Hashée</a></p>
	<p class="right"><a href="#coll">- a) Sharder la Collection</a></p>
	<p class="right"><a href="#init">- b) Spécifier le Nombre Initial de Chunks</a></p>
	<p class="elem"><a href="#auth">IV) Activer l'Authentification dans un Sharded Cluster</a></p>
	<p class="elem"><a href="#ajous">V) Ajouter des Shards à un Cluster</a></p>
	<p class="right"><a href="#ajun">- a) Ajouter un Shard au Cluster</a></p>
	<p class="elem"><a href="#srvc">VI) Déployer 3 Serveurs de Configuration pour la Production</a></p>
	<p class="elem"><a href="#conv">VII) Convertir un Replica Set en un Sharded Cluster Répliqué</a></p>
	<p class="right"><a href="#ve">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#proc">- b) Processus</a></p>
	<p class="elem"><a href="#convr">VIII) Convertir un Sharded Cluster en Replica Set</a></p>
	<p class="right"><a href="#simp">- a) Convertir un Cluster ayant un Simple Shard en un Replica Set</a></p>
	<p class="right"><a href="#rs">- b) Convertir un Sharded Cluster en un Replica Set</a></p>
</div>

<p>Bien, mettons en pratique tout ce que vous avez appris dans la partie théorique. Dans cette partie du tutoriel, vous allez apprendre à gérer et 
administrer un Sharded Cluster.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Concernant le Sharding et les adresses "localhost". Si vous utilisez les valeurs "localhost" ou "127.0.0.1" en tant que
	nom d'hôte pour identifier un hôte, par exemple l'argument "host" pour la fonction addShard() ou alors la valeur du paramètre --configfb, alors vous devrez
	impérativement utiliser les valeurs "localhost" ou "127.0.0.1" pour tous les paramètres d'hôtes de toutes les instances mongodb dans votre cluster. Si vous mixer
	des adresses locales et des adresses à distance, MongoDB va retourner un erreur.
</div>
<a name="depl"></a>

<div class="spacer"></div>

<p class="titre">I) [ Déployer un Sharded Cluster ]</p>

<p>En déployant un sharded cluster, vous allez devoir créer les dossiers de données nécessaires, démarrer les bonnes instances MongoDB et configurer
les paramètres de ce cluster en question.</p>
<a name="dema"></a>

<div class="spacer"></div>

<p class="small-titre">a) Démarrer les Instances des Serveurs de Configuation</p>

<p>Les processus des serveurs de configuration sont des instances mongod qui stockent les méta-informations du cluster. Vous désigneez un mongod en tant que serveur de
configuration en utilisant l'option --configsrv. Chaque serveur stocke une copie complète des méta-informations du cluster.
Lors des déploiements de production, vous devez déployer exactemen trois instances de serveur de configuration, chacune étant exéctuée sur un serveur différent
afin d'assurer la disponibilité des méta-données et garantir une bonne intégrité des données. Dans les environnements de test, vous pouvez exécuter ces trois instances
sur une seule machine.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Tous les membres d'un sharded cluster doivent être capables de se connecter à tous les autres membres du cluster, en incluant
	tous les shards et tous les serveurs de configuration. Assurez-vous que le réseau et vos paramètres de sécurité système, en incluant les firewalls ou autres, 
	autorisent ces connexions.
</div>

<div class="spacer"></div>

<p>1) Créer les répertoires de données pour chacunes des trois instances de serveurs de configuration. Par défaut, un serveur stocke ses données
dans le répertoire "/data/configdb". Vous pouvez bien sûr choisir un dossier différent avec la commande suivante :</p>

<pre>mkdir /data/configdb</pre>

<p>Démarrez les trois instances avec la commande suivante :</p>

<pre>mongod --configsvr --dbpath path --port port</pre>

<p>Le port par défaut des serveurs de configuration est 27019 mais vous pouvez en spécifier un différent :</p>

<pre>mongod --configsvr --dbpath /data/configdb --port 27019</pre>

<div class="alert alert-danger">
	<u>Attention</u> : Tous les serveurs de configuration doivent être exécutés et accessibles avant d'initier votre sharded cluster.
</div>
<a name="mong"></a>

<div class="spacer"></div>

<p class="small-titre">b) Démarrer les Instances mongos</p>

<p>Les instances mongos sont peu gourmandes en ressources et ne nécessitent pas de dossier de données. Vous pouvez exécuter une instance mongos en parallèle à
d'autres composants du cluster sur un même système, comme sur un serveur d'application ou un serveur exécutant déjà un processus mongod. Par défaut, une instance
mongod s'exécute sur le port 27017.
Quand vous démarrez l'instance mongos, spécifiez les noms d'hôte des trois serveurs de configuration, soit dans le fichier de configuration, soit en tant que
paramètre de ligne de commande.</p>

<div class="alert alert-success">
	<u>Astuce</u>  : Vous pouvez attribuer un DNS logique à chaque serveur de configuration (non relaté au nom d'hôte physique ou virtuel du serveur).
	Sans DNS logiques, bouger ou renommer un serveur de configuration nécessite l'arrêt de chaque instance mongod et mongos dans votre sharded cluster.
</div>

<div class="spacer"></div>

<p>Pour démarrer une instance mongos :</p>

<pre>mongos --configdb config server hostnames</pre>

<p>Par exemple, si vous souhaitez démarrer une instance mongos qui se connecte aux trois serveurs de configuration suivants (port par défaut) :

_ cfg0.example.net
_ cfg1.example.net
_ cfg2.example.net

Vous voudriez utiliser cette commande :</p>

<pre>mongos --configdb cfg0.example.net:27019,cfg1.example.net:27019,cfg2.example.net:27019</pre>

<p>Chaque instances mongos dans un sharded cluster doit utiliser la même valeur pour "configdb", avec des noms d'hôte identiques listés dans un ordre identique.
Si vous démarrez une instance mongos avec une chaîne de caractères qui ne correspond pas tout à fait à celles des autres mongos du cluster, les mongos
échouent et vous recevez une erreur de type "Config Database String Error".</p>
<a name="ajou"></a>

<div class="spacer"></div>

<p class="small-titre">c) Ajouter des Shards au Cluster</p>

<p>Un shard peut être un mongod en mode standalone ou alors un Replica Set. Dans un environnement de production, chaque shard devrait être un Replica Set.
1) Depuis un shell mongo, connectez-vous à l'instance mongos :</p>

<pre>mongo --host hostname of machine running mongos --port port mongos listens on</pre>

<p>Par exempe, si un mongos est accessible sur mongos0.example.net sur le port 27017 :</p>

<pre>mongo --host mongos0.example.net --port 27017</pre>

<p>2) Ajoutez chaque shard au cluster en utilisant la commande sh.addShard(), comme décrite dans les exemples ci-dessous. Effectuez cette méthode séparement
pour chaque shard. Si le shard est un Replica Set, spécifiez le nom du Replica Set et spécifiez un membre de l'ensemble. Dans un déploiement de production,
tous les shards devraient être des Replica Sets.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Vous pouvez à la place utiliser la commande addShard() qui vous laisse le choix de spécifier un nom et une taille maximum
	pour le shard. Si vous ne les spécifiez pas, MongoDB s'en chargera automatiquement.
</div>

<div class="spacer"></div>

<p>Les exemples suivants ajoutent un shard avec la commande sh.addShard() :

- Pour ajouter un shard pour un Replica Set nommé rs1 avec un membre exécuté sur le port 27017 sur mongodb0.example.net :</p>

<pre>sh.addShard( "rs1/mongodb0.example.net:27017" )</pre>

<p>Pour les version antérieures à MongoDB 2.0.3, vous devez spécifier tous les membres du Replica Set :</p>

<pre>sh.addShard( "rs1/mongodb0.example.net:27017,mongodb1.example.net:27017,mongodb2.example.net...</pre>

<p>- Pour ajouter un shard pour une instance mongod standalone sur le port 27017 nommée mongodb0.example.net :</p>

<pre>sh.addShard( "mongodb0.example.net:27017" )</pre>

<div class="alert alert-info">
	<u>Note</u> : Cela peut prendre du temps aux chunks de migrer sur le nouveau shard.
</div>
<a name="abdd"></a>

<div class="spacer"></div>

<p class="small-titre">d) Activer le Sharding pour une Base de Données</p>

<p>Avant que vous puissiez sharder une collection, vous devez activer le shard pour la base de données de cette collection en question. 
Activer le sharding pour une base de données ne redistribue pas les données mais le rend possible pour sharder les collections de cette base de données.

Une fois le sharding activé, MongoDB assigne un shard primaire pour cette base de données ou MongoDB stocke toutes les données avant que le sharding commence.

1) Depuis un shell mongo, connectez-vous à l'instance mongos et effectuez la commande suivante :</p>

<pre>mongo --host hostname of machine running mongos --port port mongos listens on</pre>

<p>2) Effectuez la commande sh.enableSharding() en sépcifiant le nom de la base de données pour laquelle on souhaite activer le sharding :</p>

<pre>sh.enableSharding("database")</pre>

<div class="alert alert-warning">
	<u>Optionnel</u> : vous pouvez également activer le sharding d'une base de données de cette manière : db.runCommand( { enableSharding: <database> } ).
</div>
<a name="acol"></a>

<div class="spacer"></div>

<p class="small-titre">e) Activer le Sharding pour une Collection</p>

<p>Vous activez le sharding collection par collection :

1) Déterminez ce que vous allez utiliser pour la clé de shard. La sélection de votre clé de shard affecte l'efficacité de votre sharding.
2) Si la collection contient déjà des données, vous devez créer un indexe sur la clé de shard en utilisant la commande ensureIndex(). En revanche,
si celle-ci est vide, alors MongoDB va créer l'indexe dans une étape de la commande sh.shardCollection().
3) Activez le sharding pour la collection en utilisant la commande sh.shardCollection() dans un shell mongo :</p>

<pre>sh.shardCollection("database.collection", shard-key-pattern)</pre>

<p>Remplacer database.collection par le nom de votre base de données, ".", et le nom de votre collection. L'option shard-key-pattern représente votre
clé de shard.

Par exemple, les séquences suivants shardent 4 collections :</p>

<pre>
sh.shardCollection("records.people", { "zipcode": 1, "name": 1 } )
sh.shardCollection("people.addresses", { "state": 1, "_id": 1 } )
sh.shardCollection("assets.chairs", { "type": 1, "_id": 1 } )
db.alerts.ensureIndex( { _id : "hashed" } )
sh.shardCollection("events.alerts", { "_id": "hashed" } )
</pre>

<div class="spacer"></div>

<p>Dans l'ordre, ces opérations shardent :

a) La collection people dans la base de données record utilisant la clé de shard { "zipcode": 1, "name":1 }.
Cette clé de shard distribue les documents par la valeur du champ zipcode. Si un nombre de document a la même valeur pour ce champ, alors ce chunk sera
séparable par la valeur du champ name.
b) La collection addresses de la base de données people utilise la clé de shard { "state": 1, "_id":1 }.
Cette clé de shard distribue les documents par la valeur du champ state. Si un nombre de document a la même valeur pour ce champ, alors ce chunk sera
séparable par la valeur du champ _id.
c) La collection chairs de la base de données assets utilise la clé de shard { "type": 1, "_id": 1}.
Cette clé de shard distribue les documents par la valeur du champ type. Si un nombre de document a la même valeur pour ce champ, alors ce chunk sera
séparable par la valeur du champ _id.
d) La collection alerts de la base de données events utilise la clé de shard { "_id": "hashed" }.
Nouveau dans la version 2.4, la clé de shard distribue les documents par un hash de la valeur du champ _id. MongoDB calcule le hash du champ _id pour l'indexe
hashé, ce qui devrait fournir une distribution équitable de Documents à travers le cluster.</p>
<a name="consi"></a>

<div class="spacer"></div>

<p class="titre">II) [ Considérations de Sélection de Clé de Shard ]</p>

<p>Choisissez correctement un champ dont se servira MongoDB afin de distribuer les documents de votre sharded collection à travers les shards de votre cluster.
Chaque shard détient des documents compris entre certaines valeurs minimales et maximales.</p>
<a name="choi"></a>

<div class="spacer"></div>

<p class="small-titre">a) Choisir une Clé de Shard</p>

<p>Pour la plupart des collections, il ne devrait pas y avoir de simple clé qui possède toutes les qualités d'une bonne clé de shard. Les stratégies suivantes
devraient vous aider à construire une clé de shard utile depuis vos données existantes :

1) Calculez une clé de shard plus idéale dans votre application, puis, stockez-là dans tous vos documents, potientiellement dans le champ _id.
2) Utilisez une clé de shard composée qui utilise deux ou trois valeurs de tous les documents qui fournit le bon mix de cardinalités avec des opérations d'écritures
scalables et l'isolation de requête.
3) Déterminez que l'impacte d'utiliser une clé de shard moins appropriée :
	- volume d'écriture limité
	- taille des données expectée
	- patrons de conceptions de requêtes d'application
4) Depuis la version 2.4, l'utilisation de d'une clé de shard hashée est possible. Choisissez un champ qui a une cardinalité forte et créez un indexe hashé sur
ce champ. MongoDB utilise ces valeurs d'indexe hashé comme valeurs de clé de shard, ce qui assure une répartition égale des données sur les shards.</p>

<div class="alert alert-success">
	<u>Astuce</u> : MongoDB calcule automatiquement les hash lors de l'interprétation d'une requête utilisant un indexe hashé. Vos applications
	n'ont pas besoin de calculer ces hash.
</div>

<div class="spacer"></div>

<p>Considération pour sélectionner une clé de shard : Choisir une clé de shard correcte peut avoir un gros impacte sur les performances, la capcité et le fonctionnement
de votre base de données et du cluster. Le choix d'une clé de shard appropriée dépend du schéma de vos données et de la façon dont votre application
interroge et écrit les données.</p>
<a name="divi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Créer une Clé de Shard Facilement Divisible</p>

<p>Une clé de shard divisible facilement permet à MongoDB de distribuer plus facilement du contenu sur les shards. Les clés de shard qui ont un nombre
limité de valeurs possibles peut entraîner des chunks qui sont indivisibles.</p>
<a name="alea"></a>

<div class="spacer"></div>

<p class="small-titre">c) Créer une Clé de Shard Fortement Aléatoire</p>

<p>Une clé de shard étant fortement aléatoire permet d'éviter à n'importe quel shard de devenir celui qui va en contenir beaucoup plus que les autres, d'être
le "cul de bouteille". Si la clé de shard n'est pas vraiment aléatoire, les chunks iront toujours au même shard. Plus la clé sera aléatoire et mieux les chunks
seront distribués dans le cluster.</p>
<a name="cibl"></a>

<div class="spacer"></div>

<p class="small-titre">d) Créer une Clé de Shard qui ne Cible qu'un seul Shard</p>

<p>Une clé de shard qui ne cible qu'un seul shard permet au programme mongos de retourner les résultats des requêtes directement depuis une seule instance mongod
spécifique. Votre clé de shard devrait être le champ primaire utilisé par vos requêtes. Les champs étant très aléatoires rendent la tâche plus compliquée
pour cibler des shards spécifiques.</p>
<a name="comp"></a>

<div class="spacer"></div>

<p class="small-titre">e) Utiliser une Clé de Shard Composée</p>

<p>Le challenge lors de la sélection d'une clé de shard est qu'il n'y pas toujours de choix évident. Souvent, un champ existant dans votre collection
n'est pas toujours la clé optimale. Dans ces situations, calculer une spéciale clé de shard dans un champ additionnel ou utiliser une clé de shard composée
pourrait aider à en produire une qui est plus idéale.</p>
<a name="card"></a>

<div class="spacer"></div>

<p class="small-titre">f) Cardinalité</p>

<p>La cardinalité au sein de MongoDB fait référence à la capacité du système à partitionner les données en chunks. Par exemple, considérons une collection
de données telles qu'un annuaire qui stocke des adresses :

- Considérez le champ "state" en tant que clé de shard.
La valeur de la clé state détient l'état US pour une adresse donnée par document. Ce champ a une cardinalité faible vu que tous les documents qui ont 
la même valeur dans le champ state doivent rester sur le même shard, même si le chunk d'un state particulier dépasse la taille maximum de shard.

Vu qu'il n'y a qu'un nombre limité de valeurs pour le champ state, MongoDB devrait distribuer de façon non équitable les données sur un nombre réduit
de chunks. Cela devrait avoir quelques effets :

	- Si MongoDB ne peut pas séparer un chunk car tous ses documents ont la même clé de shard, les migrations involvant ces chunks inséparables vont prendre plus de
	temps que les autres migrations, et ce sera plus difficile pour votre base de données à rester balancée correctement.
	- Si vous avez un nombre maximum de chunks fixé, vous nous pourrez alors jamais utiliser plus que ce nombre en shards pour votre collection.

- Considérons l'utilisation du champ zipcode en tant que clé de shard :
Vu que ce champ a un grand nombre de valeurs possibles, et donc a une plus forte cardinalité, il est possible qu'un grand nombre d'utilisateur ai la même valeur
pour la clé de shard, ce qui rendrait ce chunk impossible à séparer.
Dans ces situations, la cardinalité dépend des données. Si votre annuaire stocke des données pour une liste de contactes géographiquement distribués (exemple : 
"Dry cleaning businesses in America") alors la valeur de zipcode serait suffisante. En revanche, si votre annuaire est plus géographiquement concentré
par exemple : "ice cream stores in Boston Massachussetts") alors vous aurez une cardinalité beaucoup plus faible.

- Considérons l'utilisation du champ "phone-number" en tant que clé de shard :
Le numéro de téléphon a une cardinalité forte car les utilisateurs vont en général avoir une valeur unique pour ce champ. MongoDB va pouvoir séparer autant de
chunks que possibles.


Vu qu'une forte cardinalité permet d'assurer si nécessaire une distribution équitables des données, en avoir une ne garantit pas une isolation de requête suffisante
ou une scalabilité des écritures appropriée.</p>
<a name="hash"></a>

<div class="spacer"></div>

<p class="titre">III) [ Sharder une Collection avec une Clé de Shard Hashée ]</p>

<p>Vous pouvez apprendre à sharder une collection basé sur des valeurs hashées d'un champ dans le but d'assurer une distribution équitable à travers
les shards de votre collection. Nouveaux dans la version 2.4 de MongoDB, ces clés hashées utilisent les indexes hashés d'un champ en tant que clé de shard pour
partitionner les informations au sein de votre cluster.</p>

<div class="alert alert-info">
	<u>Note</u> : Si des migrations de chunks sont en cours d'exécution pendant que vous créer une clé de shard hashée de votre collection,
	la distribution intiale de chunk devrait être déséquilibrée jusqu'à ce que le balanceur équilibre automatiquement la collection.
</div>
<a name="coll"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sharder la Collection</p>

<p>Pour sharder une collection en utilisant une clé de shard hashée, utilisez une opération dans un shell mongo qui ressemble à ceci :</p>

<pre>sh.shardCollection( "records.active", { a: "hashed" } )</pre>

<p>Cette opérations sharde la collection "active" dans la collection "records", en utilisant un hash du champ "a" comme une clé de shard.</p>
<a name="init"></a>

<div class="spacer"></div>

<p class="small-titre">b) Spécifier le Nombre Initial de Chunks</p>

<p>Si vous shardez une collection vide en utilisant une clé de shard hashée, MongoDB créé et migre automatiquement les chunks vides de manière à ce que chaque
shard ai deux chunks. Pour contrôler combien de chunks MongoDB doit créer lors du Sharding d'une collection, utilisez la commande shardCollection()
avec le paramètre numInitialChunks.</p>

<div class="alert alert-danger">
	<u>Attention</u> : MongoDB 2.4 ajoute le support des clés de shard hashées. Après avoir shardé une collection avec une clée de shard
	hashée, vous devez utiliser la version 2.4 ou MongoDB ou plus pour les instances mongos et mongod pour votre sharded cluster.
</div>

<div class="alert alert-danger">
	<u>Attention</u> : Les indexes hashés de MongoDB tronques les nombres flotants en entiers 64bits avant de hasher. Par exemple,
	un indexe hashé va stocker la même valeur pour un champ qui contient la valeur de 2.3.2.2 et 2.9. Pour éviter les collisions, n'utilisez pas un indexe
	hashé pour les nombres flottants qui ne peuvent pas être convertis en entiers 64bits (et vice-versa). Les indexes hashés de MongoDB ne supportent pas
	les valeurs flottantes supérieures à 2<sup>53</sup>.
</div>
<a name="auth"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Activer l'Authentification dans un Sharded Cluster ]</p>

<p>Contrôler l'accès à votre sharded cluster avec un fichier clé et le paramètre keyFile sur chaque composant du Cluster (tous mongos, mongod des serveurs de
configuration et mongod par shard inclus). Le contenu de ce fichier est arbitraire mais doit être le même sur tous les membres du cluster.</p>

<p>Pour activer l'authentification :

1) Générez un fichier clé qui servira stocker les informations, comme décrit ici (link for par gage 256).
2) Activez ainsi l'authentificaton sur chaque composants du cluster en réalisant l'une de ces méthodes :
	- dans le fichier de configuration, définissez l'option keyFile avec le chemin de votre fichier clé et démarrez ensuite le composant :</p>

<pre>keyFile = /srv/mongodb/keyfile</pre>

<p>	- Lorsque vous démarrez le composant, démarrez avec l'option --keyFile qui est une option disponible pour les mongos et les mongod. Attribuez lui
la valeur du chemin de votre fichier clé.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Le paramètre keyFile implique "auth", ce qui signifie que vous n'aurez pas besoin de définit "auth" explicitement.
</div>

<p>3) Ajoutez le premier utilisateur administratif et ensuite, les autres utilisateurs.</p>
<a name="ajous"></a>

<div class="spacer"></div>

<p class="titre">V) [ Ajouter des Shards à un Cluster ]</p>

<p>Ajoutez un shard pour ajouter de la capacité à votre sharded cluster. N'oublions pas que dans un environnement de production, tout shard doit être un Replica
Set.</p>
<a name="ajun"></a>

<div class="spacer"></div>

<p class="small-titre">a) Ajouter un Shard au Cluster</p>

<p>Vous communiquez avec le Cluster par l'intermédiaire d'une instance mongos.
Depuis un shell mongo, connectz-vous à l'instance. Par exemple, si un mongos est accessible sur mongos0.example.net sur le port 27017 :</p>

<pre>mongo --host mongos0.example.net --port 27017</pre>

<p>2) Ajoutez le shard au cluster en utilisant la commande sh.addShard() comme dans les exemples ci-dessous. Effectuez cette commande séparement pour chaque shard.
Si le shard est un Replica Set, spécifiez le nom du Replica Set le nom d'un des membres du set.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Vous pouvez utiliser la commande de base de données addShard, qui vous permet de spécifier un nom et la taille 
	maximale du shard. Si vous ne les spécifiez pas, MongoDB s'en chargera tout seul.
</div>

<div class="spacer"></div>

<p>Les exemples suivants ajoutent un shard avec la commande sh.addShard() :
	- Pour ajouter un shard à un Replica Set nommé rs1 avec un membre écoutant sur le port 27017 nommé mongodb0.example.net :</p>
	
<pre>sh.addShard( "rs1/mongodb0.example.net:27017" )</pre>

<p>Dans les versions inférieures à la version 2.0.3, veuillez utiliser la même commande mais en spécifiant tous les noms de tous les membres du Replica Set :</p>

<pre>sh.addShard( "rs1/mongodb0.example.net:27017,mongodb1.example.net:27017,mongodb2.example.net:....</pre>

<p>- Pour ensuite ajouter un shard pour un mongod en mode standalone sur le port 27017 nommé mongodb0.exemple.net :</p>

<pre>sh.addShard( "mongodb0.example.net:27017" )</pre>

<div class="alert alert-info">
	Information : Cela peut prendre du temps pour que les chunks migrent vers le nouveau shard.
</div>
<a name="srvc"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Déployer 3 Serveurs de Configuration pour la Production ]</p>

<p>Convertir un environnement de test ayant un seul serveur de configuration en un environnement de production ayant trois serveurs de configuration.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Utilisez les CNAMEs pour identifier vos serveurs de configuration de votre cluster, comme ça vous allez pouvoir les renommer
	ou les renumérer sans devoir les stopper.
</div>

<div class="spacer"></div>

<p>Pour la redondance, tous les environnements de production doivent avoir exactement trois serveurs de configuration sur trois différentes machines.
Vous pouvez utiliser un seul serveur de configuration pour un environnement de test uniquement. Si vous migrez vers un environnement de production,
mettez à jour votre configuration de manière à ce qu'il y ait ces trois serveurs.
Pour convertir un un environnement de test ayant un seul serveur de configuration en un environnemetn de production ayant trois serveurs de configuration :

1) Stoppez tous les processus MongoDB du cluster, incluant :
	- toutes les instances mongod ou replica set qui représentent vos shards
	- toutes les instances mongos du cluster

2) Copiez le dbpath complet du serveur de configuration existant sur les deux autres machines qui vont représenter les deux autres serveurs.
Pour effectuer cela depuis un hôte mongo-config0.example.net :</p>

<pre>
rsync -az /data/configdb mongo-config1.example.net:/data/configdb
rsync -az /data/configdb mongo-config2.example.net:/data/configdb
</pre>

<p>3) Démarrez les trois serveurs de configuration, en utilisant la même méthode que ce que vous avez utilisé pour le premier serveur :</p>

<pre>mongod --configsvr</pre>

<p>Redémarrez tous les mongod et mongos du cluster.</p>
<a name="conv"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Convertir un Replica Set en un Sharded Cluster Répliqué ]</p>

<p>Convertir un Replica Set en un Sharded Cluster dans lequel chaque shard est son propre Replica Set.</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>En suivant ce tutoriel, vous allez convertir un replica set de 3 membres en un cluster à deux shards. Chaque shard va représenter un replica set de 3 membres.
Ce tutoriel utilise un environnement de test exécuté sur un système de type UNIX. Si vous souhaitez effectuer ce processus dans un environnement de production,
vous trouverez des notes tout au long de ce tutoriel.

D'un point de vue global :
1) Créer ou sélectionner un Replica Set à trois membres et insérez des données dans une collection.
2) Démarrer les bases de données de configuration et créez un cluster avec un simple shard.
3) Créez un second replica set avec 3 nouvelles instances mongod.
4) Ajoutez le second replica set en tant que shard au cluster.
5) Activer le sharding sur une ou plusieurs collection(s).
</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="small-titre">b) Processus</p>

<p>Installez MongoDB comme indiqué dans le tutoriel d'installation (link).

Déployez un Replica Set avec des données de test : Si vous avez un Replica Set existant, vous pouvez sauter cette étape et continuer à l'étape
"Déployer une Infrastructure de Sharding (ancre).
Utilisez les étapes suivantes pour configurer et déployer un Replica Set et insérer des données dedans :

1) Créez les répertoires suivants pour la première instance du Replica Set, nommée "firstset" :
	- /data/example/firstset1
	- /data/example/firstset2
	- /data/example/firstset3
Pour créer les répertoires :</p>

<pre>mkdir -p /data/example/firstset1 /data/example/firstset2 /data/example/firstset3</pre>

<p>2) Dans un shell séparé, démarrez trois instances mongod avec les commandes suivantes :</p>

<pre>
mongod --dbpath /data/example/firstset1 --port 10001 --replSet firstset --oplogSize 700 --rest .......
mongod --dbpath /data/example/firstset2 --port 10002 --replSet firstset --oplogSize 700 --rest .......
mongod --dbpath /data/example/firstset3 --port 10003 --replSet firstset --oplogSize 700 --rest .......
</pre>

<div class="alert alert-info">
	<u>Note</u> : L'option --oplogSize 700 restreint la taille de l'Oplog de chaque instance mongod à 700mo. Sans cette option,
	chaque mongod réserve environ 5% de l'espace disque disponible. En limitant la taille de l'oplog, chaque instance démarre plus rapidement.
	Supprimez ce paramètre pour un déploiement de production.
</div>

<div class="spacer"></div>

<p>3) Dans un shell mongo, connectez-vous à l'instance mongod en vous connectant au port 10001 avec la commande suivante. Si vous êtes dans un environnement
de production, lisez d'abord la note qui suite :</p>

<pre>mongo localhost:10001/admin</pre>

<div class="alert alert-info">
	<u>Note</u> : Plus haut ou dans la partie suivante, si vous êtes en situation de production ou en situation de test avec des instances mongod
	sur plusieurs systèmes, remplacez localhost par un nom de domaine, nom d'hôte ou adresse IP.
</div>

<div class="spacer"></div>

<p>4) Dans un shell mongo, intialisez le premier Replica Set avec la commande suivante :</p>

<pre>
db.runCommand(
	{
		"replSetInitiate" :
			{"_id" : "firstset", "members" : [
												{"_id" : 1, "host" : "localhost:10001"},
												{"_id" : 2, "host" : "localhost:10002"},
												{"_id" : 3, "host" : "localhost:10003"}
								 ]
			}
	}
)

{
	"info" : "Config now saved locally. Should come online in about a minute.",
	"ok" : 1
}
</pre>

<div class="spacer"></div>

<p>5) Dans un shell mongo toujours, créez une nouvelle collection avec les séquences Javascript suivantes :</p>

<pre>
use test
switched to db test
people = ["Marc", "Bill", "George", "Eliot", "Matt", "Trey", "Tracy", "Greg", "Steve", "Kristina", 

for(var i=0; i&inf;1000000; i++){
	name = people[Math.floor(Math.random()*people.length)];
	user_id = i;
	boolean = [true, false][Math.floor(Math.random()*2)];
	added_at = new Date();
	number = Math.floor(Math.random()*10001);
db.test_collection.save({"name":name, "user_id":user_id, "boolean": }
(missing datas ici)
</pre>

<div class="spacer"></div>

<p>Les opérations ci-dessus ajoute un million de documents à la collection "test_collection". Cela peut prendre plusieurs minutes en fonction de votre système.
Le script ajoute des documents de la forme :</p>

<pre>{ "_id" : ObjectId("4ed5420b8fc1dd1df5886f70"), "name" : "Greg", "user_id" : 4, "boolean" : true, "added...........</pre>

<p>Déployer une Infrastructure de Sharding : Cette procédure créer les trois bases de données de configuration qui stockent les méta-informations
du cluster.</p>

<div class="alert alert-info">
	<u>Note</u> : Pour du développement ou du test, une seule base de données suffit. Par contre, durant la production, les trois serveurs sont
	obligatoires. Du fait que ces serveurs de configuration ne gèrent que les méta-informations du cluster, ceux-ci sont très léger au niveau des besoins en ressources
	système.
</div>

<div class="spacer"></div>

<p>1) Créez les répertoires de données suivants : 
- /data/example/config1
- /data/example/config2
- /data/example/config3
avec la commande suivante :</p>

<pre>mkdir -p /data/example/config1 /data/example/config2 /data/example/config3</pre>

<p>2) Dans un shell séparé, démarrez les serveurs de configuration suivants :</p>

<pre>
mongod --configsvr --dbpath /data/example/config1 --port 20001
mongod --configsvr --dbpath /data/example/config2 --port 20002
mongod --configsvr --dbpath /data/example/config3 --port 20003
</pre>

<div class="spacer"></div>

<p>3) Dans un shell séparé encore, démarrez l'instance mongos avec la commande suivante :</p>

<pre>mongos --configdb localhost:20001,localhost:20002,localhost:20003 --port 27017 --chunkSize 1</pre>

<div class="alert alert-info">
	<u>Note</u> : Si vous utilisez la collection créée précédement, ou alors vous vous essayez juste au Sharding, vous pouvez utiliser
	un petit --chunkSize (1mo fonctionne bien). La taille de chunk par défaut de 64mo signifie que votre cluster doit contenir au moins 64mo de données
	avec que le sharding automatique de MongoDB commence à fonctionner.
	Dans les environnements de production, n'utilisez surtout pas de petites tailles de chunk.
</div>

<div class="spacer"></div>

<p>L'option configdb spécifie les bases de données de configuration (ici, localhsot:20001, localhost:20002 et localhost 20003).
L'instance mongos écoute sur les port MongoDB par défaut, 27017, donc si vous utilisez ce même port, esquivez cette option --port 27017.

4) Ajoutez le premier shard dans mongos. Dans un nouveau shell :
	a) Connectez-vous au mongos :</p>
	
<pre>mongo localhost:27017/admin</pre>

<p>b) Ajoutez le premier shard au cluster avec la commande addShard() :</p>

<pre>db.runCommand( { addShard : "firstset/localhost:10001,localhost:10002,localhost:10003" } )</pre>

<p>c) Observez le message suivant qui indique que le shard a été ajouté avec succès au cluster :</p>

<pre>{ "shardAdded" : "firstset", "ok" : 1 }</pre>

<div class="spacer"></div>

<p>Déployez un second Replica Set : Cette procédure va copier de très près celle du déploiement du premier replica set, a l'exception de l'insertion
des données de test.

1) Créez les répertoires de données pour les membres du second replica set, nommé "secondset" :

- /data/example/secondset1
- /data/example/secondset2
- /data/example/secondset3

2) Dans trois nouveaux terminaux, démarrez trois instances mongodb :</p>

<pre>
mongod --dbpath /data/example/secondset1 --port 10004 --replSet secondset --oplogSize 700 --rest .........
mongod --dbpath /data/example/secondset2 --port 10005 --replSet secondset --oplogSize 700 --rest .........
mongod --dbpath /data/example/secondset3 --port 10006 --replSet secondset --oplogSize 700 --rest .........
</pre> 

<div class="alert alert-info">
	<u>Note</u> : Le second replica set utilise un oplogSize réduit. Supprimez cette option pour un déploiement de production.
</div>

<div class="spacer"></div>

<p>3) Dans un nouveau shell, connectez-vous à une instance mongod :</p>

<pre>mongo localhost:10004/admin</pre>

<p>Dans un nouveau shell, initialisez le second replica set avec la commande suivante :</p>

<pre>
db.runCommand(
	{
		"replSetInitiate" :
			{"_id" : "secondset",
				"members" : [
					{"_id" : 1, "host" : "localhost:10004"},
					{"_id" : 2, "host" : "localhost:10005"},
					{"_id" : 3, "host" : "localhost:10006"}
				]
			}
	}
)

{
	"info" : "Config now saved locally. Should come online in about a minute.",
	"ok" : 1
}
</pre>

<div class="spacer"></div>

<p>5) Ajoutez le second Replica Set au Cluster. Connectez-vous à l'instance mongos créée dans la procédure précédente et saisissez la commande suivante :</p>

<pre>
use admin
db.runCommand( { addShard : "secondset/localhost:10004,localhost:10005,localhost:10006" } )
</pre>

<p>Cette commande retourne le message suivante :</p>

<pre>{ "shardAdded" : "secondset", "ok" : 1 }</pre>

<p>6) Vérifiez que les deux shards sont correctement configurés en exécutant la commande listShards :</p>

<pre>
db.runCommand({listShards:1})
{
	"shards" : [
		{
			"_id" : "firstset",
			"host" : "firstset/localhost:10001,localhost:10003,localhost:10002"
		},
		{
			"_id" : "secondset",
			"host" : "secondset/localhost:10004,localhost:10006,localhost:10005"
		}
	],
	"ok" : 1
}
</pre> 

<div class="spacer"></div>

<p>Activer le Sharding : MongoDB doit avoir le sharding activé à la fois au niveau de la base de données mais aussi au niveau de la collection.

Activer le sharding au niveau de la base de données : Utilisez la commande enabledSharding :</p>

<pre>
db.runCommand( { enableSharding : "test" } )
{ "ok" : 1 }
</pre>

<p>Cet exemple montre comment activer le sharding sur la base de données "test".

Créer un Indexe sur la Clé de Shard : MongoDB utilise la clé de shard pour distribuer les documents entre les shards. Une fois sélectionnée, 
vous ne pourrez pas changer cette clé. Les bonnes clés ont :

- des valeurs distribuées de façon équilibrée à travers tous les documents
- regroupe les documents souvent interrogés en même temps dans des chunks contigus et,
- favorise la bonne distribution à travers les shards

Les Clés de Shard habituelles sont des clés de shard composées, un compromis entre une sorte de hash et une sorte de clé primaire.
Sélectionner une clé de shard dépend de votre ensemble de données, de l'architecture de votre application et de l'utilisation que vous en faîtes.
Pour cet exemple, nous allons sharder la clé "numberé, ce qui ne serait pas du tout une bonne clé pour les environnements de production.
Créez l'indexe avec la procédure suivante :</p>

<pre>
use test
db.test_collection.ensureIndex({number:1})
</pre>

<div class="spacer"></div>

<p>Sharder la Collection : Effectuez la commande suivante :</p>

<pre>
use admin
db.runCommand( { shardCollection : "test.test_collection", key : {"number":1} })
{ "collectionsharded" : "test.test_collection", "ok" : 1 }
</pre>

<p>La collection test_collection est maintenant shardée !

Pendant les prochaines minutes, le balanceur va commencer à distribuer les chunks de vos documents. Vous pouvez confirmer cette activité en vous connectant à
la base de données test et exécuter la commande db.stats() ou db.printShardingStatus().

Comme les clients insèrent plus de documents dans la collection, mongos va distribuer les documents de manière équitable entre les shards.
Dans un shell mongo, effectuez la commande suivante afin d'obtenir des statistiques sur chaque cluster :</p>

<pre>
use test
db.stats()
db.printShardingStatus()
</pre>

<p>Exemple de résultat de la commande db.stats() :</p>

<pre>
{
	"raw" : {
		"firstset/localhost:10001,localhost:10003,localhost:10002" : {
			"db" : "test",
			"collections" : 3,
			"objects" : 973887,
			"avgObjSize" : 100.33173458522396,
			"dataSize" : 97711772,
			"storageSize" : 141258752,
			"numExtents" : 15,
			"indexes" : 2,
			"indexSize" : 56978544,
			"fileSize" : 1006632960,
			"nsSizeMB" : 16,
			"ok" : 1
		},
		"secondset/localhost:10004,localhost:10006,localhost:10005" : {
			"db" : "test",
			"collections" : 3,
			"objects" : 26125,
			"avgObjSize" : 100.33286124401914,
			"dataSize" : 2621196,
			"storageSize" : 11194368,
			"numExtents" : 8,
			"indexes" : 2,
			"indexSize" : 2093056,
			"fileSize" : 201326592,
			"nsSizeMB" : 16,
			"ok" : 1
		}
	},
	"objects" : 1000012,
	"avgObjSize" : 100.33176401883178,
	"dataSize" : 100332968,
	"storageSize" : 152453120,
	"numExtents" : 23,
	"indexes" : 4,
	"indexSize" : 59071600,
	"fileSize" : 1207959552,
	"ok" : 1
}
</pre>

<div class="spacer"></div>

<p>Et ici, un exemple de résultat de la commande rs.printShardingStatus() :</p>

<pre>
--- Sharding Status ---
sharding version: { "_id" : 1, "version" : 3 }
shards:
{ "_id" : "firstset", "host" : "firstset/localhost:10001,localhost:10003,localhost:10002" }
{ "_id" : "secondset", "host" : "secondset/localhost:10004,localhost:10006,localhost:10005" databases:
{ "_id" : "admin", "partitioned" : false, "primary" : "config" }
{ "_id" : "test", "partitioned" : true, "primary" : "firstset" }
test.test_collection chunks:
secondset 5
firstset 186
[...]
..................
</pre>

<p>A plusieurs reprises, vous allez pouvoir exécuter ces commandes afin de bien voir que les chunks migrent de firstset vers secondset. Une fois cette procédure
terminée, vous aurez convertit un replica set en un cluster ou chaque shard lui-même est un replica set.</p>
<a name="convr"></a>

<div class="spacer"></div>

<p class="titre">VIII) [ Convertir un Sharded Cluster en Replica Set ]</p>

<p>Remplacez votre Sharded Cluster par un simple Replica Set.</p>
<a name="simp"></a>

<div class="spacer"></div>

<p class="small-titre">a) Convertir un Cluster ayant un Simple Shard en un Replica Set</p>

<p>Dans le cas d'un sharded cluster n'ayant qu'un seul shard, tout en sachant que ce shard unique contient tout l'ensemble de données. Utilisez la procédure suivante
pour convertir ce cluster en un replica set non shardé :

1) Reconfigurez l'application pour se connecter au membre primaire du replica set hébergant le simple shard qui va être le nouveau replica set.
2) Optionnellement, supprimez l'option shardsrv si votre mongod démarrait avec celle-ci.
</p>

<div class="alert alert-info">
	<u>Note</u> : Changez l'option --shardsrv va changer le port sur lequel mongod écoute pour les connexions entrantes.
</div>

<div class="spacer"></div>

<p>Le cluster à une seul shard est maintenant un replica set non shardé qui va accepter les lectures et écritures sur l'ensemble de données.</p>
<a name="rs"></a>

<div class="spacer"></div>

<p class="small-titre">b) Convertir un Sharded Cluster en un Replica Set</p>

<p>Utilisez la procédure suivante pour transformer un un sharde cluster ayant plusieurs shards en un seul replica set :

1) Avec un cluster shardé en cours d'exécution, déployez un nouveau replica set en addition à votre sharded cluster. Le Replica Set doit avoir assez de capacité
afin de contenir touts les informations de tous les shards regroupés. Ne configurez pas l'application afin qu'elle se connecte au replica set avant que
le transfert des données soit complet.

2) Arrêtez toutes les écritures vers le sharded cluster. Vous devrez reconfigurer votre application ou stopper toutes les instances mongos. Si vous stoppez
toutes les instances mongos, les applications ne pourront pas pouvoir lire depuis la base de données. Si vous stoppez toutes les instances mongos,
démarrez une instance mongos temporaire sur laquelle les applications ne pourront se connecter pendant la procédure de migration des données.

3) Utilisez mongodump et mongorestore pour migrer les données de l'instance mongos vers le nouveau Replica Set.</p>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Toutes les collections de la base de données ne sont pas forcément shardées. Ne migrer pas uniquement les collections shardées.
	Assurez-vous que toutes les bases de données et toutes les collections migrent correctement.
</div>

<p>Reconfigurez l'application pour qu'elles utilisent le nouveau replica set non shardé à la place de l'instance mongos.

L'application va maintenant utiliser un replica set non shardé pour les lectures et écritures.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur la <a href="tutoriaux_maintenance.php">Maintenance de Sharded Cluster >></a>.</p>

<?php

	include("footer.php");

?>