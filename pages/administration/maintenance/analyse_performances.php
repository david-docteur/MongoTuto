<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Analyser les Performances des Opérations</li>
</ul>

<p class="titre">[ Analyser les Performances des Opérations ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#prof">I) Niveaux de Profiling</a></p>
	<p class="elem"><a href="#acti">II) Activer le Profiling de Base de Données et Définir le Niveau de Profiling</a></p>
	<p class="right"><a href="#spec">- a) Spécifier le Seuil pour les Opérations Lentes</a></p>
	<p class="right"><a href="#veri">- b) Vérifier le Niveau de Profiling</a></p>
	<p class="right"><a href="#desa">- c) Désactiver le Profiling</a></p>
	<p class="right"><a href="#inst">- d) Activer le Profiling pour une Instance mongod Entière</a></p>
	<p class="right"><a href="#shar">- e) Profiling de Base de Données et Sharding</a></p>
	<p class="elem"><a href="#visu">III) Visualiser les Données du Profiler</a></p>
	<p class="right"><a href="#requ">- a) Exemple de Requêtes sur le Profiler</a></p>
	<p class="right"><a href="#affi">- b) Afficher les 5 Résultats les Plus Récents</a></p>
	<p class="elem"><a href="#over">IV) OverHead de Profiler</a></p>
</div>

<p>Le profiler de base de données collecte des informations à propos des opérations d'écritures de MongoDB mais aussi des curseurs et des commandes
effectuées sur une instance mongod en cours d'exécution. Vous pouvez activer le profiling par base de données. Le niveau de profiling est aussi configurable
lorsque vous l'activez.
Le profiler de base de données écrit toutes les données qu'il collecte dans la collection system.profile, qui est une collection capped.</p>
<a name="prof"></a>

<div class="spacer"></div>

<p class="titre">I) [ Niveaux de Profiling ]</p>

<p>Les niveaux de profiling suivants sont disponibles :
0 - le profiler est désactivé, il ne collecte aucunes données.
1 - collecte des données pour les opérations lentes uniquement. Par défaut, les opérations lentes sont plus lentes que 100 millisecondes. Vous pouvez
modifier ce seuil pour les opérations lentes avec l'option d'exécution slowms ou la commande setParameter.
2 - collecte les données de toutes les opérations de la base de données.</p>
<a name="acti"></a>

<div class="spacer"></div>

<p class="titre">II) [ Activer le Profiling de Base de Données et Définir le Niveau de Profiling ]</p>

<p>Vous pouvez le profiling de base de données depuis un shell mongo ou par l'intermédiaire d'un driver en utilisant la commande "profile". Ici, nous allons
découvrir comment l'effectuer à partir du shell mongo.
Lorsque vous activez le profiling, vous définissez en même temps le niveau de profiling. Le profiler enregistre les données dans la collection system.profile.
MongoDB créé alors la collection system.profile dans une base de données après avoir activé le profiling pour cette même base de données.

Pour activer le profiling et choisir son niveau, utilisez la commande db.setProfilingLevel() dans un shell mongo, en passant le niveau de profiling en tant
que paramètre. Par exemple, pour activr le profiling pour toutes opérations de votre base de données, considérez l'opération suivante dans un shell mongo :</p>

<pre>db.setProfilingLevel(2)</pre>

<p>Le shell va retourner un document l'ancien niveau de profiling. La paire valeur-clé "ok" : 1 indique que l'opération a réussie :</p> 

<pre>{ "was" : 0, "slowms" : 100, "ok" : 1 }</pre>
<a name="spec"></a>

<div class="spacer"></div>

<p class="small-titre">a) Spécifier le Seuil pour les Opérations Lentes</p>

<p>Le seuil pour les opérations lentes s'applique à l'instance mongod entière. Lorsque vous changez ce seuil, vous le changez pour toutes les bases de données
de l'instance.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Comme que le seuil s'applique à toutes les bases de données de l'instance, veuillez définir ce seuil avec la valeur la plus haute
	possible.
</div>

<div class="spacer"></div>

<p>Par défaut, le seuil d'opérations lentes est de 100 millisecondes. Les bases de données ayant un niveau de profiling définit à 1 vont enregistrer
les opérations plus lentes que 100 millisecondes. Pour changer ce seuil, passez deux paramètres à la commande db.setProfilingLevel() dans un shell mongo.
Le premier paramètre définit le niveau de profiling pour la base de données en cours, et le second définit le seuil pour l'instance mongod entière.
Par exemple, la commande suivante définit le niveau de profiling pour la base de données courante à 0, c'est à dire que le profiling est désactivé et
que le seuil d'opérations lentes est à 20 millisecondes pour l'instance mongod. Toute base de données de l'instance utilisant le niveau de profiling 1 va utiliser
ce seuil :</p>

<pre>db.setProfilingLevel(0,20)</pre>
<a name="veri"></a>

<div class="spacer"></div>

<p class="small-titre">b) Vérifier le Niveau de Profiling</p>

<p>Pour vérifier le niveau actuel de profiling, utilisez la commande suivante :</p>

<pre>db.getProfilingStatus()</pre>

<p>Le shell retourne le document suivant :</p>

<pre>{ "was" : 0, "slowms" : 100 }</pre>

<p>Le champ "was" indique l'ancien niveau de profiling. Le champ slowms indique combien de temps une opération doit exister pour passer le seuil de lenteur.
MongoDB va enregistrer les opérations qui prennent plus de temps que ce seuil si le profiling est de niveau 1. Ce document retourne le niveau de profiling
dans le champ "was".
Pour retourner uniquement le statut de profiling, utilisez la commande suivante dans un shell mongo :</p>

<pre>db.getProfilingLevel()</pre>
<a name="desa"></a>

<div class="spacer"></div>

<p class="small-titre">c) Désactiver le Profiling</p>

<p>Pour désactiver le profiling, utilisez la commande suivante :</p>

<pre>db.setProfilingLevel(0)</pre>
<a name="inst"></a>

<div class="spacer"></div>

<p class="small-titre">d) Activer le Profiling pour une Instance mongod Entière</p>

<p>Pour des raisons de développement ou de test, vous pouvez activer le profiling de base de données pour une instance mongod entière.
Le niveau de profiling s'applique à toutes les bases de données inclues dans l'instance mongod. Pour activer le profiling pour une instance mongod,
passez les paramètres suivants à mongod au démarrage, ou alors dans un fichier de configuration :</p>

<p>mongod --profile=1 --slowms=15</p>

<p>Cela va définir le niveau de profiling à 1, ce qui collecte uniquement des informations sur les opérations lentes, et définit les opérations en tant que
lentes lorsque celles-ci dépassent les 15 millisecondes.</p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="small-titre">e) Profiling de Base de Données et Sharding</p>

<p>Vous ne pouvez pas activer le profiling sur une instance mongos. Pour activer le profiling avec un sharded cluster, vous devez activer le profiling
pour chaque instance mongod dans le cluster.</p>
<a name="visu"></a>

<div class="spacer"></div>

<p class="titre">III) [ Visualiser les Données du Profiler ]</p>

<p>Le profiler de base de données enregistre ses informations à propos des informations de la base de données dans la collection sytem.profile.
Pour voir les informations de profiling, interrogez la collection system.profile.</p>
<a name="requ"></a>

<div class="spacer"></div>

<p class="small-titre">a) Exemple de Requêtes sur le Profiler</p>

<p>Nous allons voir ici quelques requêtes effectuées sur la collection system.profile. Par exemple, pour retourner les 10 dernières entrées
de la collection system.profile, exécutez une requête similaire à celle-ci :</p>

<pre>db.system.profile.find().limit(10).sort( { ts : -1 } ).pretty()</pre>

<p>Pour retourner toutes les opérations sauf les commandes ($cmd) :</p>

<pre>db.system.profile.find( { op: { $ne : 'command' } } ).pretty()</pre>

<p>Pour retourner les opérations effectuées sur une collection particulière, exécutez une requête ressemblant à la suivante. Ici nous allons retourner
les opérations de la collection test de la base de données mydb :</p>

<pre>db.system.profile.find( { ns : 'mydb.test' } ).pretty()</pre>

<p>Pour retourner les opératios plus lentes que 5 millisecondes :</p>

<pre>db.system.profile.find( { millis : { $gt : 5 } } ).pretty()</pre>

<p>Pour retourner les opérations à une date précise :</p>

<pre>
db.system.profile.find(
	{
		ts : {
			$gt : new ISODate("2012-12-09T03:00:00Z") ,
			$lt : new ISODate("2012-12-09T03:40:00Z")
		}
	}
).pretty()
</pre>

<div class="spacer"></div>

<p>L'exemple suivant maintenant va interroger les opérations entre deux dates, supprimer le champ user du résultat en sortie pour le rendre plus facile
à lire, et trie les résultats en fonction du temps d'exécution de chaque opération :</p>

<pre>
db.system.profile.find(
	{
		ts : {
			$gt : new ISODate("2011-07-12T03:00:00Z") ,
			$lt : new ISODate("2011-07-12T03:40:00Z")
		}
	},
	{ user : 0 }
).sort( { millis : -1 } )
</pre>
<a name="affi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Afficher les 5 Résultats les Plus Récents</p>

<p>Sur une base de données qui a le profiling d'activé, la commande show profile dans un shell mongo affiche les 5 opérations les plus récentes
qui ont nécessitées au moins une milliseconde à s'exécuter :</p>

<pre>show profile</pre>
<a name="over"></a>

<div class="spacer"></div>

<p class="titre">IV) [ OverHead de Profiler ]</p>

<p>Une fois activé, le profiling a un impacte minimale sur les performances. La collection system.profile est une capped collection avec une taille
par défaut de 1 mo. Une collection de cette taille peut typiquement stocker plusieurs milliers de documents de profiling, mais certaines applications
pourraient utiliser plus ou moins d'informations de profiling par opération.

Pour changer la taille de la collection system.profile, vous devez :
1) Désactiver le profiling
2) Supprimer (drop) la collection system.profile
3) Créer une nouvelle collection system.profile
4) Ré-activer le profiling

Par exemple, pour créer une nouvelle collection system.profile qui est large de  4 000 000 bits (4mo), utilisez la séquence de commandes suivante :</p>

<pre>
db.setProfilingLevel(0)

db.system.profile.drop()

db.createCollection( "system.profile", { capped: true, size:4000000 } )

db.setProfilingLevel(1)
</pre>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="monitoring_snmp.php">"Monitoring MongoDB avec SNMP" >></a>.</p>

<?php

	include("footer.php");

?>
