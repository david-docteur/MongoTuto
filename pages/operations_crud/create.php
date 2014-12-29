<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations CREATE</li>
</ul>

<p class="titre">[ Les Opérations CREATE ]</p>

<div class="spacer"></div>

<p>Dans le tutoriel précédent, vous avez appris à <b>initialiser</b> votre base de données ainsi qu'à créer <b>une ou plusieurs collection(s)</b>.
Maintenant vous êtes enfin prêts à <b>constituer votre première requête</b> et <b>insérer des données dans votre collection</b>. Pour cela, il y a une méthode <b>très simple</b>
avec MongoDB.</p>

<div class="spacer"></div>

<p class="titre">[ Insérer un Document BSON ]</p>

<p>La méthode qui va permettre de <b>créer un document BSON</b> dans votre collection est la <b>suivante</b> :</p>

<div class="spacer"></div>

<pre>db.maCollection.insert(document)</pre>

<div class="spacer"></div>

<p>Cette méthode va simplement <b>insérer le document BSON</b> que vous lui passez en <b>paramètre</b>.
Par exemple, si vous souhaitez insérer le document <b>ci-dessous</b> :</p>

<div class="spacer"></div>

<pre>
{
	"_id" : "1",
	"nom" : "nom1"
}
</pre>

<div class="spacer"></div>

<p>Vous allez devoir saisir dans le <b>shell</b> cette commande :</p>

<pre>db.maCollection.insert( { "_id" : "1", "nom" : "nom1" } )</pre>

<p>Le shell <b>ne renverra normalement aucun message</b>, ce qui signifie que le document <b>a bien été inséré</b>.</p>

<div class="spacer"></div>

<p>Vous voulez maintenant <b>vérifier</b> que votre information est bien <b>stockée</b> ? La méthode <b>find()</b> est la méthode qui va permettre
d'<b>interroger et d'afficher</b> les documents contenus dans la collection. Celle-ci concerne les <b>opérations READ</b> du chapitre suivant. En voici un exemple :</p>

<pre>db.maCollection.find()</pre>

<p>Une fois cette commande effectuée, vous allez voir <b>la liste</b> des documents contenus dans la collection <b>"maCollection"</b>.</p>

<div class="spacer"></div>

<p>La méthode <b>db.maCollection.find( { } )</b>, qui prend en <b>paramètre</b> un <b>document vide</b>, est <b>complètement identique</b> à la précédente.
Cette commande est <b>similaire</b> à la suivante pour ceux qui connaissent déjà le <b>langage SQL</b> :</p>

<pre>SELECT * FROM maCommande;</pre>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Pour ceux qui n'ont pas inséré de champs "_id" dans leur requête, vous pouvez voir que celui-ci a été généré automatiquement
	par MongoDB. C'est un procédé tout à fait normal car MongoDB s'en sert comme clé primaire ! En gros, pour ceux qui ne viennent pas de SQL, nous allons
	voir plus tard que ce champ "_id" est généré pour distinguer chaque document et éviter les doublons dans la base de données.
</div>

<p>Nous allons voir aussi que les méthodes <b>update()</b> et <b>save()</b> peuvent aussi <b>créer des documents</b> dans certains cas. Pour cela, rendez-vous dans le chapitre
des <b>opérations UPDATE</b>.</p>

<div class="spacer"></div>

<p>Voilà, la page sur <b>les opérations CREATE</b> est terminée. Pour <b>plus d'exemples</b>, veuillez passer à la rubrique <a href="../exemples_code.php">"Exemples de code"</a>.
Si vous souhaitez poursuivre sur le <b>tutoriel des opérations de type READ</b>, avec lesquelles vous allez pouvoir <b>rechercher des documents</b> dans une collection, c'est par ici : <a href="read.php">"Opérations READ" >></a>.</p>

<?php

	include("footer.php");

?>
