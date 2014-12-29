<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Management des Données</li>
</ul>

<p class="titre">[ Management des Données ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#segr">I) Ségrégation Opérationnelle dans les Déploiements MongoDB</a></p>
	<p class="elem"><a href="#cc">II) Collection Capped</a></p>
	<p class="right"><a href="#reco">- a) Recommandations et Restrictions</a></p>
	<p class="right"><a href="#proc">- b) Procédures</a></p>
	<p class="elem"><a href="#expi">III) Expiration des Données d'une Collection en Définissant le TTL</a></p>
	<p class="right"><a href="#ttl">- a) Activer TTL pour une Collection</a></p>
	<p class="right"><a href="#cont">- b) Contraintes</a></p>
</div>

<p>MongoDB fournit un nombre de fonctionnalités qui permettent aux développeurs d'applications et aux administrateurs de bases de données de personnaliser
le comportement d'un Sharded Cluster ou d'un Replica Set de manière à ce que MongoDB soit plus orienté Data Center ou alors qu'il autorise la séparation
des opérations basée sur la localité.

MongoDB supporte aussi la ségrégation basée sur des paramètres fonctionnels pour s'assurer que certaines instances mongod sont utilisées seulement pour
du reporting ou que certaines portions d'un sharded cluster hautement exploitées existent seulement sur un shard spécifique.
<a name="segr"></a>

<div class="spacer"></div>

<p class="titre">I) [ Ségrégation Opérationnelle dans les Déploiements MongoDB ]</p>

<p>MongoDB va vous permettre de spécifier le fait que certaines opérations vont devoir utiliser certaines instances mongod.
Cette capacité de ségrégation va vous permettre de cibler des déploiements MongoDB spécifiques en considérant la location physique d'instances mongod.
MongoDB supporte la ségmentation des opérations à travers différentes dimensions, ce qui devrait inclure de multiples data centers ainsi que des régions
géographiques dans de multiples dépoiements de data centers, racks, réseaux ou circuits d'alimentation dans un simple déploiement de data center.

MongoDB supporte aussi la ségrégation des opérations d'une base de données basée sur des paramètres fonctionnels ou opérationnels, pour s'assurer que certains
mongod sont utilisés uniquement pour du reporting ou des portions du sharded cluster qui existent uniquement sur un shard particulier.
En bref, avec MongoDB vous pouvez :
- être sûr que les opérations d'écriture se propagent sur des membres spécifiques du Replica Set ou de membres spécifiques de Replica Sets.
- être sûr que les membres spécifiques d'un replica set répondent aux requêtes.
- être sûr que certaines intervalles de votre clé de shard balancent et résident sur de spécifiques shards.
- combinez les caractéristiques ci-dessus dans un unique déploiement distribué basé par opération (lecture et écriture) et par collection (distribution de chunks).</p>
<a name="cc"></a>

<div class="spacer"></div>

<p class="titre">II) [ Collection Capped ]</p>

<p>Les Capped collections sont des collections à taille fixes qui vont supporter des hauts-débits d'opérations qui insèrent, récupèrent ou suppriment des documents
basés sur l'ordre d'insertion. Les collections capped fonctionnent de façon similaire aux tampons (buffers) circulaires : une fois qu'une collection
remplit son espace initialement alloué, celle-ci attribue une place aux nouveaux documents en réécrivant par dessus les plus anciens dans la collection.
Les capped collections ont les particularités suivantes :

- les capped collections guarantient l'ordre d'insertion des documents. En conséquences, les requêtes ne nécessitent pas d'indexe afin de retourner les documents
par ordre d'insertion. Sans ce surplus d'indexage, elles peuvent supporter de gros débits d'insertion.
- Les capped collections guarantient que l'ordre d'insertion est le même que sur le disque (ordre naturel) et effectue cela en empêchant les updates
qui augmentent la taille des documents. Les capped collections autorisent uniquement les mises à jour qui tiennent dans la taille originale du document,
ce qui permet de s'assurer qu'un document ne change pas de place sur le disque.
- les capped collections suppriment automatiquement les documents les plus anciens de la collection sans avoir besoin de scripts quelqconques ou d'opérations
de suppressions.

Par exemple, la collection oplog.rs qui stocke des logs des opérations dans un Replica Set, utilise une capped collection. 
Considérons les potentiels cas d'utilisation pour les capped collections :

- Stocker les informations générées par de gros volumes. Insérer des documents dans une capped collection sans indexe est proche de la vitesse
d'écriture des informations logs directement sur un système de fichiers. Plus loin encore, les propriétés FIFO (first-in first-out) maintiennent l'ordre
des évênements tout en gérant l'utilisation de stockage.

- Mettre en cache de petites quantitées de données dans une capped collection. Depuis que les caches sont en lecture plus que d'une écriture lourde, 
vous aurez soit besoin de vous assurer que cette collection reste dnas l'ensemble de travail (dans la RAM), ou accepte quelques fautes d'écritures pour le ou les
indexe(s) nécéssaire(s).</p>
<a name="reco"></a>

<div class="spacer"></div>

<p class="small-titre">a) Recommandations et Restrictions</p>

<p>- Vous pouvez mettre à jour les documents dans une collection après les insérer. Par contre, ces mises à jours ne doivent pas augmenter la taille du document.
Si l'opération de mise à jour va faire grossir la taille du document, elle va échouer.
Si vous plannifiez de mettre à jour des documents dans une collection capped, créez un indexe de manière à ce que ces opérations update ne recquièrent pas
un scan de table.

- Vous ne pouvez pas supprimer des documents d'une capped collection. Pour supprimer tous les enrgistrements d'une capped collection, utilisez la commande
'emptycapped'. Pour supprimer entièrement la collection, utilisez la méthode drop().

- Vous ne pouvez pas sharder une capped collection.

- Les collections capped créées après la version 2.2 ont un champ _id et un indexe sur ce champ par défaut. Les capped collections créées avant la version 2.2 n'ont pas d'indexe
sur le champ _id par défaut. Si vous utilisez des capped collections avec la réplication antérieure à la version 2.2, vous devrez alors explicitement créer
un indexe sur le champ _id.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Si vous avez une capped collection dans un Replica Set en dehors de la base de données 'local', avant la version 2.2, vous 
	devrez créer un indexe unique sur le champ _id. Appliquez l'unicité en utilisant l'option unique: true avec la méthode ensureIndexe() ou en utilisant
	un ObjectId pour le champ _id. Alternativement, vous pouvez utiliser l'option autoIndexId pour créer lorsque vous initialisez la capped collection,
	comme dans procédure de 'Interroger une capped collection'.
</div>

<p>- Utilisez l'ordre naturel pour récupérer les élements les plus récements insérés dans la collection de manière efficace. C'est un peu comme la commande
'tail' sous les systèmes UNIX.</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédures</p>

<p>Créer une Capped Collection : Vous devez créer des capped collections explicitement en utilisant la méthode createCollection(). Lorsque vous créer une capped collection,
vous devez spécifier la taille maximale de la collection en bytes, que MongoDB va lui pré-allouer :</p>

<pre>db.createCollection( "log", { capped: true, size: 100000 } )</pre>

<p>Additionnellement, vous voudrez aussi spécifier un nombre maximum de documents pour la collection en utilisant le paramètre max comme suivant :</p>

<pre>db.createCollection("log", { capped : true, size : 5242880, max : 5000 } )</pre>

<div class="alert alert-danger">
	<u>Attention</u> : Le paramètre size est toujours nécessaire, même si vous spécifiez le nombre maximum de documents. MongoDB va supprimer
	les documents les plus anciens si une collection atteint sa taille maximale avant qu'elle n'atteigne son nombre maximum de documents.
</div>

<div class="spacer"></div>

<p>Interroger une Capped Collection : Si vous effectuez un find() sur une capped collection sans ordre spécifié, MongoDB va retourner les documents
dans le même ordre que quand ceux-ci ont été ajoutés.
Pour récupérer les documents dans l'ordre inverse de l'ordre d'insertion, utilisez la commande find() suivie d'un sort() avec le paramètre $natural
définit à -1 comme dans l'exemple suivant :</p>

<pre>db.cappedCollection.find().sort( { $natural: -1 } )</pre>

<p>Vérifier si une collection est capped : utilisez la méthode isCapped() pour le déterminer :</p>

<pre>db.collection.isCapped()</pre>

<p>Convertir une collection en collection capped : Vous pouvez convertir une collection non capped en collection capped avec la commande convertToCapped :</p>

<pre>db.runCommand({"convertToCapped": "mycoll", size: 100000});</pre>

<p>Le paramètre size spécifie la taille de la collection capped en bytes.
Avant la version 2.2, les capped collections n'avaient pas d'indexe sur le champ _id sans l'avoir créé. Après la 2.2, celui-ci est créé par défaut.

Supprimer automatiquement les Données après une période de temps spécifiée : Pour plus de flexibilité lorsque les données expirent, considérez les indexes TTL de MongoDB
(Time To Live) comme décris dans le paragraphe ci-dessous. Ces indexes vous permettent d'expirer et de supprimer des données des collections normales
en utilisant un type spécial, basé sur la valeur d'un champ de type date et la valeur TTL de l'indexe. Les collections TTL ne sont pas compatibles avec
les collections capped.

Curseur 'tailable' : Vous pouvez utiliser un cursor 'tailable' avec les capped collections. De façon similaire à la commande tail -f sous les systèmes
UNIX, le curseur 'tailable' va lire la fin de la capped collection. Comme les nouveaux documents sont insérés dans la capped collection, vous pouvez utiliser
ce curseur pour récupérer continuellement ces documents.</p>
<a name="expi"></a>

<div class="spacer"></div>

<p class="titre">III) [ Expiration des Données d'une Collection en Définissant le TTL ]</p>

<p>Depuis la version 2.2, les collections TTL (time to live) permettent de stocker des données dans MongoDB et d'avoir un mongod qui va supprimer automatiquement
les données après un nombre de secondes définit, ou une heure précise.
L'expiration des données est utile pour certaines catégories d'information, incluant les données d'évênements générés par votre machine, les logs et les informations
de sessions qui ont besoin de persister pendant une durée limitée dans le temps.

Un type spéciale d'indexe supporte l'implémentation des collections TTL. Le TTL s'appuye sur une Thread de fond dans mongod qui va lire la valeur 
de type date dans l'indexe et supprimer les documents expirés de la collection.</p>
<a name="ttl"></a>

<div class="spacer"></div>

<p class="small-titre">a) Activer TTL pour une Collection</p>

<p>Pour activer le TTL pour une collection, utilisez la méthode ensureIndex() pour créer l'indexe TTL comme dans les exemples ci-dessous. MongoDB
commence a supprimer les documents expirés aussitôt que l'indexe a terminé de se construire.</p>

<div class="alert alert-info">
	<u>Note</u> : Quand la thread TTL est active, vous allez remarquer des opérations de suppression dans la sortie de db.currentOp()
	ou dans les données collectées par le profiler de la base de données.
</div>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Lorsque vous activez le TTL sur des Replica Sets, la thread de fond du TTL s'exécute uniquement sur les membres primaires. Les seconds
	membres vont uniquement répliquer les opérations de suppression du primaire.
</div>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : L'indexe TTL ne garantit pas le fait que les données expirées vont être supprimées immédiatement. Il pourrait y avoir un court
	laps de temps ou le document va expirer et le moment ou MongoDB va supprimer ce même document dans la base de données. La tâche de fond qui supprime
	les documents expirés s'exécute toutes les 60 secondes. Donc, les documents peuvent rester dans la collection après avoir expiré mais avant que la tâche de fond
	s'exécute ou se termine. La durée de l'opération de suppression dépend de la charge de travail de votre instance mongod. Pour conclure, les données expirées
	peuvent exister pour un temps inférieur à 60 secondes entre les exécution de la tâche de fond.
</div>

<p>A l'exception de la thread de fond, un indexe TTL supporte les requêtes que les indexes normaux font. Vous pouvez utiliser les indexes TTL pour gérer l'expiration
des documents grâce à l'une des deux façons suivantes :

- supprimer les documents après quelques secondes après la création. L'indexe va supporter les requêtes pour le temps de création des documents.
- spécifier un temps d'expiration. L'indexe va supporter les requêtes pour le temps d'expiration du document.

Expiration après quelques secondes : En créant un indexe TTL et en spécifiant le paramètre expireAfterSeconds ayant la valeur 3600. Cela va spécifier un temps
d'expiration 1h après le temps spécifié par la valeur du champ indexé. L'exemple suivant créé un indexe sur le champ statut de la collection log.events :</p>

<pre>db.log.events.ensureIndex( { "status": 1 }, { expireAfterSeconds: 3600 } )</pre>

<p>Pour expirer les documents après un certains nombre de secondes après leur création, attribuez une valeur au champ de type date qui va correspondre au temps d'insertion
des documents. Par exemple, prenons l'indexe de la collection log.events avec la valeur 0 pour expireAfterSeconds, et la date courrante , le 22 Juillet 2013 : 13:00:00 :</p>

<pre>
db.log.events.insert(
	{
		"status": new Date('July 22, 2013: 13:00:00'),
		"logEvent": 2,
		"logMessage": "Success!",
	} 
)
</pre>

<div class="spacer"></div>

<p>Le champs status doit impérativement contenir des valeurs du type date de BSON ou un tableau d'objets BSON de types date. MongoDB va automatiquement
supprimer les documents de la collection log.events lorsqu'au moins une des valeurs du champ 'status' d'un document est un peu plus ancien que le nombre de secondes
spécifiées avec expireAfterSeconds.

Expiration à une certaine heure : Créez un indexe TTL et spécifiez expireAfterSeconds avec la valeur 0. L'exemple suivant créé un indexe sur le champ status
de la collection log.events :</p>

<pre>db.log.events.ensureIndex( { "status": 1 }, { expireAfterSeconds: 0 } )</pre>

<p>Pour que les documents expirent à une certaine heure, attribuez au champ de type date la valeur correspondante au temps ou un document devrait expirer. Par exemple,
en prenant l'indexe sur la collection log.events avec la valeur 0 pour expireAfterSeconds et une date actuelle du 22 juillet 2013 : 13:00:00 :</p>

<pre>
db.log.events.insert(
	{
		"status": new Date('July 22, 2013: 14:00:00'),
		"logEvent": 2,
		"logMessage": "Success!",
	}
)
</pre> 

<p>Le champs status doit impérativement contenir des valeurs du type date de BSON ou un tableau d'objets BSON de types date. MongoDB va automatiquement
supprimer les documents de la collection log.events lorsqu'au moins une des valeurs du champ 'status' d'un document est un peu plus ancien que le nombre de secondes
spécifiées avec expireAfterSeconds.</p>
<a name="cont"></a>

<div class="spacer"></div>

<p class="small-titre">b) Contraintes</p>

<p>- Le champ _id ne supporte pas les indexes TTL.
- Vous ne pouvez pas créer d'indexe TTL sur un champ qui a déjà un indexe.
- Un document n'arrivera pas a expiration si le champ indexé n'existe pas.
- Un document n'arrivera pas a expiration si le champ indexé n'est pas de type date de BSON ou un tableau date de BSON.
- L'indexe TTL ne doit pas être un indexe composé (avoir plusieurs champs).
- Si le champ TTL est un tableau, et qu'il y a plusieurs dates dans l'indexe, le document va expirer quand la date la plus basse (au plus tôt) arrivera à son seuil
d'expiration.
- Vous ne pouvez pas créer d'indexe sur une capped collection car MongoDB ne peut pas supprimer les documents d'une capped collection.
- Vous ne pouvez pas utiliser ensureIndex() pour changer la valeur de expireAfterSeconds. Utilisez plutôt la commande collMod en conjonction avec
le flag 'index' de la collection.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="optimisation.php">"Stratégies d'Optimisation pour MongoDB" >></a>.</p>

<?php

	include("footer.php");

?>