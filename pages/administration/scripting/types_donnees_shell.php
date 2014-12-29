<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../scripting.php">Scripting</a></li>
	<li class="active">Types de Données dans le Shell mongo</li>
</ul>

<p class="titre">[ Types de Données dans le Shell mongo ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#type">I) Types</a></p>
	<p class="right"><a href="#date">- a) Date</a></p>
	<p class="right"><a href="#obje">- b) ObjectId</a></p>
	<p class="right"><a href="#numl">- c) NumberLong</a></p>
	<p class="right"><a href="#numi">- d) NumberInt</a></p>
	<p class="elem"><a href="#veri">II) Vérifier les Types dans le Shell mongo</a></p>
</div>

<p>Le format BSON de MongoDB fournit un support pour d'autres types de données que JSON n'offre pas. Les drivers fournissent un support natif
pour ces types de données, tout comme le shell mongo qui offre des classes afin de supporter ces différents types.</p>
<a name="type"></a>

<div class="spacer"></div>

<p class="titre">I) [ Types ]</p>

<p></p>
<a name="date"></a>

<div class="spacer"></div>

<p class="small-titre">a) Date</p>

<p>Le shell mongo founrit plusieurs options pour retourner la date, en tant que string ou en tant qu'objet :
	- la méthode Date() retourne la date actuelle en tant que string
	- le constructeur Date() retourne un objet ISODate lorsque celui-ci est utilisé avec l'opérateur new.
	- Le constructeur ISODate() qui retourne un objet ISODate lorsque celui-ci est utilisé avec ou sans l'opérateur new.
	
Considérons les exemples suivants :

_ Pour retourner la date en tant que chaîne de caractères, utilisez la méthode Date() comme dans l'exemple suivant :</p>

<pre>var myDateString = Date();</pre>

<p>	- Pour afficher la valeur de la variable, tapez le nom de la variable dans le shell :</p>

<pre>myDateString</pre>

<p>Le résultat est a valeur de myDateString :</p>

<p>Wed Dec 19 2012 01:03:25 GMT-0500 (EST)</p>

<p> - Pour vérifier le type, utilisez l'opérateur typeof comme suivant :</p>

<pre>typeof myDateString</pre>

<div class="spacer"></div>

<p>L'opération retourne string.

_ Pour retourner la date en tant qu'objet ISODate, instanciez un nouvel objet Date() avec l'opérateur new comme ceci :</p>

<pre>var myDateObject = new Date();</pre>

<p> - Pour afficher la valeur de la variable :</p>

<pre>myDateObject</pre>

<p>Le résultat de la variable myDateObject est :</p>

<pre>ISODate("2012-12-19T06:01:17.171Z")</pre>

<p>Pour vérifier le type de la variable, utilisez l'opérateur typeof à nouveau :</p>

<pre>typeof myDateObject</pre>

<div class="spacer"></div>

<p>L'opération retourne object.

Pour retourner la date en tant qu'objet ISODate instanciez un nouvel objet avec le constructeur ISODate() sans l'opérateur new :</p>

<pre>var myDateObject2 = ISODate();</pre>

<p>Pour afficher la valeur , tapez le nom de la variable dans le shell :</p>

<pre>myDateObject2</pre>

<p>Le résultat de myDateObject2 est :</p>

<pre>ISODate("2012-12-19T06:15:33.035Z")</pre>

<p>Pour vérifier le type, typeof va encore être utile :</p>

<pre>typeof myDateObject2</pre>

<p>Cette opération retourne object.</p>
<a name="obje"></a>

<div class="spacer"></div>

<p class="small-titre">b) ObjectId</p>

<p>Le shell mongo fournit la classe ObjectId() pour les types de données ObjectId. Pour générer un nouvel ObjectId, utilisez la méthode suivante dans
le shell :</p>

<pre>new ObjectId</pre>
<a name="numl"></a>

<div class="spacer"></div>

<p class="small-titre">c) NumberLong</p>

<p>Par défaut, le shell mongo traite tous les nombres en tant que nombres flottants. Le shell mongo fournit la classe NumberLong() pour gérer les
entiers 64-bits. Le constructeur NumberLong() prend le nombre Long en tant que chaîne de caractères :</p>

<pre>NumberLong("2090845886852")</pre>

<p>Les exemples suivants utilisent la classe NumberLong() pour écrire dans la collection :</p>

<pre>
db.collection.insert( { _id: 10, calc: NumberLong("2090845886852") } )

db.collection.update(
	{ _id: 10 },
	{ $set:
		{ calc: NumberLong("2555555000000") } 
	}
)

db.collection.update( 
	{ _id: 10 },
	{ $inc: { calc: NumberLong(5) } } 
)
</pre>

<div class="spacer"></div>

<p>Vous pouvez récupérer ce document pour vérifier :</p>

<pre>db.collection.findOne( { _id: 10 } )</pre>

<p>Dans le document retourné, le champ calc contient un objet NumberLong :</p>

<pre>{ "_id" : 10, "calc" : NumberLong("2555555000005") }</pre>

<p>Si vous utilisez l'opérateur $inc pour incrémenter la valeur d'un champ qui contient un objet de type NumberLong par un flottant, le type de donnée
change en un nombre flottant :

1) Utilisez l'opérateur $inc pour incrémenter le champ calc de 5, que le shell mongo va interpréter comme un nombre flottant :</p>

<div class="spacer"></div>

<pre>
db.collection.update(
	{ _id: 10 },
	{ $inc: 
		{ calc: 5 } 
	}
)
</pre>

<p>2) Récupérer le document mis à jour :</p>

<pre>db.collection.findOne( { _id: 10 } )</pre>

<p>Dans ce document, le champ calc contient un nombre flottant :</p>

<pre>{ "_id" : 10, "calc" : 2555555000010 }</pre>
<a name="numi"></a>

<div class="spacer"></div>

<p class="small-titre">d) NumberInt</p>

<p>Par défaut, le shell mongo traite tous les nombres en tant que nombres flottants à virgule. Le shell mongo fournit le constructeur NumberInt()
pour spécifier explicitement les entiers 32-bits.</p>
<a name="veri"></a>

<div class="spacer"></div>

<p class="titre">II) [ Vérifier les Types dans le Shell mongo ]</p>

<p>Pour déterminer le type des champs, le shell mongo fournit les opérateurs suivants :

- instanceof qui retourne un booléen pour tester si la valeur a un type spécifique
- typeof qui retourne le type du champ

Par exemple, considérons les opérations suivantes en utilisant instanceof et typeof :

- L'opération suivante va tester si le champ _id est de type ObjectId :</p>

<pre>mydoc._id instanceof ObjectId</pre>

<p>Le résultat ici sera true.

- L'opération va maintenant retourner le type du champ _id :</p>

<pre>typeof mydoc._id</pre>

<p>Dans ce cas, typeof va retourner le type object plus générique plutôt que le type ObjectId lui-même.</p>

<div class="spacer"></div>

<p>La suite va concerner les <a href="ecrire_scripts.php">"Ecrire des Scripts avec le Shell mongo" >></a>.</p>

<?php

	include("footer.php");

?> 
