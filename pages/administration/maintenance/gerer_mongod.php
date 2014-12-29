<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Gérer les Processus mongod</li>
</ul>
<p class="titre">[ Gérer les Processus mongod ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#dema">I) Démarrer mongod</a></p>
	<p class="right"><a href="#repe">- a) Spécifier un Répertoire de Données</a></p>
	<p class="right"><a href="#tcp">- b) Spécifier un Port TCP</a></p>
	<p class="right"><a href="#demo">- c) Démarrer mongod en tant que Démon</a></p>
	<p class="elem"><a href="#arre">II) Arrêter mongod</a></p>
	<p class="right"><a href="#repl">- a) Arrêter mongod avec les Replica Sets</a></p>
	<p class="right"><a href="#sign">- b) Envoyer un Signal Unix INT ou TERM</a></p>
</div>

<p>MongoDB s'exécute comme un programme standart. Vous pouvez démarrer MongoDB en ligne de commande en utilisant la commande mongod et en spécifiant
des options. Vous souhaitez avoir la liste des options disponibles ? La voici : <a href="http://docs.mongodb.org/manual/reference/program/mongod" target="_blank">Liste des Options mongod</a>.
MongoDB peut aussi s'exécuter comme un service Windows (lien installation windows).
Les exemples suivants partent du principe que le répertoire contenant le processus mongod est dans votre PATH système. Le processus mongod est
le processus base de données primaire qui s'exécute sur un serveur. Le processus mongos fournit une interface MongoDB cohérente équivalente à mongod
du point de vue du client. Le programme mongo fournit un shell/terminal administratif.</p>
<a name="dema"></a>

<div class="spacer"></div>

<p class="titre">I) [ Démarrer mongod ]</p>


<p>Par défaut, MongoDB stocke les informations dans le répertoire /data/db. Sur windows, MongoDB stocke les données dans C:\data\db. Sur toutes les plateformes,
MongoDB écoute les connexions clientes sur le port 27017.
Pour démarrer MongoDB en utilisant tous les paramètres par défaut, utilisez la commande suivante :</p>

<pre>mongo</pre>
<a name="repe"></a>

<div class="spacer"></div>

<p class="small-titre">a) Spécifier un Répertoire de Données</p>

<p>Si vous voulez que mongod stocke les fichiers de données à un autre endroit que /data/db, vous pouvez spécifier un dbpath. Le dbpath doit impérativement
exister avant que vous démarriez mongod. S'il n'existe pas, créez le répertoire et ses permissions de manière à ce que mongod puisse lire et écrire les données
dans ce dossier. Pour ensuite spécifier un dbpath que mongod utilisera comme répertoire de données, utilisez l'option --dbpath. L'invocation suivante
va démarrer l'instance mongod et stocker les données dans le répertoire /srv/mongodb :</p>

<pre>mongod --dbpath /srv/mongodb/</pre>
<a name="tcp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Spécifier un Port TCP</p>

<p>Seulement un seul processus peut écouter les connexions pour une interface réseau à la fois. Si vous exécutez plusieurs instances mongod sur une même machine,
ou alors que vous avez d'autres processus qui doivent utiliser ce port, vous devez alors assigner à chacune un port différent pour écouter les connexions
clientes. Pour spécifier un port à votre instance mongod, utilise l'option --port en ligne de commande. L'option suivante démarre mongod en écoutant
sur le port 12345 :</p>

<p>mongod --port 12345</p>

<p>Utilisez le port par défaut dès que possible pour éviter toute confusion.</p>
<a name="demo"></a>

<div class="spacer"></div>

<p class="small-titre">c) Démarrer mongod en tant que Démon</p>

<p>Pour exécuter un processus mongod en tant que démon (par exemple : fork), et écrire sa sortie dans un fichier log, utilisez les options --fork et --logpath.
Vous devez créer le répertoire de log, en revanche, mongod va créer le fichier log s'il n'existe pas.
La commande suivante va démarrer un mongod en tant que démon et enregistrer la sortie dans le fichier log /var/log/mongodb.log :</p>

<pre>mongod --fork --logpath /var/log/mongodb.log</pre>
<a name="arre"></a>

<div class="spacer"></div>

<p class="titre">II) [ Arrêter mongod ]</p>

<p>Pour arrêter une instance mongod qui n'est pas exécutée en tant que démon, pressez Cntrl + C. MongoDB stoppe alors lorsque toutes les opérations
sont complètes et procède à une sortie propre, flushing et fermant tous les fichiers de données.
Pour arrêter une instance mongod exécutée en premier ou arrière-plan, utilisez la méthode db.shutdownServer() dans un shell mongo :

1) Pour ouvrir un shell mongo pour l'instance mongod exécutée sur le port 27017, utilisez la commande suivante :</p>

<pre>mongo</pre>

<p>Pour ensuite basculer vers la base de données admin et stopper l'instance mongod, utilisez la méthode suivante :</p>

<pre>
use admin
db.shutdownServer()
</pre>

<p>Vous devrez utiliser db.shutdownServer() seulement lorsque vous êtes connecté à l'instance mongod, lorsque vous êtes identifié sur la base de données
admin ou sur les systèmes sans identification connecté via l'interface localhost.
Alternativement, vous pouvez stopper une instance mongod depuis un driver en utilisant la commande shutdown.</p>
<a name="repl"></a>

<div class="spacer"></div>

<p class="small-titre">a) Arrêter mongod avec les Replica Sets</p>

<p>Si le mongod et le primaire d'un Replica Set, le processus d'arrêt pour ces instances est le suivant :

1) Vérifiez à quel point les membres secondaires sont à jour.
2) Si aucun des secondaires est à moins de 10 secondes du primaire, mongod va retourner un message indiquant qu'il ne s'arrêtera pas
Vous pouvez passer le paramètre timeoutSecs à la commande shutdown pour attendre qu'un membre primaire se mette à niveau.
3) S'il y a un membre secondaire étant à moins de 10 secondes du membre primaire, le primaire va s'arrêter et attendre que le secondaire se mette à son niveau.
4) Après 60 secondes ou une fois que le secondaire s'est remit à niveau, le primaire va s'arrêter.

S'il n'y a pas de membre secondaire à jour et que vous voulez que le primaire s'arrête, passez l'argument force à la commande shutdown comme dans l'exemple
suivant :</p>

<pre>db.adminCommand({shutdown : 1, force : true})</pre>

<div class="spacer"></div>

<p>Pour continuer de vérifier les membres secondaires pour un certains nombre de secondes, si aucun n'est immédiatement à jour, utilisez la commande
shutdown avec le paramètre timeoutSecs. MongoDB va continuellement vérifier les membres secondaires pour le nombre de secondes spécifié si aucun n'est
immédiatement à jour. Si l'un des secondaires se met à jour pendant le temps alloué, le primaire va s'arrêter. Si aucun des secondaires ne se rattrape,
il ne s'arrêtera pas.
La commande suivante utilise shutdown avec le paramètre timeoutSecs définit à 5 :</p>

<pre>db.adminCommand({shutdown : 1, timeoutSecs : 5})</pre>

<p>Vous pouvez aussi utiliser l'argument timeoutSecs avec la méthode db.shutdownServer() :</p>

<pre>db.shutdownServer({timeoutSecs : 5})</pre>
<a name="sign"></a>

<div class="spacer"></div>

<p class="small-titre">b) Envoyer un Signal Unix INT ou TERM</p>

<p>Vous pouvez proprement arrêter mongod en utilisant un signal SIGINT ou SIGTERM sur le systèmes UNIX. Soit Cntrl + C ou alors kill -2 "pid",
ou kill -9 "pid" vont proprement terminer l'instance.
Pour terminer une instance mongod qui n'est pas exécutée avec le journaling d'activé en utilisant kill -9 "pid" (ici, SIGKILL) va corromprendre les données.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="analyse_performances.php">"Analyser les Performances des Opérations" >></a>.</p>

<?php

	include("footer.php");

?>
