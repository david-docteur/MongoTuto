<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../scripting.php">Scripting</a></li>
	<li class="active">Ecrire des Scripts avec le Shell mongo</li>
</ul>

<p class="titre">[ Ecrire des Scripts avec le Shell mongo ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#ouvr">I) Ouvrir de Nouvelles Connexions</a></p>
	<p class="elem"><a href="#diff">II) Différences Entre mongo Interractif et Scripté</a></p>
	<p class="elem"><a href="#scri">III) Scripting</a></p>
	<p class="elem"><a href="#opti">IV) L'option --eval</a></p>
	<p class="elem"><a href="#exec">V) Exécuter un Fichier Javascript</a></p>
</div>

<p>Vous pouvez aisément écrire des scripts JavaScript avec le shell mongo qui manipulent les données MongoDB ou effectuer des tâches administratives.</p>
<a name="ouvr"></a>

<div class="spacer"></div>

<p class="titre">I) [ Ouvrir de Nouvelles Connexions ]</p>

<p>Depuis un shell/terminal mongo ou depuis un fichier JavaScript, vous pouvez instancier des connexions à une base de données en utilisant le constructeur
Mongo() :</p>

<pre>
new Mongo()
new Mongo("host")
new Mongo("host:port")
</pre>

<p>Considérons l'exemple suivant qui instancie une nouvelle connexion à l'instance MongoDB exécutée en localhost sur le port par défaut et définit la variable
globale "db" de myDatabase en utilisant la méthode getDB() :</p>

<pre>
conn = new Mongo();
db = conn.getDB("myDatabase");
</pre>

<p>De plus, vous pouvez utiliser la méthode connect() pour vous connecter à une instance MongoDB. L'exemple suivant se connecte à l'instance MongoDB
qui est exécutée en localhost avec le port 27020 et définit la variable globale db :</p>

<pre>db = connect("localhost:27020/myDatabase");</pre>
<a name="diff"></a>

<div class="spacer"></div>

<p class="titre">II) [ Différences Entre mongo Interractif et Scripté ]</p>

<p>Lorsque vous écrivez des scripts pour le shell mongo, considérez les points suivants :

- Pour définir la variable globale db, utilisez la méthode getDB() ou la méthode connect(). Vous pouvez assigner la référence de la base de données
à une variable autre que db. 
- Dans le script, appelez la méthode db.getLastError() explicitement pour attendre le résultat retourné des opérations d'écriture.
- Vous ne pouvez pas utiliser des commandes (par exemple : use "dbname", show dbs etc ...) dans un fichier JavaScript car elles ne sont pas du JavaScript valide.
Le tableau suivant associe les commandes les plus communes à leurs équivalents JavaScripts :</p>

<table>
	<tr>
		<th>Commandes Shell</th>
		<th>Equivalents JavaScript</th>
	</tr>
	<tr>
		<td>show dbs, show databases</td>
		<td>db.adminCommand('listDatabases')
</td>
	</tr>
	<tr>
		<td>use "db"</td>
		<td>db = db.getSiblingDB('db')</td>
	</tr>
	<tr>
		<td>show collections</td>
		<td>db.getCollectionNames()</td>
	</tr>
	<tr>
		<td>show users</td>
		<td>db.system.users.find()</td>
	</tr>
	<tr>
		<td>show log "logname"</td>
		<td>db.adminCommand( { 'getLog' : 'logname' } )</td>
	</tr>
	<tr>
		<td>show logs</td>
		<td>db.adminCommand( { 'getLog' : '*' } )</td>
	</tr>
	<tr>
		<td>it</td>
		<td>cursor = db.collection.find() if( cursor.hasNext() ) { cursor.next(); }</td>
	</tr>
</table>

<div class="spacer"></div>

<p>- En mode interractif, mongo affiche les résultats des opérations en incluant le contenu de tous les curseurs. Dans les scripts, utilisez soit
la fonction print() de JavaScript, soit la fonction printjson() spécifique à mongo qui retourne du JSON formatté :</p>

<pre>
cursor = db.collection.find();

while ( cursor.hasNext() ) {
		printjson( cursor.next() );
}
</pre>
<a name="scri"></a>

<div class="spacer"></div>

<p class="titre">III) [ Scripting ]</p>

<p>Depuis une invite de commande, utilisez mongo pour évaluer du JavaScript.</p>
<a name="opti"></a>

<div class="spacer"></div>

<p class="titre">IV) [ L'option --eval ]</p>

<p>Utilisez l'option --eval avec mongo pour passer un fragment Javascript au shell comme dans l'exemple suivant :</p>

<pre>mongo test --eval "printjson(db.getCollectionNames())"</pre>

<p>Cela retourne le résultat de db.getCollectionNames() en utilisant le shell mongo connecté à l'instance mongod ou mongos localhost sur le port
27017 par défaut.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">V) [ Exécuter un Fichier Javascript ]</p>

<p>Vous pouvez spécifier un fichier .js au shell mongo, et mongo va exécuter le Javascript directement. Considérez l'exemple suivant :</p>

<pre>mongo localhost:27017/test myjsfile.js</pre>

<p>Cette opération exécute le script myjsfile.js dans un shell mongo connecté à la base de données "test" sur l'instance mongod accessible via l'interface
locahost sur le port 27017. 

D'une autre façon, vous pouvez spécifier les paramètres de connexion MongoDB à l'intérieur du fichier JavaScript en utilsiant le constructeur Mongo().
Vous pouvez exécuter un fichier .js depuis le shell mongo en utilisant la méthode load() comme suivant :</p>

<pre>load("myjstest.js")</pre>

<p>La fonction charge et exécute le fichier myjstest.js

La méthode load() accepte les chemins relatifs et absolus. Si le répertoire de travail courant du shell mongo est /data/db, et que le fichier myjstest.js
réside dans /data/db/scripts, alors les appels suivants sous le shell mongo seront équivalents :</p>

<pre>
load("scripts/myjstest.js")
load("/data/db/scripts/myjstest.js")
</pre>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Il n'y a pas de chemin de recherche pour la fonction load(). Si le script désiré n'est pas dans le répertoire de travail courant,
	ou dans le chemins complet spécifié, mongo ne pourra bien évidement pas accéder au fichier souhaité.
</div>

<div class="spacer"></div>

<p>La suite va concerner les <a href="demarrer_shell.php">"Démarrer avec le Shell mongo" >></a>.</p>

<?php

	include("footer.php");

?>
