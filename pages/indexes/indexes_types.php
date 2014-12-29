<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../indexes.php">Indexes</a></li>
	<li class="active">Types d'Indexes</li>
</ul>
<p class="titre">[ Bienvenue sur la page d'indexes ]</p>

<p>Vous voici sur la page des différents types d'Indexes que MongoDB implémente !</p>

<div class="spacer"></div>

<p class="titre">[ Indexe sur simple champ ]</p>

[schéma mongodb]

<div class="spacer"></div>

<p class="trick">Astuce : Rappelez vous qu'un Indexe est créé automatique pour chaque champ "_id" de chaque Collection.
Celui-ci est obligatoire, par défaut et ne peut être supprimé ! Celui-ci est automatiquement généré sous la forme d'un ObjectId() (un identifiant de 12 bytes)
. Il est unique et peut-être considéré comme la clé primaire de la Collection.</p>

<div class="spacer"></div>

<p class="small-titre">[ Indexe sur champ ]</p>

<p>Prenons ici un exemple sur une Collection "Friends" :</p>

<pre>
{
	"_id" : ObjectID(...),
	"name" : "Alice"
	"age" : 27
}
</pre>

<div class="spacer"></div>

<p>Imaginons que nous voulons créer un Indexe sur le champ "name" :</p>

<pre>db.friends.ensureIndex( { "name" : 1 } )</pre>

<div class="spacer"></div>

<p class="small-titre">[ Indexe sur champ dans Sous-Document ]</p>

<p>Ici, si vous souhaitez cibler un champ dans un Sous-Document, vous allez devoir procéder comme ceci :</p>

<pre>
{
	"_id": ObjectId(...)
	"name": "John Doe"
	"address": {
		"street": "Main"
		"zipcode": 53511
		"state": "WI"
	}
}
</pre>

<div class="spacer"></div>

<p>Nous pouvons créer l'Indexe en effectuant la commande :</p>

<pre>db.people.ensureIndex( { "address.zipcode": 1 } )</pre>

<div class="spacer"></div>

<p class="small-titre">[ Indexe sur champ Sous-Document ]</p>

<p class="attention">Attention : La création d'Indexe sur un champ d'un Sous-Document est différente de celle de la création d'un Indexe pour
un champ qui est un Sous-Document.</p>

<div class="spacer"></div>

<p>Procédons à un exemple tout simple :</p>

<pre>
{
	_id: ObjectId("523cba3c73a8049bcdbf6007"),
	metro: {
		city: "New York",
		state: "NY"	
	},
	name: "Giant Factory"
}
</pre>

<div class="spacer"></div>

<p>Ici, pour la Collection Factory, nous créeons bien un Indexe sur le champ metro :</p>

<pre>db.factories.ensureIndex( { metro: 1 } )</pre>

<div class="spacer"></div>

<p>Bien sur, si l'on veut effectuer une requête sur le champ metro :</p>

<pre>db.factories.find( { metro: { city: "New York", state: "NY" } } )</pre>

<div class="spacer"></div>

<p>En revanche, cette requête ne correspond pas à </p>

<pre>db.factories.find( { metro: { state: "NY", city: "New York" } } )</pre>

<div class="spacer"></div>

<p>car l'ordre des champs est important, le champ doit correspondre exactement !</p>

<div class="spacer"></div>

<p class="titre">[ Indexe sur plusieurs champs combinés ]</p>

<p>Deuxième type, les Indexes combinés. Un Indexe combinés va regroupr la structure de plusieurs champs.</p> 

[schéma mongodb]

<div class="spacer"></div>

<p>Considérons la Collection produits :</p>

<pre>
{
	"_id": ObjectId(...)
	"item": "Banana"
	"category": ["food", "produce", "grocery"]
	"location": "4th Street Store"
	"stock": 4
	"type": cases
	"arrival": Date(...)
}
</pre>

<div class="spacer"></div>

<p>Si vous avez des requêtes ne rechechant pas uniquement le champ item mais item et stock à la fois, vous pouvez utiliser l'indexe combiné suivant :</p>

<pre>db.products.ensureIndex( { "item": 1, "stock": 1 } )</pre>

<div class="spacer"></div>

<p class="attention">Attention : MongoDB autorise jusqu'à 31 champs différents pour la création d'un Indexe combiné. De plus, l'ordre des champs
est important, il doit être respecté.</p>

<div class="spacer"></div>

<p class="small-titre">[ Ordre de tri ]</p>

<p>Dans le cadre des indexes composés, l'ordre de tri doit être pris en compte. Contrairement aux indexes sur un seul champ, MongoDB ne peut pas traverser
facilement un Indexe dans n'importe-quel sens facilement. Rappelez-vous que l'ordre peut être 1 croissant ou -1 décroissant.
Voyons un exemple :</p>

<pre>
db.events.find().sort( { username: 1, date: -1 } )
db.events.find().sort( { username: -1, date: 1 } )
</pre>

<div class="spacer"></div>

<p>Ces deux opérations peuvent être supportées par l'indexe suivant :</p>

<pre>db.events.ensureIndex( { "username" : 1, "date" : -1 } )</pre>

<div class="spacer"></div>

<p>Mais cette fonction ne peut supporter l'opération suivante ou les deux champs ont le même ordre de tri :</p>

<pre>db.events.find().sort( { username: 1, date: 1 } )</pre>

<div class="spacer"></div>

<p class="small-titre">[ Préfixes ]</p>

<p>Les préfixes sur les indexes composés permettent de ne spécifier qu'un sous ensemble de l'indexe pour y faire référence.
Prenons un exemple :</p>

<pre>{ a: 1, b: 1, c: 1 }</pre>

<div class="spacer"></div>

<p>Les deux sous-ensembles suivants sont des préfixes de cet indexe :</p>

<pre>
{ a: 1 }
et
{ a: 1, b: 1 }
</pre>

<div class="spacer"></div>

<p>En bref, si vous aviez eu deux indexes { a: 1 } et { a: 1, b: 1 }, vous auriez pu supprimer le { a : 1 } car de toute façon l'indexe composé réalise
la même chose. L'exemple suivant démontre :</p>

<pre>{ "item": 1, "location": 1, "stock": 1 }</pre>

<div class="spacer"></div>

<p>Cette indexe supporte les requêtes incluant :</p>

<p>le champ item,</p>
<p>item et location,</p>
<p>item et location et stock,</p>
<p>item et stock seulement (par contre, moins efficace qu'un indexe qui aurait été définit uniquement sur item et stock)</p>

<div class="spacer"></div>

<p>En revanche, cet indexe ne supporte pas les requêtes incluant seulement :</p>
<p>location,</p>
<p>stock,</p>
<p>location et stock</p>

<div class="spacer"></div>

<p class="titre">[ Indexe Multi-Clés ]</p>

<p>Comme brièvement décrit dans la page d'introduction, un Indexe multi-clés va créer un unique indexe pour chaque élément contenu dans un tableau de valeurs.
Celui-ci est automatiquement déterminé par MongoDB, aucune commande spéciale à réaliser ou option a définir. Ce genre d'indexe peut s'effectuer
sur des tableaux de valeurs comportant des chaînes de caractères, des entiers et même des Sous-Documents.</p>

[schéma mongo]

<p class="small-titre">[ Limitations ]</p>

<p>Interraction entre Indexes composés et Indexes Multi-clés : Vous pouvez avoir un Indexe ciblant des champs contenant un tableau de valeur par exemple :</p>

<pre>

{a: [1, 2], b: 1}

ou

{a: 1, b: [1, 2]}
</pre>

<p>Un indexe multi-clé peut-être créé au sain d'une indexe composé seulement s'il n'y a qu'un seul tableau de valeurs. En conséquences, cet indexe suivant
est impossible à réaliser.</p>

<pre>{a: [1, 2], b: [1, 2]}</pre>

<p>Essayez de créer cet indexe et vous verrez MongoDB vous refuser l'insertion et vous dire que "cannot index parallel arrays.</p>

<p>L'indexe d'une clé shard ne peut être un indexe multi-clés.</p>

<p>Les indexes hashés ne sont pas supportés par les indexes multi-clés.</p>

<p class="small-titre">[ Exemples ]</p>

<p>Prenons un exemple sur un Indexe pour un tableau basique :</p>

<pre>
{
	"_id" : ObjectId("..."),
	"name" : "Warm Weather",
	"author" : "Steve",
	"tags" : [ "weather", "hot", "record", "april" ]
}
</pre>

<p>Créer l'indexe { tags : 1 } permettrait d'avoir 4 différents enregistrements pour weather, hot, record et april.</p>


<p>Pour un autre exemple avec un tableau de Sous-Documents :</p>

<pre>
{
	"_id": ObjectId(...)
	"title": "Grocery Quality"
	"comments": [
		{
			author_id: ObjectId(...)
			date: Date(...)
			text: "Please expand the cheddar selection."
		},
		{
			author_id: ObjectId(...)
			date: Date(...)
			text: "Please expand the mustard selection."
		},
		{
			author_id: ObjectId(...)
			date: Date(...)
			text: "Please expand the olive selection."
		}
	]
}
</pre>

<p>Créer l'indexe comments.text serait un indexe multi-clés et créérait une entrée pour chaque sous-document dans l'indexe.
Avec l'indexe { comments.text : 1} nous aurions la requête suivante :</p>

<pre>db.feedback.find( { "comments.text": "Please expand the olive selection." } )</pre>

<p>Cela sélectionnerait le Documents qui contient le Document suivante dans le tableau comments :</p>

<pre>
{
	author_id: ObjectId(...)
	date: Date(...)
	text: "Please expand the olive selection."
}
</pre>

<p class="titre">[ Indexe GéoSpatial ]</p>

<p>Pour controller les informations géospatiales, MongoDB offre différents types d'indexes pour manipuler ces données.
On va parler plus courament de Surface afin de choisir comment vous aller stocker vos données, quel type d'indexe choisir et comment
construire vos requêtes. Voici les deux types concernés :</p>

<p>Surface sphérique : Pour calculer des données géométriques sur une sphère type globe terrestre, utiliser la surface sphérique avec l'indexe
2dsphere. Stockez vos données en tant qu'object GeojSON (longitutde, latitude). Le système de coordonnées par référence est le WGS84 datum.</p>

<p>Surface plane : Si vous souhaitez calculer sur une surface plane (Euclidiène), stockez vos données sous formes de paires coordonnées avec l'indexe 2d.

<p>Données de location : Si vous choisissez de stocker des données sphériques, vous devez utiliser soit :</p>

<p>Les objects GeoJSON supportant les objets Point, LineString et Polygon. Ou alors des paires de coordonnées que MongoDB va convertir en object Point de la
classe GeoJSON. Pour les surfaces planes, les paires de coordonnées uniquement sont accessibles.</p>

<p class="small-titre">[ Les requêtes sur les données géospatiales ]</p>

<p>Les opérateurs suivants nous permettent de réaliser des requêtes sur des indexes géospatiaux :</p>
<p>L'inclusion : L'opérateur $geoWithin va retourner les locations inclues dans un polygone spécifié.</p>
<p>L'intersection : MongoDB peut effectuer des requêtes pour connaître les locations qui se croisent. L'opérateur utilisé est l'opérateur $geoIntersects, applicable
sur les données de surfaces sphériques uniquement.</p>
<p>La proximité : Ici, MongoDB va effectuer des requêtes sur les points les plus proches d'un certain point (tiens tiens, cela rapelle le fonctionnement d'un GPS).
Ce type de requête s'effectue avec l'opérateur $near, qui nécessite un indexe 2d ou 2dsphere.</p>

<p class="small-titre">[ Les indexes geospatiaux ]</p>

<p>Les deux indexes principaux sont les indexes 2dsphere et 2d.</p>

<p>Les indexes de surface sphérique "2dsphere" permettent les calcules sur une sphere, permet l'utilisation des objects GeoJSON et paires de coordonnées.
(Ceux-ci ne sont pas disponibles avant la version 2.4).</p>

<p>Les indexes pour surface plane "2d" permettent les calculs sur de la géométrie plane, utilise des paires de coordonnées.</p>

<p>Les indexes geospatiaux ne peuvent pas être utilisés en tant que shard key dans des situations de sharding. De plus, les requêtes utilisant l'opérateur
$near ne fonctionnent pas avec des collections shardées, il faut utiliser les opérateurs $geoNear ou $geoWithin à la place.</p>

<p>Les Indexes Géospatiaux seront disponibles très bientôt sur MongoTuto.Com ! Ceux-ci sont en cours de construction. Je vais tenter
d'apporter des exemples précis et détaillés afin de faciliter votre compréhension. Encore un peu de patience :)</p>

<p class="titre">[ Indexe de Texte ]</p>

<p>Nouveaux dans la version 2.4 de MongoDB, les Indexes de texte permettent la recherche de chaîne de caractères dans un texte ou dans un tableau de textes
. Celui-ci est insensible à la casse et on peut y accéder uniquement avec la commande "text".</p>

<p class="trick">Astuce : Une collection peut avoir un et un seul indexe de texte à la fois, puis, avant de créer un indexe ou même d'exécuter la commande
text, vous devrez manuellement activer la recherche de texte (ce que nous allons avoir un peu plus bas).</p>

<p class="small-titre">[ Créer un Indexe de text ]</p>

<p>Pour créer un indexe de texte sur un champ contenant une chaîne de caratctères ou un tableau de chaînes de caractère, il vous faudra utiliser cette commande
suivante, en spécifiant le paramètre "text" :</p>

<pre>db.reviews.ensureIndex( { comments: "text" } )</pre>

<p>Cette indexe va permettre de renvoyer les mots principaux du contenu indexés en ne tenant pas compte des mots de liaison usuels tels que 
"the", "an", "a", "and" etc ... Pour spécifir un language différent, on en parlera un peu plus loin ! En revanche, ceux-ci contiennent une entrée
pour chaque mot dans l'indexe, comme les indexes multi-clés.
La recherche de texte dans une Collection va assigner un score pour chaque Document qui contient le mot que l'on recherche. C'est ce score qui va
retourner les résultats par meilleure pertinence. Par défaut, les 100 documents les plus pertinents sont retournés.
Un exemple, une recherche sur le mot blueberry, si l'on cherche blue, cela ne fonctionnera pas, mais si l'on recherche blueberry ou blueberries,
on aura des résultats.</p>


<p class="titre">[ Indexe Hashé ]</p>

<p>Nouveaux dans la version 2.4, les valeurs dans l'indexe hashé seront hashées. Les indexes multi-clés ne sont pas supportés.
En situation de sharding, on peut supporter les indexes hashés en utilisant une clé de shard hashée que nous verrons dans le chapitre de Sharding.
L'indexe hashé ne permet que les requêtes d'égalité et non les requêtes du style plus grand que, plus petit que, entre etc ...
Par contre, vous pouvez, par exemple créer un indexe sur un même champ utilisant l'ordre croissant/décroissant, et avoir aussi un indexe hashés sur le même champ.
MongoDB utilisera l'indexe non-hashé pour effectuer ce genre de requêtes.</p>

<p class="attention">Attention : MongoDB tronque les nombres flotants. N'utilisez donc pas un indexe hashé sur des flotants ne pouvant être convertis en
entiers de 64bits (supérieurs à 2<sup>53</sup>).</p>

<p>Pour créer un indexe hashé, utilisez l'exemple suivant :</p>

<pre>db.active.ensureIndex( { a: "hashed" } )</pre>

<p>Ceci va créer un indexe hashé sur le champ "a" pour la Collection "active".</p>

<p class="titre">[ Anciens Indexes ]</p>

<p class="attention">Attention : utilisez ce type d'indexe uniquement si vous avez des indexes compatibles avec une version de MongoDB inférieure à la 2.0.</p>

<p>MongoDB 2.0 a introduit les indexes de type { v : 1 } et supportait déjà les indexes de type { v : 0 }. Avant la version 2.0, uniquement les indexes
{ v : 0 } étaient supportés. Si vous devez downgrader MongoDB à une version inférieure à la 2.0, vous allez devoir supprimer et re-créer vos indexes.</p>

<p>Pour réaliser cela, veuillez utiliser ls fonctions dropIndexes() puis ensureIndex() comme l'on connait déjà. On ne peut pas simplement re-créer les indexes.
Si vous re-créez l'indexe à une version antérieure à la 2.0, les indexes ayant la valeur 1 resterons à 1, ce qui n'est pas supporté.</p>

<p>Par exemple, si vous voulez downgrader de la version 2.0 à la 1.8 et que vous avez l'indexe suivant :</p>

<pre>{ "v" : 1, "key" : { "name" : 1 }, "ns" : "mydb.items", "name" : "name_1" }</pre>

<p>Le champ v va vous dire que l'indexe est un indexe de type { v : 1 }, ce qui est incompatible avec la 1.8.</p>

<p>Pour supprimer l'indexe :</p>

<pre>db.items.dropIndex( { name : 1 } )</pre>

<p>Puis, pour le re-créer en tant que { v : 0 } :</p>

<pre>db.foo.ensureIndex( { name : 1 } , { v : 0 } )</pre>

<div class="spacer"></div>

<?php

	include("footer.php");

?>