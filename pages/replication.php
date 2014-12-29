<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Réplication</li>
</ul>
<p class="titre">[ Réplication ]</p>

<p>Ahhh nous y voilà ! Bienvenue sur la page de <b>réplication avec MongoDB</b>, cette page va vous expliquer comment arriver à <b>une sauvegarde efficace</b> ou même, <b>relayer
les opérations</b> en cas de <b>fail-over</b>.</p>
<p>Un <b>replica set</b>, ou <b>ensemble de répliques</b>, est <b>un ensemble de processus mongod partageant le même ensemble de données</b>. Un ensemble de répliques
fournit <b>la redondance des données</b> ainsi <b>qu'une haute disponibilité</b> de celles-ci, ce qui constitue la base pour <b>le déployment d'un système de production</b>.</p>
<p>Ce chapitre va apporter <b>une introduction</b> sur les replica sets mais aussi <b>décrire les composants et architectures</b> différentes.</p>

<div class="spacer"></div>

<p><a href="replication/introduction.php">[ Introduction aux Replica Sets ]</a></p>

<div class="spacer"></div>

<p class="small-titre">Concepts</p>
<p><a href="replication/concepts_membres.php">[ Membres du Replica Set ]</a></p>
<p><a href="replication/concepts_deploiement.php">[ Architectures de Déploiement de Replica Set ]</a></p>
<p><a href="replication/concepts_disponibilite.php">[ Haute-Disponibilité du Replica Set ]</a></p>
<p><a href="replication/concepts_semantiques.php">[ Sémantiques de Lecture et d'Ecriture du Replica Set ]</a></p>
<p><a href="replication/concepts_processus.php">[ Processus de Réplication ]</a></p>
<p><a href="replication/concepts_masterslave.php">[ Réplication Master-Slave ]</a></p>

<div class="spacer"></div>

<p class="small-titre">Tutoriaux</p>
<p><a href="replication/tutoriaux_deploiement.php">[ Déploiement de Replica Set ]</a></p>
<p><a href="replication/tutoriaux_configuration.php">[ Configuration de Membre ]</a></p>
<p><a href="replication/tutoriaux_maintenance.php">[ Maintenance de Replica Set ]</a></p>
<p><a href="replication/tutoriaux_diagnostics.php">[ Diagnostiques du Replica Set ]</a></p>

<div class="spacer"></div>

<p>Inutile de vous dire qu'ici il y a <b>beaucoup plus de lecture et de pratique</b> que les chapitres précédents mais cette fonctionnalité de MongoDB
est probablement <b>l'une des plus importantes</b>. Le <b>sharding</b>, le chapitre suivant <a href="sharding.php">"Sharding" >></a> va être assez lourd
aussi à digérer. Beaucoup plus lourd vu que vous devez <b>impérativement</b> connaître un minimum <b>les ensembles de répliques</b> avant d'attaquer ce chapitre.
Le sharding repose essentiellement sur ces ensembles et permet de <b>fragmenter/partager</b> une collection contenant énormement d'informations.
Lorsque vous arrivez à ce stade, c'est que vous devez probablement avoir <b>une immense quantité d'informations</b>. <b>Bon courage !</b></p>

<?php

	include("footer.php");

?>
