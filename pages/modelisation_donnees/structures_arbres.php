<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../modelisation_donnees.php">Modélisations de Données</a></li>
	<li class="active">Structures d'Arbres</li>
</ul>

<p class="titre">[ Structures d'Arbres ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#int">I) Introduction</a></p>
	<p class="elem"><a href="#par">II) Référence Parent</a></p>
	<p class="elem"><a href="#enf">III) Référence Enfant</a></p>
	<p class="elem"><a href="#anc">IV) Tableau d'Ancêtres</a></p>
	<p class="elem"><a href="#mat">V) Chemins Matérialisés</a>
	<p class="elem"><a href="#imb">VI) Ensembles Imbriqués</a></p>
</div>

<p>Encore une autre façon de <b>modéliser les données</b> avec MongoDB, <b>les structures d'arbres</b>. Chaque structure représentera <b>un arbre</b>, qui sera un <b>ensemble de noeuds</b>.
J'expliquerai brièvement ce qu'est un arbre pour ceux qui n'ont pas de notions. Ce type de structure peut être utile pour certaines applications,
probablement pour <b>un arbre généalogique</b> par exemple. Il y aura <b>plusieurs implémentations possibles</b> que nous allons voir ci-dessous.</p>
<a name="int"></a>

<div class="spacer"></div>

<p class="titre">I) [ Introduction ]</p>

<p>Allez, on se lance ! Pour <b>organiser vos données</b> avec une structure d'arbre, vous avez <b>différents types</b>. Pour ceux qui n'ont aucun notion sur
les arbres, on expliquera rapidement qu'un arbre <b>est une structure composée de noeuds</b>. Le premier noeud d'un arbre est
<b>la racine</b>. Le noeud suivant <b>a pour père</b> la racine, et peut avoir <b>un ou plusieurs enfants</b> comme sur le schéma suivant :</p>

<div class="small-spacer"></div>

<a class="screenshot" href="/img/modelisation_donnees/structures_arbres/struc_1.png" data-lightbox="struc_arbre" title="Structure Basique d'Arbre"><img src="/img/modelisation_donnees/structures_arbres/struc_1.png" /></a>
<p><h6><b>Image 1.0</b> - Structure Basique d'Arbre.</h6></p>

<div class="small-spacer"></div>

<p>En bref, pour cet arbre, qui servira pour <b>tous les exemples du tutoriel</b>, les propriétés suivantes sont <b>vraies</b> :</p>
<p>_ Le noeud <b>"Books"</b> est la <b>racine</b> de l'arbre</p>
<p>_ Le père du noeud <b>"Programming"</b> est le noeud (racine) <b>"Books"</b></p>
<p>_ Le noeud <b>"Databases"</b> a deux enfants <b>"MongoDB"</b> et <b>"dbm"</b></p>
<p>_ Le noeud <b>"dbm"</b> n'a aucun enfant</p>
<p>_ Chaque noeud représente un <b>document BSON</b>.</p>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Je ne ferais pas de cours sur les arbres en général, j'explique très brièvement mais sachez que l'on peut
	aller beaucoup plus loin sur ce sujet.
</div>
<a name="par"></a>

<div class="spacer"></div>

<p class="titre">II) [ Référence Parent ]</p>

<p>Dans ce type de structure, on insère la référence <b>"_id"</b> du père du noeud dans le noeud courant qui représente <b>notre document</b>. Par exemple :</p>

<pre>
	db.categories.insert( { _id: "MongoDB", parent: "Databases" } )
	db.categories.insert( { _id: "dbm", parent: "Databases" } )
	db.categories.insert( { _id: "Databases", parent: "Programming" } )
	db.categories.insert( { _id: "Languages", parent: "Programming" } )
	db.categories.insert( { _id: "Programming", parent: "Books" } )
	db.categories.insert( { _id: "Books", parent: null } )
</pre>

<div class="small-spacer"></div>

<p>Nous voyons que <b>le père de la racine est "null"</b> car la racine est le tout <b>premier élément</b> de l'arbre, et de ce fait, <b>n'a aucun père</b>.
Lorsque vous allez vouloir récupérer le père du noeud, la méthode suivante vous sera utile :</p>

<pre>
db.categories.findOne( { _id: "MongoDB" } ).parent
</pre>

<div class="small-spacer"></div>

<p>Vous pouvez <b>activer l'optimisation</b> de recherche par parent avec la fonction <b>ensureIndex()</b>, que nous verrons dans le chapitre sur les <b>index</b> un peu plus tard.</p>

<pre>db.categories.ensureIndex( { parent: 1 } )</pre>

<div class="small-spacer"></div>

<p>De plus, on peut même interroger MongoDB sur le père pour <b>obtenir le ou les fils</b></p>

<pre>db.categories.find( { parent: "Databases" } )</pre>

<div class="small-spacer"></div>

<p>La <b>référence parent</b> est utile pour stocker des arbres mais <b>nécessite plusieurs requêtes</b> afin de récupérer des sous-arbres.</p>
<a name="enf"></a>

<div class="spacer"></div>

<p class="titre">III) [ Référence Enfant ]</p>

<p>A l'inverse ici avec <b>la référence enfant</b>, chaque document correspondra à notre noeud et contiendra <b>un tableau des "_id" de ses fils</b> :</p>

<pre>
	db.categories.insert( { _id: "MongoDB", enfants: [] } )
	db.categories.insert( { _id: "dbm", enfants: [] } )
	db.categories.insert( { _id: "Databases", enfants: [ "MongoDB", "dbm" ] } )
	db.categories.insert( { _id: "Languages", enfants: [] } )
	db.categories.insert( { _id: "Programming", enfants: [ "Databases", "Languages" ] } )
	db.categories.insert( { _id: "Books", enfants: [ "Programming" ] } )
</pre>

<div class="small-spacer"></div>

<p>De même que dans l'exemple précédent, pour <b>récupérer les enfants</b> de manière rapide et efficace :</p>

<pre>db.categories.findOne( { _id: "Databases" } ).enfants</pre>

<div class="small-spacer"></div>

<p>Ici encore, on va probablement vouloir <b>optimiser les requêtes de sélection</b> par enfant avec un index :</p>

<pre>db.categories.ensureIndex( { enfants: 1 } )</pre>

<div class="small-spacer"></div>

<p>Enfin, pour retrouver le <b>noeud père</b> avec l'un de ses <b>noeud fils</b> :</p>

<pre>db.categories.find( { enfants: "MongoDB" } )</pre>

<div class="small-spacer"></div>

<p>La structure <b>avec référence enfant</b> est efficace pour stocker des arbres tant qu'aucune opération sur les sous-arbres est requise.
Ce qui est <b>différent</b> de la référence parent.</p>
<a name="anc"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Tableau d'Ancêtres ]</p>

<p>Comme vous pouvez vous en doutez, la structure <b>avec tableau d'ancêtres</b> va permettre de stocker, dans chaque noeud, <b>un tableau avec tous les "_id"</b> de ses parents.
L'exemple suivant permet de voir un peu comment cela fonctionne :</p>

<pre>
db.categories.insert( { _id: "MongoDB", ancetres: ["Books", "Programming", "Databases"], parent: "Databases" } )
db.categories.insert( { _id: "dbm", ancetres: [ "Books", "Programming", "Databases" ], parent: "Databases" } )
db.categories.insert( { _id: "Databases", ancetres: [ "Books", "Programming" ], parent: "Programming" } )
db.categories.insert( { _id: "Languages", ancetres: [ "Books", "Programming" ], parent: "Programming" } )
db.categories.insert( { _id: "Programming", ancetres: [ "Books" ], parent: "Books" } )
db.categories.insert( { _id: "Books", ancetres: [ ], parent: null } )
</pre>

<div class="spacer"></div>

<p>Ensuite, <b>similairement aux cas précédents</b>, pour <b>retrouver les ancêtres et/ou le chemin d'un noeud</b>, nous allons pouvoir effectuer la requête suivante :</p>

<pre>db.categories.findOne( { _id: "MongoDB" } ).ancetres</pre>

<div class="small-spacer"></div>

<p>On active l'optimisation pour la <b>recherche par ancêtres</b> avec un index :</p>

<pre>db.categories.ensureIndex( { ancetres: 1 } )</pre>

<div class="small-spacer"></div>

<p>On peut aussi <b>retrouver tous les descendants</b> du noeud en interrogeant MongoDB sur le champ <b>"ancetres"</b> :</p>

<pre>db.categories.find( { ancetres: "Programming" } )</pre>

<div class="small-spacer"></div>

<p>Ce type de structure est <b>très rapide</b> pour trouver les pères et fils du noeud, et donc un bon choix pour travailler avec des sous-arbres.
En revanche, cette structure est <b>un peu plus lente</b> que celle des chemins matérialisés, que nous allons voir dans le paragraphe précédent, mais est <b>un peu
plus simple</b> à utiliser.</p>
<a name="mat"></a>

<div class="spacer"></div>

<p class="titre">V) [ Chemins Matérialisés ]</p>

<p>Maintenant, nous allons parler des <b>chemins matérialisés</b>. Ce type de structure va stocker chaque noeud dans un document ainsi que tous les <b>"_id"</b> des parents
sous la forme d'<b>une chaîne de caractères</b>.
L'avantage va être de pouvoir travailler avec les <b>fonctions habituelles</b> sur les chaînes de caractères, mais aussi avec les <b>expressions régulières (regex)</b>.
Vous devez utiliser une virgule comme <b>séparateur entre chaque parent/chemin</b> et bien sûr, <b>respecter l'ordre de l'arbre</b>.
Voyons un petit exemple :</p>

<div class="small-spacer"></div>

<pre>
	db.categories.insert( { _id: "Books", path: null } )
	db.categories.insert( { _id: "Programming", path: ",Books," } )
	db.categories.insert( { _id: "Databases", path: ",Books,Programming," } )
	db.categories.insert( { _id: "Languages", path: ",Books,Programming," } )
	db.categories.insert( { _id: "MongoDB", path: ",Books,Programming,Databases," } )
	db.categories.insert( { _id: "dbm", path: ",Books,Programming,Databases," } ))
</pre>

<div class="small-spacer"></div>

<p>Vous pouvez retrouver <b>l'arbre entier</b> en sélectionnant tous les documents ainsi que de les <b>trier par ordre croissant de chemin</b> comme dans la requête suivante :</p>

<pre>db.categories.find().sort( { path: 1 } )</pre>

<div class="small-spacer"></div>

<p>Vous pouvez même <b>retrouver les descendants</b> de <b>"Programming"</b> en utilisant l'expression régulière comme ceci :</p>

<pre>db.categories.find( { path: /,Programming,/ } )</pre>

<div class="small-spacer"></div>

<p>Afin de retrouver tous les descendants de <b>"Books"</b> qui est au sommet de l'arbre, <b>la racine</b>, on procède comme cela :</p>

<pre>db.categories.find( { path: /^,Books,/ } )</pre>

<div class="small-spacer"></div>

<p>Ici, le caractère <b>"^"</b> signifque que l'on recherche <b>au début de l'expression</b>.
On peut enfin finir par créer un index afin <b>d'optimiser les requêtes effectuées</b> sur le champ <b>"path"</b> :</p>

<pre>db.categories.ensureIndex( { path: 1 } )</pre>
<a name="imb"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Ensembles Imbriqués ]</p>

<p>Pour cet exemple, on change de schéma et on passe à <b>l'image 1.1</b> ci-dessous.
Le principe de cette structure est que <b>l'application va parcourir l'arbre deux fois</b> : une fois <b>du début à la fin</b> et ensuite <b>de la fin pour revenir au début</b>.
Ici on va stocker l'<b>_id</b> du parent du noeud, mais aussi <b>l'arrêt initial à gauche</b> et <b>le second arrêt à droite</b>.</p>

<div class="small-spacer"></div>

<a class="screenshot" href="/img/modelisation_donnees/structures_arbres/struc_2.png" data-lightbox="struc_arbre" title="Structure d'Ensembles Imbriqués"><img src="/img/modelisation_donnees/structures_arbres/struc_2.png" /></a>
<p><h6><b>Image 1.1</b> - Structure d'Ensembles Imbriqués.</h6></p>

<div class="small-spacer"></div>

<p>Regardons on peut comment cela fonctionne :</p>

<pre>
	db.categories.insert( { _id: "Books", parent: 0, gauche: 1, droite: 12 } )
	db.categories.insert( { _id: "Programming", parent: "Books", gauche: 2, droite: 11 } )
	db.categories.insert( { _id: "Languages", parent: "Programming", gauche: 3, droite: 4 } )
	db.categories.insert( { _id: "Databases", parent: "Programming", gauche: 5, droite: 10 } )
	db.categories.insert( { _id: "MongoDB", parent: "Databases", gauche: 6, droite: 7 } )
	db.categories.insert( { _id: "dbm", parent: "Databases", gauche: 8, droite: 9 } )
</pre>

<div class="small-spacer"></div>

<p>Au premier tour, l'application va parcourir l'arbre en partant <b>du plus à gauche vers le plus à droite</b>,
puis, au second tour, l'application va encore parcourir le tableau mais dans le sens inverse, <b>du noeud le plus à droite puis remonter
vers le noeud le plus à gauche</b>.</p>

<div class="small-spacer"></div>

<p>Maintenant, vous allez pouvoir récupérer <b>les descendants d'un noeud</b> avec le code suivant :</p>

<pre>
	var databaseCategory = db.categories.findOne( { _id: "Databases" } );
	db.categories.find( { gauche: { $gt: databaseCategory.gauche }, droite: { $lt: databaseCategory.droite } } );}
</pre>

<div class="small-spacer"></div>

<p>Ce type de structure est <b>très efficace</b> pour trouver des <b>sous-arbres</b> mais est <b>inefficace</b> pour modifier la <b>structure</b> de l'arbre.</p>

<div class="spacer"></div>

<p>J'en ai terminé avec <b>les structures d'arbres</b>, encore une fois, je pense que l'on peut <b>aller plus loin</b> mais cela reste une base pour vous 
présenter <b>les grands principes</b> et vous donner des <b>idées</b>. Voilà, si vous avez des questions, n'hésitez-pas à me <a href="../contact.php">"contacter"</a>.
Je vous invite également à trouver <b>un tutoriel sur les arbres</b> afin de voir un peu les possibilités qui vous sont offertes.
Chapitre suivant : Les <a href="modeles_specifiques.php">"Contextes Spécifiques d'Applications" >></a>.</p>

<?php

	include("footer.php");

?>
