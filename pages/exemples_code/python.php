<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../exemples_code.php">Exemples de Code</a></li>
	<li class="active">Python</li>
</ul>

<p class="titre">[ Exemples de code - Python ]</p>

<p>Salut Développeur Python ! Bon choix de langage car, de mon point de vue, la syntaxe avec MongoDB est beaucoup plus simple et intuitive.
Voici le lien vers la documentation du driver Python de MongoDB : <a href="http://api.mongodb.org/python/current/api/index.html" target="_blank">documentation Python</a>.
Allez on commence :</p>

<div class="spacer"></div>

<p class="titre">[ Installation du driver MongoDB avec Python ]</p>

<p>Tout d'abord, vous allez devoir <b>télécharger et installer</b> le <a href="http://docs.mongodb.org/ecosystem/drivers/python/" target="_blank">Driver Python</a> pour MongoDB.
Les instructions du lien sont très complètes.</p>

<div class="spacer"></div>

<p class="titre">[ Connexion ]</p>

<p>N'oubliez pas d'importer pymongo afin d'accéder à la classe MongoClient :</p>

<pre>from pymongo import MongoClient</pre>

<div class="small-spacer"></div>

<p>Ensuite, une fois que votre driver est ajouté et importé, vous devez instancier un nouvel objet de type MongoClient :</p>

<pre>client = MongoClient('mongodb://localhost:27017/')</pre>

<div class="small-spacer"></div>

<p>Ou si vous souhaitez vous connecter à une base de données précise :</p>

<pre>client = MongoClient('mongodb://localhost:27017/maDB')</pre>

<div class="small-spacer"></div>

<p>Et même avec l'authentification activée :</p>

<pre>client.maDB.authenticate('monLogin', 'monM0tDeP4sse')</pre>

<div class="small-spacer"></div>

<p>Récupérez votre base de données maDB :</p>

<pre>db = client.maDB</pre>

<div class="small-spacer"></div>

<p>Pour enfin récupérer votre collection, nommée "maCollection" par exemple :</p>

<pre>collection = db.maCollection</pre>

<div class="spacer"></div>

<p>Vous pourrez alors effectuer des actions sur votre base de données avec l'objet "collection", libre à vous de consulter la documentation du driver Python.
Voilà, maintenant, regardons de plus près comment effectuer vos premières opérations CRUD.</p>

<div class="spacer"></div>

<p class="titre">[ Opération CREATE - insert(), save(), update() ]</p>

<p class="small-titre">[ insert() ]</p>

<p>Ici, vous allez insérer un simple document :</p>

<pre>
post = {"auteur": "David", "texte": "Mon premier message !", "tags": ["mongodb", "python", "pymongo"]}
post_id = collection.insert(post)
</pre>

<div class="spacer"></div>

<p class="small-titre">[ save() ]</p>

<p>Idem, la syntaxe est très similaire :</p>

<pre>
post = {"auteur": "David", "texte": "Mon premier message !", "tags": ["mongodb", "python", "pymongo"]}
post_id = collection.save(post)
</pre>

<div class="spacer"></div>

<p class="small-titre">[ update() ]</p>

<p>Presque pareil ici, sauf que l'on passe deux documents en paramètres :</p>

<pre>
postCriteres = {"auteur": "David", "texte": "Mon premier message !", "tags": ["mongodb", "python", "pymongo"]}
postMaJ = {"$set": {"auteur": "Jean"}}

post_id = collection.update(postCriteres, postMaJ)
</pre>

<div class="spacer"></div>

<p class="titre">[ Opération READ - find() ]</p>

<p>Sélection de tous les documents existants dans votre collection :</p>

<pre>documents = collection.find()</pre>

<div class="small-spacer"></div>

<p>Ou un document en particulier :</p>

<pre>documents = collection.find( { "type": "fruit", "couleur" : "jaune" })</pre>

<div class="spacer"></div>

<p class="titre">[ Opération DELETE - remove() ]</p>

<p>Supprimons tous les projets nommés "Z" qui doivent être supprimés :</p>

<pre>collection.remove( { "projet": "projet Z", "statut" : "à supprimer" } )</pre>

<div class="spacer"></div>

<p><b>Bien d'autres exemples sont à venir</b> au fur et à mesure de l'évolution de <b>MongoTuto</b> ! Vous avez une requête en tête ? Vous êtes coincé sur une
en particulier ? <b>Envoyez-moi votre requête</b> et je tenterai de vous <b>aider</b>, ainsi que de <b>l'ajouter</b> dans la rubrique de <b>chaque langage</b>.
Me contacter ? Par ici <a href="../contact.php">"Formulaire de contact"</a></p>

<?php

	include("footer.php");

?>
