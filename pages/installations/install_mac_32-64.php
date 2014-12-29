<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../installation.php">Installation</a></li>
	<li class="active">Installation avec Mac OS</li>
</ul>

<p class="titre">[ Installation avec Mac OS ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pre">I) Pré-Requis</a></p>
	<p class="elem"><a href="#instauto">II) Installation Automatique</a></p>
	<p class="elem"><a href="#instmanu">III) Installation Manuelle</a></p>
	<p class="elem"><a href="#exec">IV) Exécuter MongoDB</a></p>
	<p class="elem"><a href="#stop">V) Arrêter MongoDB</a></p>
</div>

<div class="spacer"></div>

<p>Vous êtes sur cette page car vous souhaitez <b>installer MongoDB</b> sur votre Mac, pour <b>OS X 10.6 (Snow Leopard)</b> et pour les versions plus récentes.
Nous allons voir comment installer MongoDB et surtout, comment <b>exécuter votre première instance mongo</b>. Je m'excuse à l'avance pour le manque de 
screenshots, en effet, je n'ai pas de <b>Mac</b> sous la main (très honnêtement, ça me tente ...). <b>Allez, on commence !</b></p>
<a name="pre"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-requis ]</p>

<p>Comme vous l'avez vu dans le <b>plan</b>, il y a <b>deux types</b> d'installation, <b>automatique et manuelle</b>, à vous de choisir la solution que vous préférez.</p>

<div class="alert alert-danger">
	<u>Attention</u> : A partir de la version 2.4, MongoDB supporte seulement OS X 10.6(Snow Leopard) sur Intel x86-64, et plus récentes.
</div>
<a name="instauto"></a>

<div class="spacer"></div>

<p class="titre">II) [ Installation Automatique ]</p>

<p>Alors, pour installer MongoDB <b>automatiquement</b>, mettez à jour le gestionnaire de package <b>Homebrew</b> via un terminal et tapez la commande suivante :</p>

<pre>brew update</pre>

<div class="spacer"></div>

<p>Puis, pour installer le <b>package MongoDB</b>, la commande suivante :</p>

<pre>brew install mongodb</pre>

<div class="spacer"></div>

<p>Voilà, MongoDB est installé sur votre Mac. Par contre, si plus tard vous souhaitez <b>mettre MongoDB à jour</b>, tapez les commandes qui vont suivre :</p> 

<pre>
brew update
brew upgrade mongodb
</pre>
<a name="instmanu"></a>

<div class="spacer"></div>

<p class="titre">III) [ Installation Manuelle ]</p>

<p>Pour l'installation <b>manuelle</b> de MongoDB, vous allez devoir taper la commande suivante dans un terminal pour <b>télécharger MongoDB</b> :</p>

<pre>curl http://downloads.mongodb.org/osx/mongodb-osx-x86_64-2.4.8.tgz > mongodb-osx-x86_64-2.4.8.tgz</pre>

<div class="spacer"></div>

<p>Une fois l'archive téléchargée, vous allez devoir en <b>extraire les fichiers</b> comme ceci :</p>

<pre>tar -zxvf mongodb-osx-x86_64-2.4.8.tgz</pre>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous souhaitez copier les fichiers dans un autre dossier, vous pouvez saisir la commande suivante dans un terminal :
</div>

<pre>
mkdir -p mongodb
cp -R -n mongodb-osx-x86_64-2.4.8/ mongodb
</pre>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Exécuter MongoDB ]</p>

<p>Pour exécuter MongoDB, vous allez devoir lancer le programme <b>"mongod"</b>, mais avant cela, vous devrez créer le <b>répertoire de données</b> avec la commande qui
suit. Le répertoire de données est le répertoire qui va contenir les <b>données et informations des bases de données</b> de votre déploiement MongoDB.
Il est donc <b>impératif</b> de le créer, sinon, votre instance <b>mongod</b> en s'exécutera pas.</p>

<pre>sudo mkdir -p /data/db</pre>

<div class="spacer"></div>

<p>Une fois terminé, vous allez devoir exécuter le <b>processus mongod</b> avec la commande suivante :</p>

<pre>mongod</pre>

<div class="spacer"></div>

<p>si vous êtes dans le même répertoire de l'exécutable ou alors si mongod est dans votre <b>PATH</b>. Je n'expliquerai pas comment
importer dans le PATH, mais il y a de <b>très bons tutoriaux</b>, simples et rapides, sur comment le faire via notre moteur de recherche préféré.
Ensuite, vous pouvez modifier <b>l'emplacement</b> du répertoire de données comme ceci :</p>

<pre>mongod --dbpath "autre_dossier"</pre>

<div class="small-spacer"></div>

<p>Une fois que votre instance mongod est en cours d'exécution, ouvrez un <b>autre terminal</b> et saisissez la commande suivante :</p>

<pre>mongo</pre>

<p>Cette commande va utiliser le programme <b>mongo</b> qui est <b>le client MongoDB</b> qui va ensuite se connecter à votre instance mongod que vous
venez d'exécuter. Vous allez voir que mongo se connecte <b>automatiquement</b> à une base de données nommée <b>"test"</b>.</p>
<a name="stop"></a>

<div class="spacer"></div>

<p class="titre">V) [ Arrêter MongoDB ]</p>

<p>Pour arrêter MongoDB, pressez simplement <b>les touches Cntrl+C</b> dans le terminal qui contient l'instance mongod éxecutée.</p>

<div class="spacer"></div>

<p>Ceci est la fin du <b>tutoriel d'installation</b> de MongoDB pour <b>Mac OS</b>. En cas de problèmes ou de questions, n'hésitez pas à me <a href="../contact.php">"contacter"</a>.
Je tenterai de vous répondre <b>rapidement et efficacement</b>.</p>
<p>Maintenant vous êtes prêt à passer aux <b>premières requêtes</b> ! Passons au chapitre sur les <a href="../operations_crud.php">"Opérations CRUD" >></a>. <b>Bonne chance !</b></p>


<?php

	include("footer.php");

?>
