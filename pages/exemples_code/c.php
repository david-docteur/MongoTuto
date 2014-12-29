<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../exemples_code.php">Exemples de Code</a></li>
	<li class="active">C</li>
</ul>

<p class="titre">[ Exemples de code - C ]</p>

<p>Salut Développeur C !
Voici le lien vers la documentation du driver C de MongoDB, ce lui sera très utile : <a href="http://api.mongodb.org/c/current/" target="_blank">Documentation C</a>
Allez on commence :</p>

<div class="spacer"></div>

<p class="titre">[ Installation du driver MongoDB avec C ]</p>

<p>Tout d'abord, vous allez devoir <b>télécharger</b> le <a href="http://docs.mongodb.org/ecosystem/drivers/c/" target="_blank">Driver C</a> pour MongoDB.</p>

<div class="spacer"></div>

<p class="titre">[ Connexion ]</p>

<p>Ensuite, une fois que votre driver est ajouté et importé, vous devez instancier un nouvel objet de type MongoClient :</p>

<pre>$connexion = new MongoClient("mongodb://localhost");</pre>

<div class="small-spacer"></div>

<p>Ou si vous souhaitez vous connecter à une base de données précise :</p>

<pre>$connexion = new MongoClient("mongodb://localhost/madb");</pre>

<div class="small-spacer"></div>

<p>Et même avec l'authentification activée :</p>

<pre>$connexion = new MongoClient("mongodb://localhost/madb", array("username" => "monLogin", "password" => "monM0tDeP4sse"));</pre>

<p>Pour enfin récupérer votre collection, nommée "maCollection" par exemple :</p>

<pre>$collection = $connexion->madb->maCollection;</pre>

<div class="spacer"></div>

<p>Vous pourrez alors effectuer des actions sur votre base de données avec l'objet $collection, libre à vous de consulter la documentation du driver Java.
Voilà, maintenant, regardons de plus prêt comment effectuer vos premières opérations CRUD.</p>

<div class="spacer"></div>

<p class="titre">[ Opération CREATE - insert(), save(), update() ]</p>

<p class="small-titre">[ insert() ]</p>

<p>Voici un exemple d'insertion de document avec la fonction insert(). Supposons que vous souhaitez insérer le document suivant :</p>

<pre>
{
   "nom" : "monNom",
   "prenom" : "monPrenom"
}
</pre>

<p>Insérez ce document avec la méthode insert() :</p>

<pre>$documents = $collection->insert(array('nom' => 'monNom', 'prenom' => 'monPrenom'));</pre>

<div class="spacer"></div>

<p class="small-titre">[ save() ]</p>

<p>Pour sauvegarder le document suivant :</p>

<pre>
{
   "nom" : "monNom",
   "prenom" : "monPrenom"
}
</pre>

<p>Insérez ce document avec la méthode save() :</p>

<pre>$documents = $collection->insert(array('nom' => 'monNom', 'prenom' => 'monPrenom'));</pre>

<div class="spacer"></div>

<p class="small-titre">[ update() ]</p>

<p>Pour mettre à jour un document existant :</p>

<pre>$documents = $collection->update(array('nom' => 'monNom', 'prenom' => 'monPrenom'), array('nom' => 'monNom2', 'prenom' => 'monPrenom2'));</pre>

<div class="spacer"></div>

<p class="titre">[ Opération READ - find() ]</p>

<p>Pour sélectionner tous les documents :</p>

<pre>$docs = $collection->find();</pre>

<p>Pour sélectionner un type particulier de documents :</p>

<pre>$docs = $collection->find(array('type' => 'fruit', 'couleur' => 'jaune'));</pre>

<div class="spacer"></div>

<p class="titre">[ Opération DELETE - remove() ]</p>

<p>Pour supprimer un document particulier :</p>

<pre>$docs = $collection->remove(array('type' => 'fruit', 'couleur' => 'jaune'));</pre>

<div class="spacer"></div>

<p><b>Bien d'autres exemples sont à venir</b> au fur et à mesure de l'évolution de <b>MongoTuto</b> ! Vous avez une requête en tête ? Vous êtes coincé sur une
en particulier ? <b>Envoyez-moi votre requête</b> et je tenterai de vous <b>aider</b>, ainsi que de <b>l'ajouter</b> dans la rubrique de <b>chaque langage</b>.
Me contacter ? Par ici <a href="../contact.php">"Formulaire de contact"</a></p>

<?php

	include("footer.php");

?>
