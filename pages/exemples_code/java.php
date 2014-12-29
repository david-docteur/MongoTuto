<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../exemples_code.php">Exemples de Code</a></li>
	<li class="active">Java</li>
</ul>

<p class="titre">[ Exemples de code - Java ]</p>

<p>Salut Développeur Java ! Saches que nous partageons le même métier !
Voici le lien vers la JavaDoc du driver Java de MongoDB : <a href="http://api.mongodb.org/java/current/index.html" target="_blank">Javadoc</a>.
Allez on commence :</p>

<div class="spacer"></div>

<p class="titre">[ Installation du driver MongoDB avec Java ]</p>

<p>Tout d'abord, vous allez devoir <b>télécharger</b> le <a href="http://docs.mongodb.org/ecosystem/drivers/java/" target="_blank">Driver Java</a> pour MongoDB.</p>

<p>Veuillez importer votre driver java, le fichier mongo.jar dans votre classpath, soit sous Eclipse ou en ligne de commande.
Une fois que cela sera fait, ajoutez ce bout de code Java dans votre classe :</p>

<pre>import com.mongodb.*;</pre>

<p>Pour l'instant, on va ajouter tout le package à votre classe mais vous pourrez spécifier les packages plus spécifiques plus tard en fonction de vos besoins.</p>

<div class="spacer"></div>

<p class="titre">[ Connexion ]</p>

<p>Ensuite, une fois que votre driver est ajouté et importé, vous devez instancier un nouvel objet de type MongoClient :</p>

<pre>MongoClient mongoClient = new MongoClient();</pre>

<div class="small-spacer"></div>

<p>Dans cet exemple, le driver va automatiquement détecter votre instance mongod et s'y connecter. Si vous le désirez, vous pouvez spéficier l'hôte :</p>

<pre>MongoClient mongoClient = new MongoClient( "localhost" );</pre>

<div class="small-spacer"></div>

<p>Et même spécifier un port spécifique :</p>

<pre>MongoClient mongoClient = new MongoClient( "localhost" , 27017 );</pre>

<div class="small-spacer"></div>

<p>Dans ce cas suivant, cet objet va instancier une connexion sur un ensemble de répliques, le membre primaire sera automatiquement détecté. Vous devez simplement
passer une liste d'adresses comme ceci :</p>

<pre>
MongoClient mongoClient = new MongoClient(Arrays.asList(new ServerAddress("localhost", 27017),
                                      new ServerAddress("localhost", 27018),
                                      new ServerAddress("localhost", 27019)));
</pre>

<div class="spacer"></div>

<p>Pour enfin récupérer votre base de données, nommée "maDB" par exemple :</p>

<pre>DB db = mongoClient.getDB( "maDB" );</pre>

<div class="spacer"></div>

<p>Vous pourrez alors effectuer des actions sur votre base de données avec l'objet "db", libre à vous de consulter la documentation du driver Java.
Voilà, maintenant, regardons de plus près comment effectuer vos premières opérations CRUD.</p>

<div class="spacer"></div>

<p class="titre">[ Opération CREATE - insert(), save(), update() ]</p>

<p class="small-titre">[ insert() ]</p>

<p>Voici un exemple d'insertion de document avec la fonction insert(). Supposons que vous souhaitez insérer le document suivant :</p>

<pre>
{
   "nom" : "MongoDB",
   "type" : "base de données",
   "nombre" : 1,
   "infos" : {
               x : 203,
               y : 102
             }
}
</pre>

<div class="small-spacer"></div>

<p>Insérez ce document avec la méthode insert() :</p>

<pre>
BasicDBObject doc = new BasicDBObject("nom", "MongoDB").
                              append("type", "base de données").
                              append("nombre", 1).
                              append("infos", new BasicDBObject("x", 203).append("y", 102));

collection.insert(doc);
</pre>

<div class="spacer"></div>

<p class="small-titre">[ save() ]</p>

<p>Idem ici, pour sauvegarder le document suivant :</p>

<pre>
{
	"objet" : "livre", 
	"qte" : 20 
}
</pre>

<div class="small-spacer"></div>

<p>Utilisez la méthode suivante :</p>

<pre>
BasicDBObject doc2 = new BasicDBObject("objet", "livre")
						.append("qte", 20);
						
collection.save(doc2);      
</pre>

<div class="spacer"></div>

<p class="small-titre">[ update() ]</p>

<p>Pour mettre à jour un document existant, passz deux documents, un pour la recherche et le deuxième pour les modifications :</p>

<pre>
BasicDBObject docCriteres = new BasicDBObject("moyenne", 5);
BasicDBObject docMaJ = new BasicDBObject("moyenne", 18);
                        
collection.update(docCriteres, docMaJ, true, false);
</pre>

<p>true pour l'upsert et false pour multi.</p>

<div class="spacer"></div>

<p class="titre">[ Opération READ - find() ]</p>

<p>Pour sélectionner tous les documents :</p>

<pre>collection.find();</pre>

<div class="small-spacer"></div>

<p>Pour sélectionner un type particulier de documents :</p>

<pre>collection.find(new BasicDBObject("nom", "abc").append("qte", 40));</pre>

<div class="spacer"></div>

<p class="titre">[ Opération DELETE - remove() ]</p>

<p>Pour supprimer un document particulier :</p>

<pre>collection.remove(new BasicDBObject("nom", "abc"));</pre>

<div class="spacer"></div>

<p><b>Bien d'autres exemples sont à venir</b> au fur et à mesure de l'évolution de <b>MongoTuto</b> ! Vous avez une requête en tête ? Vous êtes coincé sur une
en particulier ? <b>Envoyez-moi votre requête</b> et je tenterai de vous <b>aider</b>, ainsi que de <b>l'ajouter</b> dans la rubrique de <b>chaque langage</b>.
Me contacter ? Par ici <a href="../contact.php">"Formulaire de contact"</a></p>

<?php

	include("footer.php");

?>
