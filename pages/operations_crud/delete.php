<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations DELETE</li>
</ul>

<p class="titre">[ Les Opérations DELETE ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#rem">I) La Méthode remove()</a></p>
	<p class="right"><a href="#tous">- a) Supprimer Tous les Documents</a></p>
	<p class="right"><a href="#cert">- b) Supprimer Certains Documents</a></p>
	<p class="right"><a href="#un">- c) Supprimer un Document</a></p>
</div>

<p><b>Bien joué</b> ! Vous êtes arrivé à la <b>dernière page du tutoriel</b> sur les <b>Opérations CRUD</b>. Pour clôturer ce chapitre, les <b>opérations DELETE</b> vont vous apprendre à <b>supprimer vos données</b> obsolètes ou devenues inutiles.
Vous allez pouvoir supprimer <b>tout d'un coup</b>, ou même <b>un unique document</b> seulement, mais vous aurez aussi la possibilité de <b>choisir les documents</b> à supprimer en fonction
de leurs <b>champs</b>. C'est partit !</p>
<a name="rem"></a>

<div class="spacer"></div>

<p class="titre">I) [ La Méthode remove() ]</p>

<p>La méthode <b>remove()</b> va être utilisée pour <b>supprimer des documents</b> dans une collection, tout comme la commande <b>DELETE du langage SQL</b>.
En voici sa structure :</p>

<pre>db.maCollection.remove(sélection, unique)</pre>

<div class="spacer"></div>

<p>La <b>sélection</b> va, bien sûr, correspondre aux documents <b>que vous cherchez à supprimer</b>. Le second paramètre, <b>"unique"</b>, indique <b>si un seul document</b> doit être
supprimé ou non. Ce paramètre est similaire au paramètre <b>"multi" de la fonction update()</b> vue dans la page précédente du tutoriel.
Ce paramètre n'étant pas spécifié par défaut, la requête de suppression va supprimer <b>tous les documents</b>.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Comme vous vous en doutez, la suppression est irreversible !
</div>
<a name="tous"></a>

<div class="spacer"></div>

<p class="small-titre">a) Supprimer Tous les Documents</p>

<p>Pour supprimer <b>tous les documents</b> d'une collection, il suffit de saisir la commande :</p>

<pre>db.maCollection.remove()</pre>

<p>Une fois la commande effectuée, tous les documents de la collection <b>"maCollection"</b> auront été <b>supprimés</b>.</p>
<a name="cert"></a>

<div class="spacer"></div>

<p class="small-titre">b) Supprimer Certains Documents</p>

<p>Si vous voulez être <b>plus spécifique</b>, vous pouvez <b>supprimer des documents particuliers</b> correspondants aux critères de votre requête :</p>

<pre>db.inventaire.remove( { type : "carte-mère" } )</pre>

<p>Ici, tous les documents de <b>l'inventaire</b> correspondant aux <b>cartes-mères</b> seront supprimés de la collection.</p>
<a name="un"></a>

<div class="spacer"></div>

<p class="small-titre">c) Supprimer un Document</p>

<p>Dans ce cas, si vous voulez supprimer <b>un document particulier</b> en fonction des critères de votre requête :</p>

<pre>db.inventaire.remove( { type : "carte-mère" }, 1 )</pre>

<p>Il suffit juste <b>d'ajouter le paramètre 1</b> après la sélection. Ajouter <b>un paramètre supérieur à "1"</b> n'effacera pas plus de documents, <b>juste un seul</b>.</p>

<div class="spacer"></div>

<p>Voilà, tout est dit pour <b>la fonction remove()</b>, et même pour le chapitre <b>des Opérations CRUD</b> ! Vous savez maintenant effectuer n'importe quel type de requête
avec <b>MongoDB</b> ! Vous voulez <b>plus d'exemples</b> ? Allez visiter la rubrique <a href="../exemples_code.php">"Exemples de code"</a> du site. Si je n'est pas été clair ou si vous souhaitez
plus de détails :<a href="../contact.php">"contactez-moi"</a> !</p>

<p>Si vous êtes arrivés sérieusement jusqu'à là, <b>j'en suis ravi</b> ! Et je peux vous dire qu'il y a encore <b>beaucoup d'autres choses à découvrir</b> sur MongoDB !
Passons au chapitre suivant sur la <a href="../modelisation_donnees.php">"Modélisation de données" >></a>. Celui-ci est <b>important</b>
car il vous apprend à bien <b>gérer et structurer</b> vos données.</p>

<?php

	include("footer.php");

?>
