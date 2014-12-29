<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../modelisation_donnees.php">Modélisation de Données</a></li>
	<li class="active">Gestion des Relations de Données</li>
</ul>

<p class="titre">[ Gestion des Relations de Données ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#flex">I) Schéma Flexible</a></p>
	<p class="elem"><a href="#struc">II) Structure des Documents</a></p>
	<p class="right"><a href="#ref">- a) La Relation par Référence</a></p>
	<p class="right"><a href="#imb">- b) La Relation Avec Documents Imbriqués</a></p>
	<p class="elem"><a href="#ecr">III) Atomicité des Opérations d'Ecriture</a></p>
	<p class="elem"><a href="#tai">IV) Augmentation de la Taille d'un Document</a></p>
	<p class="elem"><a href="#perf">V) Utilisation des Données et Performances</a></p>
</div>

<p>Vous voilà sur la page d'introduction de <b>gestion des relations des données</b>. Découvrons sans plus attendre de quoi il s'agit !
Comme vous le savez, MongoDB n'est pas un système de gestion de base de données <b>relationnel</b>, ce qui signifie que les données ne sont pas
sous forme de <b>tables</b> et de <b>tuples</b>, par conséquent, <b>les relations disparaîssent aussi</b>.</p>
<p>De plus, MongoDB, comme vous le savez, est <b>orienté Big Data</b>, et donc pour la plupart des déploiements, les données seront <b>stockées abondamment et en masse</b>, il vous faudra donc une gestion <b>claire</b> ainsi qu'une <b>modélisation adaptée</b> pour votre base de données. C'est ce que nous allons voir dans ce chapitre et <b>comment MongoDB répond à ce genre de problème</b>.</p>
<a name="flex"></a>

<div class="spacer"></div>

<div class="titre">I) [ Schéma Flexbile ]</div>

<p>Commençons par une <b>légère introduction</b> sur la gestion des relations de données. Il est essentiel de considérer <b>plusieurs principes</b> lorsque vous
choisissez le type de modélisation qui vous concerne, en passant par <b>l'accessibilité des informations jusqu'à l'optimisation des requêtes</b>.
MongoDB a <b>un schéma flexible</b>, ce qui signifie que vous pouvez ajouter plusieurs documents ayant <b>une structure différente</b> dans une même collection.
Par exemple, ajouter ces <b>deux documents</b> suivants dans une collection <b>"Cuisine"</b> serait possible :</p>

<div class="small-spacer"></div>

<pre>
{
	'_id' : 13,
	'nom' : 'Poireaux',
	'type' : 'Légume'
}


{
	'_id' : 27,
	'nom' : 'Banane',
	'type' : 'Fruit',
	'couleur' : 'jaune',
	'poids' : '20kg',
	'description' : 'Très grosse banane'
	
}
</pre>

<div class="small-spacer"></div>

<p>Ces deux documents vont pouvoir être ajoutés sans problèmes. <b>Essayez !</b> Créez votre collection <b>"Cuisine"</b> et vous verrez que les insertions
s'effectuent <b>avec succès</b>. Alors <b>le piège</b> ici est que tout cela a un côté très pratique, vous pouvez ajouter tout et n'importe quoi, surtout
lorsque vous souhaitez associer une collection à un ou des types d'objets mais, pour cela je vous invite <b>fortement</b> à bien <b>structurer vos collections</b>
et garder une <b>trace écrite</b> des champs que peut contenir une collection. Si vous oubliez de faire cela, vous allez <b>perdre beaucoup de temps</b> avant de retrouver
"quoi appartient à qui".</p>
<a name="struc"></a>

<div class="spacer"></div>

<div class="titre">II) [ Structure des Documents ]</div>

<p>Avec MongoDB, la structure des documents va déterminer <b>la performance et la fiabilité</b> de votre application.
On peut trouver <b>deux types majeurs</b> de relations qui sont : <b>relation par référence</b> et <b>relation avec documents imbriqués</b>.
Commençons par le premier type : <b>la relation par référence</b>.</p>
<a name="ref"></a>

<div class="spacer"></div>

<div class="small-titre">a) La Relation par Référence</div>

<p>La relation par référence consiste à <b>inclure une référence (un champ) d'un document vers un autre</b>. Bon, jusqu'ici, cela ressemble
beaucoup aux <b>clés étrangères</b> que l'on peut trouver dans une table SQL. Regardons un bref schéma pour voir à quoi cela ressemble :</p>

<div class="small-spacer"></div>

<a class="screenshot" href="/img/modelisation_donnees/introduction/intro_1.png" data-lightbox="intro_mod" title="Relation par Référence"><img src="/img/modelisation_donnees/introduction/intro_1.png" /></a>
<p><h6><b>Image 1.0</b> - Relation par Référence.</h6></p>

<div class="small-spacer"></div>

<p>On peut voir sur le schéma du dessus que l'<b>_id</b> de l'utilisateur se trouve dans ses documents de <b>Contact</b> et d'<b>Accès</b>.
Exactement comme le principe de <b>clé primaire/étrangère</b> en SQL. En bref, on dit que ce modèle est <b>normalisé</b>.</p>
<a name="imb"></a>

<div class="spacer"></div>

<div class="small-titre">b) La Relation Avec Documents Imbriqués</div>

<p>Ici, la relation <b>avec documents imbriqués</b> va vous permettre de <b>réduire le nombre de collections et de regrouper les données</b> en imbriquant
le document dans un sous-document. Pas de meilleure explication qu'avec un <b>exemple</b>, regardez :</p>

<div class="small-spacer"></div>

<a class="screenshot" href="/img/modelisation_donnees/introduction/intro_2.png" data-lightbox="intro_mod" title="Relation avec Documents Imbriqués"><img src="/img/modelisation_donnees/introduction/intro_2.png" /></a>
<p><h6><b>Image 1.1</b> - Relation avec Documents Imbriqués.</h6></p>

<div class="small-spacer"></div>

<p>Ici, on peut voir que le choix a été d'<b>imbriquer les documents de Contact et d'Accès</b> pour un utilisateur, dans chaque document Utilisateur respectif.
Si votre application va devoir interroger, très souvent, à la fois le <b>nom utilisateur</b>, son <b>e-mail</b>, mais aussi, de quel <b>groupe de travail</b> il fait partit,
alors le mieux sera de <b>regrouper les documents en un seul</b>, afin de réduire le nombre de requêtes à effectuer.
Ce modèle, quant à lui, est <b>dénormalisé</b>, on ne peut pas réaliser cela en SQL.</p>
<a name="ecr"></a>

<div class="spacer"></div>

<div class="titre">III) [ Atomicité des Opérations d'Ecriture ]</div>

<p>Par l'<b>atomicité des opérations d'écriture</b>, je veux dire qu'aucune écriture ne peut affecter <b>plus qu'un seul et unique document</b>, ou <b>plus qu'une seule
et unique collection</b>. Le modèle <b>dénormalisé</b> (documents imbriqués) combine toute les données pour <b>une entité</b>, par exemple ici pour l'utilisateur, on va pouvoir regrouper ses informations
de contact et ses informations d'accès au bâtiment où il travaille. Ce type de modélisation va <b>faciliter les opérations d'écriture atomiques</b>
vu <b>qu'une seule opération d'écriture</b> permet l'insertion ou la modification de données pour une entité.
En revanche, adopter la modélisation <b>normalisée</b> (par référence) va vous forcer à <b>effectuer plusieurs requêtes</b> qui ne seront pas atomiques collectivement.</p>
<a name="tai"></a>

<div class="spacer"></div>

<div class="titre">IV) [ Augmentation de la Taille d'un Document ]</div>

<p>Certaines mises à jour des informations, telles que l'ajout d'un champ ou l'insertion d'un élément dans un tableau, vont <b>augmenter la taille</b>
de votre document concerné. Si la taille maximale allouée à ce document est <b>dépassée</b>, ce qui est généralement <b>limité à 16Mo</b> à cause de BSON, MongoDB va déplacer le fichier sur le disque-dur.
Ceci peut-être un <b>facteur de décision</b> pour le choix d'un modèle de données plutôt normalisé ou dénormalisé.</p>
<a name="perf"></a>

<div class="spacer"></div>

<div class="titre">V) [ Utilisation des Données et Performances ]</div>

<p>Un dernier aspect à prendre en compte, vous allez devoir considérer la façon dont vos applications vont <b>utiliser vos bases de données</b>.
Par exemple, si vous avez développé une application qui n'utilise que des <b>informations récement ajoutées</b>, vous pouvez utiliser une <b>Collection Plafonnée</b>,
dont nous parlerons un peu plus tard. De plus, si votre application exécute de <b>nombreuses opérations de lecture</b>, vous pouvez ajouter des <b>Index</b>, dont nous parlerons
plus loin également, afin d'<b>optimiser les performances</b>.</p>

<div class="spacer"></div>

<p>C'est maintenant terminé pour ce qu'il en est de la petite <b>introduction à la modélisation de données</b>, vous avez une vue d'ensemble de ce qui est possible.
Vous voyez que le <b>schéma flexible</b> offre beaucoup d'avantages mais à condition d'être <b>bien précis</b> dans ce que l'on fait et de garder de <b>bons
diagrammes</b> afin de savoir quel <b>document</b> peut contenir tel ou tel champ.
Passons au chapitre suivant sur les <a href="relations_documents.php">"Relations entre Documents" >>.</a></p>

<?php

	include("footer.php");

?>
