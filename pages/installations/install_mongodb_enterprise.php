<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation de MongoDB Enterprise</li>
</ul>

<p class="titre">[ Installation de MongoDB Enterprise ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="right"><a href="#pack1">- a) Version 2.4.4 ou plus</a></p>
	<p class="right"><a href="#pack2">- b) Versions précédentes</a></p>
	<p class="elem"><a href="#inst">II) Installation des Packages</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB Enterprise</a></p>
	<p class="elem"><a href="#stop">IV) Arrêter MongoDB Enterprise</a></p>
</div>

<p>Pour les déploiements plus <b>sérieux</b> ainsi que pour ceux qui souhaitent accéder à un <b>support</b> et une <b>sécurité</b> plus <b>accrus</b>, bienvenue sur la page
d'installation de <b>MongoDB Enterprise</b>.</p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-requis ]</p>

<p>Vous allez tout d'abord avoir besoin d'installer les <b>packages suivants</b> (sélectionnez la version qui vous correspond). Pour ceux qui remarqueront
que la version MongoDB Enterprise <b>n'existe pas pour Microsoft Windows</b> ... désolé, elle n'est <b>pas encore disponible</b>, mais cela ne saurait tarder pour la version
<b>2.6</b> il me semble.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Ici, les exemples comportent presque tous des packages incluant leur numéro de version. Veuillez bien faire attention
	en téléchargeant les packages dont vous aurez besoin.
</div>
<a name="pack1"></a>

<div class="spacer"></div>

<p class="small-titre">a) Version 2.4.4 ou plus</p>

<p class="un-list">Debian et Ubuntu 12.04 :</p><p>libssl0.9.8, snmp, snmpd, cyrus-sasl2-dbg, cyrus-sasl2-mit-dbg, libsasl2-2, libsasl2-dev, libsasl2-modules, and
libsasl2-modules-gssapi-mit</p>

<pre>sudo apt-get install libssl0.9.8 snmp snmpd cyrus-sasl2-dbg cyrus-sasl2-mit-dbg libsasl2-2 libsasl2-dev libsasl2-modules libsasl2-modules-gssapi-mit</pre>

<div class="spacer"></div>

<p class="un-list">CentOS et Red Hat Enterprise Linux 6.x et 5.x et Amazon Linux AMI :</p><p>net-snmp, net-snmp-libs, openssl, net-snmp-utils, cyrus-sasl, cyrus-sasl-lib, cyrus-sasl-devel, and cyrus-sasl-gssapi
<pre>sudo yum install openssl net-snmp net-snmp-libs net-snmp-utils cyrus-sasl cyrus-sasl-lib cyrus-sasl-devel cyrus-sasl-gssapi</pre>

<div class="spacer"></div>

<p class="un-list">SUSE Enterprise :</p><p>libopenssl0_9_8, libsnmp15, slessp1-libsnmp15, snmp-mibs, cyrus-sasl, cyrus-sasl-devel, and cyrus-sasl-gssapi</p>

<pre>sudo zypper install libopenssl0_9_8 libsnmp15 slessp1-libsnmp15 snmp-mibs cyrus-sasl cyrus-sasl-devel cyrus-sasl-gssapi</pre>
<a name="pack2"></a>

<div class="spacer"></div>

<p class="small-titre">b) Versions précédentes</p>

<p class="un-list">Ubuntu 12.04 :</p><p>libssl0.9.8, libgsasl, snmp, and snmpd</p>
<pre>sudo apt-get install libssl0.9.8 libgsasl7 snmp snmpd</pre>

<div class="spacer"></div>

<p class="un-list">Red Hat Enterprise Linux 6.x series et Amazon Linux AMI :</p><p>openssl, libgsasl7, net-snmp, net-snmp-libs, and net-snmp-utils</p>
<pre>
sudo rpm -ivh http://download.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
sudo yum update -y
sudo yum install openssl net-snmp net-snmp-libs net-snmp-utils libgsasl
</pre>
 
 <div class="spacer"></div>
 
<p class="un-list">SUSE Enterprise :</p><p>libopenssl0_9_8, libsnmp15, slessp1-libsnmp15, and snmp-mibs</p>

<pre>sudo zypper install libopenssl0_9_8 libsnmp15 slessp1-libsnmp15 snmp-mibs</pre>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">II) [ Installation des Packages ]</p>

<p>Idem ici, vous allez installer les <b>packages nécessaires à MongoDB</b>, donc choisissez bien la version en fonction de votre <b>système d'exploitation</b>.
<b>N'oubliez pas de modifier</b> la version des packages si besoin.</p>

<div class="spacer"></div>

<p class="small-titre">Ubuntu 12.04</p>
<pre>
curl http://downloads.10gen.com/linux/mongodb-linux-x86_64-subscription-ubuntu1204-2.4.8.tgz > mongodb-tar -zxvf mongodb-linux-x86_64-subscription-ubuntu1204-2.4.8.tgz
cp -R -n mongodb-linux-x86_64-subscription-ubuntu1204-2.4.8/ mongodb
</pre>

<div class="spacer"></div>

<p class="small-titre">Red Hat Enterprise Linux 6.x</p>
<pre>
curl http://downloads.10gen.com/linux/mongodb-linux-x86_64-subscription-rhel62-2.4.8.tgz > mongodb-linux-tar -zxvf mongodb-linux-x86_64-subscription-rhel62-2.4.8.tgz
cp -R -n mongodb-linux-x86_64-subscription-rhel62-2.4.8/ mongodb
</pre>

<div class="spacer"></div>

<p class="small-titre">Amazon Linux AMI</p>
<pre>
curl http://downloads.10gen.com/linux/mongodb-linux-x86_64-subscription-amzn64-2.4.8.tgz > mongodb-linux-tar -zxvf mongodb-linux-x86_64-subscription-amzn64-2.4.8.tgz
cp -R -n mongodb-linux-x86_64-subscription-amzn64-2.4.8/ mongodb
</pre>

<div class="spacer"></div>

<p class="small-titre">SUSE Enterprise Linux</p>

<pre>
curl http://downloads.10gen.com/linux/mongodb-linux-x86_64-subscription-suse11-2.4.8.tgz > mongodb-linux-tar -zxvf mongodb-linux-x86_64-subscription-suse11-2.4.8.tgz
cp -R -n mongodb-linux-x86_64-subscription-suse11-2.4.8/ mongodb
</pre>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">III) [ Exécuter MongoDB Enterprise ]</p>

<p>Une fois que vous aurez installé les <b>packages</b> correspondants et ceux de <b>MongoDB Enterprise</b>, vous pouvez enfin <b>exécuter MongoDB</b>.
Avant de lancer le processus <b>mongod</b> pour la première fois, <b>créez le répertoire de données</b> comme le montre la commande suivante.
Le répertoire de données va correspondre au répertoire où votre déploiement MongoDB va <b>stocker tous ses fichiers de données</b> concernant vos bases de données :</p>

<pre>sudo mkdir -p /data/db</pre>

<div class="spacer"></div>

<p>Vous pouvez exécuter la commande suivante afin <b>d'exécuter mongod</b> :</p>

<pre>mongod</pre>

<div class="spacer"></div>

<p>si vous êtes dans le répertoire de <b>MongoDB</b> ou même si <b>mongod</b> est dans votre <b>PATH</b>. Pour trouver une bonne explication sur comment ajouter MongoDB
dans votre <b>PATH</b>, utilisez votre moteur de recherche favoris.</p>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Vous pouvez changer l'emplacement du dossier de données avec la commande qui suit :
</div>

<pre>mongod --dbpath "autre_dossier"</pre>

<div class="spacer"></div>
<p>Une fois votre instance <b>mongod initialisée</b>, tapez la commande suivante pour vous y connecter avec le <b>client mongo</b> :</p>

<pre>mongo</pre>

<p>Vous verrez que <b>mongo</b> se connecte <b>par défaut</b> à la base de données <b>"test"</b>.</p>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Arrêter MongoDB Enterprise ]</p>

<p>Pour finir, si vous souhaitez <b>arrêter MongoDB Enterprise</b>, pressez simplement les touches <b>Cntrl+C</b> dans le terminal qui contient l'instance <b>mongod exécutée</b>.</p>

<div class="spacer"></div>

<p>Voilà, ceci est la fin du tutoriel d'<b>installation de MongoDB Enterprise</b>. Si vous avez des <b>questions</b> ou alors si vous rencontrer un <b>problème</b>, n'hésitez pas à me <a href="../contact.php">"contacter"</a>.
Je tenterai de vous répondre <b>rapidement et efficacement</b>.</p>
<p>Maintenant, vous êtes prêts à passer aux <b>premières requêtes</b> ! Passons au chapitre sur les <a href="../operations_crud.php">"Opérations CRUD" >></a>. <b>Bon courage !</b></p>

<?php

	include("footer.php");

?>
