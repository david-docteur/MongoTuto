<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../scripting.php">Scripting</a></li>
	<li class="active">Accéder aux Informations d'Aide du Shell mongo</li>
</ul>

<p class="titre">[ Accéder aux Informations d'Aide du Shell mongo ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#lign">I) Aide en Ligne de Commande</a></p>
	<p class="elem"><a href="#shell">II) Aide du Shell</a></p>
	<p class="elem"><a href="#bdd">III) Aide Base de Données</a></p>
	<p class="elem"><a href="#coll">IV) Aide pour les Collections</a></p>
	<p class="elem"><a href="#curs">V) Aide de Curseur</a></p>
	<p class="elem"><a href="#type">VI) Aide de Type</a></p>
</div>

<p>En plus de l'aide locale, le shell mongo offre plus d'informations sur son système d'aide "en ligne". Regardons ça d'un peut plus près :</p>
<a name="lign"></a>

<div class="spacer"></div>

<p class="titre">I) [ Aide en Ligne de Commande ]</p>

<p>Pour afficher l'aide et la liste des options en démarrant le shell mongo utilisez l'option :</p>

<pre>mongo --help</pre>
<a name="shell"></a>

<div class="spacer"></div>

<p class="titre">II) [ Aide du Shell ]</p>

<p>Pour voir l'aide du shell, tapez la commande suivante dans celui-ci :</p>

<pre>help</pre>
<a name="bdd"></a>

<div class="spacer"></div>

<p class="titre">III) [ Aide Base de Données ]</p>

<p>- Pour voir la liste des bases de données sur le serveur :</p>

<pre>show dbs</pre>

<p>ou, depuis la version 2.4 :</p>

<pre>show databases</pre>

<p>- Pour voir l'aide pour les méthodes, vous pouvez utiliser l'objet db :</p>

<pre>db.help()</pre>

<p>- Pour voir l'implémentation d'une méthode dans le shell, tapez la commande db.methodname sans les parenthèses comme dans l'exemple suivant pour la commande 
db.addUser() :</p>

<pre>db.addUser</pre>
<a name="coll"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Aide pour les Collections ]</p>

<p>- Pour voir la liste des collections disponibles :</p>

<pre>show collections</pre>

<p>Pour voir la liste d'aide des méthodes disponibles pour l'objet collection :</p>

<pre>db.maCollection.help()</pre>

<p>Idem que pour les bases de données, pour voir l'implémentation d'une méthode :</p>

<pre>db.maCollection.save</pre>
<a name="curs"></a>

<div class="spacer"></div>

<p class="titre">V) [ Aide de Curseur ]</p>

<p>Lorsque vous effectuez des opérations de lecture avec la méthode find() dans un shell mongo, vous pouvez utiliser des méthodes variées de curseur
pour modifier le comportement de find() et les méthodes JavaScript pour contrôler la méthode find().

- Pour lister les méthodes disponibles de contrôle de curseur, utilisez la commande :</p>

<pre>db.maCollection.find().help()</pre>

<p>- Pour voir l'implémentation d'une méthode, appellez cette méthode mais sans les parenthèses :</p>

<pre>db.collection.find().toArray</pre>

<p>Quelques méthodes efficaces pour contrôler les curseurs sont :

- hasNext() qui vérifie si le curseur a plus de documents à retourner
- next() qui retourne le prochain document et avance le curseur d'une position en avant
- forEach(fonction) qui itère sur le curseur entier et applique la fonction à chaque document retourné par le curseur.
  La fonction attends un seul argument qui correspond au document de chaque itération.</p>
<a name="type"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Aide de Type ]</p>

<p>Pour avoir la liste des méthodes de classes dans le shell mongo, comme pour BinData(), exécutez la commande :</p>

<pre>help misc</pre>

<div class="spacer"></div>

<p>Voila, ceci était la derniere section du tutoriel sur l'administration, la suite va porter sur la Sécurité avec MongoDB. Par accéder a la premiere page, c'est ici : <a href="../../securite/introduction.php">"Sécurité - Introduction" >></a>.</p>

<?php

	include("footer.php");

?>
