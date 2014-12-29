<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../aggregations.php">Aggrégations</a></li>
	<li class="active">Le Pipeline d'Aggrégation</li>
</ul>

<p class="titre">[ Le Pipeline d'Aggrégation ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#exe">I) Exemples</a></p>
	<p class="right"><a href="#post">- a) Code Postal</a></p>
	<p class="right"><a href="#pref">- b) Préférences Utilisateurs</a></p>
</div>

<p>Introduit dans la version 2.2, la transformation se base sur une modèle pipeline pour la tranformation du/des Documents.
Ceux-ci passent par plusieurs étapes à travers le pipeline (un peu le principe du pipe "|" pour ceux qui sont habitués à UNIX, pour ensuite arriver à un résultat final.</p>
<p>Les étapes les plus basiques du pipeline consistent à des filtres qui vont effectuer la requête puis des tranformations de Documents qui vont
changer le rendu du résultat sur votre écran.</p>
<p>Les opérateurs peuvent aussi être utilisés pour les aggrégations. Le pipeline d'aggrégation est la méthode préférée afin de réaliser ces opérations,
il est beaucoup plus simple que la fonction Map-Reduce, mais parfois moins flexible.
Il y a, en revanche, une limite du 16mb pour le résultat retourné due à la limitation du type BSON.</p>

<p>Alors ... imaginez un tube orienté horizontalement ou verticalement, comme vous préférez. Dès que vous utilisez la fonction aggregate() sur une Collection,
l'ensemble des Documents de la Collection passe dans ce tube. Les Documents vont progresser étape par étape, ou chaque opérateur ($group, $match $unwind ...)
est une étape. L'étape 1, représentant le premier opérateur, va traiter tous les Documents de la collection. L'opérateur 2 va effectuer sa transformation
sur les Documents retournés par l'étape 1, l'étape 3 sur les documents de l'étape 2 et ainsi de suite ...</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Bien sûr, les aggrégations ne modifient pas les Documents de base, même si l'affichage en sortie offre
	des Documents totalement différents, vos Documents resteront intactes.
</div>
<a name="exe"></a>

<div class="spacer"></div>

<p class="titre">I) [ Exemples ]</p>

<p></p>
<a name="post"></a>

<div class="spacer"></div>

<p class="small-titre">a) Code Postal</p>

<p>Nous y sommes, premier exemple avec les Codes Postaux ! Prenons le Document suivant :</p>

<pre>
{
	"_id" : "10280",
	"city" : "NEW YORK",
	"state" : "NY",
	"pop" : 5574,
	"loc" : [ -74.016323, 40.710537 ]
}
</pre>

<p>ou _id est l'identifiant du code postal,
city est le nom de la ville,
state représente aux initiales de l'état,
pop la population en nombre
et loc un tableau contenant la longitude et latitude.</p>

<p>Nous allons procéder à un exemple d'aggrégation avec la fonction aggregate() qu'offre MongoDB.</p>

<div class="spacer"></div>

<p>Si l'on veut retourner les états avec une population supérieure à 10 millions :</p>

<pre>
db.zipcodes.aggregate(
	[
		{
			$group : { 
				_id : "$state",
				totalPop : { $sum : "$pop" }
			}
		},
		{
			$match : {
				totalPop: { $gte : 10 * 1000 * 1000 }	
			}
		}
	]
)
</pre>

<p>Pas de panique, cela peut paraître compliqué au début, mais on s'y fait, c'est comme tout ! :)</p>

<div class="alert alert-success">
	<u>Astuce</u> : Pour les plus attentifs, vous aurez remarquez que lorsqu'un champs de notre Document de base est utilisé en tant que
	valeur dans le regroupement que l'on veut faire, on le précède d'un dollar.
</div>

<div class="spacer"></div>

<p>Rappelez-vous, le pipeline d'aggrégation va regrouper les données et créer un ou plusieurs Documents pour former ce groupe.
Ici, avec l'opérateur $group, le pipeline va commencer par créer un Document pour chaque nom d'état et calculer la population totale.</p>

<p>Si vous avez trois Documents dans votre Collection zipcodes qui ressemblent à ceci :</p>

<pre>
{
	"_id" : "165165666656566562", "city" : "Paris", state : NY, pop : 20000, loc : [ 10, 20]
}

{
	"_id" : "165165666656566563", "city" : "Paris", state : NY, pop : 30000, loc : [ 10, 20]
}

{
	"_id" : "165165666656999942", "city" : "Paris", state : NY, pop : 50000, loc : [ 10, 20]
}
</pre>

<div class="spacer"></div>

<p>Le Pipeline va regrouper comme ceci :</p>

<pre>
{
	"_id" : "NY", "totalpop" : 100 000
}
</pre>

<p>Les Documents générés cosnt constitués des deux champs _id et totalpop, bien sur, on aurait pu donner des noms différents.
Ensuite, l'opérateur $match va rechercher (filtrer), à travers tous les Documents regroupés, ceux qui ont la somme totalpop supérieure($gte) à 10 000 000.
Bien sur, ici la population est égale à 100 000 donc cela ne sera pas retourné.</p>

<div class="spacer"></div>

<p>En bref, cela revient au même que d'effectuer cette requête en SQL :</p>

<pre>
SELECT state, SUM(pop) AS totalPop FROM zipcodes GROUP BY state HAVING totalPop >= (10 * 1000 * 1000);
</pre>

<div class="spacer"></div>

<p>Voilà, c'est terminé pour le premier exemple, en espérant que ça a été bien clair pour vous. Je le rappelle, si vous avez besoin d'aide,
d'explications plus claires/détaillées, ou si vous pensez que cette partie est mal expliquée, <a href="../contact.php">Contactez-moi</a> !</p>

<div class="spacer"></div>

<p>Encore un autre exemple, si l'on veut retourner la population totale par ville et par état, nous avons le code suivant, que nous allons décortiquer :</p>

<pre>
db.zipcodes.aggregate(
	[
		{ 
			$group: {
				_id : { state : "$state", city : "$city"},
				pop : { $sum : "$pop" } 
			} 
		},
		{
			$group : {
				_id : "$_id.state",
				avgCityPop:{ $avg : "$pop" }
			}
		}
	]
)
</pre>

<div class="spacer"></div>

<p>En parcourant le pipeline, le Document va passer par l'étape de regroupement par état puis par ville avec l'opérateur $group.
Ce traitement va donc renvoyer un Document pour chaque couple état+ville, car on sait bien qu'un état peut contenir plusieurs villes.</p>

<pre>
{
	"_id" : {
		"state" : "CO",
		"city" : "EDGEWATER"
	},
	"pop" : 13154
}
</pre>

<div class="spacer"></div>

<p>Une fois fait, on avance à la deuxième étape, un autre $group (hé bien oui, on peut en refaire un autre, sans forcément utiliser l'opérateur $match
si l'on n'en a pas besoin !) Cette dernière étape va regrouper tous les Documents retournés du premier $group, puis les transformer en un seul (si un état) ou plusieurs
afin de calculer la population moyenne ($avg) de chaque ville.</p>

<pre>
{
	"_id":"MN",
	"avgCityPop":5335
},
...
</pre>

<div class="spacer"></div>

<p>Encore un autre exemple un peu plus compliqué, retourner les plus petites et plus grandes villes par état :</p>

<pre>
db.zipcodes.aggregate(
	[
		{
			$group: {
				_id: { state:"$state", city:"$city"},
				pop:{ $sum:"$pop"}
			}
		},
		{ $sort:{ pop:1} },
		{
			$group: {
				_id:"$_id.state",
				biggestCity:{ $last:"$_id.city"},
				biggestPop:{ $last:"$pop"},
				smallestCity:{ $first:"$_id.city"},
				smallestPop:{ $first:"$pop"}
			}
		},
		
		// l'étape $project est optionnelle,
		// elle modifie le résultat en sortie
		
		{
			$project : {
				_id:0,
				state:"$_id",
				biggestCity:{ name:"$biggestCity", pop:"$biggestPop"},
				smallestCity:{name:"$smallestCity", pop:"$smallestPop"}
			}
		}
	]
)
</pre>

<div class="spacer"></div>

<p>Alors ici, suivez-bien, et vous verez que ce n'est pas si terrible que ça.</p>
<p>On récupère tous les Documents de la collection zipcodes et on les envoie dans le pipeline (tube) avec la fontion aggregate() que vous connaissez déjà.
Ensuite, le premier opérateur $group regroupe tous les Documents en un (si un seul état) ou plusieurs par couple état + ville en gardant la population pour chaque ville.
Suivant, l'opérateur $sort, comme vous vous en doutez, effectue un tri sur le nombre de la population par ordre croissant (-1 aurait été décroissant),
donc des villes des moins peuplées au plus peuplées.
Le prochain $group va regrouper tous ces Documents de manière à ne garder que le nom de l'état dans "_id", et, pour chaque état, on va mémoriser
le nom + le nombre de la plus grande ville avec l'opétaeur $last (le dernier de liste car on a trié par ordre croissant) puis, la même chose
avec l'opérateur $first pour la plus petite.</p>

<div class="spacer"></div>

<p>Vous suivez ? J'spère que oui :p</p>

<div class="spacer"></div>

<p>Partie optionnelle, l'opétaeur $project va permettre de changer la forme des Documents traités et finaux, ceux que l'on va voir apparaître sur notre écran,
celui-ci va masquer le champ _id car on le définit à 0, retourner un champ nommé state correspondant à chaque _id des Documents générés par le dernier groupe du pipeline.
Et va ensuite, dans biggestCity et smallestcity, insérer respectivement le nom + nombre de population des plus grosses et plus petites villes.
Cette dernière partie est juste pour le rendu, l'affichage.</p>
<a name="pref"></a>

<div class="spacer"></div>

<p class="small-titre">b) Préférences Utilisateurs</p>

<p>On va considérer l'exemple suivant d'un club de sport, gardant le nom, la date d'arrivée et les sports préférés pour chaque joueur :</p>

<pre>
{
	_id : "jane",
	joined:ISODate("2011-03-02" ),
	likes :
		[
			"golf",
			"racquetball"
		]
}

{
	_id:"joe",
	joined:ISODate("2012-07-02"),
	likes :
	[
		"tennis",
		"golf",
		"swimming"
	]
}
</pre>

<div class="spacer"></div>

<p>Supposons que l'on souhaite normaliser et trier les Documents suivants, on souhaite retourner la liste des noms des joueurs en majuscule puis par ordre croissant :</p>

<pre>
db.users.aggregate(
	[
		{
			$project : {
				name : { $toUpper : "$_id" },
				_id:0
			}
		},
		{ $sort : { name : 1 } }
	]
)
</pre>

<div class="spacer"></div>

<p>Dans un premier temps, tous les documents de la collection users vont passer dans le pipeline.
tout d'abord, ceux-ci vont être transformés avec l'opérateur $project en Documents ayant le champ name (l'id du joueur en majuscule avec $toUpper)
sans le champ _id que l'on met à 0. Puis, on va les trier avec l'opérateur $sort par nom croissant.</p>

<p>L'aggrégation va donc ressembler à ceci :</p>

<pre>
{
	"name" : "JANE"
},
{
	"name" : "JILL"
},
{
	"name" : "JOE"
}
</pre>

<div class="spacer"></div>

<p>De même, un autre exemple si l'on veut retourner les utilisateurs triés par date d'arrivée :</p>

<pre>
db.users.aggregate(
	[
		{
			$project : {
				month_joined: { $month : "$joined" },
				name : "$_id",
				_id : 0
			},
		{
			$sort : { month_joined : 1}
		}
	]
)
</pre>

<p>Les Documents vont d'abord être transformés ($project) en Documents contenant le champ month_joinded, name mais sans l'_id que l'ont met explicitement à 0.</p>
<p>Pour mieux comprendre, le champ month_joined va recevoir le champ des Documents précédents "joined" mais convertis en valeur numérique.
On aura donc pour Septembre par exemple, le nombre 9 dans le champ month_joined.</p>

<div class="spacer"></div>

<p>Ensuite, on trie les Documents par month_joined par date de la plus vieille à la plus récente.
Voici l'affichage que nous allons avoir en sortie :</p>

<pre>
{
	"month_joined":1,
	"name":"ruth"
},
{
	"month_joined":1,
	"name":"harold"
},
{
	"month_joined":1,
	"name":"kate"
}
{
	"month_joined":2,
	"name":"jill"
}
</pre>

<div class="spacer"></div>

<p>Si l'on souhaite retourner le nombre total d'inscriptions par mois :</p>

<pre>
db.users.aggregate(
	[
		{
			$project : {
				month_joined : { $month : "$joined" }
			}
		},
		{
			$group: {
				_id : { month_joined : "$month_joined"},
				number : { $sum : 1 }
			}
		},
		{
			$sort : {
				"_id.month_joined" : 1
			}
		}
	]
)
</pre>

<div class="spacer"></div>

<p>Dans un premier temps, on va passer tous les Documents de la Collection users afin de ne générer des Documents ne contenant que le champ _id,
car n'étant pas défini, il apparaîtra par défaut, puis le champ month_joinded qui va contenir la valeur du champ de base joined, sa valeur sera
convertie en numéro de mois dans l'année. Ensuite, nous allons avoir plusieurs Documents ayant le même champ _id qui correspondront au même mois.
La deuxième aggrégation va donc ressortir des Documents, depuis les précédents, qui auront le nom du mois en tant qu'id puis le champ number qui s'incrémentera a
chaque fois que l'on rencontre le même mois.
Pour finir, on triera les Documents par date d'arrivée de la plus ancienne à la plus actuelle. Le résultat en sortie sera donc comme ceci :</p>

<pre>
{
	"_id" : {"month_joined" : 1},
	"number" : 3
},
{
	"_id" : {"month_joined" : 2},
	"number" : 9
},
{
	"_id" : {"month_joined" : 3},
	"number" : 5
}
</pre>

<div class="spacer"></div>

<p>Le dernier exemple consiste à retourner le top 5 des sports les plus appréciés des joueurs. L'aggrégation qui suit va nous permettre de faire ça:</p>

<pre>
db.users.aggregate(
	[
		{
			$unwind : "$likes"
		},
		{
			$group : {
				_id:"$likes", 
				number : { $sum:1 }
			}
		},
		{ $sort:{ number:-1} },
		{ $limit:5}
	]
)
</pre>

<div class="spacer"></div>

<p>Ha un nouvel opérateur, le $unwind ! Cet opérateur va créer, pour chaque valeur du tableau "likes", un Document se basant sur le Document de départ.
Nous allons donc pouvoir séparer chaque valeur efficacement comme ceci :</p>

<pre>
{
	_id:"jane",
	joined:ISODate("2011-03-02"),
	likes:["golf","racquetball"]
}
</pre>

<div class="spacer"></div>

<p>Va se transformer en :</p>

<pre>
{
	_id:"jane",
	joined:ISODate("2011-03-02"),
	likes:"golf"
}
{
	_id:"jane",
	joined:ISODate("2011-03-02"),
	likes:"racquetball"
}
</pre>

<div class="spacer"></div>

<p>Ensuite, la seconde étape, le deuxième $group va créer un Document pour chaque sport, le placer dans le champ _id et compter à chaque fois que l'on obtient un like
avec l'opérateur $sum que l'on incrémente de 1 dans number.
L"opérateur $sort va trier ces derniers Documents par ordre décroissant avec -1 pour obtenir les sports les plus aimés aux moins aimés.
La dernière étape, l'opérateur $limit va limiter le nombre de résultats retournés à 5. On va donc retourner les 5 premiers de ceux qui on été traités après
l'opérateur $sort, ce qui va nous renvoyer les 5 plus aimés :</p>

<pre>
{
	"_id":"golf",
	"number":33
},
{
	"_id":"racquetball",
	"number":31
},
{
	"_id":"swimming",
	"number":24
},
{
	"_id":"handball",
	"number":19
},
{
	"_id":"tennis",
	"number":18
}
</pre>

<div class="spacer"></div>

<p>Voilà, c'est terminé pour les exemples sur le pipeline d'aggrégation. Si vous désirez plus de détails, <a href="../contact.php">contactez-moi</a> !</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Les aggrégations paraîssent plutôt compliquées au premier abord, c'est pour cela qu'il ne faut pas vous décourager, surout pour ceux
	qui sont habitués au SQL, je dirais un bon pourcentage de ceux qui liront ce tutoriel. Entraînez-vous, jouez avec ce pipeline pour voir étape
	par étape à quoi votre Document ressemble.Vous y arrivez sans trop de difficultées ? Parfait ! Passons à la suite !
</div>

<div class="spacer"></div>

<p>Ca vous a plu ? Je parie que oui ;) Maintenant, si vous souhaitez utiliser une méthode d'aggrégation offrant plus de flexibilité et d'options,
veuillez passer au chapitre suivant sur les <a href="aggregation_mapreduce.php">Aggrégations Map-Reduce</a>. >></p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
