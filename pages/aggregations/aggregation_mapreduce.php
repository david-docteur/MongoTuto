<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../aggregations.php">Aggrégations</a></li>
	<li class="active">La Fonction Map-Reduce</li>
</ul>

<p class="titre">[ La Fonction Map-Reduce ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#exe">I) Exemples</a></p>
	<p class="right"><a href="#ret">- a) Retourner le prix total par client</a></p>
	<p class="right"><a href="#cal">- b) Calculer le nombre de commandes et la quantité totale avec quantité moyenne par object</a></p>
	<p class="elem"><a href="#eff">II) Effectuer une Map-Reduce incrémentale</a></p>
	<p class="elem"><a href="#map">III) Débugger la fonction Map</a></p>
	<p class="elem"><a href="#red">IV) Débugger la fonction Reduce</a></p>
	<p class="right"><a href="#veri">- a) Vérifier le type de sortie</a></p>
	<p class="right"><a href="#ins">- b) Insensibilité de l'ordre des valeurs associées</a></p>
	<p class="right"><a href="#ide">- c) Idempotence de la fonction Reduce</a></p>
</div>

<p>La fonction Map-Reduce s'exécute avec la fonction mapReduce() et permet de traiter une quantité de données avec de multiples phases.</p>
<p>Celle-ci est similaire au Pipeline d'Aggrégation, on peut trier, limiter, ajouter des conditions à nos requêtes.
Elle s'exécute en deux phases généralement :
- Phase 1 : l'étape de mappage (map) qui va traiter chaque Document et la phase d'émission (emits) qui va générer un ou plusieurs object pour chaque Document
traité.
- Phase 2 : la phase de réduction (reduce) qui regroupe les données traitées de la phase de mappage.
Optionenellement, on peut avoir une phase de finalisation (finalize) pour effectuer des dernières modifications.</p>

<div class="spacer"></div>

<p>La fonction Map-Reduce utilise des fonctions de Javascript afin d'effectuer ces différentes phases, et même la fonction finalize.
Même si JavaScript offre une grande flexibilité comparé au Pipeline d'aggrégation, Map-Reduce est moins performant et plus compliquer à implémenter
que le Pipeline d'Aggrégation, mais offre plus de flexibilité grace à Javascript.</p>

<div class="spacer"></div>

<p>En revanche, Map-Reduce peut retourner des résultats de plus de 16Mo, tandis que le Pipeline d'Aggrégation ne peut pas.
Puis, depuis avant Mongo 2.4, le code Javascript exécuté en arrière-plan pour les fontions Map-Reduce était exécuté via un simple Thread, ce qui
pouvait poser des problèmes.</p>
<p>Le but principal de la fonction Map-Reduce va être de mapper ou associer des valeurs à une clef. On pourra même insérer les résultats dans une Collection.</p>
<a name="exe"></a>

<div class="spacer"></div>

<p class="titre">I) [ Exemples ]</p>

<p></p>
<a name="ret"></a>

<div class="spacer"></div>

<p class="small-titre">a) Retourner le prix total par client</p>

<p>Dans ce premier exemple, nous allons devoir regrouper chaque client par leur id puis calculer la somme de leur commandes pour avoir un total :</p>

<p>Premièrement, on définit la phase de mappage pour chaque Document de la Collection, sachant que this réfère au Document courant.</p>

<pre>
var mapFunction1 = function() {
	emit(this.cust_id, this.price);
};
</pre>

<p>Ici on va associer le prix au cust_id pour chaque Document puis emettre la paire cust_id et price.</p>

<div class="spacer"></div>

<p>Ensuite nous allons réduire les données retournées par la première fonction :</p>

<pre>
var reduceFunction1 = function(keyCustId, valuesPrices) {
	return Array.sum(valuesPrices);
};
</pre>

<p>Alors, ce qu'il va se passer ici, l'argument valuesPrices est un tableau qui va contenir tous les prix que le client courant a payé
et la clé keyCustId l'id du client que l'on est en train de traiter. Car rappelez-vous, un peu plus haut on explique que le role de la fonction de mappage
est d'associer la ou les valeur(s) à la clé. Ici, on associe donc les prix à chaque client.</p>

<p>On réduit enfin le tableau de prix par la somme de tous les élements.</p>

<div class="spacer"></div>

<p>Pour finir, nous allons exécuter le tout avec cette fonction :</p>

<pre>
db.orders.mapReduce(
	mapFunction1,
	reduceFunction1,
	{ out: "map_reduce_example" }
)
</pre>

<p>Comme vous pouvez le voir, on exécute d'abord la fonction map, puis la fonction réduce, pour ensuite insérer optionnellement les résultats
dans une nouvelle Collection map_reduce_example". Si la Collection existe déjà, les données seront totalement remplacées par celle de la nouvelle
fonction.</p>
<a name="cal"></a>

<div class="spacer"></div>

<p class="small-titre">b) Calculer le nombre de commandes et la quantité totale avec quantité moyenne par object</p>

<pre>
var mapFunction2 = function() {
	for (var idx = 0; idx < this.items.length; idx++) {
		var key = this.items[idx].sku;
		var value = {
			count: 1,
			qty: this.items[idx].qty
		};
		emit(key, value);
	}
};
</pre>

<p>Cette fonction va associer le sku du produit avec une valeur, un object contenant un champ count à 1 puis la quantité commandée par objet.</p>

<div class="spacer"></div>

<p>Maintenant nous allons procéder à la phase de réduction :</p>

<pre>
var reduceFunction2 = function(keySKU, countObjVals) {
	reducedVal = { count: 0, qty: 0 };
	for (var idx = 0; idx < countObjVals.length; idx++) {
		reducedVal.count += countObjVals[idx].count;
		reducedVal.qty += countObjVals[idx].qty;
	}
	return reducedVal;
};
</pre>

<p>Pour chaque produit sku, on va stocker dans l'object reducedVal, qui contient le nombre d'objets et la quantité.</p>

<div class="spacer"></div>

<p>On va ensuite procéder à une phase de finalisation, celle-ci va modifier l'objet reducedVal en ajoutant le champ avg qui va être
la quantité moyenen de chaque object :</p>

<pre>
var finalizeFunction2 = function (key, reducedVal) {
	reducedVal.avg = reducedVal.qty/reducedVal.count;
	return reducedVal;
};
</pre>

<div class="spacer"></div>

<p>On termine par l'exécution de la fonction:</p>

<pre>
db.orders.mapReduce(
	mapFunction2,
	reduceFunction2,
	{
		out: { merge: "map_reduce_example" },
		query: { ord_date: { $gt: new Date('01/01/2012') }
	},
	finalize: finalizeFunction2
}
</pre>

<p>Ici, on va spécifier une requête ne sélectionnant que les commandes après le 01/01/2012, puis on va écrire les résutats dans une collection "map_reduce_example".
Si la Collection existe déj, on va ajouter les derniers résultats à ceux qui existent déjà grâce au paramètre merge.</p>
<a name="eff"></a>

<div class="spacer"></div>

<p class="titre">II) [ Effectuer une Map-Reduce incrémentale ]</p>

<p>Si vous commptez effectuer une fonction Map-Reduce sur un ensemble de données constament en train de grossir, vous pouvez effectuer
ce type incrémental de Map-Reduce. Pour cela vous devez donc insérer les résultats dans un nouvelle collection, et quand vous avez plus données
à traiter, réalisez la même Map-Reduce avec une requête qui spécifie des paramètres rejoignant les nouveaux Documents. Puis, le paramètre out dans la
phase de réduction afin d'insérer les résultats dans la Collection qui existe déjà.</p>

<div class="spacer"></div>

<p>Par exemple, si l'on souhaite effectuer cette aggrégation sur tous les Logs à la fin de chaque jour :</p>

<pre>
db.sessions.save( { userid: "a", ts: ISODate('2011-11-03 14:17:00'), length: 95 } );
db.sessions.save( { userid: "b", ts: ISODate('2011-11-03 14:23:00'), length: 110 } );
db.sessions.save( { userid: "c", ts: ISODate('2011-11-03 15:02:00'), length: 120 } );
db.sessions.save( { userid: "d", ts: ISODate('2011-11-03 16:45:00'), length: 45 } );
db.sessions.save( { userid: "a", ts: ISODate('2011-11-04 11:05:00'), length: 105 } );
db.sessions.save( { userid: "b", ts: ISODate('2011-11-04 13:14:00'), length: 120 } );
db.sessions.save( { userid: "c", ts: ISODate('2011-11-04 17:00:00'), length: 130 } );
db.sessions.save( { userid: "d", ts: ISODate('2011-11-04 15:37:00'), length: 65 } );
</pre>

<div class="spacer"></div>

<p>L'insertion des données va ressember à quelque chose comme cela. Ensuite, nous devons créer la phase de mappage :</p>

<pre>
var mapFunction = function() {
	var key = this.userid;
	var value = {
		userid: this.userid,
		total_time: this.length,
		count: 1,
		avg_time: 0
	};
	emit( key, value );
};
</pre>

<p>Pour chaque utilisateur (clé), nous allons associer(mapper) le Document value (la valeur), contenant l'id de l'utilisateur aussi, le temps total de la session,
le nombre de connexion ainsi que le temps moyen.</p>

<div class="spacer"></div>

<p>Effectuons donc une phase de réduction (reduce) afin de calculer le temps total de la session de chaque utilisateur puis le nombre de fois qu'il s'est
connecté, pour chaque utilisateur :</p>

<pre>
var reduceFunction = function(key, values) {
	var reducedObject = {
		userid: key,
		total_time: 0,
		count:0,
		avg_time:0
	};
	values.forEach(
		function(value) {
			reducedObject.total_time += value.total_time;
			reducedObject.count += value.count;
		}
	);
	return reducedObject;
};
</pre>

<div class="spacer"></div>

<p>Ensuite, nous voulons définir le temps moyen de connexion, procédons à une étape de finalisation :</p>

<pre>
var finalizeFunction = function (key, reducedValue) {
	if (reducedValue.count > 0)
		reducedValue.avg_time = reducedValue.total_time / reducedValue.count;
	return reducedValue;
};
</pre>

<p>ce qui va calculer le temps de connexion moyenne en divisant le temps total par le nombre de connexions.</p>

<div class="spacer"></div>

<p>On termine donc par l'exécution totale de toutes nos fonctions :</p>

<pre>
db.sessions.mapReduce(
	mapFunction,
	reduceFunction,
	{
		out: { reduce: "session_stat" },
		finalize: finalizeFunction
	}
)
</pre>

<p>Si la Collection session_stat existe déjà, on ajoute les résultats à ceux qui existent déjà.</p>

<div class="spacer"></div>

<p>Et donc, pour démontrer l'utilité de cette fonction Map-Réduce incrémentale, nous allons, à la fin de chaque journée, spécifier la même fonction avec un
paramètre de la requête qui correspond à la date du jour pour obtenir les Documents de sessions du jour, admettons que l'on veuille ajouter ces
dernières données :</p>

<pre>
db.sessions.save( { userid: "a", ts: ISODate('2011-11-05 14:17:00'), length: 100 } );
db.sessions.save( { userid: "b", ts: ISODate('2011-11-05 14:23:00'), length: 115 } );
db.sessions.save( { userid: "c", ts: ISODate('2011-11-05 15:02:00'), length: 125 } );
db.sessions.save( { userid: "d", ts: ISODate('2011-11-05 16:45:00'), length: 55 } );
</pre>

<div class="spacer"></div>

<p>Afin, d'effectuer cette Map-Reduce routinière, nous allons effectuer le même appel de toutes nos fonctions mais avec un paramètre de requête en plus :</p>

<pre>
db.sessions.mapReduce(
	mapFunction,
	reduceFunction,
	{
		query: { ts: { $gt: ISODate('2011-11-05 00:00:00') } },
		out: { reduce: "session_stat" },
		finalize: finalizeFunction
	}
);
</pre>
<a name="map"></a>

<div class="spacer"></div>

<p class="titre">III) [ Débugger la fonction Map ]</p>

<p>Comme nous le savons déjà, la fonction Map va associer une valeur à une clé et va ensuite emettre cette paire ent guise de résultat. Pour vérifier
les pairs clé + valeur qui sont émits, il est possibnle d'écrire votre propre fonction emit :</p>

<pre>
{
	_id: ObjectId("50a8240b927d5d8b5891743c"),
	cust_id: "abc123",
	ord_date: new Date("Oct 04, 2012"),
	status: 'A',
	price: 250,
	items: [
		{ sku: "mmm", qty: 5, price: 2.5 },
		{ sku: "nnn", qty: 5, price: 2.5 }
	]
}
</pre>

<p>Toujours avec ce type de Documents, mappons la valeur prix et la clé cust_id :</p>

<pre>
var map = function() {
	emit(this.cust_id, this.price);
};
</pre>

<p>Vérifions maintenant chaque valeur de la fonction emit :</p>

<pre>
var emit = function(key, value) {
	print("emit");
	print("key: " + key + " value: " + tojson(value));
}
</pre>

<p>Invoquons la fonction map pour un simple Document puis vérifier la sortie de la paire clé+valeur qui en ressort :</p>

<pre>
var myDoc = db.orders.findOne( { _id: ObjectId("50a8240b927d5d8b5891743c") } );
map.apply(myDoc);
</pre>

<pre>
emit
key: abc123 value:250
</pre>

<p>Effectuons la même opération mais avec plusieurs Documents à mapper :</p>

<pre>
var myCursor = db.orders.find( { cust_id: "abc123" } );
	
while (myCursor.hasNext()) {
	var doc = myCursor.next();
	print ("document _id= " + tojson(doc._id));
	map.apply(doc);
	print();
}
</pre>

<p>Voilà, vous n'avez plus qu'à tester vos valeurs</p>
<a name="red"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Débugger la fonction Reduce ]</p>

<p>La fonction Reduce va réduire en un seul objet toutes les valeurs associés à une clé particulière. Celle-ci doit retourner un object
exactement du même que celui de la valeur émise par la fonction map. L'ordre des éléments dans le tableau de valeurs n'affecte pas le résultat
généré par la phase de réduction. Celle-ci doit être idempotente.</p>
<a name="veri"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vérifier le type de sortie</p>

<p>Afin de tester si la fonction reduce retourne un résultat du même type que celui émit par la fonction map, on définit la fonction et le tableau d'entiers
suivant :</p>

<pre>
var reduceFunction1 = function(keyCustId, valuesPrices) {
	return Array.sum(valuesPrices);
};

var myTestValues = [ 5, 5, 10 ];
</pre>

<p>On appelle la fonction reduce avec le tableau d'entiers :</p>

<pre>reduceFunction1('myKey', myTestValues);</pre>

<p>Vérifiez bien que la valeur retournée est égale à 20.</p>

<p>On définit une deuxième fonction afin de calculer le nombre d'objets et la quantité totale :</p>

<pre>
var reduceFunction2 = function(keySKU, valuesCountObjects) {
	reducedValue = { count: 0, qty: 0 };
	for (var idx = 0; idx < valuesCountObjects.length; idx++) {
		reducedValue.count += valuesCountObjects[idx].count;
		reducedValue.qty += valuesCountObjects[idx].qty;
	}
	return reducedValue;
};
</pre>

<p>On va donc exécuter cette fonction avec le Document suivant :</p>

<pre>
var myTestObjects = [
	{ count: 1, qty: 5 },
	{ count: 2, qty: 10 },
	{ count: 3, qty: 15 }
];
</pre>

<p>Appelons notre fonction :</p>

<pre>reduceFunction2('myKey', myTestObjects);</pre>

<p>La fonction reduce va retourner { "count" : 6, "qty" : 30 } qui est un Document contenant exactement un champ count et qty.</p>
<a name="ins"></a>

<div class="spacer"></div>

<p class="small-titre">b) Insensibilité de l'ordre des valeurs associées</p>

<p>On peut tester avec cet exemple, le fait que la fonction reduce soit complètement indifférente de l'ordre des valeurs associées par la fonction map :</p>

<pre>
var values1 = [
	{ count: 1, qty: 5 },
	{ count: 2, qty: 10 },
	{ count: 3, qty: 15 }
];
var values2 = [
	{ count: 3, qty: 15 },
	{ count: 1, qty: 5 },
	{ count: 2, qty: 10 }
];
</pre>

<p>On déclare notre fonction comme ceci :</p>

<pre>
var reduceFunction2 = function(keySKU, valuesCountObjects) {
	reducedValue = { count: 0, qty: 0 };
	for (var idx = 0; idx < valuesCountObjects.length; idx++) {
		reducedValue.count += valuesCountObjects[idx].count;
		reducedValue.qty += valuesCountObjects[idx].qty;
	}
	return reducedValue;
};
</pre>

<p>On appelle maintenant notre fonction qui va tester les deux tableaux de valeurs :</p>

<pre>
reduceFunction2('myKey', values1);
reduceFunction2('myKey', values2);
</pre>

<p>Les deux fonctions retournent exactement le même résultat : { "count" : 6, "qty" : 30 }</p>
<a name="ide"></a>

<div class="spacer"></div>

<p class="small-titre">c) Idempotence de la fonction Reduce</p>

<p>Nous devons vérifier l'imdepotence de la fonction Reduce car la fonction Map-Reduce peut appeler plusieurs fois la même Reduce pour la même clé
et ne va pas appeler une reduce pour une seule instance d'une clé se trouvant dans l'ensemble des données.
La fonction reduce doit obligatoirement retourner une valeur du même type que la valeur émise de la fonction map.
Vous pouvez tester que la fonction reduce a bien réduit les valeurs sans affecter le résultat final.</p>

<pre>
var reduceFunction2 = function(keySKU, valuesCountObjects) {
	reducedValue = { count: 0, qty: 0 };
	for (var idx = 0; idx < valuesCountObjects.length; idx++) {
		reducedValue.count += valuesCountObjects[idx].count;
		reducedValue.qty += valuesCountObjects[idx].qty;
		return reducedValue;
	};
}
</pre>

<p>Déclare une clé : </p>

<pre>var myKey = 'myKey';</pre>

<p>Déclarons deux tableaux de valeurs :</p>

<pre>
// Ici, on appelle un élément directement dans la fonction Reduce
var valuesIdempotent = [
	{ count: 1, qty: 5 },
	{ count: 2, qty: 10 },
	reduceFunction2(myKey, [ { count:3, qty: 15 } ] )
];

var values1 = [
	{ count: 1, qty: 5 },
	{ count: 2, qty: 10 },
	{ count: 3, qty: 15 }
];
</pre>

<p>On appelle la fonction reduceFonction2 pour chaque tableau de valeurs avec la clé myKey :</p>

<pre>
reduceFunction2(myKey, valuesIdempotent);
reduceFunction2(myKey, values1);
</pre>

<p>Maintenant, vous pouvez constater si la fonction reduceFonction2 retourne bien la même résultat dans les deux cas : { "count" : 6, "qty" : 30 }</p>

<div class="spacer"></div>

<p>Voilà, vous êtes arrivés au bout du tutoriel sur la fonction Map-Reduce, celui-ci n'est pas l'un des plus simple mais j'espère que les exemples
vous ont aidés à comprendre le fonctionnement. MongoTuto.Co, à l'avenir, complètera une série d'exemples au hasard.
Il ne vous reste qu'une petite partie des aggrégations, celle sur les <a href="aggregation_simple.php">Simples commandes d'aggrégation</a> qui va expliquer
comment réaliser des opérations telles que count(), distinct() etc ... >></p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>