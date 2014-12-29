<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../scripting.php">Scripting</a></li>
	<li class="active">Javascript Côté Serveur</li>
</ul>

<p class="titre">[ Javascript Côté Serveur ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#ve">I) Vue d'Ensemble</a></p>
	<p class="right"><a href="#js">- a) JavaScript dans MongoDB</a></p>
	<p class="elem"><a href="#exec">II) Exécuter des Fichiers .js Via un Shell mongo sur un Serveur</a></p>
</div>

<p>Depuis la version 2.4, le moteur JavaScript V8 est devenu le moteur par défaut. Celui-ci autorise plusieurs opérations JavaScript à s'exécuter en même temps.
Avant la version 2.4, les opérations MongoDB qui nécessitaient l'interpréteur JavaScript devaient acquérir un verrou, et une simple instance mongod
ne pouvait exécuter qu'une seule opération JavaScript à la fois.</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="titre">I) [ Vue d'Ensemble ]</p>

<p>MongoDB supporte l'exécution de code JavaScript pour les opérations côté-serveur suivantes :

- mapReduce et la méthode de shell db.collection.mapReduce().
- la commande eval et sa méthode de shell, db.eval().
- l'opérateur $where
- les fichiers .js exécutés via une instance mongo sur le serveur.</p>
<a name="js"></a>

<div class="spacer"></div>

<p class="small-titre">a) JavaScript dans MongoDB</p>

<p>Bien que les opérations ci-dessus utilisent JavaScript, la plupart des interractions avec MongoDB n'utilisent pas JavaScript mais utilisent
un driver dans le langage de programmation de l'application. Vous pouvez désactiver toutes les exécutions JavaScript côté serveur en passant le paramètre
--noscripting en ligne de commande ou en définissant noscripting dans un fichier de configuration.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">II) [ Exécuter des Fichiers .js Via un Shell mongo sur un Serveur ]</p>

<p>Vous pouvez exécuter un fichier .js en utilisant un shell mongo sur le serveur. Ceci est une bonne technique pour effectuer des tâches administratives en groupe.
Lorsque vous exécutez un shell mongo sur un serveur, en vous connectant via l'interface localhost, la connexion est rapide avec une faible latence.
Les méthodes fournit dans le shell mongo ne sont pas disponibles dans les fichiers JavaScript car elles ne correspondent pas à du JavaScript valide.
Le tableau suivant associe les méthodes les plus communes à leur équivalent JavaScript :</p>

<div class="spacer"></div>

<table>
	<tr>
		<th>Commandes Shell</th>
		<th>Equivalents JavaScript</th>
	</tr>
	<tr>
		<td>show dbs, show databases</td>
		<td>db.adminCommand('listDatabases')
</td>
	</tr>
	<tr>
		<td>use "db"</td>
		<td>db = db.getSiblingDB('db')</td>
	</tr>
	<tr>
		<td>show collections</td>
		<td>db.getCollectionNames()</td>
	</tr>
	<tr>
		<td>show users</td>
		<td>db.system.users.find()</td>
	</tr>
	<tr>
		<td>show log "logname"</td>
		<td>db.adminCommand( { 'getLog' : 'logname' } )</td>
	</tr>
	<tr>
		<td>show logs</td>
		<td>db.adminCommand( { 'getLog' : '*' } )</td>
	</tr>
	<tr>
		<td>it</td>
		<td>cursor = db.collection.find() if( cursor.hasNext() ) { cursor.next(); }</td>
	</tr>
</table>

<div class="spacer"></div>

<p>La suite va concerner les <a href="types_donnees_shell.php">"Types de Données dans le Shell mongo" >></a>.</p>

<?php

	include("footer.php");

?>
