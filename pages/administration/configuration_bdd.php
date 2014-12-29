<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Configuration d'Exécution de Base de Données</li>
</ul>

<p class="titre">[ Configuration d'Exécution de Base de Données ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#conf">I) Configurer la Base de Données</a></p>
	<p class="elem"><a href="#cons">II) Considérations de Sécurité</a></p>
	<p class="elem"><a href="#repl">III) Configuration de Réplication et de Sharding</a></p>
	<p class="elem"><a href="#exec">IV) Exécuter Plusieurs Instances de Base de Données sur un Même Système</a></p>
	<p class="elem"><a href="#diag">V) Diagnostique de Configurations</a></p>
</div>

<p>L'interface en ligne de commande et le fichier de configuration fournissent aux administrateurs MongoDB un large nombre d'options et de paramètres
pour gérer et contrôller les opérations du SGDB. Nous allons voir dans cette partie du tutoriel une vue d'ensmble des configurations les plus courantes
ainsi que des exemples.

Alors que ces deux interfaces fournissent un accès au même ensemble d'options et de paramètres, le tutoriel va majoritairement utiliser un fichier
de configuration. Si vous exécutez MongoDB en utilisant un script de contrôle ou en ayant installé un package pour votre système d'exploitation, 
vous aurez déjà un fichier de configuration situé dans /etc/mongodb.conf. Vérifiez cela en explorant le contenu du script /etc/init.d/mongod ou
/etc/rc.d/mongod afin de vous assurer que les scripts de contrôle exécutent le processus mongod avec le fichier de configuration approprié.

Pour démarrer une instance MongoDB en utilisant cette configuration :</p>

<pre>
mongod --config /etc/mongodb.conf
mongod -f /etc/mongodb.conf
</pre>

<p>Modifiez alors les valeurs dans le fichier /etc/mongodb.conf de votre système pour contrôler la configuration de l'instance de votre base de données.</p>
<a name="conf"></a>

<div class="spacer"></div>

<p class="titre">I) [ Configurer la Base de Données ]</p>

<p>Prenons la configuration basique suivante :</p>

<pre>
fork = true
bind_ip = 127.0.0.1
port = 27017
quiet = true
dbpath = /srv/mongodb
logpath = /var/log/mongodb/mongod.log
logappend = true
journal = true
</pre>

<p>Pour la plupart des serveurs standalone, cette configuration est suffisante. Regardons de plus près cette configuration :

- fork est définità true, ce qui active le mode démon pour le processus mongod, ce qui détache (fork) MongoDB de la session en cours et vous permet d'exécuter
la base de données en tant que serveur.

- bind_ip est 127.0.0.1, ce qui force le serveur à écouter uniquement les requêtes depuis localhost. N'associez uniquement qu'aux interfaces sécurisées
auxquelles l'application peut accéder grâce au contrôle d'accès fournit pas le pare-feu.

- port est sur 27017 qui est le port MongoDB par défaut pour les instances de base de données. MongoDB peut associer n'importe quel port.</p>

<div class="alert alert-info">
	<u>Note</u> : Pour les systèmes UNIX, vous devez disposer des privilèges superutilisateur afin d'associer une instance sur un port plus petit que 1024.
</div>

<div class="spacer"></div>

<p>- quiet est définit à true. Cela désactive toutes les entrées (sauf les plus critiques) du fichier log. En temps normal, cette option est préférable pour éviter
des fichiers logs trop gourmands. Lors de situations de test ou de diagnostique, définissez cette valeur à false. Utilisez le paramètre setParameter pour modifier
cette valeur lorsqu'une instance est en cours d'exécution.

- dbpath est /srv/mongodb, ce qui spécifie ou MongoDB va stocker ses fichiers de données. /srv/mongodb et /var/lib/mongodb sont des locations populaires.
Le compte utilisateur, sous lequel ses instances sont exécutées, aura besoin des droits d'accès en lecture et en écriture sur ce dossier.

- logpath est /var/log/mongodb/mongod.log est l'endroit ou mongod va écrire ses logs. Si vous ne définissez pas cette valeur, mongod écrit toutes ses sorties
sur la sortie standard (par exemple : stdout).

- logappend est définit à true, ce qui permet d'ajouter le contenue des nouveaux logs à ceux qui existent déjà, sans réécrire par dessus le fichier.

- journal est définit à true ce qui va activer le journaling. Les versions 64bits de mongod ont le journaling d'activé par défaut. Ce paramètre devrait être redondant.

En prenant en compte la configuration par défaut, certaines de ces valeurs pourraient être redondantes. En revanche, dans certains cas, définir explicitement
la configuration augmente le bon fonctionnement du système et réduit la probabilité d'erreurs.</p>
<a name="cons"></a>

<div class="spacer"></div>

<p class="titre">II) [ Considérations de Sécurité ]</p>

<p>Les options de configuration suivantes sont utiles pour limiter l'accès à une instance mongod :</p>

<pre>
bind_ip = 127.0.0.1,10.8.0.10,192.168.4.24
nounixsocket = true
auth = true
</pre>

<p>Regardons ce que tout cela signifie :

- bind_ip a trois valeurs : 127.0.0.1 pour l'interface localhost, 10.8.0.10 une adresse IP privée typiquement utilisées pour les réseaux locaux ou les VPN,
puis, l'adresse 192.168.4.24 d'une interface de réseau privé typiquement utilisée pour les réseaux locaux.
Les instances MongoDB de production ont besoin d'être accessibes depuis plusieurs serveurs de base de données, pour cette raison, il est important
d'associer MongoDB à plusieurs interfaces qui sont accessibles depuis vos serveurs d'application. Il est aussi important de limiter ces interfaces aux interfaces
contrôllées et protégées au sein du réseau.
  
- nounixsocket est définit à true pour désactiver le Socket UNIX, qui est activé par défaut. Cela limite les accès au système local. Cela peut-être utile
lorsque MongoDB est exécuté sur des systèmes ayant un accès partagé, mais a un impacte minimale dans la plupart des situations.

- auth est définit à true, ce qui va activer l'identification système pour MongoDB. Si vous l'activez, vous devrez vous identifier en connectant sur l'interface
localhost pour la première fois afin de créer des identifiants utilisateur.</p>
<a name="repl"></a>

<div class="spacer"></div>

<p class="titre">III) [ Configuration de Réplication et de Sharding ]</p>
  
<p>Configuration de réplication : La configuration de Replica Set est très rapide, celle-ci à besoin que l'option replSet ait une valeur qui est la même
pour tous les membres de l'ensemble :</p>

<pre>replSet = set0</pre>

<p>Utilisez des noms descriptifs pour les noms d'ensembles. Une fois configuré, utilisez le shell mongo pour ajouter les hôtes au Replica Set.
Pour activer l'authentification sur un Replica Set :</p>

<pre>keyFile = /srv/mongodb/keyfile</pre>

<p>Depuis la version 1.8 pour les Replica Sets et depuis la version 1.9.1 pour les Sharded Clusters, le paramètre keyFile active l'authentification
et spécifie un fichier clé pour que chaque membre du Replica Set utilise afin de se connecter aux autres. Le contenu du fichier clé est arbitraire et doit
être exactement le même sur tous les membres du Replica Set ainsi que sur les instances mongods qui se connectent à cet ensemble. Le fichier clé doit être
impérativement en dessous de 1ko et ne doit contenir uniquement des caractères appartenant à l'ensemble base64. De plus, ce fichier ne doit absolument pas
avoir les permissions "group" ou "world" pour les systèmes UNIX.</p>

<p>Configuration de Sharding : Le sharding nécessite un certain nombre d'instances mongod ayant des configurations différentes. Les serveurs de configuration
stockent les méta-information du cluster pendant que le cluster, lui, va distribuer les données sur un ou plusieurs serveurs shard.</p>

<div class="alert alert-info">
	<u>Note</u> : Les serveurs de configuration ne sont pas des Replica Sets.
</div>

<div class="spacer"></div>

<p>Pour définir un ou trois instances de serveur de configuration en tant qu'instances mongod normales :</p>

<pre>
configsvr = true
bind_ip = 10.8.0.12
port = 27001
</pre>

<p>Cela va créer un serveur de configuration exécuté sur l'adresse IP privée 10.8.0.12 sur le port 27001. Asurez-vous qu'il n'y a aucun conflit de port et que
votre serveur de configuration est accessible depuis toutes vos instances mongod et mongos.

Pour définir les shards, configurez deux ou plusieurs instances mongod en utilisant la configuration de base en ajoutant le paramètre shardsrv :</p>

<pre>shardsvr = true</pre>

<p>Enfin, pour établir le cluster, configurez au moins un processus mongos avec les paramètres suivants :</p>

<pre>
configdb = 10.8.0.12:27001
chunkSize = 64
</pre>

<p>Vous pouvez spécifier plusieurs instances configdb en spécifiant les noms d'hôtes et les ports dans une liste séparés par des virgules. En général, évitez
de modifier le paramètre chunkSize de sa valeur par défaut (64) et assurez-vous que ce paramètre est le même sur toutes les instances mongos.</p>
<a name="exec"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Exécuter Plusieurs Instances de Base de Données sur un Même Système ]</p> 
  
<p>Dans plusieurs cas, exécuter plusieurs instances mongod sur un seul et même système n'est pas recommandé. Sur certains types de déploiements ainsi que pour des raisons
de tests, vous serez amenés à exécuter plusieurs instances mongod sur un même système. Si vous avez de petites bases de données ou un disque dûr ultra-rapide
du genre SSD, vous pourrez vous permettre d'exploiter plusieurs mongod sur un même système.
Dans ces cas là, utilisez une configuration de base pour chaque instance, tout en considérant les valeurs suivantes :</p>

<pre>
dbpath = /srv/mongodb/db0/
pidfilepath = /srv/mongodb/db0.pid
</pre>

<p>La valeur du dbpath contrôle l'endroit ou vont se situer les fichiers de données de l'instance mongod. Assurez-vous que chaque base de données à 
un répertoire de données bien distinct avec un nom compréhensif. Le paramètre pidfilepath contrôle ou le processus mongod va placer son fichier process id.
Comme cela traque le fichier mongod spécifique, il est cruciale que se fichier soit unique et bien labellé afin de rendre facilement l'exécution et l'arrêt
de ces processus.

Créez des scripts de contrôle additionnels et/ou ajustez votre configuration MongoDB existante ainsi que les scripts de contrôle pour contrôller ces processus.</p>
<a name="diag"></a>

<div class="spacer"></div>

<p class="titre">V) [ Diagnostique de Configurations ]</p>

<p>Les options de configuration suivantes vont contrôller diverses particularités de MongoDB pour des raisons de diagnostique. Les paramètres suivants
ont des valeurs par défaut qui sont définient pour des déploiement de production :</p>

<pre>
slowms = 50
profile = 3
verbose = true
diaglog = 3
objcheck = true
cpu = true
</pre>

<p>Utilisez la configuration de base et ajoutez ces options si vous rencontrez des problèmes encore inconnus ou des problèmes de performances :

- slowms configure le seuil pour qu'un profiler de base de données considère une requête comme lente. La valeur par défaut est de 100 millisecondes.
Vous pouvez réduire cette valeur si le profiler ne retourne pas de résultats assez satisfaisants. 

- profile définit le niveau de profiling de la base de données. Le profiler est désactivé par défaut car il peut avoir un impacte sur les performances
générales. Tant que ce paramètre n'a pas de valeurs, les requêtés ne sont pas profilées.

- verbose : accentue le nombre d'entrées dans le fichier log pour une instance mongod. Utilisez cette option seulement si vous rencontrez un problème 
qui n'est pas refletté avec le niveau logging normal. Si vous avez besoin davantage d'informations :</p>

<pre>
v = true
vv = true
vvv = true
vvvv = true
vvvvv = true
</pre>

<div class="spacer"></div>

<p>Chaque v additionnel ajoute des informations dans le fichier log. L'option verbose est égale à v = true.

- dialog active le log pour le diagnostique. Le niveau trois enregistre toutes les options de lecture et d'écriture.

- objcheck force mongod à valider toutes les requêtes des clients jusqu'à la réception. Utilisez cette option pour vous assurer que les requêts invalides
ne causent pas d'erreurs 
, particulièrement lorsqu vous exécutez une base de données avec des clients non-fiables. Cette option peut affecter les performances de la base de données.

- cpu force mongod à retourner le pourcentage de la dernière intervalle passée en verrou d'écriture. L'intervalle est en général de 4secondes, et chaque ligne
retournée dans le log inclut l'intervale actuelle depuis le dernier rapport et le pourcentage de temps passé en verrou d'écriture.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="import_export.php">"Importer/Exporter les Données MongoDB" >></a>.</p>

<?php

	include("footer.php");

?>