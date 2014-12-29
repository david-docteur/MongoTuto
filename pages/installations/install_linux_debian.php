<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec Debian</li>
</ul>

<p class="titre">[ Installation avec Debian ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="elem"><a href="#inst">II) Installer MongoDB</a></p>
	<p class="right"><a href="#script">- a) Scripts de Contrôle</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#stop">IV) Arrêter MongoDB</a></p>
	<p class="elem"><a href="#reb">V) Redémarrer MongoDB</a></p>
</div>

<p>Vous êtes ici car vous souhaitez <b>installer MongoDB</b> sur une distribution <b>Debian</b>.
Cette distribution nécessite des <b>packages</b> de type <b>.DEB</b> et nous allons décrire ici comment les <b>télécharger</b> et les <b>installer</b>.
Ensuite, nous allons procéder à la <b>première exécution</b> de MongoDB sur votre machine. <b>Allez, on commence !</b></p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-requis ]</p>

<p>Pour installer MongoDB sous Debian, vous devez installer le package <b>mongodb-10gen</b> si votre version de Debian ne contient pas déjà les packages <b>mongodb</b>, <b>mongodb-server</b> ou <b>mongodb-clients</b>.
L'installation en parallèle avec une autre est <b>impossible</b> à moins de <b>supprimer</b> ces packages déjà existants.</p>
<a name="inst"></a>
<div class="spacer"></div>

<p class="titre">II) [ Installer MongoDB ]</p>

<p>Afin de garantir l'<b>intégrité</b> et l'<b>authenticité</b> des packages que vous allez télécharger, vous devez importer <b>la clé publique GPG</b> pour MongoDB avec la commande suivante :</p>

<pre>sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/debian/installation_debian_1.png" data-lightbox="debian" title="Ajout de la clé publique GPG avec Debian 64 Bits"><img src="/img/installations/debian/installation_debian_1_mini.png" /></a>
<p><h6><b>Image 1.0</b> - Ajout de la clé publique GPG avec Debian 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Ensuite, vous devez <b>créez le fichier</b> suivant afin de mettre à jour vos <b>sources</b>, à partir desquelles vous allez trouver le package nécessaire à MongoDB : <b>/etc/apt/sources.list.d/mongodb.list</b></p>

<pre>echo 'deb http://downloads-distro.mongodb.org/repo/debian-sysvinit dist 10gen' | sudo tee /etc/apt/sources.list.d/mongodb.list</pre>

<div class="spacer"></div>

<p>Une fois cela effectué, <b>APT</b> devrait être capable de trouver et de récupérer le/les bons package(s), vous devez <b>mettre à jour votre repository</b> :</p>

<pre>sudo apt-get update</pre>

<div class="spacer"></div>

<p>Voilà, maintenant, il suffit d'effectuer la commande suivante pour télécharger MongoDB :</p>

<pre>sudo apt-get install mongodb-10gen</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/debian/installation_debian_2.png" data-lightbox="debian" title="Installation de MongoDB avec Debian 64 Bits"><img src="/img/installations/debian/installation_debian_2_mini.png" /></a>
<p><h6><b>Image 1.1</b> - Installation de MongoDB avec Debian 64 Bits.</h6></p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Vous pouvez spécifier la version que vous souhaitez avec ce même package ! Par exemple, ici vous allez installer la version 2.2.3 de MongoDB.
</div>

<pre>apt-get install mongodb-10gen=2.2.3</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Pour éviter que APT mette à jour automatiquement une possible version antérieure de MongoDB, si vous le souhaitez, entrez la commande :
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

<p>Voilà, alors une fois que votre version de MongoDB est <b>installée</b>, vous allez pouvoir <b>exécuter MongoDB</b> comme ceci :</p>

<pre>sudo /etc/init.d/mongodb start</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/debian/installation_debian_3.png" data-lightbox="debian" title="Exécution de MongoDB sous Debian 64 Bits"><img src="/img/installations/debian/installation_debian_3_mini.png" /></a>
<p><h6><b>Image 1.2</b> - Exécution de MongoDB sous Debian 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Normalement, le service MongoDB sera <b>déjà initialisé</b> à la fin de l'installation, donc, vous verrez <b>peut-être</b> un message dans votre shell
indiquant que <b>la base de données est déjà active</b>. Pour ensuite vérifier que <b>tout s'est bien déroulé</b>, exécutez la commande <b>"mongo"</b> dans un autre shell.
Vous devriez voir <b>mongo</b> se connecter à la base de données <b>"test"</b> par défaut.</p>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Arrêter MongoDB ]</p>

<p>Dans le cas contraire, si vous désirez <b>mettre fin</b> à votre instance MongoDB <b>en cours d'exécution</b>, saissez la commande suivante dans un shell :</p>

<pre>sudo /etc/init.d/mongodb stop</pre>
<a name="reb"></a>

<div class="spacer"></div>

<p class="titre">V) [ Redémarrer MongoDB ]</p>

<p>Enfin, la dernière commande du tutoriel, le <b>redémarrage de MongoDB</b>, plutôt similaire à la commande précédente :</p>

<pre>sudo /etc/init.d/mongodb restart</pre>

<div class="small-spacer"></div>

<p>Vous pouvez avoir une trace de chaque commande avec le fichier log <b>"/var/log/mongodb/mongodb.log"</b></p>

<div class="spacer"></div>

<p>Voilà, le tutoriel d'<b>installation pour Debian</b> s'achève !. En cas de <b>problèmes</b> ou d'une <b>question</b> qui vous viendrait à l'idée, n'hésitez pas à me <a href="../contact.php">"contacter"</a>.
Je tenterai de vous répondre <b>rapidement et efficacement</b>.</p>
<p>Allons ensemble maintenant aux <b>Opérations CRUD</b> qui vous permettrons d'effectuer vos <b>premières requêtes</b> : <a href="../operations_crud.php">"Opérations CRUD" >></a>.</p>
 
 
<?php

	include("footer.php");

?>
