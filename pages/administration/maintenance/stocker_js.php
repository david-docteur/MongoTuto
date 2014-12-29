<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Stocker une Fonction JavaScript sur le Serveur</li>
</ul>

<p class="titre">[ Stocker une Fonction JavaScript sur le Serveur ]</p>

<div class="alert alert-danger">
	<u>Attention</u> : MongoDB ne recommande pas l'utilisation de fonctions stockées côté server si possible.
</div>

<div class="spacer"></div>

<p>Il y a une collection système spéciale nommée system.js qui peut stocker des fonctions JavaScript pour les réutiliser.
Pour stocker une fonction, vous pouvez utiliser la méthode db.collection.save() comme dans l'exemple suivant :</p>

<pre>
db.system.js.save(
	{
		_id : "myAddFunction" ,
		value : function (x, y){ return x + y; }
	}
);
</pre>

<p>Le champ _id détient le nom de la fonction et est unique par base de données.
Le champ value détient la définition de la fonction.

Une fois que vous sauvegardez une fonction dans la collection system.js, vous pouvez utiliser cette fonction depuis n'importe quel contexte JavaScript
(par exemple la commande eval ou la méthode db.eval() via le shell mongo, l'opérateur $where, mapReduce ou la méthode du shell mongo db.collection.mapReduce()).

Considérez l'exemple suivant depuis le shell mongo qui sauvegarde dans un premier temps une fonction nommée echoFunction dans la collection system.js
et qui appelle cette fonction avec la méthode db.eval() :</p>

<div class="spacer"></div>

<pre>
db.system.js.save(
	{ 
		_id: "echoFunction",
		value : function(x) { return x; }
	}
)

db.eval( "echoFunction( 'test' )" )
</pre>

<p>Besoin d'un exemple plus concret ? Par ici => <a href="https://github.com/mongodb/mongo/blob/master/jstests/storefunc.js" target="_blank">Exemple</a>.
Depuis la version 2.1, avec le shell mongo, vous pouvez utiliser la méthode db.loadServerScripts() pour charger tous les scripts sauvegardés dans la collection
system.js pour la base de données courante. Une fois chargés, vous pouvez invoquer les fonctions directement dans le shell comme dans l'exemple suivant :</p>

<div class="spacer"></div>

<pre>
db.loadServerScripts();

echoFunction(3);
	
myAddFunction(3, 5);
</pre>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la partie Restauration et Sauvegarde, la suite sur <a href="../sauvegarde_restauration/outils_backup.php">"Sauvegarder et Restaurer avec les Outils MongoDB" >></a>.</p>

<?php

	include("footer.php");

?>
