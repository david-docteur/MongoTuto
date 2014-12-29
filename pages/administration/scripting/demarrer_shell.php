<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../scripting.php">Scripting</a></li>
	<li class="active">Démarrer avec le Shell mongo</li>
</ul>

<p class="titre">[ Démarrer avec le Shell mongo ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#dema">I) Démarrer le Shell mongo</a></p>
	<p class="elem"><a href="#exec">II) Exécuter des Requêtes</a></p>
	<p class="elem"><a href="#prin">III) Print</a></p>
	<p class="elem"><a href="#eval">IV) Evaluer un Fichier JavaScript</a></p>
	<p class="elem"><a href="#pers">V) Utiliser un Shell Personnalisé</a></p>
	<p class="elem"><a href="#exte">VI) Utiliser un Editeur Externe au Shell mongo</a></p>
	<p class="elem"><a href="#quit">VII) Quitter le Shell</a></p>
</div>

<p>Ici nous allons avoir un aperçu de toutes les commandes de base pour bien démarrer avec le shell mongo.</p>
<a name="dema"></a>

<div class="spacer"></div>

<p class="titre">I) [ Démarrer le Shell mongo ]</p>

<p>Pour démarrer le shell mongo et vous connecter à votre instance MongoDB exécutée sur l'interface localhost avec le port par défaut :

1) Allez dans le répertoire d'installation de MongoDB :</p>

<pre>cd "mongodb installation dir"</pre>

<p>2) Tapez ./bin/mongo pour démarrer mongo :</p>

<pre>./bin/mongo</pre>

<p>Si vous avez ajouté "mongodb installation dir"/bin à votre variable d'environnement $PATH, vous pouvez juste taper
mongo plutôt que ./bin/mongo.

3) Pour afficher la base de données que vous êtes en train d'utiliser :</p>

<div class="spacer"></div>

<pre>db</pre>

<p>L'opération devrait retourner "test", qui est la base de données par défaut. Pour changer de base de données, utilisez la commande suivante :</p>

<pre>use "dbname"</pre>

<p>Afin de lister les bases de données disponibles, utilisez la commande show dbs.</p>

<div class="alert alert-info">
	<u>Note</u> : En démarrant, mongo vérifie le répertoire HOME de l'utilisateur pour vérifier la présence d'un fichier JavaScript
	nommé .mongorc.js. Si celui-ci est trouvé, mongo interprète son contenu avant d'afficher le terminal pour la première fois. Si vous utilisez le shell
	pour évaluer un fichier ou une expression JavaScript, soit en utilisant l'ooption --eval ou en ligne de commande ou en spécifiant un fichier .js à mongo,
	mongo va lire le fichier .mongorc.js une fois que le JavaScript ai terminé son exécution.
</div>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">II) [ Exécuter des Requêtes ]</p>

<p>Depuis le shell mongo, vous pouvez utiliser des méthodes du shell pour effectuer des requêtes comme dans l'exemple suivant :</p>

<pre>db.maCollection.find()</pre>

<p>- db fait référence à la base de données en cours
- maCollection est le nom de la collection à interroger.
Si le shell mongo n'accepte pas le nom de la collection, s'il contient un espace ou commence par un nombre par exemple, vous pouvez utiliser une syntaxe 
différente pour faire référence à la collection :</p>

<pre>
db["3test"].find()
db.getCollection("3test").find()
</pre>

<p>La méthode find() est une méthode JavaScript qui va retourner les documents depuis maCollection. La méthode find() retourne un curseur en tant que résultat.
Par contre, dans le shell mongo, si le curseur retourné n'est pas assigné à une variable en utilisant la mot-clé var, alors le cursuer est automatiquement
itéré jusqu'à 20 fois pour afficher les 20 premiers documents qui correspondent à la requête exécutée. Le shell mongo va afficher 
"Type it" pour itérer 20 autres fois.
Vous pouvez définir l'attribut DBQuery.shellBatchSize pour changer le nombre d'itérations de la valeur défaut définie à 20, comme dans l'exemple suivant
qui la définit à 10 :</p>

<pre>DBQuery.shellBatchSize = 10;</pre>
<a name="prin"></a>

<div class="spacer"></div>

<p class="titre">III) [ Print ]</p>

<p>Le shell mongo va automatiquement afficher les résultats de find() si le curseur retourné n'est pas assigné à une variable en utilisant le mot-clé var.
Pour formatter ce résultat, vous pouvez ajouter la méthode .pretty() à l'opération :</p>

<pre>db.maCollection.find().pretty()</pre>

<p>De plus, vous pouvez utiliser les méthodes explicites print suivantes dans le shell mongo :

- print() pour afficher dans formattage
- print(tojson(obj)) pour afficher au format JSON et est similaire à printJson()
- printJson() pour afficher au format JSON et est équivalent à print(tojson(obj))</p>
<a name="eval"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Evaluer un Fichier JavaScript ]</p>

<p>Vous puvez exécuter un fichier .js depuis votre shell mongo en utilisant la fonction load() comme dans l'exemple suivant :</p>

<pre>load("myjstest.js")</pre>

<p>Cette fonction charge et exécute le fichier myjstest.js. La fonction load() accepte les chemins relatifs et absolus. Si le répertoire courant de travail
du shell mongo est /data/db, et que le fichier myjstest.js réside dans /data/db/scripts, alors les appels suivants dans le shell mongo seront équivalents :</p>

<pre>
load("scripts/myjstest.js")
load("/data/db/scripts/myjstest.js")
</pre>

<div class="alert alert-info">
	<u>Note</u> : Il n'y a pas de recherche de chemin pour la fonction load(). Si le script désiré n'est pas dans le répertoire de travail ou dans
	le chemin complet spécifié, mongo ne pourra pas accéder au fichier.
</div>
<a name="pers"></a>

<div class="spacer"></div>

<p class="titre">V) [ Utiliser un Shell Personnalisé ]</p>

<p>Vous aurez peut-être envie de modifier le contenu du shell en créant la variable "prompt" dans le shell. La variable prompt peut contenir
des chaînes de caractères tout comme quelconque code JavaScript. Si prompt détient une fonction qui retourne un string, mongo peut afficher
les informations dynamiques dans chaque prompt. Par exemple, créez un shell avc un nombre d'opérations dans la session courante, définissez les variables suivantes :</p>

<pre>
cmdCount = 1;

prompt = function() {
	return (cmdCount++) + "> ";
}
</pre>

<p>Le shell voudrait donc ressembler à :</p>

<pre>
1> db.collection.find()
2> show collections
3>
</pre>

<p>Pour créer un shell mongo dans la forme de database@hostname$, définissez les variables suivantes :</p>

<div class="spacer"></div>

<pre>
host = db.serverStatus().host;

prompt = function() {
	return db+"@"+host+"$ ";
}
</pre>

<p>Le prompt retournerait alors :</p>

<pre>
database@hostname$ use records
switched to db records
records@hostname$
</pre>

<p>Par exemple, pour créer un shell mongo qui contient le temps d'exécution du système ainsi que le nombre de documents dans la base de données :</p>

<pre>
prompt = function() {
	return "Uptime:"+db.serverStatus().uptime+" Documents:"+db.stats().objects+" > ";
}
</pre>

<div class="spacer"></div>

<p>Le prompt ressemblerait à ceci :</p>

<pre>
Uptime:5897 Documents:6 > db.people.save({name : "James"});
Uptime:5948 Documents:7 >
</pre>
<a name="exte"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Utiliser un Editeur Externe au Shell mongo ]</p>

<p>Depuis la version 2.2, vous pouvez utiliser l'opération edit dans le shell mongo pour éditer une fonction ou une variable dans un éditeur externe.
L'opération edit utilise la valeur de votre variable d'environnement EDITOR.
Avec un terminal de votre système, vous pouvez définir la variable EDITOR et démarrer mongo avec les deux opérations suivantes :</p>

<pre>
export EDITOR=vim
mongo
</pre>

<p>Considérez ensuite l'exemple suivant dans le shell :</p>

<pre>
MongoDB shell version: 2.2.0
> function f() {}
> edit f
> f
function f() {
print("this really works");
}
> f()
this really works
> o = {}
{ }
> edit o
> o
{ "soDoes" : "this" }
>
</pre>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Vu que le shell mongo interprète le code édité depuis un éditeur externe, cela devrait modifier le code dans les fonctions, 	
	en fonction du compilateur JavaScript. mongo devrait convertir 1+1 en 2 ou supprimer les commentaires. Les changements actuels affectent uniquement
	l'apparence du code et va varier en fonction la version de JavaScript utilisée mais ne va pas affecter les sémantiques du code.
</div>
<a name="quit"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Quitter le Shell ]</p>

<p>Pour quitter le shell, tapez quit() ou utilisez le raccourci Cntrl + C.</p>

<div class="spacer"></div>

<p>Derniere section du tutoriel sur l'administration, la suite va concerner les <a href="aide_shell.php">"Accéder aux Informations d'Aide du Shell mongo" >></a>.</p>

<?php

	include("footer.php");

?>
