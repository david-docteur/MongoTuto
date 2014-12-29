<?php

set_include_path("../");
include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Bienvenue</li>
</ul>
<p class="titre">Bienvenue sur MongoTuto.com !</p>


<p><b>Bonjour</b> (ou Bonsoir) à tous et bienvenue sur <b>MongoTuto.com</b>, la <b>première</b> et <b>unique</b> référence francophone sur MongoDB ... rien que pour les <b>francophones</b> !</p>
<p>Par francophone j'entends bien sûr <b>la France</b>, <b>le Canada</b>, <b>le Maroc</b>, <b>la Belgique</b>, <b>la Suisse</b> ainsi que d'<b>autres</b> ... la liste n'est pas exhaustive.
Vous allez pouvoir apprendre, ici, tout l'essentiel de <b>MongoDB</b>, en Français, interprété depuis la <b>documentation officielle</b> (Anglais), <b>la plus récente possible</b>.</p>

<div class="spacer"></div>

<div class="titre">[ Présentation ]</div>
<p>Cela vous tente ? Un nouveau <b>Système de Gestion de Bases de Données ?</b> Vous êtes à la bonne place ! Vous allez apprendre à utiliser <b>MongoDB</b>, découvrir les différentes méthodes d'interrogation et de manipulation des données,
avoir une vue d'ensemble des caractéristiques qui le rendent unique et bien sûr, comment MongoDB est orienté <b>"Big Data"</b>.</p>
<p>Par exemple, pourquoi plusieurs entreprises comme <b>SAP</b>, <b>eBay</b>, <b>GitHub</b>, <b>Foursquare</b> ou encore <b>Cisco</b> implémentent MongoDB, je pense
qu'il est inutile de présenter ces géants de l'IT. <b>Big Data</b> ? Oui ! Mais <b>MongoDB</b> est aussi utilisable pour les <b>petites</b> bases de données bien entendu :)</p>

<div class="small-spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Ce tutoriel n'est pas officiel mais va tenter de vous apporter tout ce qu'il y a à savoir sur MongoDB, 
	avec une tentative de reconversion pour ceux qui viennent de langage SQL utilisant des SGBDR tels que MySQL, PostGreSql etc ...
</div>

<div class="small-spacer"></div>

<p>Tout d'abord, une petite définition ici me paraît essentielle, <b>qu'est-ce qu'un Système de Gestion de Base de Données</b> ?</p>
<p><b>Un Système de Gestion de Base de Données (ou SGBD) est un logiciel système qui permet de stocker et de partager les informations d'une base de données
afin de garantir l'intégrité, la qualité et la confidentialité des informations.</b></p>
<p>Prenons l'exemple d'un site web en ligne qui vend des <b>billets
de concerts</b>, celui-ci va utiliser un <b>SGBD</b> afin de stocker les informations des produits (les billets majoritairement), les clients, les
transactions (achats, commandes etc ...) et probablement d'autres informations essentielles. Voilà, c'est ça un Système de Gestion de Base de Données.
Vous allez aussi pouvoir <b>interroger et manipuler les données</b> en effectuant des opérations de <b>sélection, d'insertion, de modification et de suppression</b>.
Allez, assez discuté, <b>passons à la suite</b> !</p>

<div class="spacer"></div>

<div class="titre">[ Plan ]</div>

<p>Regardons maintenant ensemble ce que vous allez pouvoir découvrir tout à long de ce tutoriel, la liste suivante correspond aux différentes rubriques disponibles avec un bref descriptif :</p>

<div class="small-spacer"></div>

<ul>
	<li><p class="un-list">Bienvenue</p><p>Tout simplement cette page que vous êtes en train de lire, l'admin qui vous souhaite la bienvenue et une bonne découverte !</p></li><br />
	<li><p class="un-list">Introduction</p><p class="first-line">Donne une brève description de MongoDB, ses origines, les principes et fonctionnalités.</p></li><br />
	<li><p class="un-list">Installation</p><p class="first-line">Ce chapitre détaille les possibilités d'installations pour différents systèmes d'exploitation tels que GNU/Linux, Microsoft Windows ou encore Mac OS.</p></li><br />
	<li><p class="un-list">Opérations CRUD</p><p class="first-line">Descriptions et exemples des opérations possibles sur les bases de données et les collections avec MongoDB.</p></li><br />
	<li><p class="un-list">Modélisations des Données</p><p class="first-line">Ce chapitre va surtout être orienté sur la façon dont vous allez organiser vos données et la structure que vous allez implémenter.</p></li><br />
	<li><p class="un-list">Aggrégations</p><p class="first-line">Les aggrégations vont vous permettre de réaliser des opérations plus précises sur l'ensemble des données telles que GROUP BY, COUNT, HAVING et bien d'autres ...</p></li><br />
	<li><p class="un-list">Indexes</p><p class="first-line">Que sont les indexes et comment permettent t'ils l'optimisation de vos requêtes ?</p></li><br />
	<li><p class="un-list">Réplication</p><p class="first-line">La réplication est l'art de copier une même base de données sur plusieurs machines afin de garantir la redondance des informations.</p></li><br />
	<li><p class="un-list">Sharding</p><p class="first-line">Le Sharding, probablement la fonctionnalité la plus importante de MongoDB, permet de segmenter la même base de données sur différents serveurs si vous avez un volume important d'informations à stocker et à gérer.</p></li><br />
	<li><p class="un-list">Administration</p><p class="first-line">L'administration est un chapitre important portant sur la configuration, la sauvegarde, la restauration, les diagnostiques ainsi que les différents outils de MongoDB.</p></li><br />
	<li><p class="un-list">Sécurité</p><p class="first-line">Très important, surtout quand vous gérez du Big Data, MongoDB prend la sécurité des données très au sérieux. Vous allez, ici, apprendre à gérer vos filtres réseaux ainsi que les privilièges nécessaires aux différents utilisateurs.</p></li><br />
	<li><p class="un-list">Exemples de code</p><p class="first-line">Sur cette page, vous allez trouver de multiples exemples pour différents langages supportés par MongoDB. Parce-que c'est toujours plus compréhensible avec des exemples !</p></li><br />
	<li><p class="un-list">Outils</p><p class="first-line">Cette rubrique va vous donner accès à des liens de téléchargements et de descriptions de différents outils utiles pour MongoDB.</p></li><br />
</ul>

<div class="spacer"></div>

<div class="titre">[ Navigation ]</div>
<p>Vous pouvez utiliser la navigation au fur et à mesure avec un <b>breadcrumb</b> en haut de chaque page, ou alors, utiliser le <b>menu de gauche</b> pour aller vers les points qui vous intéressent.
Si vous remarquez un <b>lien mort</b>, n'hésitez pas à me <a href="contact.php">contacter</a> via le <b>formulaire</b> de la page de contact.</p>

<div class="small-spacer"></div>

<p><b>Vous êtes prêt à vous lancer ?</b> Passons donc au chapitre suivant <a href="introduction.php">Introduction >></a></p>
<p>Ce chapitre vous montrera <b>les possibilités et avantages</b> qu'offre MongoDB ainsi que de <b>multiples informations</b> concernant ce SGBD.</p>
<p>Bonne lecture à tous, en espérant que vous allez vous <b>amuser</b> ! :)</p>

<?php

	include("footer.php");

?>
