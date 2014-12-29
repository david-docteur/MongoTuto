<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../indexes.php">Indexes</a></li>
	<li class="active">Gestion d'indexes et Options</li>
</ul>
<p class="titre">[ Gestion d'indexes et Options ]</p>

<p class="titre">[ Propriétés d'Indexe ]</p>

<p>En plus des différents types d'indexes que MongoDB fournit, ceux-ci peuvent avoir des propriétés configurables que nous allons détailler dans les sections suivantes.
Ces propriétés concernent :</p>

<p>le TTL (Time To Live) : utilisé pour les Collections TTL afin que les données expirent après un certains temps.</p>
<p>l'unicité : MongoDB rejette automatiquement les Documents contenant une valeur identitique pour le champ indexé.</p>
<p>le sparse : N'indexe pas les Documents qui ne comportent pas le champ indexé.</p>

<div class="spacer"></div>

<p class="small-titre">[ Indexes TTL ]</p>

<p>Les indexes TTL utilisés dans les Collections TTL permettent à MongoDB de les supprimer automatiquement après une période de temps
que vous allez pouvoir configurer. Ceci peut etre pratique pour les données de types évênements, logs etc ...
Par contre, les indexes composés ne sont pas supportés, les champs indexés DOIVENT être du type Date.
Si le champ indexés comporte un tableau de données de type Date, celui-ci va expirer lorsque le plus petit (plus tôt) de la liste est atteint.</p>

<p class="attention">Attention : Il y a une différence entre le moment ou la date d'expiration est atteinte et au moment ou l'instance de
MongoDB aura physiquement terminé de supprimer les données ! La tâche de fond qui va vérifier les TTL et exécuter la suppression des Documents concernés
s'exécute toutes les 60 secondes, donc il y aura un laps de temps : les Documents concernés resteront dans la Collection après la date d'expiration
atteinte mais avant que MongoDB ai terminé son job ! Après cela dépend de l'état d'occupation de votre instance mongod !</p>

<div class="spacer"></div>

<p class="small-titre">[ Indexes Uniques ]</p>

<p>Les indexes uniques vont faire en sorte que MongoDB interdise l'insertion de Document ayant un champ indexé ayant la même valeur qu'un Document
qui existe déjà. Par défaut, l'unicité n'est pas activée, mais pour le faire :</p>

<pre>db.addresses.ensureIndex( { "user_id": 1 }, { unique: true } )</pre>

<p>Si vous utilisez l'unicité sur un indexe composé, alors l'unicité se fera sur l'ensemble des valeurs des champs concernés, et non sur la valeur de
chaque champ.</p>

<pre>db.collection.ensureIndex( { a: 1, b: 1 }, { unique: true } )</pre>

<p>Aussi ce que vous devez savoir, si l'indexe sur un champ rencontre un Document n'ayant pas ce champ en question, la valeur nulle lui sera attribuée dans l'indexe.
Dans ce cas, si un autre Document n'a toujours pas de valeur pour ce champ non plus, alors l'indexe sera rejetté et affichera une erreur de clé dupliquée.</p>

<p>Vous pouvez eviter ce genre d'erreur en utilisant la propriété sparse avec la propriété d'unicité afin de filtrer ces valeurs nulles car la propriété sparse
n'indexe que les champ ayant une valeur dans les Documents qui les comportent.</p>

<p>Il n'est pas possible d'utiliser la contrainte d'unicité sur des indexes hashés.</p>

<div class="spacer"></div>

<p class="small-titre">[ Indexes Sparses ]</p>

<p>La propriété sparse indique à l'indexe de ne créer des entrées uniquement que pour les Documents qui contiennent le champ que l'on veut indexer.
Même pour les champs ayant la valeur null. A l'inverse, tous les autres types d'indexes contiennent une entrée pour chaque Document, avec la valeur null si le champ
n'existe pas dans le Document. Cette propriété est false par défaut. Voici comment on indique la propriété sparse :</p>

<pre>db.addresses.ensureIndex( { "xmpp_id": 1 }, { sparse: true } )</pre>

<div class="spacer"></div>

<p class="attention">Attention : cette propriété va parfois retourner des résultats incomplets vu qu'il ne contient pas une entrée pour chaque Document
existant dans la Collection. Effectuer des requêtes de tri, de filtre ou même de moyenne par exemple, ne retournerait pas toujours le bon résultat.
Si vous souhaitez effectuer une moyenne en vous servant de la somme des scores divisée par la somme totale des Documents, vous n'aurez
pas le bon chiffre en sortie.</p>

<div class="spacer"></div>

<p class="trick">Astuce : Vous pouvez coupler les propriétés sparse et d'unicité pour rejetter les Documents qui ont une valeur du champ à indexer
qui existe déjà mais qui autorise les Documents qui n'ont pas cette clé, car rappelez-vous, l'indexe d'unicité ajoute la valeur null pour le champ
qui n'est pas comporté dans un Document en question.</p>

<div class="spacer"></div>

<p class="attention">Attention : Ne pas confondre les indexes sparse de MongoDB et les indexes sparse d'autre SGBD, les notions sont complètement différentes
et n'ont rien à voir !</p>

<div class="spacer"></div>

<p class="small-titre">[ Exemples ]</p>

<p>Voyons un exemple de résultat incomplet :</p>

<pre>
{ "_id" : ObjectId("523b6e32fb408eea0eec2647"), "userid" : "newbie" }
{ "_id" : ObjectId("523b6e61fb408eea0eec2648"), "userid" : "abby", "score" : 82 }
{ "_id" : ObjectId("523b6e6ffb408eea0eec2649"), "userid" : "nina", "score" : 90 }
</pre>

<div class="spacer"></div>

<p>Ajoutons un indexe sparse sur le champs score :</p>

<pre>db.scores.ensureIndex( { score: 1 } , { sparse: true } )</pre>

<div class="spacer"></div>

<p>nous voullons sélectionner tous ls scores des joueurs par ordre décroissant :</p>

<pre>db.scores.find().sort( { score: -1 } )</pre>

<div class="spacer"></div>

<p>Les résultat est incomplet car le champ Document newbie ne comporte pas le champ score :</p>

<pre>
{ "_id" : ObjectId("523b6e6ffb408eea0eec2649"), "userid" : "nina", "score" : 90 }
{ "_id" : ObjectId("523b6e61fb408eea0eec2648"), "userid" : "abby", "score" : 82 }
</pre>

<div class="spacer"></div>

<p>Maintenant voyons un exemple avec les contraintes sparse + unique en même temps, reprenons la même Collection scores :</p>

<pre>
{ "_id" : ObjectId("523b6e32fb408eea0eec2647"), "userid" : "newbie" }
{ "_id" : ObjectId("523b6e61fb408eea0eec2648"), "userid" : "abby", "score" : 82 }
{ "_id" : ObjectId("523b6e6ffb408eea0eec2649"), "userid" : "nina", "score" : 90 }
</pre>

<div class="spacer"></div>

<p>Créeons l'indexe comme ceci :</p>

<pre>db.scores.ensureIndex( { score: 1 } , { sparse: true, unique: true } )</pre>

<div class="spacer"></div>

<p>Cet indexe va autoriser l'insertion de Documents ayant un champ avec une valeur unique, puis, autoriserait l'insertion de Documents n'ayant pas le champ
score :</p>

<pre>
db.scores.insert( { "userid": "PWWfO8lFs1", "score": 43 } )
db.scores.insert( { "userid": "XlSOX66gEy", "score": 34 } )
db.scores.insert( { "userid": "nuZHu2tcRm" } )
db.scores.insert( { "userid": "HIGvEZfdc5" } )
</pre>

<div class="spacer"></div>

<p>En revanche, insérer les Documents suivants ne serait pas permit :</p>

<pre>
db.scores.insert( { "userid": "PWWfO8lFs1", "score": 82 } )
db.scores.insert( { "userid": "XlSOX66gEy", "score": 90 } )
</pre>

<div class="spacer"></div>

<p>En effet, les scores 82 et 90 existent déjà ! Bon, en réalité on peut accepter des joueurs/équipes ayant des scores identiques, ce qui renverrait à une égalité
mais comme exemple, on ne l'autorise pas.</p>

<div class="spacer"></div>

<p>MongoDB fournit des options qui s'appliquent uniquement lors de la création de l'indexe, mais qui ne s'appliquent pas après que l'indexe soit créé
comme les options ttl, unique ou sparse. Ces options se passe en second paramètre de la fonction ensureIndex() qui permet de créer un indexe.</p>

<div class="spacer"></div>

<p class="titre">[ Création en arrière-plan ]</p>

<p class="small-titre">[ Construction en arrière-plan ]</p>

<p>Lorsqu'un indexe est en cours de création, celui-ci bloque touts les opérations de lectures et écritures sur la Collection concernée par l'indexe.
Avec l'option "background", vous pouvez éviter cela et choisir d'exécuter la création d'un indexe qui serait plutôt long à gérer :</p>

<pre>db.people.ensureIndex( { zipcode: 1}, {background: true} )</pre>

<div class="spacer"></div>

<p>Dans cet exemple, la création de l'indexe sur le champ zipcode de la collection people s'exécutera en arrière-plan. Cette option est false par défaut.</p>

<p>Vous pouvez également combiner d'autres paramètres avec l'option background :</p>

<pre>db.people.ensureIndex( { zipcode: 1}, {background: true, sparse: true } )</pre>

<div class="spacer"></div>

<p class="small-titre">[ Comportement ]</p>

<p>Depuis la version 2.4, MongoDB ne pouvait créer plusieurs indexes en arrière-plan. En fait, les opérations de lecture et d'écriture continuerons en parallèle pendant
la création de l'indexe, mais votre shell ouvert va bloquer pendant la création. Si vous souhaitez continuer d'autres opérations,il vous suffit tout simplement d'ouvrir un autre
shell.Bien sur, les requêtes que vous effectuerez sur cet indexe pendant qu'il est en création ne seront pas partielles, MongoDB va attendre que l'indexe soit terminé
avant d'utiliser l'indexe sur le champ correspondant.</p>

<div class="spacer"></div>

<p class="attention">Attention : Si un indexe est en cours de  création, vous ne pourrez pas effectuer des tâches administratives telles que réparer la base de données,
supprimer la collection en question et exécuter compact. Essayez et vous vous verrez retourner une erreur.</p>

<div class="spacer"></div>

<p class="small-titre">[ Performance ]</p>

<p>La création d'indexe en arrière-plan est plus longue que la création d'indexe basique, surtout si l'indexe est plus lourd que la mémoire RAM disponible.</p>
<p>Pour éviter des problèmes de performances, vérfiez que votre application verifie les indexes disponibles avec getIndexes().</p>

<div class="spacer"></div>

<p class="small-titre">[ Création d'indexes sur seconds membres ]</p>

<p>Lors d'une configuration ayant un ensemble de répliques (ou replica set), la création d'indexe sur le premier membre en arrière-plan devient une création 
au premier plan pour les seconds membres de l'ensemble. La réplication est donc bloquée sur les membres secondaires. Pour exécuter une création d'indexe longue sur
un second membre, le mieux est de le redémarrer en mode standalone et de construire l'indexe. Une fois créé, redémarrer le membre et synchronisez-le avec les autres membres,
et faites-de même pour les autres secondaires. Une fois tout cela effectué, arrêter le membre primaire, redémarrez le aussi en mode standalone puis construisez l'indexe.</p>

<div class="spacer"></div>

<p>Rappelez-vous que le temps requis pour construire un indexe sur un second membre doit être dans la fenêtre de l'oplog, pour que le secondaire puisse se rattraper avec
le membre primaire.</p>

<p>Les indexes sur les seconds membres en mode "recovery" sont toujours exécutés en premier plan pour leur permettre de se rattraper aussi vite que possible.</p>

<p>Voici l'étape à suivre :</p>

<p>Attention, d'abord commencer par le premier membre. Les secondaires commencerons la création de l'indexe une fois que le premier aura terminé
en arrière plan.</p>

<p>Céer l'indexe en arrière plan puis arrêter le premier avec l'option rs.stepDown(), afin de faire passer le primaire en cours en tant que secondaire
puis laisser l'ensemble voter pour un nouveau primaire.</p>

<p>Ensuite, pour chaque autre membre secondaire :</p>

<p>Pour construire les indexes sur les membres secondaires de l'ensemble de répliques :
pour chaque membres faire :
1) stop membre
2)construire l'indexe
3) redémarrer mongod
</p>

<p>1 : stoppez et redémarrer le membre secondaire en mode standalone sans l'argument --replSet puis sur un port différent. Normalement mongod est sur le port 27017 :</p>

<pre>mongod --port 47017</pre>

<p>En choisissant un port différent, vous êtes sûr que les autres membres de ne pourront absolument pas contacter ce membre.</p>

<p>2 : créez l'indexe</p>

<p>3 : redémarrez le membre normalement avec l'option --replSet puis sur le port habituel avec son nom (exemple rs0) :</p>

<pre>mongod --port 27017 --replSet rs0</pre>

<div class="spacer"></div>

<p class="titre">[ Supprimer les dupliqués ]</p>

<p>MongoDB ne peut pas créer un indexe unique sur un champ qui a déjà la valeur spécifiée comme vous le savez déjà. Pour forcer la création d'un indexe unique,
vous pouvez spécifier l'option dropDups qui va seulement indexer la première occurence d'une valeur d'une clef, et supprimer les autres valeurs.</p>

<p>Comme tous les indexes uniques, si un Document ne contient pas le champ à indexer, celui-ci sera ajouté à la structure de l'indexe avec la valeur null.
Si d'autres Documents n'ont pas le champ à indexer non plus, et que vous avez l'option { dropDups : true }, MongoDB va supprimer tous ces Documents de la Collection
lors de la création l'indexe. Si vous combinez sparse à dropDups, cela va inclure uniquement les Documents dans l'indexe qui ont la valeur, tout en laissant les Documents,
qui ne contiennent pas la valeur, dans la base de données. L'option dropDups est false par défaut.</p>

<div class="spacer"></div>

<p>Pour supprimer les valeurs dupliquées sur le nom d'utilisateur d'une Collection accounts :</p>

<pre>db.accounts.ensureIndex( { username: 1 }, { unique: true, dropDups: true } )</pre>

<div class="spacer"></div>

<p class="attention">Attention : Activer dropDrups va supprimer les données dupliquées de votre collection, soyez bien sur de ce que vous faites !</p>

<div class="spacer"></div>

<p class="titre">[ Noms d'Indexes ]</p>

<p>Le résultat du nom d'un indexe est la concaténation du nom de la clé et de sa direction (1 ou -1).</p>

<p>Si l'on créé un indexe sur le champ item et quantity :</p>

<pre>db.products.ensureIndex( { item: 1, quantity: -1 } )</pre>

<div class="spacer"></div>

<p>Le nom en sortie de l'indexe sera item_1_quantity_-1.</p>

<p>Vous pouvez choisir de spécifier un nom différent, comme dans l'exemple suivant ou l'on créé un indexe avec le nom inventory :</p>

<pre>db.products.ensureIndex( { item: 1, quantity: -1 } , { name: "inventory" } )</pre>

<div class="spacer"></div>

<p>Dernière chose, si vous souhaitez obtenir le nom d'un indexe, utilisez la méthode getIndexes().</p>

<div class="spacer"></div>

<p class="titre">[ Suppression d'Indexe ]</p>

<p>Vous pouvez supprimer un indexe d'une Collection, appelez s'implemen la méthode dropIndex() sur une Collection. Par exemple : </p>

<pre>db.accounts.dropIndex( { "tax-id": 1 } )</pre>

<p>Va supprimer l'indexe tax-id pour la Collection accounts. Une fois terminée et validée, la suppression renvoie :</p>

<pre>{ "nIndexesWas" : 3, "ok" : 1 }</pre>

<p>Le champ nIndexesWas représente le nombre d'Indexes existants avant la suppression de celui-ci. Si vous auriez voulu supprimer tous les indexes,
l'appel de la fonction dropIndexes() aurait suffit :</p>

<pre>db.accounts.dropIndexes()</pre>

<p>Rappelez-vous que tous les indexes peuvent être supprimés, sauf ceux sur le champ _id qui est automatiquement construit par défaut.</pre>

<div class="spacer"></div>

<p class="titre">[ Reconstruction d'Indexe ]</p>

<p>La méthode qui va re-créer tous les indexes d'une collection (supprimer puis reconstruire) sauf les indexes sur le champ _id, est la méthode suivante :</p>

<pre>db.collection.reIndex()</pre>

<p>Une fois terminée, la reconstruction, par exemple sur la collection accounts, va retourner ceci :</p>

<pre>
{
	"nIndexesWas" : 2,
	"msg" : "indexes dropped for collection",
	"nIndexes" : 2,
	"indexes" : [
		{
			"key" : {
			"_id" : 1,
			"tax-id" : 1
		},
			"ns" : "records.accounts",
			"name" : "_id_"
		}
	],
	"ok" : 1
}
</pre>

<div class="spacer"></div>

<p class="titre">[ Gérer la progression de la création d'Indexe ]</p>

<p>Afin de voir ou en est la création d'un Indexe, utilisez la fonction :</p>

<pre>db.currentOp()</pre>

<div class="spacer"></div>

<p>Un résultat sera retourné avec les clés "query"  et "msg" qui auront pour valeurs le type d'opération puis le pourcentage de progression.</p>

<p>Pour mettre fin à une création d'Indexe :</p>

<pre>db.killOp()</pre>

<div class="spacer"></div>

<p>Depuis la version 2.4, il est possible d'enrayer les indexes en premier plan et en arrière-plan. avant celle-ci, uniquement les arrière plan</p> 

<div class="spacer"></div>

<p class="titre">[ Retourner tous les Indexes ]</p>

<p>Dans MongoDB, vous allez probablement vouloir afficher les indexes existants pour des raisons de maintenance, voir de curiosité et voir la structure.</p>
<p>Pour stocker les Indexes, la Collection "systen.indexes" existe dans MongoDB. Celle-ci va recenser tous les Indexes de votre base de données.</p>

<div class="spacer"></div>

<p class="small-titre">[ Lister tous les Indexes d'une Collection ]</p>

<p>Afin de lister tous les indexes d'une Collection particulière, vous allez pouvoir utiliser cette fonction :</p>

<pre>db.collection.getIndexes()</pre>

<div class="spacer"></div>

<p>Par exemple, pour afficher tous les indexes de la collection personnes :</p>

<pre>db.personnes.getIndexes()</pre>

<div class="spacer"></div>

<p class="small-titre">[ Lister tous les Indexes de la Base de Données ]</p>

<p>Pour lister tous les indexes de la base de données, nous allons utiliser la table existante "system.indexes" :</p>

<pre>db.system.indexes.find()</pre>

<div class="spacer"></div>

<p>Une simple fonction find() que l'on connait déjà sur cette Collection sera suffisante pour retourner tous les indexes.</p>

<div class="spacer"></div>

<p class="titre">[ Mesurer l'utilisation d'un Indexe ]</p>

<p>Pour des raisons de performance et d'optimisation plus avancée, vous allez vouloir observer et calculer ds statistiques d'utilisation de vos indexes.
Pour cela, il existe plusieurs fonctions permettant de réaliser cela.</p>

<div class="spacer"></div>

<p class="small-titre">[ La méthode explain() ]</p>

<p>Ajoutez la méthode explain() à n'importe quel curseur afin de retourner un Document ayant des statistiques de votre requête, incluant l'indexe utilisé,
le nombre de Documents scannés et, information très utilie, le temps d'éxecution de la requête en question.</p>

<div class="spacer"></div>

<p class="small-titre">[ La méthode hint() ]</p>

<p>Utilisez la méthode hint() à la fin d'une requête, en spécifiant l'indexe, pour forcer MongoDB a effectuer cette requête avec l'indexe spécifié.
Voici un exemple :</p>

<pre>db.people.find( { name: "John Doe", zipcode: { $gt: 63000 } } } ).hint( { zipcode: 1 } )</pre>

<div class="spacer"></div>

<p>Vous pouvez utiliser explain() et hint() en même temps afin de comparer la performance d'une requête en fonction de l'indexe passé en paramètre.
Par contre, si vous souhaitez forcer MongoDB à ne pas utiliser d'indexe du tout, vous pouvez spécifier l'opérateur $natural avec la fonction hint() :</p>

<pre>db.people.find( { name: "John Doe", zipcode: { $gt: 63000 } } } ).hint( { $natural: 1 } )</pre>

<div class="spacer"></div>

<p class="small-titre">[ Reporting ]</p>

<p>MongoDB fournit un nombre de métriques sur l'utilisation d'un indexe ou des opérations que vous souhaiteriez analyser l'utilisation d'un indexe
pour votre base de données :</p>

<pre>
sortie de serverStatus:
– indexCounters
– scanned
– scanAndOrder
Sortie de collStats:
– totalIndexSize
– indexSizes
Sortie de dbStats:
– dbStats.indexes
– dbStats.indexSize
</pre>

<div class="spacer"></div>

<p class="titre">[ S'assurer que votre Indexe a assez de place en mémoire RAM ]</p>

<p>Pour s'assurer que votre indexe a assez de place en mémoire et que MongoDb n'ai pas a scanner depuis votre disque dur, ce qui ralentit considérablement
une requête, vous pouvez obtenir la taille totale des Indexes d'une Collection comme ceci :</p>

<pre>db.collection.totalIndexSize()</pre>

<p>Cela devrait ressembler à quelque chose comme :</p>

<pre>
> db.collection.totalIndexSize()
4294976499
</pre>

<p>L'exemple du dessus montre un Indexe pesant 4.3go. Pour s'assurer que cet indexe a assez de place en mémoire, vous devez avoir au moins son montant de mémoire
disponible, mais pas seulement, vous devez avoir en plus l'espace requis pour l'ensemble de travail.</p>

<p>Si vous utilisez plusieurs Collections, vous devez vérifier la taille de tous les Indexes de toutes les Collections.</p>
<p>Il y a des cas ou certains indexes n'ont pas besoin de rester uniquement en mémoire.</p>
<p>Voir aussi collStats and db.collection.stats().</p>

<p class="small-titre">[ Indexes gardant uniquement les données récentes en mémoire ]</p>

<p>Les Indexes n'ont pas toujours à prendre entièrement place dans la mémoire RAM. Si la valeur du champ indexé augmente avec chaque insert, et que la plupart
des requêtes sélectionne les Documents les plus récentes, alors MongoDB garde en mémoire uniquement la partie des Documents ls plus récents en mémoire.
Cela optimise les opérations de lecture/écriture et minimise l'espace RAM nécessaire à cet indexe.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>