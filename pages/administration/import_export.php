<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Importer/Exporter les Données MongoDB</li>
</ul>

<p class="titre">[ Importer/Exporter les Données MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#fid">I) Fidélité du Type de Données</a></p>
	<p class="elem"><a href="#imp">II) Importation/Exportation des Données et Opérations de Sauvegardes</a></p>
	<p class="elem"><a href="#for">III) Formats d'Importation/Exportation</a></p>
</div>

<p>Ici, nous allons parler des programmes que MongoDB inclut pour l'importation et l'exportation des données. Ces outils sont utiles
lorsque vous désirez sauvegarder ou exporter une portion de vos données sans capturer la base de données entière. Pour des tâches de migrations plus complètes,
vous voudrez écrire vos propres scripts d'import/export en utilisant un driver client pour intérragir avec la base de données elle-même.
Concernant une récupération en cas de catastrophe et une sauvegarde de routine de la base de données, utilisez les sauvegardes de la base de données intégrale.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Ces outils peuvent avoir un impacte sur les performances sur votre base de données en cours d'exploitation
	car ces outils utilisent l'instance mongod exécutée. Ces processus ne créent pas seulement de traffique sur l'instance exécutée, mais ils forcent
	la base de données à lire toutes les informations dans la mémoire. Lorsque MongoDB lit des données non fréquement utilisées, cela peut causer la détérioration
	des performances pour la base de données. Les outils mongoimport et mongoexport ne préservent pas le type BSON tel qu'il est car BSON est un sous-ensemble
	de JSON. Donc, mongoimport et mongoexport ne peuvent pas représenter les données BSON en JSON. En conséquences, les données exportées ou importées avec
	ces outils vont perdre un peu de leur intégritée.
</div>
<a name="fid"></a>

<div class="spacer"></div>

<p class="titre">I) [ Fidélité du Type de Données ]</p>

<p>JSON n'a pas tous les types que BSON offre : data_binary, data_date, data_timestamp, data_regex, data_oid et data_ref. En conséquences, utiliser un outil qui
décode les documents BSON en JSON va provoquer une perte sensible sur la fidélité des types de données.

Si maintenir la fidélité du type de données est important, écrivez un système d'import ou d'export qui ne force pas les documents BSON en JSON. La liste suivante
des types contient des exemples sur comment MongoDB va représenter les documents BSON en JSON :

- data_binary :</p>

<pre>{ "$binary" : "bindata", "$type" : "t" }</pre>

<p>"bindata" est la représentation en base64 d'un string binaire. t est la représentation hexadécimale d'un simple byte indiquant le type de données.

- data_date</p>

<pre>Date( date )</pre>

<p>"date" est la représentation JSON d'un entier signé 64bits en millisecondes.

- data_timestamp :</p>

<pre>Timestamp( t, i )</pre>

<p>"t" est la représentation JSON d'un entier non signé 32bits en millisecondes. i est un entier non signé 32bits pour l'incrémentation.

- data_regex :</p>

<pre>/jRegex/jOptions</pre>

<p>"jRegex" est une chaîne de caractères qui va contenir des caractères JSON valides ainsi que les doubles quotes ("), mais ne contiendra pas de slash (/).
jOption est un string qui contient uniquement les caractères g, i, m et s.

- data_oid :</p>

<pre>ObjectId( "id" )</pre>

<p>"id" est une chaîne de caractères hexadécimale de longueur 24 maximum. Ces représentations nécessitent que les valeurs data_oid aient un champ "_id"
associé.

- data_ref :</p>

<pre>DBRef( "name", "id" )</pre>

<p>"name" est un string de caractères JSON valides. "id" est une chaîne de charactère hexadécimale de longueur 24.</p>
<a name="imp"></a>

<div class="spacer"></div>

<p class="titre">II) [ Importation/Exportation des Données et Opérations de Sauvegardes ]</p>

<p>Les outils et opérations fournissent des fonctionnalités qui sont utiles afin de fournir certains types de sauvegarde.
En bref, utilisez les outils d'import/export pour sauvegarde un petit sous-ensemble de vos données ou alors pour déplacer vos données depuis ou vers un autre système.
Ces sauvegardes vont capturer un petit ensemble de données crucial ou alors une section des données fréquement modifiée. Peu importe la façon dont vous allez
décider d'importer ou d'exporter vos données, considérez les grandes lignes suivantes :

- nommés les fichiers de sauvegarde afin de les identifier dans le temps, à quel moment l'exportation ou la sauvegarde a été effectuée.
- Le nom doit renseigner sur le contenu de la sauvegarde ainsi que le sous-ensemble de données que le fichier détient.
- N'effectuez ou ne créez pas d'exportations si le processus de sauvegarde va avoir un effet inverse sur le système de production.
- Soyez sûr que l'ensemble de vos données est inclut. Les processus de sauvegarde ou d'exportation peuvent avoir un impacte sur l'intégrité des données
(par exemple : types) ainsi que la pertinence de celles-ci si les mises à jour continuent pendant le processus de sauvegarde.
- Testez vos sauvegardes et exportations en restaurant et en important pour vous assurer que vos sauvegardes sont utiles.</p>
<a name="for"></a>

<div class="spacer"></div>

<p class="titre">III) [ Formats d'Importation/Exportation ]</p>

<p>Ici, nous allons décrire le processus d'import/export de votre base de données, ou une portion, vers un fichier JSON ou CSV.
N'oubliez pas les liens des documentations officielles pour les outils <a href="http://docs.mongodb.org/manual/reference/program/mongoimport" target="_blank">mongoimport</a>
et <a href="http://docs.mongodb.org/manual/reference/program/mongoexport" target="_bank">mongoexport</a>.

Si vous souhaitez copier simlement une base de données ou une collection d'une instance vers une autre, utilisez les commandes copydb, clone ou encore
cloneCollection, qui devraient être plus appropriées pour ce genre d'opération. Le shell mongo fournit la méthode db.copyDatabase().
Ces outils sont aussi utiles pour importer des données dans une base de données MongoDB depuis des applications tierces-parties.

Exportation de Collection avec mongoexport : Avec l'outil mongoexport, vous pourrez créer un fichier de sauvegarde. L'invocation la plus simple ressembe à ceci :</p>

<pre>mongoexport --collection collection --out collection.json</pre>

<p>Cela va exporter tous les documents de la collection appelée "collection" dans le fichier collection.json. Sans le paramètre (--out collection.json), mongoexport
écrit sur la sortie standard (exemple : stdout). Pour aller plus loin, vous pouvez même spécifier un filtre avec une requête en utilisant l'option --query
et limiter les résultats à une seule base de données en utilisant l'option --db :</p>

<pre>mongoexport --db sales --collection contacts --query '{"field": 1}'</pre>

<p>Cette commande retourne tous les documents de la collection contacts de la base de données sales avec un champ nommé field ayant une valeur de 1.
Entourrez la requête de simple guillemets (') pour vous assurez qu'elle n'interagisse pas avec votre shell. Idem ici, les résultats seront envoyés vers la sortie standard.

Par défaut, mongoexport retourne un document JSON par document MongoDB. Spécifiez le paramètre --jsonArray pour retourner le résultat de l'exportation
en tant que simple tableau JSON. Utilisez l'option --csv pour retourner un fichier CSV. Les fichiers CSV vont simplement contenir les informations
de manière à ce que chaque ligne représente un document et chaque valeur est séparée par une virgule (comma separated values).

Si votre instance mongod n'est pas en cours d'exécution, vous pouvez utiliser l'option --dbpath pour spécifier l'endroit ou se trouvent les fichiers de données
de votre instance MongoDB :</p>

<div class="spacer"></div>

<pre>mongoexport --db sales --collection contacts --dbpath /srv/MongoDB/</pre>

<p>Cela lit les fichiers de données directement. Ce processus verrouille le répertoire de données pour éviter les conflits d'écriture. Le processus mongod
ne doit pas être en cours d'exécution ou attaché à ces fichiers de données lorque vous exécutez mongoexport avec cette configuration.

Les options --host et --port vous permettent de spécifier un hôte non-local sur lequel se connecter pour capturer l'exportation, comme dans l'exemple suivant :</p>

<pre>mongoexport --host mongodb1.example.net --port 37017 --username user --password pass --collection...........</pre>

<p>Vous devrez spécifier un nom d'utilisateur et un mot de passe pour chaque commande mongoexport.

Importation de Collection avec mongoimport : Nous allons voir comment restaurer une sauvegarde prise avec mongoexport. La plupart des arguments de mongoexport
existent aussi pour mongoimport :</p>

<pre>mongoimport --collection collection --file collection.json</pre>

<div class="spacer"></div>

<p>Cette commande va importer le contenue du fichier collection.json dans la collection nommée "collection". Si vous ne spécifiez pas l'option --file pour inclure
le fichier de backup, mongoimport accepte la saisie par l'entrée standard (par exemple : stdin).
Si vous spécifiez l'option --upsert, toutes les opérations de mongoimport vont tenter de mettre à jour les documents existants dans la base de données
et insérer ceux qui n'existent pas. Cette option aura un impacte sur les performances en fonction de votre configuration.

Vous pouvez spécifier l'option --db pour importer ces documents dans une base de données particulière. Si votre instance MongoDB n'est pas en cours d'exécution,
utilisez l'option --dbpath pour définir le chemin ou réside le répertoire de données de votre instance. Considérez l'option --journal pour être sûr que
mongoimport enregistre ses opérations dans le journal. Le processus mongod ne doit pas être exécuté ou attaché à ces fichiers de données lorsque vous exécutez
mongoimport.
Utilisez enfin l'option --ignoreBlanks pour ignorer les champs vides. Pour les importations de fichiers CSV et TSV, cette option fournit les mêmes fonctionnalités.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="management_donnees.php">"Management des Données" >></a>.</p>


<?php

	include("footer.php");

?>