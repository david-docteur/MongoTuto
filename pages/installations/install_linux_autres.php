<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec les Autres Linux</li>
</ul>

<p class="titre">[ Installation avec les Autres Linux ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#inst">I) Installer MongoDB</a></p>
	<p class="right"><a href="#sys64">- a) Systèmes 64 Bits</a></p>
	<p class="right"><a href="#sys32">- b) Systèmes 32 Bits</a></p>
	<p class="elem"><a href="#dbpath">II) Répertoire de Données</a></p>
	<p class="elem"><a href="#exec">III) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#stop">IV) Arrêter MongoDB</a></p>
</div>

<p>Vous êtes ici car vous souhaitez <b>installer MongoDB</b> sur une distribution Linux autre que <b>RedHat Enterprise</b>, <b>Fedora</b>, <b>CentOs</b>, <b>Ubuntu</b> ou même <b>Debian</b>.
Pour cela, nous allons installer les <b>packages nécessaires</b>, que ce soit pour une version <b>32 Bits</b> ou <b>64 Bits</b> de votre système.
Cette installation est assez <b>générale</b>, ce qui veut dire que vous serez <b>peut-être</b> amené à effectuer de <b>légères modifications</b> pour installer <b>MongoDB</b>.</p>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">I) [ Installer MongoDB ]</p>

<p>Commençons ici par voir comment vous pouvez <b>télécharger</b> et <b>extraire</b> les packages nécessaires à votre système Linux.
Les deux versions supportées sont présentées :</p>
<a name="sys64"></a>

<div class="spacer"></div>

<p class="small-titre">a) Systèmes 64 Bits</p>

<p>Dans un shell, <b>téléchargez</b> le package de la version que vous allez vouloir <b>installer</b> sur votre système (<b>modifiez bien la version en conséquence</b>) :</p>

<pre>curl http://downloads.mongodb.org/linux/mongodb-linux-x86_64-2.4.9.tgz > mongodb-linux-x86_64-2.4.9.tgz</pre>

<div class="spacer"></div>

<p>Ensuite, une fois cela effectué, vous allez devoir <b>extraire</b> cette archive :</p> 

<pre>tar -zxvf mongodb-linux-x86_64-2.4.9.tgz</pre>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous voulez copier le dossier extrait dans un autre, utilisez les commandes suivantes :
</div>

<pre>
mkdir -p mongodb
cp -R -n mongodb-linux-x86_64-2.4.8/ mongodb
</pre>

<p>Avec la première commande, on va d'abord <b>créer un dossier "mongodb"</b> et copier le contenu de l'archive, qui a été extrait, dans ce <b>nouveau répertoire</b>.</p>
<a name="sys32"></a>

<div class="spacer"></div>

<p class="small-titre">b) Systèmes 32 Bits</p>

<div class="alert alert-danger">
	<u>Attention</u> : Les versions de MongoDB 32 Bits ne supportent que les bases de données de 2Go maximum à cause des limitations
	des systèmes d'exploitation. Pour cette raison, n'utilisez jamais MongoDB pour de la production sur un système 32 Bits, ou même une base de données
	de plus de 2Go.
</div>

<div class="spacer"></div>

<p>Téléchargez le <b>package 32 Bits</b> pour votre système :</p>

<pre>curl http://downloads.mongodb.org/linux/mongodb-linux-i686-2.4.9.tgz > mongodb-linux-i686-2.4.9.tgz</pre>

<div class="spacer"></div>

<p><b>Extraire</b> ensuite l'archive que vous venez de télécharger :</p>

<pre>tar -zxvf mongodb-linux-i686-2.4.9.tgz</pre>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous voulez extraire l'archive dans un autre dossier, vous pouvez le faire avec cette commande suivante :
</div>

<pre>
mkdir -p mongodb
cp -R -n mongodb-linux-i686-2.4.8/ mongodb
</pre>

<p>Voilà, ici on va créer, comme pour <b>la version 64 Bits</b>, un dossier nommé <b>"mongodb"</b> et copier le contenu de l'<b>archive</b> dedans.</p>
<a name="dbpath"></a>

<div class="spacer"></div>

<p class="titre">II) [ Répertoire de Données ]</p>

<p>MongoDB se sert du dossier <b>/data/db</b> pour stoquer les données de la base, il est donc essentiel de créer ce dossier dans le répertoire MongoDB :</p>

<pre>mkdir -p /data/db</pre>

<div class="small-spacer"></div>

<p>Vérifiez bien que <b>l'utilisateur exécutant mongod</b> ai les permissions de <b>lecture et d'écriture</b>, au cas où, pour changer les <b>droits d'écriture</b> :</p>

<pre>chown mongodb /data/db</pre>

<div class="small-spacer"></div>

<p>Il est aussi possible de <b>modifier</b> l'emplacement du répertoire de données avec <b>l'option --dbpath</b> comme ceci :</p>

<pre>mongod --dbpath /votre/nouveau/repertoire</pre>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">III) [ Exécuter MongoDB ]</p>

<p>Afin d'exécuter <b>MongoDB</b>, vous allez devoir taper la commande suivante dans un shell :</p>

<pre>mongod</pre>

<p>à partir du dossier ou <b>le ficher mongod</b> se trouve.</p>

<div class="spacer"></div>

<p>De plus, la commande <b>mongod</b> avec l'option</p>

<pre>mongod --dbpath "autre_dossier"</pre>


<div class="spacer"></div>

<p>permet d'indiquer à <b>mongod</b> d'utiliser un emplacement d'un <b>autre répertoire</b> ou sont stockées les données.
Une fois cela terminé et que <b>mongod est en cours d'exécution</b>, tapez la commande <b>"mongo"</b> dans un autre shell afin de vous connecter à votre
base de données. MongoDB va se connecter <b>par défaut</b>, pour la première fois, à la base de données <b>"test"</b>.</p>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Arrêter MongoDB ]</p>
Pour arrêter l'instance <b>mongod</b>, appuyez sur <b>Cntrl + C</b> dans le shell.

<div class="spacer"></div>

<p>Ceci est la fin du tutoriel d'installation de MongoDB pour les versions autres que <b>RedHat Enterprise</b>, <b>Fedora</b>, <b>CentOs</b>, <b>Ubuntu</b> et <b>Debian</b>. Si vous rencontrez un problème durant
l'<b>installation</b>, l'<b>exécution</b> ou même pour tout <b>autre question</b>, n'hésitez pas à me <a href="../contact.php">"contacter"</a>.</p>
<p>Passons maintenant à <b>la suite de ce tutoriel</b> afin d'apprendre comment effectuer vos premières requêtes : <a href="../operations_crud.php">"Opérations CRUD" >></a>.
<b>Bonne chance !</b></p>
 
<?php

	include("footer.php");

?>
