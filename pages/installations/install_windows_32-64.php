<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec Windows</li>
</ul>

<p class="titre">[ Installation avec Windows ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="elem"><a href="#inst">II) Installer MongoDB</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#serv">IV) Service MongoDB</a></p>
	<p class="right"><a href="#insts">- a) Installer le Service</a></p>
	<p class="right"><a href="#execs">- b) Exécuter le Service</a></p>
	<p class="right"><a href="#stops">- c) Arrêter le Service</a></p>
	<p class="right"><a href="#dels">- d) Supprimer le Service</a></p>
</div>

<p>Vous êtes ici car vous souhaitez <b>installer MongoDB</b> sur votre version de Windows (<b>à partir de Windows Vista jusqu'à Windows 8</b>). Vous allez
apprendre à déployer MongoDB en tant que <b>simple exécutable</b>, mais aussi en tant que <b>service</b>. N'oubliez-pas que si vous rencontrez
un <b>problème</b> ou s'il y a quelque chose que vous <b>ne comprenez pas</b>, <a href="../contact.php">"contactez-moi"</a> sans hésiter.
Voilà, commençons par les <b>pré-requis</b>.</p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-requis ]</p>

<p>Quelques petites indications avant de commencer. <b>Attention ! Lisez-bien tout !</b></p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Windows XP n'est plus supporté depuis la version 2.2 de MongoDB.
</div>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Si vous prévoyez d'installer MongoDB sur Windows Server 2008R2 ou Windows 7, vous devez installer ce hotfix :
	<a href="http://support.microsoft.com/kb/2731284" target="_blank">http://support.microsoft.com/kb/2731284</a>
</div>

<div class="spacer"></div>

<p>Si vous souhaitez déterminer l'<b>architecture</b> de votre version Windows car vous ne savez pas si elle est de <b>32 Bits ou 64 Bits</b>, exécutez la commande suivante dans un invite de commande (<b>comme dans l'image 1.0 ci-dessous</b>) :</p>

<div class="spacer"></div>

<pre>wmic os get osarchitecture</pre>

<a class="screenshot" href="/img/installations/windows/installation_windows_1.png" data-lightbox="windows" title="Déterminer votre architecture Windows"><img src="/img/installations/windows/installation_windows_1_mini.png" /></a>
<p><h6><b>Image 1.0</b> - Déterminer votre architecture Windows.</h6></p>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">II) [ Installer MongoDB ]</p>
<p>Comme promis, MongoDB supporte les architectures <b>32 et 64 Bits</b>, mais attention, l'installation sur les machines 32 Bits est <b>uniquement</b> employée pour des scénarios de <b>développement</b> ou de <b>test</b>, ainsi que les
bases de données ayant une taille <b>inférieure à 2Go</b>. Pour votre système de <b>production</b>, veuillez installer une version <b>64 Bits</b> qui prend en compte plus de <b>mémoire RAM</b>.</p>

<div class="small-spacer"></div>

<p>Vous pouvez <b>télécharger l'archive</b> qui vous correspond grâce à ce lien <a href="http://www.mongodb.org/downloads" target="_blank">"Télécharger MongoDB"</a> et en <b>extraire le contenu</b> dans votre répertoire <b>"C:\mongodb"</b> par exemple. 
Vous pouvez extraire l'archive où vous le voulez, en sachant que MongoDB n'a <b>aucune dépendance</b>.</p>

<p>Ensuite, MongoDB va avoir besoin d'un <b>répertoire de données</b>. Un répertoire de données ? <b>Pourquoi</b> ?
En effet, ce dossier va correspondre à l'emplacement où MongoDB va <b>stocker toutes les informations</b> concernant les bases de données.
Il va vous falloir créer le répertoire de données <b>à l'intérieur</b> du répertoire de MongoDB.</p>

<div class="small-spacer"></div>

<p>Par exemple, si MongoDB se situe dans <b>"C:\mongodb"</b>, le répertoire de données se trouvera ici <b>"C:\mongodb\data\db"</b>. Le nom <b>"\data\db"</b> n'est pas obligatoire, vous pouvez renommer ce répertoire comme vous le voulez.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/windows/installation_windows_2.png" data-lightbox="windows" title="Installation de MongoDB sur Windows 64 Bits"><img src="/img/installations/windows/installation_windows_2_mini.png" /></a>
<p><h6><b>Image 1.1</b> - Installation de MongoDB sur Windows 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Vous allez <b>impérativement</b> devoir créer ce dossier et le définir en choisissant l'option <b>--dbpath</b> avec l'exécutable <b>mongod.exe</b>, vous pourrez specifier un chemin différent pour le répertoire de données.</p>

<pre>C:\mongodb\bin\mongod.exe --dbpath C:\mongodb\data\db</pre>

<div class="spacer"></div>

<p>L'image 1.2 <b>ci-dessous</b> vous montre un aperçu d'une <b>instance mongod</b> en cours d'exécution.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/windows/installation_windows_3.png" data-lightbox="windows" title="Instance mongod en cours d'exécution sur Windows 64 Bits"><img src="/img/installations/windows/installation_windows_3_mini.png" /></a>
<p><h6><b>Image 1.2</b> - Instance mongod en cours d'exécution sur Windows 64 Bits.</h6></p>

<div class="spacer"></div>

<p>Si le chemin de votre répertoire de données contient <b>au moins un espace</b>, ajoutez des doubles quotes comme ceci :</p>

<pre>C:\mongodb\bin\mongod.exe --dbpath "D:\test\mongo db data"</pre>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">III) [ Exécuter MongoDB ]</p>

<p>Comme vu dans le paragraphe précédent, vous savez comment initialiser le processus <b>mongod.exe</b> qui va être ensuite à l'écoute d'éventuels <b>clients</b>.
Si vous voyez dans l'invite de commande <b>"waiting for connections"</b>, cela veut dire que mongod.exe est <b>exécuté avec succès !</b></p>
<p>Vous devriez avoir une <b>alerte de sécurité Windows</b> s'afficher, sélectionnez <b>réseaux privés</b> et <b>"autoriser l'accès"</b></p>

<div class="alert alert-danger">
	<u>Attention</u> : N'autorisez pas les réseaux publiques pour MongoDB s'il n'est pas configuré pour être en mode sécurisé. Ce mode n'est pas activé par défaut.
</div>

<div class="spacer"></div>

<p>Maintenant, vous allez devoir initialiser votre <b>premier client MongoDB</b>, à partir duquel vous allez avoir accès aux données
que votre déploiement MongoDB détient (en l'occurence, pas grand chose en premier lieu).
Connectez-vous maintenant à MongoDB avec un invite de commande Windows et utilisez l'outil <b>mongo.exe</b> :</p>

<pre>C:\mongodb\bin\mongo.exe</pre>

<div class="spacer"></div>

<p>Une fois effectué, le programme <b>mongo.exe</b> va se connecter à l'instance <b>mongod.exe</b>, sur le port <b>27017</b> par défaut, qui est en cours d'exécution. <b>L'image 1.3</b> vous montre un aperçu d'un client mongo connecté.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/installations/windows/installation_windows_4.png" data-lightbox="windows" title="Instance mongo connectée sur Windows 64 Bits"><img src="/img/installations/windows/installation_windows_4_mini.png" /></a>
<p><h6><b>Image 1.3</b> - Instance mongo connectée sur Windows 64 Bits.</h6></p>

<div class="spacer"></div>

<p>C'est tout ! Vous venez d'installer <b>MongoDB</b> sur votre Windows !</p>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Le prochain paragraphe est optionel. Celui-ci démontre comment installer MongoDB en tant que service Windows.
	Si vous n'envisagez pas cette option, passez directement au chapitre suivant sur les <a href="../operations_crud.php">"Opérations CRUD"</a>.
	Ce chapitre va vous expliquer comment effectuer vos premières requêtes.
</div>
<a name="serv"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Service MongoDB ]</p>

<p>Nouveau depuis la version 2.0 pour mongod.exe, vous pouvez définir MongoDB en tant que service, de cette manière la base de données s'exécutera après <b>chaque redémarrage</b> du système.</p>

<p>Afin d'obtenir des infos sur ce nouveau service, créez le dossier <b>"C:\mongodb\log"</b>.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous désirez créer un fichier de configuration, veuillez taper la commande suivante dans un invite de commande :
</div>

<pre>echo logpath=C:\mongodb\log\mongo.log > C:\mongodb\mongod.cfg</pre>

<div class="spacer"></div>

<p>Considérez l'option <b>"loggappend"</b> pour continuer d'enregistrer dans le fichier après chaque exécution de <b>mongod.exe</b>, sinon par défault, celui ci supprime le contenu.</p>
<a name="insts"></a>

<div class="spacer"></div>

<p class="small-titre">a) Installer le Service</p>

<p>En mode <b>administrateur</b>, veuillez saisir la commande suivante :</p>

<pre>C:\mongodb\bin\mongod.exe --config C:\mongodb\mongod.cfg --install</pre>
<a name="execs"></a>

<div class="spacer"></div>

<p class="small-titre">b) Exécuter le Service</p>

<p>Vous pouvez maintenant exécuter le <b>service MongoDB</b> par la commande suivante :</p>

<pre>net start MongoDB</pre>

<a class="screenshot" href="/img/installations/windows/installation_windows_5.png" data-lightbox="windows" title="Initialisation du service sur Windows 64 Bits"><img src="/img/installations/windows/installation_windows_5_mini.png" /></a>
<p><h6><b>Image 1.4</b> - Initialisation du service sur Windows 64 Bits.</h6></p>
<a name="stops"></a>

<div class="spacer"></div>

<p class="small-titre">c) Arrêter le Service</p>

<p>Au contraire, si vous souhaitez l'<b>arrêter</b> :</p>

<pre>net stop MongoDB</pre>
<a name="dels"></a>

<div class="spacer"></div>

<p class="small-titre">d) Supprimer le Service</p> 

<p>Pour supprimer le service, la commande qui correspond est la <b>suivante</b> :</p>

<pre>C:\mongodb\bin\mongod.exe --remove</pre>

<div class="spacer"></div>

<p>Ceci est la fin du tutoriel d'<b>installation de MongoDB pour Windows</b>. En cas de problèmes, ou alors si vous avez des questions, n'hésitez pas à me <a href="../contact.php">"contacter"</a>.
Je tenterai de vous répondre <b>rapidement et efficacement</b>.</p>
<p>Maintenant, vous allez pouvoir passer aux <b>premières requêtes</b> ! Passons au chapitre sur les <a href="../operations_crud.php">"Opérations CRUD" >></a>.</p>


<?php

	include("footer.php");

?>
