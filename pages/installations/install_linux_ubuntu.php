<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec Ubuntu</a></li>
</ul>

<p class="titre">[ Installation avec Ubuntu ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="elem"><a href="#inst">II) Installer MongoDB</a></p>
	<p class="right"><a href="#script">- a) Scripts de Contrôle</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#stop">IV) Arrêter MongoDB</a></p>
	<p class="elem"><a href="#reb">V) Redémarrer MongoDB</a></p>
</div>

<p>Vous êtes sur cette page car vous souhaitez <b>installer MongoDB</b> sur votre distribution <b>Ubuntu</b> ! L'une de mes distributions Linux préférées,
<b>excellent choix</b>. Nous allons voir, ici, comment <b>télécharger et installer</b> les packages requis pour l'<b>installation</b> de MongoDB.
Ensuite, nous effectuerons une <b>connexion de test via mongo</b> pour vérifier que tout a bien été installé. Les packages
supportés sont de type <b>.DEB</b>. <b>C'est partit</b> !</p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-requis ]</p>

<div class="alert alert-danger">
	<u>Attention</u> : Si vous utiliser une version d'Ubuntu inférieure à la version 9.10 “Karmic”,
	repportez-vous au tutoriel d'<a href="install_linux_debian.php">"Installation pour Debian"</a>.
</div>

<p>Vous devez installer le package <b>mongodb-10gen</b> si votre version d'Ubuntu <b>ne contient pas déjà</b> les packages <b>mongodb</b>, <b>mongodb-server</b> ou <b>mongodb-clients</b>.</p>
<p>L'installation en parallèle est <b>impossible</b> à moins de <b>supprimer</b> ces packages déjà existants.</p>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">II) [ Installer MongoDB ]</p>

<p>Afin de garantir l'<b>intégrité</b> et l'<b>authenticité</b> des packages, vous devez importer <b>la clé publique GPG</b> pour MongoDB avec la commande suivante :</p>

<pre>sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/ubuntu/installation_ubuntu_1.png" data-lightbox="ubuntu" title="Ajout de la clé GPG pour Ubuntu"><img src="/img/installations/ubuntu/installation_ubuntu_1_mini.png" /></a>
<p><h6><b>Image 1.0</b> - Ajout de la clé GPG pour Ubuntu.</h6></p>

<div class="spacer"></div>

<p>Ensuite, vous allez devoir créer le fichier qui va contenir les sources à partir desquelles <b>APT va récupérer les packages</b>. Pour créez le fichier <b>/etc/apt/sources.list.d/mongodb.list :</b></p>

<pre>echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | sudo tee /etc/apt/sources.list.d/mongodb.list</pre>

<div class="spacer"></div>

<p>Une fois cela effectué, vous devez <b>mettre à jour votre repository</b> afin de prendre en compte les nouveaux paramètres :</p>

<pre>sudo apt-get update</pre>

<div class="spacer"></div>

<p>Maintenant, afin d'installer <b>MongoDB</b>, il suffit d'exécuter APT en utilisant la commande suivante :</p> 

<pre>sudo apt-get install mongodb-10gen</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/ubuntu/installation_ubuntu_2.png" data-lightbox="ubuntu" title="Installation de MongoDB avec APT pour Ubuntu"><img src="/img/installations/ubuntu/installation_ubuntu_2_mini.png" /></a>
<p><h6><b>Image 1.1</b> - Installation de MongoDB avec APT pour Ubuntu.</h6></p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Vous pouvez spécifier la version que vous souhaitez avec ce même package. Par exemple,
	si vous souhaitez installer la version 2.2.3 de MongoDB, tapez la commande suivante :
</div>

<pre>apt-get install mongodb-10gen=2.2.3</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Pour éviter que APT mette à jour une possible version antérieure de MongoDB, si vous le souhaitez, entrez la commande :
</div>

<pre>echo "mongodb-10gen hold" | sudo dpkg --set-selections</pre>
<a name="script"></a>

<div class="spacer"></div>

<p class="small-titre">a) Scripts de Contrôle</p>

<p>Les packages contiennent des <b>scripts de contrôle</b> tels que : <b>/etc/rc.d/init.d/mongod</b></p>
<p>La configuration utilisée par ces scripts se trouve dans <b>/etc/mongod.conf</b></p>

<div class="small-spacer"></div>

<p>Depuis la version <b>2.4.9</b>, il n'y a plus de fichier de configuration pour le script <b>mongos</b>.</p>
<p>En effet, celui-ci est utilisé uniquement dans des <b>situations de sharding</b> que nous verrons dans un chapitre plus loin.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">III) [ Exécuter MongoDB ]</p>

<p>Pour exécuter MongoDB, vous pouvez exécuter la commande suivante :</p>

<pre>sudo service mongodb start</pre>

<p>La plupart du temps, <b>Ubuntu</b> va indiquer que le service est <b>déjà exécuté</b>.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/ubuntu/installation_ubuntu_3.png" data-lightbox="ubuntu" title="Exécution de MongoDB avec Ubuntu"><img src="/img/installations/ubuntu/installation_ubuntu_3_mini.png" /></a>
<p><h6><b>Image 1.2</b> - Exécution de MongoDB avec Ubuntu.</h6></p>

<div class="spacer"></div>

<p>Pour vérifier que votre instance est <b>bien exécutée</b>, exécutez la commande <b>mongo</b> dans un autre shell. Vous devriez voir, normalement,
quelque chose de similaire à <b>l'image 1.2</b>.</p>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Arrêter MongoDB ]</p>

<p>A l'opposé, si vous désirez <b>arrêter MongoDB</b>, vous pouvez le faire avec la commande ci-dessous :</p>

<pre>sudo service mongodb stop</pre>
<a name="reb"></a>

<div class="spacer"></div>

<p class="titre">V) [ Redémarrer MongoDB ]</p>

<p>Enfin, pour redémarrer MongoDB :</p>

<pre>sudo service mongodb restart</pre>

<div class="small-spacer"></div>

<p>Vous pouvez avoir une trace de chaque commande avec le fichier log <b>"/var/log/mongo/mongod.log"</b></p>

<div class="spacer"></div>

<p>Voilà vous avez terminé l'installation de MongoDB sur votre <b>distribution Ubuntu</b>. En cas de problèmes ou si une question vous passe par la tête, <b>n'hésitez pas à me</b> <a href="../contact.php">"contacter"</a>.
Je tenterai de vous répondre rapidement.</p>
<p>Prochaine étape, <b>les opérations CRUD</b> ! En bref, vous allez <b>découvrir</b> comment effectuer vos premières requêtes : <a href="../operations_crud.php">"Opérations CRUD" >></a>.</p>
 

<?php

	include("footer.php");

?>
