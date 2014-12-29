<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Initialisation</li>
</ul>

<p class="titre">[ Initialisation ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#shell">I) Le Shell MongoDB</a></p>
	<p class="elem"><a href="#list">II) Lister les Bases de Données</a></p>
	<p class="elem"><a href="#connex">III) Connexion à la Base de Données</a></p>
	<p class="elem"><a href="#creer">IV) Créer une Collection</a></p>
</div>

<p>Dans un premier temps, veuillez exécuter votre <b>shell mongo</b> afin de vous connecter à la base de données. Puis, <b>sélectionnez la collection appropriée</b> et insérez vos premières données (<b>pas de panique</b>, nous allons voir ça dans le prochain chapitre).
Sur cette page, tout est expliqué afin d'utiliser le <b>shell mongo</b> fournit durant l'installation. Si vous souhaitez installer un <b>client doté
d'une interface utilisateur (GUI)</b>, reportez-vous à la page des <a href="../outils.php">Outils</a> où l'utilisation est beaucoup plus <b>intuitive</b>.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Les clients GUI pour MongoDB implémentent en général un shell où vous allez pouvoir taper exactement les même commandes
	que dans le shell mongo habituel.
</div>
<a name="shell"></a>

<div class="spacer"></div>

<p class="titre">I) [ Le Shell MongoDB ]</p>

<p>Il est <b>important</b> de savoir que le shell MongoDB implémente <b>les fonctions et la syntaxe de Javascript !</b>
Les <b>conditions</b> ou même les <b>boucles</b> sont tout à fait utilisables avec le shell mongo. Petit rappel, sous <b>Linux</b>, <b>Windows</b> ou <b>Mac</b>, la commande pour exécuter le shell mongo est <b>"mongo"</b>, si celui-ci est dans le <b>PATH</b>, ou alors, déplacez-vous
dans le dossier <b>"DOSSIER_DE_MONGO/bin"</b> et tapez cette même commande.
Vous devriez voir quelque chose comme <b>ceci</b> :</p>

<div class="spacer"></div>

<pre>
MongoDB shell version: 2.4.9
connecting to: test
Welcome to the MongoDB shell.
For interactive help, type "help".
For more comprehensive documentation, see
        http://docs.mongodb.org/
Questions? Try the support group
        http://groups.google.com/group/mongodb-user
>
</pre>

<p>Le shell MongoDB est <b>prêt à recevoir vos commandes</b> (au passage, nous aperçevons à la <b>ligne 2</b> que MongoDB se connecte <b>par défaut</b> à la base de données <b>"test"</b>.
Tapez la commande <b>"help"</b> pour plus d'aide sur les différentes <b>fonctions disponibles</b>.</p>
<a name="list"></a>

<div class="spacer"></div>

<p class="titre">II) [ Lister les Base de Données ]</p>

<p>Bien, maintenant que vous êtes <b>connecté</b>, vous allez vouloir <b>afficher la liste des bases de données</b> existantes de votre instance.
Dans votre shell mongo, vous allez devoir taper la commande <b>suivante</b> :</p>

<div class="spacer"></div>

<pre>show dbs</pre>

<p>où encore :</p>

<div class="spacer"></div>

<pre>show databases</pre>

<p>Ces deux commandes auront <b>exactement la même fonction</b>.</p> 
<a name="connex"></a>

<div class="spacer"></div>

<p class="titre">III) [ Connexion à la Base de Données ]</p>

<p>Ensuite, tout comme dans un shell <b>MySql</b> ou tout autre <b>SGBD</b>, vous allez
vouloir <b>choisir</b> et vous <b>connecter</b> à une base de données qui vous <b>intéresse</b>.
Pour cela, dans votre shell mongo, saisissez la commande suivante :</p>

<div class="spacer"></div>

<pre>use maBDD</pre>

<p>Vous devriez avoir le message <b>"switched to db maBDD"</b>.</p> 

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce :</u> Même si vous saisissez la commande "use" suivie d'un nom de base de données qui n'existe pas encore,
	MongoDB va créer cette base de données et la garder en mémoire UNIQUEMENT si vous inserez des données dedans.
</div>

<p>Pour vérifier la base de données que vous <b>utilisez actuellement</b>, vous pouvez taper la commande :</p>

<pre>db</pre>

<p>Cela devrait vous afficher <b>"maBDD"</b> dans le shell.
Maintenant, vous voudrez ajouter, non pas une <b>Table</b> comme en SQL mais une <b>Collection</b>.</p>
<a name="creer"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Créer une Collection ]</p>

<p><b>Continuons</b>, ici, vous allez <b>créer une collection</b> dans laquelle vous allez, par la suite, <b>stocker vos documents</b>, exactement comme vous le feriez pour <b>stocker vos données dans des tables</b>, pour ceux qui connaissent
déjà le monde <b>SQL</b>. Mantenant que vous avez sélectionné votre <b>base de données</b>, vous aller pouvoir saisir la commande <b>suivante</b> afin de créer une collection :</p>

<div class="spacer"></div>

<pre>db.createCollection("maCollection")</pre>

<p>Vous devriez voir le message <b>"{ "ok" : 1 " }"</b> si celle-ci a bien été créée.
Pour tester si celle-ci est bien <b>sauvegardée</b> dans votre base de données, tapez la commande :</p>

<div class="spacer"></div>

<pre>show collections</pre>

<p>Vous avez donc <b>"maCollection"</b> et <b>"system.indexes"</b> qui apparaissent.
Ne vous occupez pas de la collection <b>"system.indexes"</b> pour l'instant, on verra plus tard à quoi elle correspond.</p>

<div class="spacer"></div>

<p>Voilà, votre base de données est <b>prête</b>, maintenant, passons <b>sans tarder</b> aux <b>premières opérations CRUD</b> : <a href="create.php">"Opérations Create" >></a></p>

<?php

	include("footer.php");

?>
