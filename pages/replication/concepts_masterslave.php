<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Réplication Master-Slave</li>
</ul>

<p class="titre">[ Réplication Master-Slave ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#oper">I) Opérations Fondementales</a></p>
	<p class="right"><a href="#depl">- a) Déploiement Initial</a></p>
	<p class="right"><a href="#opti">- b) Options de Configuration</a></p>
	<p class="right"><a href="#cons">- c) Considérations Opérationnelles Pour Réplication</a></p>
	<p class="elem"><a href="#exec">II) Exécution de Configuration Master-Slave</a></p>
	<p class="right"><a href="#diag">- a) Diagnostiques</a></p>
	<p class="elem"><a href="#secu">III) Sécurité</a></p>
	<p class="elem"><a href="#admi">IV) Administration et Opération en Continu</a></p>
	<p class="right"><a href="#deplm">- a) Déploiement Master-Slave en Utilisant les Replica Sets</a></p>
	<p class="right"><a href="#conv">- b) Convertir un Déploiement Master-Slave en un Replica Set</a></p>
	<p class="right"><a href="#fo">- c) FailOver Vers un Esclave</a></p>
	<p class="right"><a href="#inve">- d) Inverser Master et Slave</a></p>
	<p class="right"><a href="#cem">- e) Créer un Esclave Depuis une Image d'un Master Existant</a></p>
	<p class="right"><a href="#cee">- f) Créer un Esclave Depuis une Image d'un Slave Existant</a></p>
	<p class="right"><a href="#resy">- g) Resynchroniser un Esclave Trop Obsolète pour Restaurer</a></p>
	<p class="right"><a href="#chai">- h) Chaînage d'Esclaves</a></p>
	<p class="right"><a href="#corr">- i) Corriger la Source d'un Esclave</a></p>
</div>

<p>La réplication <b>Master-Slave</b> est mise en place afin de garantir <b>le déploiement d'un replica set de plus de 12 membres</b> si nécessaire.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Si possible, utilisez la réplication avec les replica sets et non la réplication Master-Slave pour tous les nouveaux
	déploiements. Cette partie de la documentation est détaillée dans un but d'achivage uniquement.
</div>

<div class="small-spacer"></div>

<p>En plus de fournir toutes les fonctionnalités des déploiements Master-Slave, <b>les replica sets sont beaucoup plus robustes pour la production</b>.
La réplication Master-Slave <b>précédait le replica set</b> et permettait <b>un large nombre de noeuds non-maîtres (esclaves)</b>, aussi bien que pour restreindre les opérations
répliquées sur une seule base de données. Puis, la réplication Master-Slave <b>apporte moins de redondance et n'automatise pas le processus de failover</b>.
Si vous souhaitez convertir un déploiement Master-Slave existant en un replica set, jettez un oeil au paragraphe sur comment <b>convertir un master-slave en replica set</b> un peu plus loin.</p>
<a name="oper"></a>

<div class="spacer"></div>

<p class="titre">I) [ Opérations Fondementales ]</p>

<div class="spacer"></div>

<p>Voyons ici comment <b>initialiser un déploiement de type master-slave</b>.</p>
<a name="depl"></a>

<div class="spacer"></div>

<p class="small-titre">a) Déploiement Initial</p>

<p>Afin de démarrer un déploiement master-slave, exécutez <b>deux instances mongod</b> : une en mode master et une autre en mode slave.
Pour démarrer une instance mongod en mode Master :</p>

<pre>mongod --master --dbpath /data/bddmaster/</pre>

<div class="spacer"></div>

<p>Avec l'option <b>"--master"</b>, mongod va créer une collection <b>"local.oplog.$main"</b> ayant un opLog qui va empiler les opérations (principe de queue) que le slave va répliquer.
L'option <b>"--dbpath"</b>, comme nous avons déjà vue, est optionnelle.
Pour les instances slave, MongoDB va stocker les informations du serveur source <b>dans une collection "local.sources"</b>.
Pour démarrer en mode slave :</p>

<pre>mongod --slave --source hotemaitre:port --dbpath /data/bddslave/</pre>

<div class="spacer"></div>

<p>Spécifiez le nom d'hôte et le port du master avec le paramètre <b>"--source"</b>. Là aussi, l'option <b>"--dbpath"</b> est optionnelle.</p>
<a name="opti"></a>

<div class="spacer"></div>

<p class="small-titre">b) Options de Configuration</p>

<p>Une alternative à spécifier l'option <b>"--source"</b> consiste à <b>ajouter un document à la collection "local.sources"</b> en spécifier l'instance du master comme dans
la commande suivante :</p>

<pre>
use local
db.sources.find()
db.sources.ajout( { hote: hotemaitre, only: nombdd } );
</pre>

<div class="spacer"></div>

<p>La première ligne indique que l'on doit <b>utiliser la base de données "local"</b>. La ligne 2 permet de vérifier qu'aucun document de configuration existe déjà.
Et enfin, la dernière ligne, va insérer dans la collection "ajout" le document source.
Le modèle d'un document <b>"local.sources"</b> est le suivant :</p>

<div class="small-spacer"></div>

<div class="un-list">- hote : Spécifie l'instance mastermongod et comporte un nom d'hôte (adresse IP ou nom depuis un fichier host ou alors un nom de domaine).
Vous pouvez ajouter le port au nom d'hôte si le mongod n'écoute pas sur le port 27017 par défaut.</div>

<div class="un-list">- only : Optionnel, spécifie le nom d'une base de données, Quand celui-ci est spécifié, MongoDB va répliquer uniquement la base de données
indiquée.</div>
<a name="cons"></a>

<div class="spacer"></div>

<p class="small-titre">c) Considérations Opérationnelles Pour Réplication avec Déploiements Master-Slave</p>

<p>Les instances maîtres <b>sauvegardent leurs opérations dans un oplog</b>. En conséquences, si un esclave a un état trop ancien
par rapport à celui du master,  <b>il ne peut pas se rattraper et doit se re-synchroniser de 0</b>. Les esclaves peuvent devenir hors de synchronisation du maître si :</p>

<ul>
	<li><b>L'esclave est bien loin d'être à jour avec l'état actuel du Master</b></li>
	<li><b>L'esclave s'arrête( shutdown par exemple ) et redémarre bien après que le Master a enregistré par dessus certaines de ses opérations.</b></li>
</ul>

<div class="small-spacer"></div>

<p>Quand les esclaves sont <b>hors de synchronisation</b>, la réplication s'arrête et les administrateurs doivent <b>intervenir manuellement</b> afin de redémarrer
la réplication en utilisant <b>la commande "resync"</b>. D'une autre façon, l'option <b>"--autoresync"</b> autorise un esclave à redémarrer la réplication automatiquement
après une pause de <b>10 secondes</b>. Avec cette même option, l'esclave va essayer de se <b>resynchroniser seulement une fois toutes les 10 minutes</b>.
Afin d'éviter ce genre de situations, vous devriez spécifier un opLog plus grand quand vous démarrez l'instance du maître en ajoutant l'option
<b>"--opLogSize"</b> en démarrant le mongod. Si vous ne spécifiez pas cette option, MongoDB utilise <b>5% de l'espace disque</b> disponible par défaut, avec un
minimum de <b>1go sur les systèmes 64bits et 50mo pour les 32bits</b>.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">II) [ Exécution de Configuration Master-Slave ]</p>

<p>MongoDB fournit un nombre d'options de configuration pour les instances mongod lors des déploiements master-slave. Vous pouvez spécifier ces options
dans des <b>fichiers de configuration</b> ou <b>en ligne de commande</b>.
Pour cela, veuillez vous renseigner sur les différents paramètres :</p>

<div class="small-spacer"></div>

<p>1) Pour les Masters :</p>

<ul>
	<li><b>master</b></li>
	<li><b>slave</b></li>
</ul>

<p>2) Pour les Slaves :</p>

<ul>
	<li><b>source</b></li>
	<li><b>only</b></li>
	<li><b>slaveDelay</b></li>
</ul>
<a name="diag"></a>

<div class="spacer"></div>

<p class="small-titre">a) Diagnostiques</p>

<p>Avec une instance maître, exécutez la fonction suivante afin d'obtenir <b>le statut de la réplication</b> depuis la perspctive du maître :</p>

<pre>db.printReplicationInfo()</pre>

<div class="spacer"></div>

<p>Pour la même chose depuis une instance esclave :</p>

<pre>db.printSlaveReplicationInfo()</pre>

<div class="spacer"></div>

<p>Utilisez la commande <b>serverStatus()</b> afin de retourner le statut général de la réplication :</p>

<pre>db.serverStatus()</pre>
<a name="secu"></a>

<div class="spacer"></div>

<p class="titre">III) [ Sécurité ]</p>

<p>Lorsque vous exécutez MongoDB avec <b>"auth"</b> activée, veuillez configurer <b>un fichier clé "keyFile"</b> dans un déploiement master-slave, de cette façon les mongod esclaves
pourront s'authentifier et communiquer avec l'instance mongod maître. Pour activer l'authentification et configurer le <b>keyFile</b>, ajoutez l'option
suivante à votre fichier de configuration :</p>

<pre>fichierCle = /srv/mongodb/keyfile</pre>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Vous pouvez aussi définir cette option en ligne de commande avec "--keyFile" spécifié.
</div>

<div class="spacer"></div>

<p>Définir un "keyFile" <b>active l'authentication</b> et spécifie une clé pour que les instances mongod puissent communiquer entre elles.
Le contenu du fichier clé est <b>arbitraire</b>, vous mettez ce que vous souhaitez dedans, et doit être <b>exactement le même</b> sur tous les membres afin
qu'ils puissent se connecter entre eux.
Le fichier clé doit peser <b>moins d'1ko</b> et ne doit contenir <b>que des charactères en base64</b>. Ce fichier ne doit absolument pas avoir
<b>de permission de groupe ou "world (tout le monde)"</b> sur les systèmes <b>UNIX</b>. Utilisez la commande suivante pour utiliser <b>le packet openSSL</b> afin de
générer du contenu aléatoire utilisable dans le fichier clé :</p>

<pre>openssl rand -base64 741</pre>
<a name="admi"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Administration et Opération en Continu ]</p>

<div class="spacer"></div>

<p>Un peu d'<b>administration</b> dans tout ça !</p>
<a name="deplm"></a>

<div class="spacer"></div>

<p class="small-titre">a) Déploiement Master-Slave en Utilisant les Replica Sets</p>

<p>Si vous souhaitez obtenir une configuration de réplication qui ressemble à une réplication master-slave, considérez <b>le document de configuration suivant</b>.
Dans ce déploiement, les hôtes master et slave fournissent une réplication qui est presque équivalente à un déploiement master-slave à deux instances :</p>

<pre>
{
	_id : 'nom',
	membres : [
		{ _id : 0, hote : "maitre", priorite : 1 },
		{ _id : 1, hote : "esclave", priorite : 0, votes : 0 }
	]
}
</pre>
<a name="conv"></a>

<div class="spacer"></div>

<p class="small-titre">b) Convertir un Déploiement Master-Slave en un Replica Set</p>

<p>Afin de convertir un déploiement master-slave en un replica set, <b>redémarrez le master actuel</b> en tant que replica set à 1 membre. Ensuite,
<b>supprimez les données des anciens secondaires et ajoutez les en tant que nouveaux secondaires</b> au replica set.
1) Pour confirmer que l'instance actuelle est <b>maître</b>, exécutez :</p>

<pre>db.isMaster()</pre>

<div class="spacer"></div>

<p>Ce qui devrait retourner un document qui ressemble à ceci :</p>

<pre>
{
	"ismaster" : true,
	"maxBsonObjectSize" : 16777216,
	"maxMessageSizeBytes" : 48000000,
	"localTime" : ISODate("2013-07-08T20:15:13.664Z"),
	"ok" : 1
}
</pre>

<div class="spacer"></div>

<p>2) <b>Arrêter les processus mongod</b> du master et de tous les slaves, en utilisant la commande suivante :</p>

<pre>db.adminCommand({shutdown : 1, force : true})</pre>

<div class="spacer"></div>

<p>3) Sauvegardez vos données situées <b>dans les répertoires "/data/db"</b>, au cas ou vous auriez besoin de revenir au déploiement master-slave.
4) Démarrez le master avec l'option <b>"--replSet"</b> :</p>

<pre>mongod --replSet "nom"</pre>

<div class="spacer"></div>

<p>5) <b>Connectez-vous au mongod</b> avec le shell mongo et <b>initialisez le replica set</b> avec la commande suivante :</p>

<pre>rs.initiate()</pre>

<div class="spacer"></div>

<p>Quand la commande termine et retourne un résultat, vous aurez donc déployé <b>avec succès</b> votre replica set à 1 membre. Vous vouvez vérifier
le statut de votre replica set à n'importe quel moment avec la commande suivante :</p>

<pre>rs.status()</pre>
<a name="fo"></a>

<div class="spacer"></div>

<p class="small-titre">c) FailOver Vers un Esclave</p>

<p>Pour effectuer de façon permanente un FailOver depuis un Master A endommagé ou indisponible vers un Esclave B :</p>

<ul>
	<li><b>1) Arrêtez le Master A</b></li>
	<li><b>2) Arrêtez le mongod sur l'esclave B</b></li>
	<li><b>3) Restaurez et bougez tous les fichiers de données qui commencent par "local" sur B du dbpath</b></li>
</ul>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Supprimer "local.*" est irréversible et ne peut pas être annulé. Effectuez cette tâche avec la plus grande
	précaution.
</div>

<div class="small-spacer"></div>

<p><b>4) Redémarrez mongod sur B avec l'option --master. Cette opération n'est pas réversible. A ne peut pas devenir un esclave de B jusqu'à ce qu'il ai
terminé une resynchronisation complète.</b></p>
<a name="inve"></a>

<div class="spacer"></div>

<p class="small-titre">d) Inverser Master et Slave</p>

<p>Si vous avez un <b>master A</b> et un <b>slave B</b>, et que vous souhaiteriez <b>inverser les rôles</b>, suivez la procédure suivante. La procédure part du principe que A est sain,
à jour et disponible.
Si A n'est <b>pas sain</b> mais que le matériel <b>est bon</b> (coupure de courant, crash du serveur etc ...), <b>sautez les étapes 1 et 2</b>, puis, dans l'étape 8, remplacez
<b>tous les fichiers</b> de A avec les fichiers de B.</p>

<p>Si A n'est pas sain et que le matériel <b>n'est pas opérationnel</b>, remplacez A avec une <b>nouvelle machine</b>. Et surtout, suivez les instructions du paragraphe précédent.
Pour inverser un <b>master et un slave</b> dans un déploiement :</p>

<ul>
	<li><b>1) Arrêtez les écritures sur A en utilisant la commande fsync.</b></li>
	<li><b>2) Soyez sûr que B est à jour avec l'état de A.</b></li>
	<li><b>3) Arrêtez B.</b></li>
	<li><b>4) Sauvegardez et déplacez les fichiers commençant par "local" sur B depuis le dbpath afin de supprimer les données de la collection "local.sources" existante.</b></li>
</ul>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Encore une fois, supprimer "local.*" est irréversible et ne peut pas être annulé. Effectuez cette tâche avec la plus grande
	précaution.
</div>

<div class="small-spacer"></div>

<ul>
	<li><b>5) Démarrez B avec l'option --master</b></li>
	<li><b>6) Effectuez une écriture sur B, ce qui indique à l'Oplog de fournir un nouveau point de synchronisation pour démarrer.</b></li>
	<li><b>7) Arrêtez B, celui-ci va maintenant avoir un nouvel ensemble de données qui aura des fichiers commençant par "local".</b></li>
	<li><b>8) Arrêtez A et remplacez tous les fichiers dans le dbpath de A qui commençent par "local" avec une copie des fichiers dans le dbpath de B qui commencent par "local".
	Peut-être devriez-vous compresser les fichiers "local" de B pendant que vous les copiez, ils risquent d'être larges.</b></li>
	<li><b>9) Démarrez B avec l'option --master</b></li>
	<li><b>10) Démarrez A avec les options habituelles d'un Slave, mais ajoutez l'option "fastsync" pour une synchronisation rapide.</b></li>
</ul>
<a name="cem"></a>

<div class="spacer"></div>

<p class="small-titre">e) Créer un Slave Depuis une Image d'un Master Existant</p>

<p>Si vous pouvez <b>stopper les opérations d'écriture</b> d'un master pour une période de temps indéfinie, vous pouvez copier les fichiers de données du master
vers le nouveau slave et ensuite démarrez le slave avec l'option <b>"--fastsync"</b>.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Faîtes attention avec l'option "--fastsync". Si les données sur les deux instances sont identiques, un désaccord
	existera pour toujours.
</div>

<div class="spacer"></div>

<p><b>"fastsync"</b> est une façon de démarrer un esclave en démarrant avec une image disque/sauvegarde d'un maître existant. Cette option
indique que l'administrateur <b>garantit une image correcte et complètement à jour</b> avec celle du master. Si vous avec une copie
complète des données du master, vous pouvez utiliser cette option afin <b>d'éviter une synchronisation pleine au démarrage</b> du slave.</p>
<a name="cee"></a>

<div class="spacer"></div>

<p class="small-titre">f) Créer un Slave Depuis une Image d'un Slave Existant ]</p>

<p>Vous pouvez juste <b>copier les fichiers</b> de l'autre slave sans aucune manoeuvre particulière. Prenez simplement une copie des données quand un processus
<b>mongod est arrêté</b> ou verrouillé en utilisant la commande <b>db.fsyncLock()</b>.</p>
<a name="resy"></a>

<div class="spacer"></div>

<p class="small-titre">g) Resynchroniser un Esclave Trop Obsolète Pour Restaurer</p>

<p>Les slaves appliquent, <b>de façon asynchrone</b>, les opérations du master situées dans l'oplog de celui-ci. L'oplog à une taille finie et si un esclave est
trop loin derrière (périmé), <b>une resynchronisation complète sera nécessaire</b>. Pour resynchroniser l'esclave, connectez-vous à un slave via le shell mongo
en utilisant la commande <b>"resync"</b> :</p>

<pre>
use admin
db.runCommand( { resync: 1 } )
</pre>

<div class="spacer"></div>

<p>Cela <b>force une resynchronisation complète</b> de toutes les données (ce qui serait très lent sur une lourde base de données). Vous pouvez effectuer <b>la même chose
en stoppant mongod sur le slave</b>, supprimant le contenu entier du <b>dbpath</b> du slave et redémarrer le mongod associé.</p>
<a name="chai"></a>

<div class="spacer"></div>

<p class="small-titre">h) Chaînage d'Esclaves</p>

<p>Les esclaves ne peuvent pas être <b>"chaînés"</b>, ils doivent tous se connecter au master <b>directement</b>. Si un slave essaye d'être le slave d'un autre slave,
le shell mongod va vous <b>renvoyer cette erreur</b> :</p>

<pre>assertion 13051 tailable cursor requested on non capped collection ns:local.oplog.$main</pre>
<a name="corr"></a>

<div class="spacer"></div>

<p class="small-titre">i) Corriger la Source d'un Esclave</p>

<p>Afin de <b>changer la source d'un esclave</b>, modifiez manuellement la collection <b>"local.sources"</b> du slave.
Par exemple, si vous définissez <b>par erreur</b> un nom d'hôte incorrecte pour la source du slave comme dans l'exemple suivant :</p>

<pre>mongod --slave --source prod.paris</pre>

<div class="spacer"></div>

<p>Vous pouvez <b>corriger cela en redémarrant le slave</b> avec les arguments <b>"--slave"</b> et <b>"--source"</b> :</p>

<pre>mongod</pre>

<div class="spacer"></div>

<p>Connectez-vous à cette instance mongod en utilisant le shell mongo et <b>mettez à jour la collection "local.sources"</b> en utilisant la séquence suivante :</p>

<pre>
use local
db.sources.update(
	{
		host : "prod.paris"
	},
	{ 
		$set : { host : "prod.paris.exemple.fr" }
	} 
)
</pre>

<div class="spacer"></div>

<p>Redémarrez ensuite le slave avec la ligne de commande ayant les bons arguments ou alors <b>sans l'arguement "--source"</b>. Après avoir configuré la collection
<b>"local.sources"</b> la première fois, l'argument <b>"--source"</b> n'aura aucun effets. En revanche, les deux commandes suivantes sont correctes :</p>

<pre>mongod --slave --source prod.paris.exemple.fr</pre>

<p>ou,</p>

<pre>mongod --slave</pre>

<div class="small-spacer"></div>

<p>L'esclave récupère finalement les données <b>depuis le bon maître</b>.</p>

<div class="spacer"></div>

<p>Je vous invite maintenant à passer <b>à la page suivante</b>, qui débute ainsi la section des <b>tutoriaux</b> : <a href="tutoriaux_deploiement.php">"Déploiement de Replica Set" >></a>.
Dans cette partie, on trouvera <b>plus de pratique</b> afin de <b>déployer correctement</b> vos replica sets.</p>

<?php

	include("footer.php");

?>
