<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec RedHat Enterprise, Fedora et CentOs</a></li>
</ul>

<p class="titre">[ Installation avec RedHat Enterprise, Fedora et CentOs ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="elem"><a href="#inst">II) Installer MongoDB</a></p>
	<p class="right"><a href="#sys64">- a) Systèmes 64 Bits</a></p>
	<p class="right"><a href="#sys32">- b) Systèmes 32 Bits</a></p>
	<p class="right"><a href="#script">- c) Scripts de Contrôle</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#stop">IV) Arrêter MongoDB</a></p>
	<p class="elem"><a href="#reb">V) Redémarrer MongoDB</a></p>
</div>

<p>Vous êtes ici car vous souhaitez <b>installer MongoDB</b> sur une distribution Linux telle que <b>RedHat Enterprise</b>, <b>Fedora</b> ou même <b>CentOs</b>.
Ces distributions nécessitent des <b>packages</b> de type <b>.RPM</b> et nous allons décrire ici comment les <b>télécharger</b> et les <b>installer</b>.
Nous allons ensuite utiliser le <b>processus mongod</b> principal de <b>MongoDB</b>, rappelez-vous le <b>tableau</b> qui se trouve en page d'<a href="../introduction.php">"Introduction"</a>.
Au passage, si vous bossez chez <b>Red Hat</b>, y aurait-il une place pour moi ? :)</p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-Requis ]</p>

<p>Dans un premier temps, pour les utilisateurs de <b>Linux RedHat Enterprise Edition</b>, <b>Fedora</b>, <b>CentOs</b> ou autre distribution <b>dépendante</b>,
il va vous falloir installer <b>deux packages .RPM</b> qui sont les suivants :</p>

<div class="small-spacer"></div>

<p>- <b>mongo-10gen-server</b> : Contient les démons mongod et mongos et autres scripts de configurations.</p>
<p>- <b>mongo-10gen</b> : Tous les outils qui sont fournis avec MongoDB et que vous devrez installer sur tous les clients.</p>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">II) [ Installer MongoDB ]</p>

<p>Commençons maintenant l'<b>installation</b>. Créez le fichier de configuration <b>/etc/yum.repos.d/mongodb.repo</b> pour le gestionnaire de <b>packages YUM</b>.
Ce fichier contiendra les lignes suivantes :</p>
<a name="sys64"></a>

<div class="spacer"></div>

<p class="small-titre">a) Systèmes 64 Bits</p>

<pre>
[mongodb]
name=MongoDB Repository
baseurl=http://downloads-distro.mongodb.org/repo/redhat/os/x86_64/
gpgcheck=0
enabled=1
</pre>
<a name="sys32"></a>

<div class="spacer"></div>

<p class="small-titre">b) Systèmes 32 Bits</p>

<pre>
[mongodb]
name=MongoDB Repository
baseurl=http://downloads-distro.mongodb.org/repo/redhat/os/i686/
gpgcheck=0
enabled=1
</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/redhat/installation_redhat_1.png" data-lightbox="redhat" title="Edition de mongodb.repo avec vi sur Fedora 64 Bits"><img src="/img/installations/redhat/installation_redhat_1_mini.png" /></a>
<p><h6><b>Image 1.0</b> - Edition de mongodb.repo avec vi sur Febdora 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Installez donc ensuite les packages <b>mongo-10gen</b> et <b>mongo-10gen-server</b> avec YUM, en tant qu'utilisateur <b>root</b> ou avec l'outil <b>sudo</b> :</p>

<pre>yum install mongo-10gen mongo-10gen-server</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/redhat/installation_redhat_2.png" data-lightbox="redhat" title="Installation de MongoDB sur Fedora 64 Bits"><img src="/img/installations/redhat/installation_redhat_2_mini.png" /></a>
<p><h6><b>Image 1.1</b> - Installation de MongoDB sur Febdora 64 Bits.</h6></p>

<div class="spacer"></div>

<div class="alert alert-success">
    <u>Astuce</u> : Saviez-vous qu'il vous est possible d'installer une version spécifique de MongoDB avec ces même packages ?
	Vous pouvez le faire avec la même commande, mais en précisant la version que vous souhaitez.
</div>

<p>Avec cette commande, vous allez installer la version <b>2.2.3</b> de MongoDB :</p>
<pre>yum install mongo-10gen-2.2.3 mongo-10gen-server-2.2.3</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : YUM va mettre à jour ces packages une fois qu'une mise à jour sera détectée ! Si vous souhaitez
	bloquer ce processus, ajouter la ligne suivante dans le fichier de configuration /etc/yum.conf :
</div>

<pre>exclude=mongo-10gen,mongo-10gen-server</pre>
<a name="script"></a>

<div class="spacer"></div>

<p class="small-titre">c) Scripts de Contrôle</p>

<p>Les packages contiennent des <b>scripts de contrôle</b> tels que : <b>/etc/rc.d/init.d/mongod</b></p>
<p>La configuration utilisée par ces scripts se trouve dans <b>/etc/mongod.conf</b></p>

<div class="small-spacer"></div>

<p>Depuis la version <b>2.4.9</b>, il n'y a plus de fichier de configuration pour le script <b>mongos</b>.</p>
<p>En effet, celui-ci est utilisé uniquement dans des <b>situations de sharding</b> que nous verrons dans un chapitre plus loin.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">III) [ Exécuter MongoDB ]</p>

<p>Voilà, maintenant que les packages sont <b>installés</b>, vous allez pouvoir <b>exécuter MongoDB</b> en tant que root ou avec sudo comme ceci :</p>

<pre>service mongod start</pre>

<div class="spacer"></div>

<p>Le service devrait être exécuté et donc vous pouvez <b>tester</b> une connexion à votre <b>service mongod</b> avec le client <b>mongo</b>, dans un autre shell :</p>

<pre>mongo</pre>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/redhat/installation_redhat_3.png" data-lightbox="redhat" title="Exécution de mongo sur Fedora 64 Bits"><img src="/img/installations/redhat/installation_redhat_3_mini.png" /></a>
<p><h6><b>Image 1.2</b> - Exécution de mongo sur Febdora 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Vous voyez le message <b>"connecting to: test"</b> ? <b>Vous êtes connecté à votre service mongod !</b> Pour être sûr que MongoDB <b>s'exécutera après le prochain redémarrage</b> de l'ordinateur, exécutez la commande suivante dans un shell :</p>

<pre>chkconfig mongod on</pre>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Arrêter MongoDB ]</p>

De la même manière, si vous souhaitez <b>arrêter</b> le processus MongoDB, toujours avec root ou avec sudo :

<pre>service mongod stop</pre>
<a name="reb"></a>

<div class="spacer"></div>

<p class="titre">V) [ Redémarrer MongoDB ]</p>

<p>Une dernière option, <b>redémarrer MongoDB</b> avec l'outil sudo ou l'utilisateur root :</p>

<pre>service mongod restart</pre>

<div class="small-spacer"></div>

<p>Vous pouvez avoir une <b>trace de chaque commande</b> que vous avez utilisée avec le <b>fichier log "/var/log/mongo/mongod.log"</b></p>

<div class="spacer"></div>

<p>Voilà, vous êtes arrivé à <b>la fin du tutoriel d'installation</b> ! Si vous avez rencontré des <b>problèmes</b>, des <b>messages d'erreurs</b> ... <b>n'hésitez pas
à me contacter</b> via la page de <a href="../contact.php">"Contact"</a> du site, je vous répondrai dès que possible !</p>
<p>Pour passer au chapitre suivant, celui qui vous permettra de réaliser <b>vos premières requêtes</b>, veuillez avancer vers les <a href="../operations_crud.php">"Opérations CRUD" >></a></p>

<?php

	include("footer.php");

?>
