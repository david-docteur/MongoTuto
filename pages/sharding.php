<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Sharding</li>
</ul>

<p class="titre">[ Sharding ]</p>

<p>Vous avez <b>un important volume de données</b> à gérer et vous souhaitez <b>utiliser la puissance de plusieurs machines</b> ? <b>Le Sharding est exactement ce que vous cherchez</b> !
Le <b>sharding</b>, ou <b>la fragmentation</b> des informations, <b>est le processus qui va vous permettre de stocker vos données sur plusieurs serveurs</b>.
Concrètement, le volume de vos données devient <b>trop important</b> (nombre de commandes clients qui explose, des interractions entre utilisateurs qui
s'intensifient, des transactions en tous genres qui se multiplient etc ...), à tel point <b>qu'une seule machine, même avec des ressources prévues à cet effet, ne puisse
plus tenir la cadence</b> ou alors, <b>le débit de lecture/écriture n'est plus assez élevé</b>. Pour répondre à cette demande, il suffit simplement <b>d'ajouter
des machines à votre cluster</b>. Je vous invite à parcourir les différentes rubriques de cette partie du tutoriel, n'hésitez pas à <b>lire les différents
concepts</b> du sharding afin de mieux comprendre comment tout se passe en interne. <b>C'est à vous !</b></p>

<div class="spacer"></div>

<p><a href="sharding/introduction.php">[ Introduction au Sharding ]</a></p>

<div class="spacer"></div>

<p class="small-titre">Concepts</p>

<p><a href="sharding/concepts_composants.php">[ Composants d'un Cluster Partagé ]</a></p>
<p><a href="sharding/concepts_architectures.php">[ Architectures de Cluster Partagé ]</a></p>
<p><a href="sharding/concepts_comportement.php">[ Comportement de Cluster Partagé ]</a></p>
<p><a href="sharding/concepts_mecaniques.php">[ Mécaniques de Cluster Partagé ]</a></p>

<div class="spacer"></div>

<p class="small-titre">Tutoriaux</p>

<p><a href="sharding/tutoriaux_deploiement.php">[ Déploiement de Cluster Partagé ]</a></p>
<p><a href="sharding/tutoriaux_maintenance.php">[ Maintenance de Cluster Partagé ]</a></p>
<p><a href="sharding/tutoriaux_gestion.php">[ Gestion des Données avec un Cluster Partagé ]</a></p>
<p><a href="sharding/tutoriaux_diagnostique.php">[ Diagnostique de Clusters Partagés ]</a></p>

<div class="spacer"></div>

<p>Exactement comme je l'ai cité à la fin du chapitre sur <b>la réplication</b> avec MongoDB, le sharding est <b>une partie lourde à gérer et maintenir</b>
(et surtout à traduire, croyez-moi). Pas plus de commentaires là-dessus, bien sûr, si vous avez <b>des questions</b> ou alors si vous <b>rencontrez
des difficultés</b>, <a href="contact.php">"contactez-moi"</a>. Vous avez maintenant, en complément, les <b>deux derniers chapitres</b> portant
sur <b>l'administration</b> de MongoDB ainsi que tout ce qui est aspect <b>sécurité</b> des données. Ces deux chapitres vous seront essentiels afin de vous aider
à <b>administrer et sécuriser</b> vos environnements. Je vous invite à découvrir et à revenir sur le chapitre sur l'<a href="administration.php">"Administration" >></a> avec MongoDB.</p>

<?php

	include("footer.php");

?>
