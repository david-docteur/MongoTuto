<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../indexes.php">Indexes</a></li>
	<li class="active">Introduction</li>
</ul>
<p class="titre">[ Introduction ]</p>

<p><b>Les indexes</b> ? Oui, vous en avez souvent entendu parlé depuis le début du tutoriel et ce n'est pas pour rien
car c'est une <b>notion importante</b> avec MongoDB. Les indexes permettent <b>l'optimisation des requêtes de sélection</b>. Ceux-ci sont similaires aux indexes que l'on
peut trouver dans <b>les autres SGBD</b> et évitent à MongoDB de <b>scanner tous les documents</b> à chaque fois sur les champs les plus recherchés.
Les données sont stockées dans un <b>B Arbre ou arbre binaire de recherche</b> car ce type d'arbre permet d'accéder <b>rapidement</b> aux données qui sont ordonnées
par valeur.</p>

<p>MongoDB implémente les indexes afin de scanner <b>le plus petit nombre de documents</b> possible. Un indexe peut être <b>créé sur un champ</b> d'un document et/ou d'un sous-document
. En général, on utilisera les indexes sur les requêtes <b>les plus utilisées</b> et sur les fonctionnalités que les clients utilisent.</p>

<div class="spacer"></div>

<p>Les indexes se créent <b>comme ceci</b> :</p>

<pre>db.maCollection.ensureIndex("{ champ : 1 }")</pre>

<div class="small-spacer"></div>

<p>En bref, la forme du document passé en paramètre correspond <b>au champ et à l'ordre de tri</b>, 1 pour l'ordre <b>croissant</b> (par défaut), et -1 pour l'ordre <b>décroissant</b>.</p>

<div class="spacer"></div>

<p>A chaque <b>ajout</b>, <b>suppression</b> ou <b>modification</b> sur un champ indexé, mongo va perdre un peu de temps à synchroniser l'indexe, en revanche,
sur des requêtes de sélection souvent effectuée, celui-ci gagne énormement. Mongo va s'assurer alors de scanner <b>le plus petit nombre</b> de documents possible.</p>

<div class="spacer"></div>

<p>Cela peut s'averer très efficace sur des requêtes particulières :</p>
<p><b>_ résultats triés : comme les données sont déjà stockées par leur valeur (clef de l'indexe), cela évite une autre phase de tri</b></p>
<p><b>_ résultats couverts : lors d'une requête, lorsque les critères et la projection ne contiennent uniquement un ou des champs indexé, les performances
s'avèrent impressionnantes</b></p>

<div class="spacer"></div>

<p class="titre">[ Types d'Indexes ]</p>

<div class="spacer"></div>

<p class="small-titre">[ Défaut (_id) ]</p>
<p>Si celui-ci n'existe pas et n'est pas définit par votre application, celui-ci sera généré automatiquement par MongoDB, il est unique, obligatoire car permet d'éviter
les doublons</p>

<p class="small-titre">[ Simple champ ]</p>

<p>Lorsqu'une opération  s'effectuera sur ce champs, le requête de sélection sera optimisée</p>

<p class="small-titre">[ Indexe combiné]</p>

<p>Plusieurs champs, l'ordre est important, 1, -1 ordre</p>

<p class="small-titre">[ Indexes Multi-Clés ]</p>

<p>Créés pour les champ qui sont des tableaux, va créer un indexe pour chaque élément contenu dans le tableau.</p>

<p class="small-titre">[ Indexe GéoSpatial]</p>

<p>Fait pour des données de coordonnées géospatiales :
utilise les indexes 2d pour la géométrie planaire et les 2dsphere pour la géométrie sphérique.</p>

<p class="small-titre">[ Indexe de Texte ]</p>

<p>Recherche des chaînes de caractères et stocke les mots détectés, sauf les mots de liaisons tels que "the, a, or" etc ...</p>

<p class="small-titre">[ Indexes Hashés ]</p>

<p>Fait pour le sharding hashé, celui-ci va stocker des indexes hashés, ne fonctionne que par comparaison et égalité, comme le md5.</p>

<p class="small-titre">[ Anciens Indexes ]</p>

<p>Les indexes old-school de MongoDB avant la version 2.0</p>

<div class="spacer"></div>

<p>Voilà pour <b>l'introduction</b>, passons maintenant aux différents <a href="indexes_types.php">Types d'Indexes</a> afin de voir en détail ce que <b>chaque type</b>
offre comme possiblités.</p>

<?php

	include("footer.php");

?>
