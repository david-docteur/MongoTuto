<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../modelisation_donnees.php">Modélisations des Données</a></li>
	<li class="active">Relations entre Documents</li>
</ul>

<p class="titre">[ Relations entre Documents ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#11">I) Modèle 1-1 Avec Document Imbriqué</a></p>
	<p class="elem"><a href="#1n">II) Modèle 1-N Avec Documents Imbriqués</a></p>
	<p class="elem"><a href="#1nn">III) Modèle 1-N Avec Références</a></p>
</div>

<p>Bienvenue sur la page de <b>relations entre documents</b>. Nous allons voir <b>plusieurs types de relations</b> qui vont vous permettre d'explorer
<b>les possibilités</b> offertes par MongoDB afin de <b>gérer et "bien ranger"</b> vos données. Assez de "bla-bla", passons immédiatement à <b>la suite</b>.</p>
<a name="11"></a>

<div class="spacer"></div>

<p class="titre">I) [ Modèle 1-1 Avec Document Imbriqué ]</p>

<p>Premier exemple <b>simple</b> et <b>rapide</b>, avec <b>un salarié</b> et <b>son adresse postale</b> correspondante, prenons les deux documents suivants :</p>

<div class="small-spacer"></div>

<p><b>Document 1 dans la collection "Salariés"</b></p>
<pre>
{
	_id: "jean",
	nom: "Jean Bon"
}
</pre>

<div class="small-spacer"></div>

<p>Le <b>document 1</b> représente tout simplement <b>un salarié</b> avec des informations très basiques.</p>

<div class="small-spacer"></div>

<p><b>Document 2 dans la collection "Adresses"</b></p>
<pre>
{
	id_salarie: "jean",
	adresse: "123 rue Boucher",
	ville: "Paris",
	cp: '95'
}
</pre>

<div class="small-spacer"></div>

<p>Le <b>deuxième document</b> contient l'<b>adresse</b> et l'<b>id</b> du salarié en question.
Ici, nous aurions deux collections <b>"salariés"</b> et <b>"adresses"</b> par exemple, tout comme avec un <b>SGBDR</b>.
N'y aurait-il pas un moyen de <b>modifier cette structure</b> afin de n'avoir qu'<b>une seule collection</b> pour toutes ces informations ?
<b>OUI !</b> Cela va être utile surtout si votre application va <b>souvent interroger le nom et l'adresse</b> du salarié Jean. Pourquoi avoir <b>deux collections</b> alors que l'on peut en avoir qu'<b>une seule</b> ?
Le schéma est flexible après tout, <b>profitons-en</b> !</p>

<div class="spacer"></div>

<p><b>Document final dans la collection "Salariés"</b></p>
<pre>
{
	_id: "jean",
	nom: "Jean Bon",
	adresse: {
		rue: "123 rue Boucher",
		ville: "Paris",
		cp: '95'
	}
}
</pre>

<div class="small-spacer"></div>

<p>Voilà, ici, il a simplement fallu <b>intégrer le document d'adresse de Jean dans un sous-document</b> de la collection "Salariés".
Plutôt que d'effectuer <b>plusieurs requêtes</b> ou une jointure pour récupérer les informations, <b>une seule requête</b> simple suffira
pour retrouver toutes les informations dont nous avons besoin. De plus, cela nous permet de <b>supprimer l'id du document 2</b> afin de gagner un peu de place. Facile, non ?</p>
<a name="1n"></a>

<div class="spacer"></div>

<p class="titre">II) [ Modèle 1-N Avec Documents Imbriqués ]</p>

<p>Modèle suivant, <b>la relation 1-N</b> avec des documents imbriqués. Reprenons l'exemple précédent. Nous avons vu comment <b>imbriquer
l'adresse</b> du salarié Jean dans <b>sa propre entité Salarié</b> afin d'optimiser la sélection de ses informations. Et maintenant, imaginons que Jean
a <b>deux ou plusieurs adresses</b>, comment fait-on ? Hé bien on va faire exactement <b>la même chose</b>, sauf que notre champ <b>"adresse"</b> ne sera pas un sous-document ce coup-ci,
mais <b>un tableau de sous-documents</b>, regardez :</p>

<div class="small-spacer"></div>

<p><b>Document 1 dans la collection "Salariés"</b></p>

<pre>
{
	_id: "jean",
	nom: "Jean Bon"
}
</pre>

<div class="small-spacer"></div>

<p>Jusqu'ici rien de nouveau.</p>

<div class="small-spacer"></div>

<p><b>Document 2 dans la collection "Adresses"</b></p>
<pre>
{
	id_salarie: "jean",
	adresse: "123 rue Boucher",
	ville: "Paris",
	cp: '95'
}
</pre>

<div class="small-spacer"></div>

<p>L'adresse primaire de Jean que l'on connait déjà.</p>

<div class="small-spacer"></div>

<p><b>Document 3 dans la collection "Adresses"</b></p>

<pre>
{
	id_salarie: "jean",
	adresse: "456 Baker Street",
	ville: "London",
	cp: 'SW1P 000'
}
</pre>

<div class="small-spacer"></div>

<p>Ici, <b>l'adresse secondaire de Jean</b> ! Et en plus c'est à <b>Londres</b> ! Paris/Londres, en bref, Jean ne travaille uniquement pour payer ses loyers, et rien d'autre !
Maintenant que nous connaissons ses <b>deux adresses</b>, voici comment <b>optimiser la sélection</b> des informations personnelles de Jean
en <b>une seule requête</b> :</p>

<div class="small-spacer"></div>

<p><b>Document final dans la collection "Salariés"</b></p>
<pre>
{
	_id: "jean",
	nom: "Jean bon",
	adresses: [
		{
			adresse: "123 rue Boucher",
			ville: "Paris",
			cp: '95'
		},
		{
			adresse: "456 Baker Street",
			ville: "London",
			cp: 'SW1P 000'
		}
	]
}
</pre>

<div class="small-spacer"></div>

<p>Nous aperçevons ici <b>le tableau de sous-documents</b> où chaque sous-document est une adresse postale de Jean. Vous pouvez bien évidement vous entraîner à insérer
autant d'adresses que vous le voulez pour Jean. Lorsque vous effectuerez une requête de sélection afin d'obtenir <b>toutes les adresses</b> de Jean, vous n'interrogerez qu'<b>une seule
collection</b>.</p>
<a name="1nn"></a>

<div class="spacer"></div>

<p class="titre">III) [ Modèle 1-N Avec Références ]</p>

<p>On appelle <b>référence</b> le champ <b>"_id"</b> (un peu le principe d'une clé primaire) ou un autre champ que pouvez choisir si celui-ci respecte la <b>contrainte d'unicité</b> !
Dans cet exemple, <b>contraitement aux précédents</b>, nous allons plutôt <b>séparer les sous-documents</b> pour plus de souplesse.
Prenons un exemple, <b>deux documents</b> représentant <b>deux livres</b> et leurs informations respectives :</p>

<div class="small-spacer"></div>

<p><b>Document 1 dans la collection "Livres"</b></p>
<pre>
{
	titre: "The Mythical Man-Month",
	auteur: [ "Fred Brooks", "Mike Dirolf" ],
	date_publication: ISODate("1975-01-01"),
	pages: 322,
	langue: "English",
	editeurs: {
		nom: "O'Reilly Media",
		fondation: 1980,
		location: "CA"
	}
}
</pre>

<div class="small-spacer"></div>

<p>Le <b>premier livre</b> de notre collection, un que je conseille particulièrement de lire, <b>le deuxième</b> auteur n'est pas le bon mais c'est uniquement
pour vous montrer un exemple avec un tableau d'auteurs.</p>

<div class="small-spacer"></div>

<p><b>Document 2 dans la collection "Livres"</b></p>
<pre>
{
	titre: "L'Art de l'Intrusion",
	auteur: "Kevin Mitnick",
	date_publication: ISODate("2005-07-15"),
	pages: 300,
	langue: "Français",
	editeurs: {
		nom: "O'Reilly Media",
		fondation: 1980,
		location: "CA"
	}
}
</pre>

<div class="small-spacer"></div>

<p>Et notre <b>deuxième livre</b> faisant partit de la même collection.
Dans cet exemple, on voit bien que <b>les informations des éditeurs se répètent</b>. Il serait mieux de passer la référence des livres (leur champ "_id") <b>dans les documents de chaque éditeurs</b> afin d'établir un lien à un document dans une éventuelle collection
réservée aux éditeurs. 
Si l'on veut gérer ces informations d'une meilleure façon, on peut <b>placer la liste des livres</b> dans un champ <b>"livres"</b> qui est <b>un tableau d'_id</b> des livres.</p>

<div class="small-spacer"></div>

<p><b>Document 1 dans la collection "Editeurs"</b></p>
<pre>
{
	nom: "O'Reilly Media",
	fondation: 1980,
	location: "CA",
	livres: [12346789, 234567890, ...]
}
</pre>

<div class="small-spacer"></div>

<p>Voilà notre nouvelle collection <b>"Editeurs"</b> qui va contenir <b>la liste des maisons d'édition</b> ainsi que leurs informations relatives.
Les <b>"_id"</b> de chaque livre seront stockés dans <b>un tableau de livres</b>.</p>

<div class="small-spacer"></div>

<p><b>Document 2 dans la collection "Livres"</b></p>
<pre>
{
	_id: 123456789,
	titre: "The Mythical Man-Month",
	auteur: [ "Fred Brooks", "Mike Dirolf" ],
	date_publication: ISODate("1975-01-01"),
	pages: 322,
	langue: "English"
}
</pre>

<div class="small-spacer"></div>

<p>Notre premier livre dans notre collection <b>"Livres"</b>, cela ne change pas, hormis le fait que les informations de l'éditeur <b>ont disparues</b>.</p>

<div class="small-spacer"></div>

<p><b>Document 3 dans la collection "Livres"</b></p>
<pre>
{
	_id: 234567890,
	titre: "L'Art de l'Intrusion",
	auteur: "Kevin Mitnick",
	date_publication: ISODate("2005-07-15"),
	pages: 300,
	langue: "Français"
}
</pre>

<div class="small-spacer"></div>

<p>Idem pour le deuxième livre de la collection. D'accord, c'est beaucoup mieux mais cet exemple n'est pas forcément <b>le plus optimal</b>.
En effet, que se passerait-il si un editeur avait <b>des milliers de livres</b> ? Le champ <b>"livres"</b> pourrait devenir un tableau énorme.
Pour cela, il serait plus juste de placer l'id de l'éditeur <b>dans chaque document</b> de la collection <b>"Livre"</b> comme dans l'exemple suivant :</p>

<div class="small-spacer"></div>

<p><b>Document 1 dans la collection "Editeurs"</b></p>
<pre>
{
	_id: "oreilly",
	nom: "O'Reilly Media",
	fondation: 1980,
	location: "CA"
}
</pre>

<div class="small-spacer"></div>

<p>Notre même collection <b>"Editeurs"</b> ayant des documents pour chaque éditeurs mais <b>sans les références</b> de chaque livre.</p>

<div class="small-spacer"></div>

<p><b>Document 2 dans la collection "Livres"</b></p>
<pre>
{
	_id: 123456789,
	titre: "The Mythical Man-Month",
	auteur: [ "Fred Brooks", "Mike Dirolf" ],
	date_publication: ISODate("1975-01-01"),
	pages: 322,
	langue: "English",
	id_editeur: "oreilly"
}
</pre>

<div class="small-spacer"></div>

<p>Le premier livre de notre collection qui, maintenant, a <b>un champ "id_editeur"</b> qui va contenir la liste des éditeurs du livre.
Ici, il n'y en a qu'un seul mais plusieurs éditeurs reviendrait à <b>changer le type</b> du champ "id_editeur" en un tableau pour tous les contenir.</p>

<div class="small-spacer"></div>

<p><b>Document 3 dans la collection "Livres"</b></p>
<pre>
{
	_id: 234567890,
	titre: "L'Art de l'Intrusion",
	auteur: "Kevin Mitnick",
	date_publication: ISODate("2005-07-15"),
	pages: 300,
	langue: "Français",
	id_editeur: "oreilly"
}
</pre>

<div class="small-spacer"></div>

<p>Le deuxième livre, qui lui aussi, va contenir son <b>champ "id_editeur"</b> avec le ou les "_id" de chaque éditeur.
Techniquement, il y aura <b>beaucoup moins</b> d'éditeurs que de livres, donc le tableau des éditeurs d'un livre sera toujours <b>relativement réduit</b>,
alors qu'une maison d'édition peut éditer <b>des milliers de livres</b>.</p>

<div class="spacer"></div>

<p>C'est terminé pour ce tutoriel sur <b>les différentes relations entre documents avec MongoDB</b>. Cela peut vous servir de base afin de <b>gérer
la structure des documents</b> de vos collections et aussi à prendre en compte le fait que certains documents <b>sont susceptibles de contenir
plus de données que d'autres avec le temps</b>. Il est aussi possible d'organiser vos données d'<b>une autre façon</b>, c'est ce que nous allons voir dans
le chapitre suivant sur les <a href="structures_arbres.php">"Structures d'Arbres" >></a>.</p>

<?php

	include("footer.php");

?>
