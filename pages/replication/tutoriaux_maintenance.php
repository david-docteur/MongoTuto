<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Maintenance de Replica Set</li>
</ul>

<p class="titre">[ Maintenance de Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#modi">I) Modifier la Taille de l'Oplog</a></p>
	<p class="right"><a href="#modpr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#modp">- b) Procédure</a></p>
	<p class="elem"><a href="#forc">II) Forcer un Membre à devenir Primaire du Replica Set</a></p>
	<p class="right"><a href="#forpr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#forp">- b) Procédure</a></p>
	<p class="elem"><a href="#resy">III) Resynchroniser un Membre du Replica Set</a></p>
	<p class="right"><a href="#synca">- a) Synchroniser un Membre Automatiquement</a></p>
	<p class="right"><a href="#syncc">- b) Synchroniser un Membre en copiant les fichiers de données d'un Autre</a></p>
	<p class="elem"><a href="#conf">IV) Configurer les Ensembles de Tags du Replica Set </a></p>
	<p class="right"><a href="#diff">- a) Différences entre les Préférences de Lecture et le Write Concern</a></p>
	<p class="right"><a href="#ajen">- b) Ajouter un Ensemble de Tags à un Replica Set</a></p>
	<p class="right"><a href="#wc">- c) Write Concerns Personnalisés pour de Multiples DataCenters</a></p>
	<p class="right"><a href="#tags">- d) Configurer les Ensembles de Tags pour une Séggrégation Fonctionnelle des Opérations de Lecture et d'Ecriture</a></p>
	<p class="elem"><a href="#recon">V) Reconfigurer un Replica Set avec des Membres Indisponibles</a></p>
	<p class="right"><a href="#recfo">- a) Reconfigurer en forcant la Reconfiguration</a></p>
	<p class="right"><a href="#recre">- b) Reconfigurer en remplaçant le Replica Set</a></p>
	<p class="elem"><a href="#gere">VI) Gérer la Réplication en Chaîne</a></p>
	<p class="right"><a href="#desa">- a) Désactiver la Réplication en Chaîne</a></p>
	<p class="right"><a href="#reac">- b) Réactiver la Réplication en Chaîne</a></p>
	<p class="elem"><a href="#chan">VII) Changer les noms d'hôtes dans un Replica Set</a></p>
	<p class="right"><a href="#ve">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#asso">- b) Assomptions</a></p>
	<p class="right"><a href="#nh">- c) Changer les noms d'Hôtes en Maintenant la Disponibilité du Replica Set</a></p>
	<p class="right"><a href="#nhmt">- d) Changer Tous les Noms d'hôtes en Même Temps</a></p>
	<p class="elem"><a href="#cibl">VIII) Configurer la Cible de Synchronisation d'un Membre Secondaire</a></p>
</div>

<p></p>
<a name="modi"></a>

<div class="spacer"></div>

<p class="titre">I) [ Modifier la taille de l'Oplog ]</p>

<p>L'Oplog existe en interne de MongoDB en tant que Collection capped, donc vous ne pouvez pas modifier sa taille en ce qui concerne les opérations normales.
En général, la taille par défaut de l'Oplog est une taille acceptable et sera suffisante pour ce que vous souhaitez faire. Par contre, dans certaines situations,
vous aurez besoin d'un Oplog plus grand ou alors plus petit. Par exemple, vous aurez besoin de changer sa taille si votre application réalise de nombreuses
multi-updates ou alors de nombreuses suppressions pendant une courte durée. Nous allons donc voir maintenant commen modifier la taille de l'Oplog afin que celle-ci
corresponde à votre application.</p>
<a name="modpr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-requis</p>

<p>Afin de changer la taille de l'Oplog, vous devrez effectuer une maintenant sur chacun des membres du Replica Set. En bref, il voudra faudra stopper
chaque instance mongod, la redémarrer en mode standalone, modifier la taille de l'Oplog puis redémarrer le membre en question.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Commencez toujours par modifier l'Oplog des membres secondaires en terminant par le membre primaire.
</div>
<a name="modp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>- Redémarrez le membre en mode standalone</p>

<div class="alert alert-success">
	<u>Astuce</u> : Utilisez toujours la commande rs.stepDown() pour forcer le membre primaire à devenir secondaire avant d'arrêter le serveur.
	Cela facilite un processus d'élection plus efficace.
</div>

<div class="spacer"></div>

<p>- Re-créez l'Oplog avec une nouvelle taille puis, avec une entrée de l'ancien Oplog en tant que point de repère.

- Redémarrez l'instance en tant que membre du Replica Set</p>

<p>Si vous souhaitez redémarrer un membre secondaire en mode Standalone sur un numéro de port différent, arrêtez d'abord une des instances non-primaire de votre
Replica Set. Par exemple, pour arrêter :</p>

<pre>db.shutdownServer()</pre>

<div class="spacer"></div>

<p>Redémarrez ensuite cette instance mongod en standalone sur un port différent, sans l'option --replSet :</p>

<pre>mongod --port 37017 --dbpath /srv/mongodb</pre>

<div class="spacer"></div>

<p>Créez maintenant une sauvegare de l'Oplog (cette procédure est bien sûr optionelle) :</p>

<pre>mongodump --db local --collection 'oplog.rs' --port 37017</pre>

<div class="spacer"></div>

<p>Re-créez l'Oplog avec la nouvelle taille et une "graîne" de l'ancien Oplog en sauvegardant la dernière entrée :</p>

<pre>
use local
db = db.getSiblingDB('local')
</pre>

<div class="spacer"></div>

<p>Utilisez en suite la méthode db.collection.save() puis un sort() inverse afin de trouver la dernière entrée de l'ancien Olplog puis de la sauvegarder
dans une collection temporaire :</p>

<pre>db.temp.save( db.oplog.rs.find( { }, { ts: 1, h: 1 } ).sort( {$natural : -1} ).limit(1).next() )</pre>

<div class="spacer"></div>

<p>Pour voir cette entrée :</p>

<pre>db.temp.find()</pre>

<div class="spacer"></div>

<p>Maintenant, vous allez vouloir supprimer la collection de l'Oplog courant. Supprimez l'ancienne collection oplog.rs se situant dans la base de données local :</p>

<pre>
db = db.getSiblingDB('local')
db.oplog.rs.drop()
</pre>

<div class="spacer"></div>

<p>Si tout s'est bien déroulé, le shell retourne true.
Crééons maintenant le nouvel Oplog en utilisant la command create. Spécifiez sa nouvelle taille en paramètre avec la valeur de votre choix :</p>

<pre>db.runCommand( { create: "oplog.rs", capped: true, size: (2 * 1024 * 1024 * 1024) } )</pre>

<div class="spacer"></div>

<p>Une taille de 2 * 1024 * 1024 * 1024 va créer un nouvel Oplog de 2Go. Si tout cela s'est bien terminé, le shell vous retourne :</p>

<pre>{ "ok" : 1 }</pre>

<div class="spacer"></div>

<p>Insérez maintenant la dernière entrée de l'ancien Oplog dans le nouveau avec la fonction suivante :</p>

<pre>db.oplog.rs.save( db.temp.findOne() )</pre>

<div class="spacer"></div>

<p>Pour confirmer que cette entrée est bien située dans le nouvel Oplog :</p>

<pre>db.oplog.rs.find()</pre>

<div class="spacer"></div>

<p>Redémarrez enfin le mongod en tant que membre du Replica Set avec son port habituel :</p>

<pre>
db.shutdownServer()
mongod --replSet rs0 --dbpath /srv/mongodb
</pre>

<div class="spacer"></div>

<p>Ce membre va se restaurer se mettre à niveau avant qu'il soit éligibile pour une élection pour devenir primaire.
Répetez le processus pour tous les membres qui risqueraient de devenir primaire.
Pour changer la taille de l'Oplog sur le membre primaire, arrêtez le membre primaire avec la méthode rs.stepDown et répétez la procédure
de changement d'Oplog comme l'on vient de la décrire.</p>
<a name="forc"></a>

<div class="spacer"></div>

<p class="titre">II) [ Forcer un Membre à devenir Primaire du Replica Set ]</p>

<p></p>
<a name="forpr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-Requis</p>

<p>Vous pouvez forcer un membre à devenir primaire en lui donnant une priorité plus forte que celle de chaque autre membre. En revanche, vous pouvez
forcer un membre à ne jamais devenir primaire en définissant sa priorité à 0.</p>
<a name="forp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>La procédure suivante permet de forcer un membre à devenir primaire en définissant sa priorité plus forte que les autres.
Supposons que votre membre primaire est m1.exmaple.net et vous souhaiteriez que m3.example.net le devienne à la place (peut-être avez-vous
installé du matériel plus puissant sur celui-ci ?). Cette procédure prend en compte un Replica Set de trois membres ayant la configuration suivante :</p>

<pre>
{
	"_id" : "rs",
	"version" : 7,
	"members" : [
		{
			"_id" : 0,
			"host" : "m1.example.net:27017"
		},
		{
			"_id" : 1,
			"host" : "m2.example.net:27017"
		},
		{
			"_id" : 2,
			"host" : "m3.example.net:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>1) Dans le shell mongo, utilisez la configuration suivante afin de rendre m3 plus éligible que les autres :</p>

<pre>
cfg = rs.conf()
cfg.members[0].priority = 0.5
cfg.members[1].priority = 0.5
cfg.members[2].priority = 1
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Cela permet à m3 d'avoir une priorité plus forte que les autres instances local.system.replset.members[n].priority.
Voici ce qui se passe :
- m3.example.net et m2.example.net se synchronisent avec m1.example.net (ce qui prend moins de 10 secondes en général)
- m1.example.net voit bien que sa priorité n'est plus la plus forte, donc il devient secondaire, mais ne le devient pas tant que m3.example.net
n'est pas synchronisé correctement avec lui.
- Le fait que m1.example.net rétrograde force une élection durant laquelle m3.example.net devient primaire grâce à sa nouvelle priorité.
2) De manière optionelle, si m3.example.net a un Optime de plus de 10 secondes de retard sur m1.example.net, et que vous n'avez pas besoin d'un membre primaire
désigné sous 10 secondes, vous pouvez forcer m1.example.net à rétrograder avec la commande suivante :</p>

<pre>db.adminCommand({replSetStepDown:1000000, force:1})</pre>

<div class="spacer"></div>

<p>Cela empêche m1.example.net de devenir primaire pendant 1 000 000 secondes, même si aucun autre membre peut devenir primaire.
Quand m3.example.net se remet à niveau avec m1.example.net, il va donc devenir primaire.
Si plus tard vous souhaitez repasser m1.example.net en tant que primaire du Replica Set, pendant qu'il attende que m3.example.net se remette à niveau,
exécutez la commande suivante afin que m1 recherche une élection :</p>

<pre>rs.freeze</pre>

<div class="spacer"></div>

<p>Cette commande représente replSetFreeze.
Si vous souhaitez forcer un membre primaire avec la commandes de la base de données : on considère un Replica Set avec les trois membre suivants :
- mdb0.example.net en tant que primaire
- mdb1.example.net un secondaire
- mdb2.example.net un autre secondaire</p>

<p>Pour forcer un membre à devenir primaire, utilisez la procédure suivante :

1) Dans un shell mongo, exécutez la commande rs.status() pour vérifier que votre Replica Set est en cours d'éxécution
2) Dans un shell mongo connecté au mongod de mdb2.example.net, gelez mdb2.example.net afin qu'il ne devienne pas primaire pendant 120 secondes :</p>

<pre>rs.freeze(120)</pre>

<div class="spacer"></div>

<p>3) Dans un shell mongo connecté au shell de mdb0.example.net, rétrogradez cette instance afin qu'elle ne soit pas éligible pendant 120 secondes :</p>

<pre>rs.stepDown(120)</pre>

<div class="spacer"></div>

<p>mdb1.example.net devient donc primaire</p>
<a name="resy"></a>

<div class="spacer"></div>

<p class="titre">III) [ Resynchroniser un Membre du Replica Set ]</p>

<p>Un membre d'un Replica Set peut devenir obsolète quand son processus de réplication ne peut plus suivre le membre primaire qui écrit par dessus
des entrées de son Oplog que le membre secondaire n'a pas eu le temps de répliquer. Quand ce genre de situation arrive, vous devez alors complètement resynchroniser
le membre en supprimant ses données et réalisant un synchronisation initiale. Cette partie du tutoriel va expliquer comment resynchroniser un membre obsolète
et comment créer un nouveau membre en utilisant une "graîne" depuis un autre membre. Lorsque vous synchronisez un membre, choisissez une heure à laquelle le système
a assez de bande passante afin de transférer une grosse quantité de données. Planifiez la synchronisation pendant une période ou il y a peu d'utilisation
de celle-ci.
MongoDB fournit deux moyens de procéder à une synchronisation initiale :

- Redémarrer le mongod avec un répertoire des données vide et laisser MongoDB agir tout seul pour restaurer les données. C'est la solution la plus simple
mais elle prend plus de temps.

- Redémarrez la machine avec une copie des données du répertoire des données récent d'un autre membre du Replica Set. Cette procédure est plus rapide
mais nécessite plusieurs interventions manuelles.
</p>
<a name="synca"></a>

<div class="spacer"></div>

<p class="small-titre">a) Synchroniser un Membre Automatiquement</p>

<p>Cette procédure s'appuye sur le <a href="concepts_processus.php#initsync">processus de synchrnisation initiale</a> habituel de MongoDB, Cela va stocker les données sur le membre que l'on souhaite
synchroniser. Pour synchroniser ou resynchroniser un membre, vous devez :</p>

<p>
1) Si le membre existe :
a) Stopper l'instance de mongod. Pour garantir un arrêt propre, utilisez la commande db.shutdownServer() depuis le shell mongo ou alors l'option mongod --shutdown sur
les systèmes Linux.
b) Supprimez toutes les données et sous-répertoires du dossier des données du membre. En supprimant les données du dbpath, MongoDB va effectuer une resynchronisation complète.
Effectuez une sauvegarde avant si nécessaire.

2) Démarrez l'instance mongod sur le membre :</p>

<pre>mongod --dbpath /data/db/ --replSet rsProduction</pre>

<div class="spacer"></div>

<p>Arrivé à ce point, mongod va effectuer une synchronisation initiale. La rapidité de la synchronisation initiale va bien sûr dépendre de votre quantité
de données et de la vitesse sur le réseau entre les membres du Replica Set.
Les opérations de synchronisation initiale peut influencer les autres membres de l'ensemble et créer du traffique additionnel au primaire et peut
arriver uniquement si l'un des membres de l'ensemble est accessible et mis à jour.</p>
<a name="syncc"></a>

<div class="spacer"></div>

<p class="small-titre">b) Synchroniser un Membre en copiant les fichiers de données d'un Autre</p>

<p>Cette approche va permettre à un membre nouveau ou obsolète de se resynchroniser en utilisant les fichiers des données d'un autre membre existant dans votre
Replica Set. Les fichiers de données doivent être suffisament récents pour autoriser un nouveau membre à se rattraper avec l'Oplog. Sinon, le membre
devrait effectuer une synchronisation initiale.</p>

<p>Pour copier les fichiers de données, vous pouvez les capturer comme un snapshot ou alors une copie directe. Dans la plupart des cas, vous ne pouvez pas copier les fichiers
de données depuis une instance mongod en cours d'exécution vers une autre car les fichiers de données vont changer durant la copie.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Si vous choisissez de copier les fichiers de données, vous devez impérativement copier le contenu de la base de données
	local du membre.
</div>

<div class="spacer"></div>

<p>Vous nez pouvez pas utiliser une backup mongodump pour les fichiers de données, mais seulement une backup de snapshot.
Après avoir copié les fichiers de données, synchronisez le membre démarrez l'instance mongod et autorisez-lui à appliquer toutes les opérations de l'Oplog
jusqu'à ce qu'elle reflette les données les plus récentes du Replica Set.</p> 
<a name="conf"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Configurer les Ensembles de Tags du Replica Set ]</p>

<p>Les ensembles de tags vous permettent de configurer vos propres Write Concerns et vos propres préférences de lecture pour un Replica Set.
MongoDB stocke les ensembles de tags dans l'objet de configuration du Replica Set, qui correspond au document retourné par rs.conf(), dans le sous-document
members[n].tags.</p>
<a name="diff"></a>

<div class="spacer"></div>

<p class="small-titre">a) Différences entre les Préférences de Lecture et le Write Concern</p>

<p>Les préférences de lecture et les Write Concerns personnalisés évaluent les ensembles de tags de plusieurs façons :</p>

<p>- Les préférences de lecture prennent en compte la valeur d'un tag en sélectionnant un membre depuis lequel on va lire
- Les Write Concerns n'utilisent pas la valeur d'un Tag pour sélectionner un membre, sauf pour prendre en compte si la valeur est unique ou non

Par exemple, un ensemble de tags pour une opération de lecture peut ressembler au document suivante :</p>

<pre>{ "disk": "ssd", "use": "reporting" }</pre>

<p>Afin de compléter une telle opération de lecture, un membre devrait avoir ces deux tags. Nimporte quel de ces ensembles de tags vont satisfaire ce besoin :</p>

<pre>
{ "disk": "ssd", "use": "reporting" }
{ "disk": "ssd", "use": "reporting", "rack": "a" }
{ "disk": "ssd", "use": "reporting", "rack": "d" }
{ "disk": "ssd", "use": "reporting", "mem": "r"}
</pre>

<div class="spacer"></div>

<p>En revanche, ces ensembles de tags ne pourraient pas satisfaire une telle requête :</p>

<pre>
{ "disk": "ssd" }
{ "use": "reporting" }
{ "disk": "ssd", "use": "production" }
{ "disk": "ssd", "use": "production", "rack": "k" }
{ "disk": "spinning", "use": "reporting", "mem": "32" }
</pre> 
<a name="ajen"></a>

<div class="spacer"></div>

<p class="small-titre">b) Ajouter un Ensemble de Tags à un Replica Set</p>

<p>Supposons la configuration suivante :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 1,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongodb0.example.net:27017"
		},
		{
			"_id" : 1,
			"host" : "mongodb1.example.net:27017"
		},
		{
			"_id" : 2,
			"host" : "mongodb2.example.net:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>Vous pourriez ajouter des ensembles de tags aux membres de ce Replica Set avec la séquence de commandes suivante :</p>

<pre>
conf = rs.conf()
conf.members[0].tags = { "dc": "east", "use": "production" }
conf.members[1].tags = { "dc": "east", "use": "reporting" }
conf.members[2].tags = { "use": "production" }
rs.reconfig(conf)
</pre>

<div class="spacer"></div>

<p>Une fois cette opération effectuée, vous allez ré-afficher la configuration du Replica Set avec rs.conf() :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 2,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongodb0.example.net:27017",
			"tags" : {
				"dc": "east",
				"use": "production"
			}
		},
		{
			"_id" : 1,
			"host" : "mongodb1.example.net:27017",
			"tags" : {
				"dc": "east",
				"use": "reporting"
			}
		},
		{
			"_id" : 2,
			"host" : "mongodb2.example.net:27017",
			"tags" : {
				"use": "production"
			}
		}
	]
}
</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Dans les ensembles de tags, toutes les valeurs de tags doivent être de type String.
</div>
<a name="wc"></a>

<div class="spacer"></div>

<p class="small-titre">c) Write Concerns Personnalisés pour de Multiples DataCenters</p>

<p>Supposons un Replica Set de cinq membres disposé sur 2 data centers :
1) un site VA taggué dc.va
2) un autre GTO taggué dc.gto
</p>

<p>Creéz un Write Concern personnalisé afin de demander confirmation de deux data centers utilisant les tags de Replica Set en utilisant
les commandes suivantes depuis un shell mongo:</p>

<p>1) Créez un objet Javascript de configuration de Replica Set :</p>

<pre>conf = rs.conf()</pre>

<p>2) Ajoutez des tags aux membres du Replica Set en respectant leur location :</p>

<pre>
conf.members[0].tags = { "dc.va": "rack1"}
conf.members[1].tags = { "dc.va": "rack2"}
conf.members[2].tags = { "dc.gto": "rack1"}
conf.members[3].tags = { "dc.gto": "rack2"}
conf.members[4].tags = { "dc.va": "rack1"}
rs.reconfig(conf)
</pre>

<div class="spacer"></div>

<p>3) Créez un paramètre getLastErrorModes personnalisé pour vous assurez que l'opération d'écriture va se propager à au moins un membre de chaque site :</p>

<pre>conf.settings = { getLastErrorModes: { MultipleDC : { "dc.va": 1, "dc.gto": 1}}</pre>

<p>Reconfigurez le Replica Set en utilisant l'objet modifié conf :</p>

<pre>rs.reconfig(conf)</pre>

<p>Afin d'être sûr que l'opération d'écriture se propage à au moins un membre de l'ensemble de répliques dans les deux data centers, utilisez le WriteConcern MultipleDC :</p>

<pre>db.runCommand( { getLastError: 1, w: "MultipleDC" } )</pre>

<p>Alternativement, si vous voulez être sûrs que chaque opération d'écriture se propage à au moins 2 racks sur chaque site :

1) Reconfigurez le Replica Set
avec le shell mongo comme suivante :</p>

<pre>conf = rs.conf()</pre>

<p>2) Redéfinissez la valeur de getLastErrorModes afin de demander deux valeurs différentes à la fois de dc.va et de dc.gto :</p>

<pre>conf.settings = { getLastErrorModes: { MultipleDC : { "dc.va": 2, "dc.gto": 2}}</pre>

<p>3) Reconfigurez le Replica Set avec le nouvel objet conf :</p>

<pre>rs.reconfig(conf)</pre>

<p>Maintenant, la prochaine opération de Write Concern va retourner un résultat une fois que l'opération d'écriture se sera propagée sur au moins deux racks
de chaque site :</p>

<pre>db.runCommand( { getLastError: 1, w: "MultipleDC" } )</pre>
<a name="tags"></a>

<div class="spacer"></div>

<p class="small-titre">d) Configurer les Ensembles de Tags pour une Séggrégation Fonctionnelle des Opérations de Lecture et d'Ecriture</p>

<p>Supposons un Replica Set avec des ensembles de tags qui reflettent :
- un data center
- un rack physique
- un type système de stockage (exemple disque dur)
</p>

<p>Ou chaque membre de l'ensemble a un ensemble de tags qui ressemble au suivant :</p>

<pre>
{"dc.va": "rack1", disk:"ssd", ssd: "installed" }
{"dc.va": "rack2", disk:"raid"}
{"dc.gto": "rack1", disk:"ssd", ssd: "installed" }
{"dc.gto": "rack2", disk:"raid"}
{"dc.va": "rack1", disk:"ssd", ssd: "installed" }
</pre>

<div class="spacer"></div>

<p>Pour rediriger une opération de lecture vers un membre du Replica Set ayant un disque dur de type ssd, vous pouvez utiliser le l'ensemble de tags suivant :</p>

<pre>{ disk: "ssd" }</pre>

<p>Par contre, pour créer un des modes de WriteConcern comparables, vous devez spécifier un ensemble différent de configuration de getLastErrorModes :
1) créez un objet de configuration :</p>

<pre>conf = rs.conf()</pre>

<p>2) Redéfinissez la valeur de getLastErrorModes afin de configurer deux modes de Write Concern :</p>

<pre>
conf.settings = {
	"getLastErrorModes" : {
		"ssd" : {
			"ssd" : 1
		},
		"MultipleDC" : {
			"dc.va" : 1,
			"dc.gto" : 1
		}
	}
}
</pre>

<div class="spacer"></div>

<p>3) Reconfigurez le Replica Set avec le nouvel objet modifié :</p>

<pre>rs.reconfig(conf)</pre>

<p>Maintenant, vous pouvez spécifier le mode, de Write Concern, MultipleDC, afin de s'assurer que chaque opération est redirigée vers chaque data center :</p>

<pre>db.runCommand( { getLastError: 1, w: "MultipleDC" } )</pre>

<p>Addionnellement, vous pouvez spécifier le mode de Write Concern "ssd" afin de vous assurer que l'opération de lecture se propage sur au moins une 
instance ayant un SSD.</p>
<a name="recon"></a>

<div class="spacer"></div>

<p class="titre">V) [ Reconfigurer un Replica Set avec des Membres Indisponibles ]</p>

<p>Afin de reconfigurer un Replica Set quand une minorité de membres est indisponible, utilisez la commande rs.reconfig() sur le primaire actuel.
Ce tutoriel explique comment reconfigurer un Replica Set quand  majorité de membre est inaccessible en forcant la reconfiguration ou en remplaçant le Replica Set.
Vous aurez probablement besoin d'utiliser une de ces opérations, par exemple, si vous avez un Replica Set distribué géographiquement ou aucun membre local du groupe
ne peut rejoindre la majorité.</p>
<a name="recfo"></a>

<div class="spacer"></div>

<p class="small-titre">a) Reconfigurer en forcant la Reconfiguration</p>

<p>Cette procédure vous permet de restaurer pendant qu'une majorité des membres du Replica Set ne sont pas disponibles ou accessibles. Vous vous connectez
à nimporte quel membre encore "en vie" et utilisez l'option force à la méthode rs.reconfig().
L'option force va forcer une nouvelle configuration. Utilisez cette procédure uniquement pour restaurer en cas d'interruption catastrophique. Ne forcez pas 
à chaque fois que vous devez reconfigurer. Aussi, n'utilisez pas l'option force dans un script automatisé et quand il reste un membre primaire.</p>

<p>Pour forcer la reconfiguration :
1) Effectuez une backup d'un membre survivant
2) Connectez-vous à un membre survivant et sauvegardez la configuration actuelle :</p>

<pre>
cfg = rs.conf()
printjson(cfg)
</pre>

<div class="spacer"></div>

<p>3)Sur le même membre, supprimez les membres indisponibles et inaccessibles du Replica Set dans le tableau de membres en définissant le tableau ne contenant
que les membres encore en vie. Considérez l'exemple suivant reprenant la variable cfg de l'étape précédente :</p>

<pre>cfg.members = [cfg.members[0] , cfg.members[4] , cfg.members[7]]</pre>

<p>4) Sur le même membre, reconfigurez l'ensemble en utilisant rs.reconfig() avec l'option force définie à true :</p>

<pre>rs.reconfig(cfg, {force : true})</pre>

<p>Cette opération force le membre secondaire à utiliser la nouvelle configuration. Cette configuration est ensuite propagée à tous les membres survivants du Replica
Set listés dans le tableau de membres. Le Replica Set élit un nouveau membre primaire.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Quand vous utilisez l'option force : true, le numéro de version du Replica Set augmente beaucoup, par dizaines, centaines ou milliers.
	C'est tout à fait normal et a été mit en place afin d'éviter les collisions de verions d'ensembles si vous forcez accidentellemet une reconfiguration à travers
	les partitions réseau.
</div>

<div class="spacer"></div>

<p>5) Si l'échec est temporaire, arrêtez les membres supprimés dès que possible.</p>
<a name="recre"></a>

<div class="spacer"></div>

<p class="small-titre">b) Reconfigurer en remplaçant le Replica Set</p>

<p>Utilisez la procédure suivante uniquement pour les versions de MongoDB inférieures à la 2.0. Dans le cas contraire, utilisez l'étape précédente.
Ces procédures concernent les situations qui ont une majorité de membres indisponibles ou inaccessibles. Si une majorité est en cours d'exécution,
alors évitez ces procédures et utilisez la commande rs.reconfig(). Si vous utilisez une version antérieure à la 2.0 et que la majorité de votre
Replica Set est indisponible, vous avez les deux options suivantes :</p>

<p>Reconfigurer en arrêtant la réplication qui remplace le Replica Set par un serveur Standalone.
1) Arrêter les instances mongod en cours d'exécution. Afin de garantir une extinction propre, utilisez un script de contrôle existant ou la méthode
db.shutdownServer(). Par exemple, via un shell mongo :</p>

<pre>
use admin
db.shutdownServer()
</pre>

<div class="spacer"></div>

<p>2) Créez une backup du répertoire de données (dbpath) des membres survivants de l'ensemble.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous avez une sauvegarde des données, vous voudriez plutôt supprier ces données.
</div>

<div class="spacer"></div>

<p>3) Redémarrez l'une des instances mongod sans le paramètre --replSet. Les données sont maintenant accessibles et fournient par un seul et même serveur
qui n'est pas un membre du Replica Set. Les clients peuvent l'utiliser pour les lectures et écritures.

Dès que possible, re-déployez un Replica Set afin de garantir la redondance des informations et de protéger votre déploiement de certaines interruptions.</p>

<p>Reconfigurez en "brisant la glace". Cette option sélectionne un membre survivant du Replica Set afin de l'élire nouveau primaire. Dans la procédure suivante,
le nouveau primaire db0.example.net. MongoDB copie les données depuis db0.example.net vers tous les autres membres.

1) Arrêter les instances mongod en cours d'exécution. Afin de garantir une extinction propre, utilisez un script de contrôle existant ou la méthode
db.shutdownServer(). Par exemple, via un shell mongo :</p>

<pre>
use admin
db.shutdownServer()
</pre>

<div class="spacer"></div>

<p>2) Bougez tous les répertoires de données (dbpath) de tous les membres (sauf db0.example.net) de manière à ce que tous les membres aient un répertoire de données
vide sauf db0.example.net :</p>

<pre>mv /data/db /data/db-old</pre>

<p>3) Bougez tous les dossiers de données pour la base de données local (local.*) afin que db0.example.net n'aie pas de base données local :</p>

<pre>
mkdir /data/local-old
mv /data/db/local* /data/local-old/
</pre>

<div class="spacer"></div>

<p>4) Démarrez chaque membre du Replica Set normalement.
5) Connectez-vous à db0.example.net dans un shell mongo et exécutez rs.initiate() afin d'initier le Replica Set.
6) Ajoutez les autres membres en utiisant rs.add(). Par exemple, pour ajouter un membre sur db1.example.net sur le port 27017 :</p>

<pre>rs.add("db1.example.net:27017")</pre>

<p>MongoB réalise une synchronisation initiale sur les membres ajoutés en copiant toutes les données de db0.example.net vers les membres ajoutés.</p>
<a name="gere"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Gérer la Réplication en Chaîne ]</p>

<p>Débutant depuis la version 2.0, MongoDB supporte la réplication en chaîne. Une réplication en chaîne a lieue quand un membre secondaire
se réplique depuis un autre membre secondaire plutôt que depuis le primaire. Cela devrait être le cas, par exemple, si un membre secondaire sélectionne
sa cible de réplication basée sur le temps de latence et si le membre le plus proche est un autre secondaire.
La réplication en chaîne peut réduire la charge de travail du membre primaire mais peut aussi entraîner un plus gros lag de réplication, en fonction de la topologie
de votre réseau.
De plus, depuis la version 2.2 de MongoDB, vous pouvez utilisez le paramètre chainingAllowed dans la configuration du Replica Set pour arrêter 
la réplication en chaîne durant les situation ou la réplication en chaîne cause des problèmes de latence.
MongoDB active la réplication en chaîne par défaut. Les procédures suivantes montrent comment la désactiver et la réactiver.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Si la réplication en chaîne est désactivée, vous pouvez toujours utiliser replSetSyncFrom pour spécifier le fait qu'un membre
	secondaire se réplique depuis un autre secondaire. Mais cette configuration va durer jusqu'à ce que membre secondaire recalcule depuis quel membre
	il doit se synchroniser.
</div>
<a name="desa"></a>

<div class="spacer"></div>

<p class="small-titre">a) Désactiver la Réplication en Chaîne</p>

<p>Pour désactiver la réplication en chaîne, définissez le champ chainingAllowed à false dans la configuration du Replica Set.
Pour le réaliser, vous pouvez utiliser les séquences suivantes :

1) On place la configuration du Replica Set dans une variable</p>

<pre>cfg = rs.config()</pre>

<p>2) Regardez si votre configuration actuelle comporte le sous-document "settings". Si c'est le cas, esquivez cette étape.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Pour éviter la perte de données, esquivez cette étape si votre configuration comporte le sous-document settings.
</div>

<div class="spacer"></div>

<p>Si le sous-document settings n'existe pas, créez-le avec la commande suivante :</p>

<pre>cfg.settings = { }</pre>

<p>Ensuite, définissez le paramètre chainingAllowed a false :</p>

<pre>
cfg.settings.chainingAllowed = false
rs.reconfig(cfg)
</pre>
<a name="reac"></a>

<div class="spacer"></div>

<p class="small-titre">b) Réactiver la Réplication en Chaîne</p>

<p>Pour réactiver cette option, il vous suffira de définir ce même paramètre à true :</p>

<pre>
cfg = rs.config()
cfg.settings.chainingAllowed = true
rs.reconfig(cfg)
</pre>
<a name="chan"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Changer les noms d'hôtes dans un Replica Set ]</p>

<p>Pour la plupart des Replica Sets, les noms d'hôtes dans le champ host ne changent jamais. En revanche, dans des besoins de réorganisation , vous devriez
avoir besoin de migrer la plupart ou tous les noms d'hôtes.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Toujours utiliser des noms d'hôtes résolvables pour la valeur du champ host dans la configuration du Replica Set
	pour éviter les confusions et complexités.
</div>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Nous allons aborder deux procédures distinctes pour changer de noms d'hôtes dans le champ "host". Utilisez l'une des deux :

- Changer les noms d'hôtes sans perturber la disponiblité du Replica Set, de cette façon, votre application sera toujours apte à lire et écrire les données
depuis votre Replica Set mais cette procédure sera un peu plus longue.
Si vous utilisez la premièr procédure, vous devez configurer vos applications pour qu'elles se connectent à la fois à l'ancienne et à la nouvelle localité
de votre Replica Set, ce qui recquiert souvent un redémarrage et une reconfiguration ou niveau de l'application et ce qui devrait perturber la disponibilité
de votre application. Vous devrez donc les reconfigurer.

- Stopper tous les membres exécutés des anciens noùs d'hôtes en même temps sera plus rapide mais votre Replica Set sera indisponible durant
l'opération.</p>
<a name="asso"></a>

<div class="spacer"></div>

<p class="small-titre">b) Assomptions</p>

<p>Nous avons le Replica Set à trois membres suivant :</p>

<p>
database0.example.com:27017 - le primaire
database1.example.com:27017 - un secondaire
database2.example.com:27017 - un autre secondaire

Puis, la sortie du rs.conf() :</p>

<pre>
{
	"_id" : "rs",
	"version" : 3,
	"members" : [
		{
			"_id" : 0,
			"host" : "database0.example.com:27017"
		},
		{
			"_id" : 1,
			"host" : "database1.example.com:27017"
		},
		{
			"_id" : 2,
			"host" : "database2.example.com:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>Les procédures suivantes changent les nos d'hôtes des membres comme suivante :

- mongodb0.example.net:27017 - le primaire
- mongodb0.example.net:27017 - un secondaire
- mongodb0.example.net:27017 - un autre secondaire

Utilisez la procédure la plus appropriée pour votre déploiement.</p>
<a name="nh"></a>

<div class="spacer"></div>

<p class="small-titre">c) Changer les noms d'Hôtes en Maintenant la Disponibilité du Replica Set</p>

<p>1) Pour chaque membre secondaire du Replica Set, effectuez les séquences d'opérations suivantes :
a) arrêtez le secondaire
b) redémarrez le secondaire à une nouvelle location
c) Ouvrez un shell mongo connecté au primaire du Replica Set. Par exemple, sur le port 27017 :</p>

<pre>mongo --port 27017</pre>

<p>d) Utilisez rs.reconfig() pour mettre à jour le document de configuration du Replica Set avec le nouveau nom d'hôte.
Par exemple, la séquence de commandes suivante met à jour le nom d'hôte du membre se situant à l'indice 1 du tableau de membres (members[1]) dans le document
de configuration du Replica Set :</p>

<pre>
cfg = rs.conf()
cfg.members[1].host = "mongodb1.example.net:27017"
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>e) Soyez sûrs que vos applications clientes peuvent accéder au Replica Set à la nouvelle location et que le secondaire a une chance de se mettre à niveau
avec les autres membres de l'ensemble.

Répetez ces étapes avec chaque membre non-primaire de l'ensemble.</p>

<p>2) Ouvrez un shell mongo connecté au membre primaire, et rétrogradez le primaire en utilisant la commande rs.stepDown() :</p>

<pre>rs.stepDown()</pre>

<p>Le Replica Set va donc élire un nouveau primaire.
3) Quand le rétrogradage se termine avec succès, arrêtez l'ancien primaire.
4) Démarrez l'instance mongod de la nouvelle localité qui va devenir primaire.
5) Connectez-vous au primaire actuel, qui a juste été élu, et mettez à jour le document de configuration du Replica Set avec le nom d'hôte du noeud qui va
devenir primaire.
Par exemple, si l'ancien primaire était en position 0 et qu le nouveau primaire mongodb0.example.net:27017 devrait s'exécuter :</p>

<pre>
cfg = rs.conf()
cfg.members[0].host = "mongodb0.example.net:27017"
rs.reconfig(cfg)
</pre>

<p>6) Ouvrez un shell mongo connecté au nouveau primaire.
7) Pour confirmer votre nouvelle configuration, appellez rs.conf() dans le shell mongo :</p>

<pre>
{
	"_id" : "rs",
	"version" : 4,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongodb0.example.net:27017"
		},
		{
			"_id" : 1,
			"host" : "mongodb1.example.net:27017"
		},
		{
			"_id" : 2,
			"host" : "mongodb2.example.net:27017"
		}
	]
}
</pre>
<a name="nhmt"></a>

<div class="spacer"></div>

<p class="small-titre">d) Changer Tous les Noms d'hôtes en Même Temps</p>

<p>1) Arrêtez tous les membres du Replica Set
2) Redémarrez chaque membre sur un port différent et sans l'option --replSet. Changer de port évite aux applications clientes de se connecter sur ce membre
pendant la maintenance. Utiliser le --dbpath habituel du membre, qui dans cete exemple est /data/db1 :</p>

<pre>mongod --dbpath /data/db1/ --port 37017</pre>

<p>3) Pour chaque membre du Replica Set, effectuez les séquences d'opérations suivantes :
a) Ouvrez un shell mongo connecté au mongod en cours d'exécution sur le nouveau port temporaire. Par exeple, un membre exécuté sur le port 37017 :</p>

<pre>mongo --port 37017</pre>

<p>b) Editez manuellement la configuration du Replica Set. Cette configuration est l'unique document dans la collection system.replset dans la base de données "local".
Editez cette configuraiton avec les nouveaux noms d'hôtes et les ports relatifs aux membres du Replica Set :</p>

<pre>
use local
cfg = db.system.replset.findOne( { "_id": "rs" } )
cfg.members[0].host = "mongodb0.example.net:27017"
cfg.members[1].host = "mongodb1.example.net:27017"
cfg.members[2].host = "mongodb2.example.net:27017"
db.system.replset.update( { "_id": "rs" } , cfg )
</pre>

<div class="spacer"></div>

<p>c) Arrêter le processus mongod du membre.
4) Après avoir reconfiguré tous les membres du Replica Set, démarrez chanque instance mongod normalement, utilisez le port habituel avec l'option --replSet :</p>

<pre>mongod --dbpath /data/db1/ --port 27017 --replSet rs</pre>

<p>5) Connectez-vous à l'une des instances mongod en utilisant un shell mongo :</p>

<pre>mongo --port 27017</pre>

<p>6) Pour confirmer la nouvelle configuration, appelez la méthode rs.conf() dans le shell mongo, vous devriez avoir ceci :</p>

<pre>
{
	"_id" : "rs",
	"version" : 4,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongodb0.example.net:27017"
		},
		{
			"_id" : 1,
			"host" : "mongodb1.example.net:27017"
		},
		{
			"_id" : 2,
			"host" : "mongodb2.example.net:27017"
		}
	]
}
</pre>
<a name="cibl"></a>

<div class="spacer"></div>

<p class="titre">VIII) [ Configurer la Cible de Synchronisation d'un Membre Secondaire ]</p>

<p>Pour écraser la logique de sélection de cible de synchronsation par défaut, vous pouvez configurer manuellement la cibl de synchronisation d'un membre
secondaire pour tirer les entrées de l'Oplog temporairement, pour cela, deux façons :
- la commande replSetSyncFrom
- rs.syncFrom() dans un shell mongo</p>

<p>Ne modifiez seulement la logique de synchronisation en fonction de vos besoins, et faîtes toujours attention. La méthode rs.syncFrom() ne va pas affecter
une synchronisation initiale en cours. Pour affecter la cible de synchronisation pour la synchronisation initiale, exécutez rs.synFrom() avant la synchronisation
intiale. Si vous exécutez rs.syncFrom() durant une synchronisation initiale, MongoDB ne produit pas d'erreur mais la cible de synchronisation ne va pas changer
avant la fin de l'opération de synchronisation initiale.</p> 

<div class="spacer"></div>
 
<div class="alert alert-success">
	Astuce : replSetSyncFrom et rs.syncFrom() fournissent un écrasement temporaire du comportement par défaut. mongod va redéfinir le comportement
	par défaut dans les situations suivantes :
	- l'instance mongod redémarre
	- la connexion entre le mongod et la cible de synchronisation se ferme
</div>

<div class="spacer"></div>

<p>Depuis la version 2.4, la cible de synchronisation est à plus de 30 secondes derrière un autre membre du Replica Set, le mongod va redéfinir la cible de
synchronisation par défaut.</p> 
 
<div class="spacer"></div>

<p>Je vous invite maintenant à passer à la page suivante : <a href="tutoriaux_diagnostics.php">"Diagnostiques du Replica Set" >></a>.

<?php

	include("footer.php");

?> 