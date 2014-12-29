<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations READ - Sélection et Opérateurs</li>
</ul>

<p class="titre">[ Les Opérations READ - Sélection et Opérateurs ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#find">I) La Méthode find()</a></p>
	<p class="right"><a href="#tout">- a) Sélectionner Tous les Documents</a></p>
	<p class="right"><a href="#spec">- b) Sélectionner des Documents Spécifiques</a></p>
	<p class="elem"><a href="#ope">II) Sélection avec les Opérateurs</a></p>
	<p class="right"><a href="#in">- a) Sélection avec $in</a></p>
	<p class="right"><a href="#and">- b) Sélection avec AND</a></p>
	<p class="right"><a href="#or">- c) Sélection avec $or</a></p>
	<p class="right"><a href="#andor">- d) Sélection avec AND et $or</a></p>
	<p class="elem"><a href="read2.php">Sous-Documents et Tableaux</a></p>
	<p class="elem"><a href="read3.php">Limites de Projection et Curseurs</a></p>
</div>

<p>Bienvenue sur la section des <b>opérations READ</b>, celles qui vont vous permettre d'<b>interroger MongoDB</b> ainsi que d'<b>afficher les documents
correspondants à votre requête</b>. Puis, nous allons voir les différents opérateurs <b>les plus importants</b> de MongoDB. Ceux-ci vont vous permettre
d'être <b>plus précis</b> et <b>plus efficace</b> lors d'une recherche. <b>Soyez attentifs</b> car, ici, il y a du boulot !</p>
<a name="find"></a>

<div class="spacer"></div>

<p class="titre">I) [ La Méthode find() ]</p>

<p>La méthode <b>db.maCollection.find()</b> prend en compte <b>deux arguments</b> : <b>les critères</b> et <b>la projection</b>. Je m'explique :
Prenons l'exemple suivant :</p>

<div class="small-spacer"></div>

<pre>db.maCollection.find(critères, projection)</pre>

<p>Là ou tout change par <b>rapport à SQL</b>, est la façon dont vous allez <b>modéliser cette requête</b>. Les critères correspondent aux champs
<b>contenus dans les documents</b> que vous recherchez (en général ce qui se trouve après la clause <b>WHERE</b> en langage <b>SQL</b>). La projection, elle, va correspondre aux champs <b>que vous souhaitez retourner</b> dans votre résultat (en général ce qui se trouve après la clause <b>SELECT</b> en <b>SQL</b>).</p>
<b>Ne paniquez pas, on va commencer en douceur.</b>
Commençons notre <b>première requête de sélection</b>, dont vous aviez eu un bref aperçu dans le chapitre précédent sur <b>les opérations CREATE</b>.
Cette requête va tout simplement permettre d'<b>interroger MongoDB</b> pour <b>afficher tous les documents</b> contenus dans une collection.</p>
<a name="tout"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sélectionner Tous les Documents</p>

<p>Voici la commande, toute simple, pour afficher <b>tous les documents</b> contenus dans la collection <b>"maCollection"</b>.</p>

<pre>db.maCollection.find()</pre>

<div class="small-spacer"></div>

<p>La commande suivante est <b>complètement identitique</b> :</p>

<pre>db.maCollection.find( { } )</pre>

<div class="small-spacer"></div>

<p>En effet, celle-ci représente la sélection d'<b>un document vide</b> et qui est très similaire à la <b>requête SQL suivante</b> :</p>

<pre>SELECT * FROM maTable;</pre>
<a name="spec"></a>

<div class="spacer"></div>

<p class="small-titre">b) Sélectionner des Documents Spécifiques</p>

<p>Voilà, maintenant vous souhaitez interroger un <b>certains type de documents</b> ayant des <b>caractéristiques précises</b>.
Pour cela, vous devez comprendre que le principe de ce genre de requête est basé sur <b>le modèle suivant</b> :
Vous allez devoir <b>passer un document BSON</b> en paramètre à la méthode <b>find()</b>. Ce document
va contenir <b>une ou plusieurs paires</b> de clés/valeurs qui seront <b>définies dans le document que vous recherchez</b>.
Par exemple, si vous recherchez un <b>fruit de couleur jaune</b> dans votre collection <b>"fruits"</b>, vous allez devoir effectuer la requête suivante :</p>

<div class="spacer"></div>

<pre>db.fruits.find( { "couleur" : "jaune" } )</pre>

<div class="small-spacer"></div>

<p>Ce qui correspond exactement, <b>en syntaxe SQL</b>, à la requête ci-dessous :</p>

<pre>SELECT * FROM fruits WHERE couleur = 'jaune';</pre>

<div class="small-spacer"></div>

<p>Vous souhaitez être <b>plus précis</b> ? Vous souhaitez sélectionner <b>les fruits de couleur jaune et de forme ronde</b> ? Voici la requête permettant de le faire :</p>

<div class="small-spacer"></div>

<pre>db.fruits.find( { "couleur" : "jaune", "forme" : "ronde" } )</pre>

<div class="small-spacer"></div>

<p>
    De même avec la <b>syntaxe SQL</b> :
</p>

<pre>SELECT * FROM fruits WHERE couleur = 'jaune' AND forme = 'ronde';</pre>

<div class="small-spacer"></div>

<p>Bien sur, vous pouvez ajouter <b>autant de critères que vous le souhaitez</b>, du moment que le champ se trouve dans votre document.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Vous allez vite remarquer que le shell mongo affiche les résultats d'une façon ... comment dire ... pas très lisible au premier abord.
	Pour obtenir un meilleur rendu, ajoutez la méthode pretty() à la fin de votre méthode find() comme dans l'exemple suivant :
</div>

<pre>db.maCollection.find().pretty()</pre>
<a name="ope"></a>

<div class="spacer"></div>

<p class="titre">II) [ Sélection avec les Opérateurs ]</p>
<p>Vous allez maintenant faire connaissance avec <b>les opérateurs MongoDB</b> afin d'être encore <b>plus spécifique</b> dans vos requêtes.
Pour consulter <b>la liste complète</b> des opérateurs existants : <a href="http://docs.mongodb.org/manual/reference/operator/" target="_blank">"Opérateurs MongoDB"</a>.
Bien sûr, le but de la section <a href="../exemples_code.php">"Exemples de Code"</a> est de fournir <b>autant d'exemples que possible</b>
pour chaque opérateur. Si vous ne voyez pas celui que vous cherchez à utiliser, <a href="../contact.php">"contactez-moi"</a> et j'ajouterai
ce qu'il vous manque.
Je ne vais pas tous les détailler sur cette page, mais au moins vous montrer quelques exemples sur <b>comment les utiliser</b>.
Les opérateurs sont des commandes, <b>précédées du signe $</b> afin d'être interprétées par le <b>shell MongoDB</b>.
</p>
<a name="in"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sélection avec $in</p>

<p>Commençons par le <b>premier opérateur $in</b>. L'opérateur <b>$in</b> va vous permettre d'effectuer une requête sur un certains type de document
ayant une <b>certaine valeur dans un tableau</b> contenu dans celui-ci.
Par exemple, prenons un petit ensemble de documents de <b>la collection "livres"</b>, un exemple minimal vous aidera à mieux comprendre.
Les documents de cette collection vont avoir pour <b>attributs</b> un <b>titre</b> de type chaîne de caractères, puis, un tableau de <b>tags</b>, comme ceci :</p>

<div class="small-spacer"></div>

<pre>

{
	"_id" : "001",
	"titre" : "Livre1",
	"tags" : [
		"cuisine",
		"chef",
		"poireaux"
	]
}

{
	"_id" : "002",
	"titre" : "Livre2",
	"tags" : [
		"entreprise",
		"drh",
		"salaire"
	]
}

{
	"_id" : "003",
	"titre" : "Livre3",
	"tags" : [
		"voyages",
		"Maldives",
		"sable blanc",
		"moi aussi j'ai envie de partir !"
	]
}
</pre>

<div class="small-spacer"></div>

<p>Nous avons ici <b>trois livres</b> traitant respectivement de <b>Cuisine, de Management ainsi que de Voyages</b>. Imaginons que vous souhaitez sélectionner
tous les livres contenant les tags <b>"chef", "poireaux" et "sable blanc"</b>, c'est-à-dire, ceux qui contiennent ces <b>mots-clés</b>
dans le <b>tableau "tags"</b> de la collection <b>"livres"</b>, la requête suivante permet de le faire :</p>

<div class="small-spacer"></div>

<pre>db.livres.find( 
        {
               tags : { $in :  [
                                    "chef",
                                    "poireaux",
                                    "sable blanc"
                                ] 
                      }
        }
)
</pre>

<div class="small-spacer"></div>

<p>Après cela, vous verrez dans votre shell les livres <b>"001"</b> et <b>"003"</b>.</p>

<div class="spacer"></div>

<p><b>Hey psssttttt ..... !</b> N'y aurait-il pas un opérateur <b>$or</b> pour ce genre d'opération ? <b>Si mais ...</b></p>

<div class="alert alert-danger">
	<u>Attention</u> : Il est recommandé d'utiliser l'opérateur $or pour comparer des champs différents.
	Ici, on compare le même champ 'tags', on doit donc utiliser l'opérateur $in qui est plus optimisé pour ce genre de requête.
</div>
<a name="and"></a>

<div class="spacer"></div>


<p class="small-titre">b) Sélection avec AND</p>

<p>Ici, pas besoin d'un opérateur <b>$and</b>, vous allez comprendre, <b>regardez</b> :</p>

<div class="spacer"></div>

<pre>
db.maCollection.find( 
             { 
               type: 'alimentation',
               prix: { $lt: 9.95 }
             }
)
</pre>

<div class="spacer"></div>

<p>La requête va sélectionner <b>naturellement</b> les champs de type '<b>alimentation</b>' ayant un prix <b>inférieur à 9.95</b>.
Tant que vous spécifiez des champs dans votre document qui est passé en paramètre à <b>la fonction find()</b>,
votre requête va sélectionner les documents qui contiennent ces <b>mêmes champs</b>.
<b>Un petit bonus : $lt pour "lower than", ce qui va spécifier la contrainte sur le prix.</b></p>
<a name="or"></a>

<div class="spacer"></div>

<p class="small-titre">c) Sélection avec $or</p>

<p>L'opérateur <b>$or</b> va consister en <b>un tableau</b> de documents BSON qui vont constituer <b>une condition</b> dans votre requête, comme ci-dessous :</p>

<div class="spacer"></div>

<pre>
db.inventaire.find(
        { 
            $or: [
                    { qte: { $gt: 100 } },
                    { prix: { $lt: 25 } }
                 ]
        }
)
</pre>

<div class="spacer"></div>

<p>Ici, la requête va sélectionner <b>tous les documents</b> de la collection '<b>inventaire</b>' ayant, <b>soit une quantité supérieure à 100</b> (greater than), <b>soit ceux qui ont un prix inférieur à 25</b> (lower than).</p>
<a name="andor"></a>

<div class="spacer"></div>

<p class="small-titre">d) Sélection avec AND et $or</p>

<p>Il est possible de <b>mélanger les conditions AND et OR ensembles</b> si vous souhaitez interroger des documents <b>incluant certains champs</b>, et, pour certains,
laisser un <b>choix de valeurs</b> plus flexible :</p>

<div class="spacer"></div>

<pre>
db.inventaire.find( 
        { 
            type: 'alimentation',
            $or: [ 
                    { qte: { $gt: 200 } },
                    { prix: { $lt: 25 } }
                 ]
        }
)
</pre>

<div class="spacer"></div>

<p>La requête ci-dessus va interroger les documents ayant <b>au moins</b> un type ayant la valeur '<b>alimentation</b>' et, <b>soit une quantité supérieure à 200</b> ou <b>un prix inférieur
à 25</b>.</p>

<div class="spacer"></div>

<p>Vous avez tout digéré ? <b>Bien !</b> Ici la <b>liste des opérateurs</b> n'est pas exhaustive mais cette page vous fournit <b>les bases</b> afin de savoir comment vous en servir. L'utilisation est
<b>très similaire</b> pour les autres.
Passons à la suite sur les <b>Sous-Documents et Tableaux</b>, cela va être <b>essentiel</b> pour la structure de vos données !
Si vous coincez encore un peu avec <b>les opérateurs</b> par exemple, n'hésitez pas à <b>revenir sur le tutoriel</b>, ou alors, <a href="../contact.php">"contactez-moi"</a> sans hésiter.</p>
Voici le lien vers la page suivante : <a href="read2.php">"Opérations READ - Sous-Documents et Tableaux" >></a>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
