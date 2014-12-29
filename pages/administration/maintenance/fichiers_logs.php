<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Fichiers Logs</li>
</ul>

<p class="titre">[ Fichiers Logs ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#ve">I) Vue d'Ensemble</a></p>
	<p class="elem"><a href="#rot">II) Rotation de Log avec MongoDB</a></p>
	<p class="elem"><a href="#sys">III) Rotation de Log Syslog</a></p>
</div>

<p></p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="titre">I) [ Vue d'Ensemble ]</p>

<p>La rotation des fichiers logs avec MongoDB archive le fichier log courant puis en démarre un nouveau. Pour faire cela, les instances
mongod et mongos renomment le fichier log en cours en ajoutant un timestamp UTC (GMT) au nom du fichier, au format ISODate. Il ouvre ensuite un nouveau
fichier log, ferme l'ancien fichier et envoie toutes les dernières entrées de log vers ce nouveau fichier.
Cette opération est déclenchée par l'appel de la commande logRotate ou quand un processus mongod ou mongos reçoit un signal SIGUSR1 depuis le système
d'exploitation.
Alternativement, vous devrez configurer mongod pour qu'il envoie des données de log à syslog. Dans ce cas, vous pouvez utiliser d'autres outils alternatifs
de rotation de log.</p>
<a name="rot"></a>

<div class="spacer"></div>

<p class="titre">II) [ Rotation de Log avec MongoDB ]</p>

<p>Les étapes suivantes vont créer et pivoter un fichier log :
1) Démarrez une instance mongod en mode verbose, en ajoutant les informations après celles existantes, avec le fichier log suivant :</p>

<pre>mongod -v --logpath /var/log/mongodb/server1.log --logappend</pre>

<p>Dans un terminal séparé, listez les fichiers correspondants :</p>

<pre>ls /var/log/mongodb/server1.log*</pre>

<p>Vous obtenez donc le résultat suivant :</p>

<pre>server1.log</pre>

<p>3) Pivotez le fichier log en utilisant l'une des méthodes suivantes :
	- depuis un shell mongo, utilisez la commande logRotate depuis la base de données admin.</p>

<pre>
use admin
db.runCommand( { logRotate : 1 } )
</pre>

<div class="spacer"></div>

<p>C'est la seule méthode pour pivoter des fichiers logs sur les systèmes Windows.
	- Pour les systèmes Linux, pivotez les logs en utilisant la commande suivante :</p>

<pre>kill -SIGUSR1 "mongod process id"</pre>

<p>4) Listez les fichiers correspondants à nouveau :</p>

<pre>ls /var/log/mongodb/server1.log*</pre>

<p>Vous obtenez un résultat similaire au suivant, les timestamps seront différents :</p>

<pre>server1.log server1.log.2011-11-24T23-30-00</pre>

<p>L'exemple indique une rotation de log effectuée exactement à 23h30 le 24 Novembre 2011 UTC, qui est le temps local définit par la timezone.
Le fichier log original est celui avec un timestamp. Le nouveau est donc server1.log.
Si vous exécutez une autre commande logRotate une heure plus tard, alors un nouveau fichier va apparaître lors du listing de fichiers correspondants :</p>

<pre>server1.log server1.log.2011-11-24T23-30-00 server1.log.2011-11-25T00-30-00</pre>

<p>Cette opération ne modifie pas le fichier server1.log.2011-11-24T23-30-00 créé plus tôt. Le fichier server1.log.2011-11-25T00-30-00
est l'ancien server1.log, renommé. Le fichier server1.log est maintenant un nouveau fichier vide qui reçoit toutes les données de log.</p>
<a name="sys"></a>

<div class="spacer"></div>

<p class="titre">III) [ Rotation de Log Syslog ]</p>

<p>Depuis la version 2.2, pour configurer mongod afin qu'il envoie des données de log à syslog plutôt que d'écrire les données dans un fichier, utilisz la
procédure suivante :
1) Démarrez mongod avec l'option syslog.
2) Stockez et pivotez la sortie du log en utilisant le mécanisme de rotation de log de votre système.
</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Vous ne pouvez pas utiliser syslog avec logpath.
</div>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="gerer_journaling.php">"Gérer le Journaling" >></a>.</p>

<?php

	include("footer.php");

?>
