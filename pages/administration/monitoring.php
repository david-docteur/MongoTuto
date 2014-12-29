<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Monitoring avec MongoDB</li>
</ul>

<p class="titre">[ Monitoring avec MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#stra">I) Stratégies de Monitoring</a></p>
	<p class="elem"><a href="#outi">II) Outils MongoDB de Reporting</a></p>
	<p class="elem"><a href="#proc">III) Processus de Logging</a></p>
	<p class="elem"><a href="#diag">IV) Diagnostique des Problèmes de Performance</a></p>
	<p class="elem"><a href="#repl">V) Réplication et Monitoring</a></p>
	<p class="elem"><a href="#shar">VI) Sharding et Monitoring</a></p>
</div>

<p>Le monitoring est un aspect très important pour toute administration de base de données. Une bonne compréhension du reporting de MongoDB va vous permettre
de vérifier l'état de votre base de données et maintenir votre déploiement sans problèmes. De plus, la compéhension des paramètres d'opérations MongoDB
va vous permettre de diagnostiquer avant un échec.

Nous allons voir les différents utilitaires de monitoring ainsi que les statistiques de reporting disponibles dans MongoDB. Mais aussi une introduction
aux stratégies de diagnostique et des suggestions de monitoring pour les Replica Sets et les sharded clusters.</p>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Le MMS (MongoDB Management Service) est un service de monitoring hébergé qui récupère et aggrège les données
	afin d'avoir une vue d'ensemble des performances ainsi que des opérations des déploiements MongoDB.
</div>
<a name="stra"></a>

<div class="spacer"></div>

<p class="titre">I) [ Stratégies de Monitoring ]</p>

<p>Il y a trois méthodes pour récolter les données concernant l'état d'une instance MongoDB en cours d'exécution :

- Premièrement, il y a un ensemble d'utilitaires fournis avec MongoDB qui permet le reporting en temps réel des activités de la base de données.
- Deuxièmement, les commandes de base de données retournent des statistiques en fonction de l'état de la base de données actuelle.
- Troisièmement, le MMS récupère les données des déploiements MongoDB en cours d'exécution et fournit une visualisation et des alertes en fonction de ces données.
MMS est un service gratuit fournit par MongoDB.

Chaque stratégie peut aider à répondre à différentes questions et est utile selon le contexte. Ces méthodes sont complémentaires.</p>
<a name="outi"></a>

<div class="spacer"></div>

<p class="titre">II) [ Outils MongoDB de Reporting ]</p> 

<p>Nous allons parler des différents outils de reporting MongoDB :

Outils : MongoDB inclut un nombre d'outils qui retournent rapidement des statistiques à propos des performances et des activés de vos instances. Typiquement,
ils sont surtout utiles pour le diagnostique des problèmes.

mongostat : cet outil va capturer et retourner le nombre d'opérations d'opération de base de données par type (exemple : insert, update, delete etc ...).
Ces totaux vos donner des informations sur la charge distribuée sur le serveur.
Utilisez mongostat pour comprendre la distribution des types d'opérations.</p>

[ screen mongostat ]

<div class="spacer"></div>

<p>mongotop : cet outil observe et rapporte les opérations d'écritures et de lectures d'une instance MongoDB en cours, et effectue un rapport de ces
statistiques par collection.
Utilisez mongotop pour vérifier si l'activité et l'utilisation de votre base de données correspondent à vos attentes.</p>

[ screen mongotop ] 

<div class="spacer"></div>

<p>L'interface REST : MongoDB fournit une simple interface REST qui peut être utile pour configurer le monitoring et pour d'autres tâches administratives.
Pour l'activer, configurez votre mongod soit en le démarrant avec l'option --rest ou alors en définissant la paramètre rest à true dans un fichier de configuration.</p>

[ screen interface REST ]

<div class="spacer"></div>

<p>La console HTTP : MongoDB fournit une interface web qui affiche des informations de monitoring et de diagnostique dans une simple page web. Cette interface
est accessible à l'adresse localhost:port ou le port est égal à la valeur de votre port mongod + 1000.
Par exemple, si votre mongod est exécuté sur le port 27017 par défaut, le port de la console sera 28017.</p>

[ screenshot interface HTTP ]

<div class="spacer"></div>

<p>Les commandes : MongoDB inclut un nombre de commandes qui donnent des informations sur l'état de la base de données.
Ces informations vont fournir des informations plus précises que les outils décrits au dessus. Utilisez leur résultats retournés dans des scripts
et des programmes pour développer des alertes personnalisées ou pour modifier le comportement de votre application en fonction de l'activité de votre instance.
La méthode db.currentOp est un autre outil utile pour identifier les opérations en cours de l'instance de votre base de données.

serverStatus : La commande serverStatus, ou db.serverStatus() depuis un shell, retourner une vue d'ensemble du statut d'une base de données, en détaillant
l'utilisation de l'espace disque, utilisation de la mémoire, la connexion, le journaling ainsi que d'autres ... La commande retourne un résultat rapidement
et n'affecte pas les performances de MongoDB.
serverStatus retourne l'état d'une instance MongoDB. Cette commande est rarement exécutée directement. Dans la plupart des cas, les données
ont plus de sens une fois agrégées, comme avec l'outil de monitoring MMS. Sans aucun doute, les administrateurs doivent être familiers avec les données
retournées par cette commande.

dbStats : La commande dbStats, ou db.stats() depuis un shell, retourne un document qui indique des informations sur l'utilisation de l'espace de stockage 
ainsi que des volumes de données. La commande dbStats reflette le montant de l'espace de stockage utilisé, la quantité de données que la base de données contient,
ainsi que les objets, les collections et les compteurs d'indexe.
Utilisez ces information pour du monitoring regardant l'état et la capacité de stockage d'une base de données spécifique. Cette commande vous permet aussi de comparer
l'utilisation entre les bases de données et pour déterminer la taille moyenne par document dans une base de données.

collStats : La commande collStats fournit des statistiques qui ressemble à dbStats mais pour chaque collection, en incluant le total d'objets dans une collection,
la taille de la collection, l'espace disque utilisé par celle-ci et des informations à propos de ses indexes.</p>

<div class="spacer"></div>

<p>replSetGetStatus : La commande replSetGetStatus, ou rs.status depuis un shell, retourne une vue d'ensemble du statut de votre Replica Set. 
Le document retourné par cette commande retourne des détails sur l'état et la configuration du Replica Set et des statistiques à propos de ses membres.
Utilisez ces informations pour vous assurer que votre réplication est bien configurée ainsi que pour vérifier les connexiosn entre les hôtes actuels 
et les autrees membres du Replica Set.

Outils de Tierce Partie : Un nombre d'outil de tierce partie pour le monitoring supportent MongoDB, soit directement, soir avec leur propre plugin.

Outils de Monitoring Directement hébergés : Il y a des outils de monitoring que vous devez installer, configurer et installer sur votre propre serveur.
La plupart sont open-source :</p>

<table>
	<tr>
		<th>Outil</th>
		<th>Plugin</th>
		<th>Description</th>
	</tr>
	<tr>
		<td>Ganglia</td>
		<td>mongodb-ganglia</td>
		<td>Script Python pour retourner les opérations par secondes, utilisation de la mémoire, statistiques b-arbres, statut master/slave et connexions en cours.</td>
	</tr>
	<tr>
		<td>Ganglia</td>
		<td>gmond_python_modules</td>
		<td>Utilise les résultats des commandes serverStatus et replSetGetStatus.</td>
	</tr>
	<tr>
		<td>Motop</td>
		<td>-</td>
		<td>Monitoring en temps réel pour les serveurs MongoDB. Affiche les opérations courantes par ordre de durée chaque secondes.</td>
	</tr>
	<tr>
		<td>mtop</td>
		<td>-</td>
		<td>Un outil ressemblant à top.</td>
	</tr>
	<tr>
		<td>Munin</td>
		<td>mongo-munin</td>
		<td>Récupère des statistiques serveur.</td>
	</tr>
	<tr>
		<td>Munin</td>
		<td>mongomon</td>
		<td>Récupère des statistiques de collection (tailles, tailles d'indexes et chaque totaux de collection pour une base de données donnée).</td>
	</tr>
	<tr>
		<td>Munin</td>
		<td>munin-plugins Ubuntu PPA</td>
		<td>Des plugins munin additionnels qui ne sont pas dans la distribution principale.</td>
	</tr>
	<tr>
		<td>Nagios</td>
		<td>nagios-plugin-mongodbo</td>
		<td>Un simpel script Nagios de vérification écrit en Python.</td>
	</tr>
	<tr>
		<td>Zabbix</td>
		<td>mikoomi-mongodb</td>
		<td>Monitoring, utilisation de ressources, santé, performance et autres métriques importantes.</td>
	</tr>
</table>

<div class="spacer"></div>

<p>Considérez aussi dex, un outil d'analyse de requête et d'indexes pour MongoDB qui compare les fichiers logs de MongoDB et les indexes pour réaliser des
recommandations d'indexes.
Dans le package MongoDB Enterprise, vous pouvez exécuter MMS On-Prem, qui offre des fonctionnalités de MMS dans un package mais qui s'exécute sous votre infrastructure.

Outils de Monitoring hébergés (SaaS) : Ces outils de monitoring sont fournis en tant que service hébergé, habituellement à travers une souscription payante.</p>

<table>
	<tr>
		<th>Nom</th>
		<th>Notes</th>
	</tr>
	<tr>
		<td>MongoDB Management Service</td>
		<td>MMS est un pack de services basé dans le cloud pour gérer ls déploiements MongoDB. MMS fournit des fonctionnalités de monitoring et de backup.</td>
	</tr>
	<tr>
		<td>Scout</td>
		<td>Une variété de plugins incluant MongoDB Monitoring, MongoDB Slow Queries et MongoDB Replica Set Monitoring.</td>
	</tr>
	<tr>
		<td>Server Density</td>
		<td>Tableau de bord pour MongoDB, les alertes spécifiques, timeline de FailOver de réplication et applications iPhone, iPad et Android.</td>
	</tr>
</table>
<a name="proc"></a>

<div class="spacer"></div>

<p class="titre">III) [ Processus de Logging ]</p>

<p>Pendant les opérations habituelles, les instances mongod et mongos enregistrent toute les opérations et activités du serveur en temps réel via la sortie
standart ou un fichier log. Les paramètres d'exécutions suivants contrôlent ces options :

- quiet : Limite le nombre d'informations écritent via la sortie standart ou un dans le fichier log.
- verbose  : Augmente le nombre d'informations écritent dans le log ou via la sortie standart.
	Vous pouvez aussi spécifier cette option avec v (-v). Pour plus de verbosité, définissez plusieurs v comme vvvv = True.
	Vous pouvez aussi changer la verbosité d'une instance mongod ou mongos en cours d'exécution avec la commande setParameter.
- logpath : Active l'écriture des logs dans un fichier, plutôt que d'utiliser la sortie standart. Vous devez spécifier le chemin complet du fichier log
lorsqu vous définissez le paramètre.
-logappend : Ajoute des informations à un fichier log plutôt que de ré-écrire par dessus.</p>

<div class="small-spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Vous pouvez spécifier ces opérations de configuration comme argument de ligne de commande avec mongod ou mongos. Par exemple :
	mongod -v --logpath /var/log/mongodb/server1.log --logappend
	Démarrez une instance mongod en mode verbose, en ajoutant les données au fichier log se situant dans /var/log/mongodb/server1.log/.
</div>

<p>Les commandes de base de données suivantes affectent l'écriture des logs :
- getLog : affiche les messages récents depuis le log du processus mongod.
- logRotate : Rotate les fichiers logs des processus mongod seulement.</p>
<a name="diag"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Diagnostique des Problèmes de Performance ]</p>

<p>Des performances dégradées au sein de MongoDB est lié à la relation entre la quantité de données stockée dans la base de données, le montant de mémoire RAM,
le nombre de connexions à la base de données ainsi que le temps que la base de données reste dans un état verouillé (lock).

Dans certains cas, les problèmes de performance peuvent être liés à la charge de travail, aux accès des données ou la disponibilité du matériel sur le système
pour les environnements virtualisés. Certains utilisateurs rencontrent des limitations de performance à cause d'une stratégie d'indexe inappropriée, ou à cause
d'un pauvre schéma de conception des données. Dans d'autre situations, les défauts de performances reflettent une base de données pleine ou presque, à ce moment
il sera temps d'ajouter plus de capacité à cette base.
Les points suivants décrivent des causes de dégradation de performance dans MongoDB :

Verrous (locks) : MongoDB utilise un système de verrou pour assurer la cohérence des données. En revanche, si certaines opérations prennent du temps à s'exécuter,
ou forment une queue, les performances vont se ralentir vu que d'autres opérations et requêtes vont attendre que le verrou se libère. Les ralentissements
liés au verrou peuvent être intermittents. Pour voir si le verrou a affecté vos performances, observez les données dans la section globalLock du document
retourné par la commande serverStatus. Si globalLock.currentQueue.total est haut, alors il y a une chance pour qu'un large de nombre de requêtes
soit en train d'attendre pour un verrou. Cela indique un probable problème de concurrence qui pourrait affecter les performances.

Si globalLock.totalTime est haut par rapport à uptime, la base de données a été dans un état vérouillé pour pas mal de temps. Si globalLock.ratio est aussi élevé,
MongoDB a traité un grand nombre de longue requêtes. Les requêtes longues sont souvent le résultat d'un nombre de facteurs différents : utilisation insuffisante
des indexes, schéma des données non optimal, faible structure des requêtes, problèmes d'architecture du système ou mémoire RAM insuffisante.</p>

<div class="spacer"></div>

<p>Utilisation de la mémoire : MongoDB utilise l'association de fichiers à la mémoire pour stocker les données. Prenons en compte un ensemble de données
de taille suffisante, le processus MongoDB va allouer toute la mémoire disponible du système pour cette utilisation. Alors que ceci est une partie du processus,
et rend MongoDB plus performant, les fichiers associés en mémoire rendent difficile le fait de déterminer si la quantité de mémoire RAM est suffisante
pour cet ensemble de données.

Les métriques d'utilisation de la mémoire du résultat de la commande serverStatus peuvent fournir des informations sur l'utilisation de la mémoire par MongoDB.
Vérifiez la mémoire utilisée (par exemple : rem.resident) : si cela excède le montant disponible de mémoire système et qu'il y a un grand nombre de 
données sur le disque qui n'est pas dans la RAM, alors vous avez certainement dépassé les capacités de votre système.

Vous devriez vérifier aussi la quantitée de mémoire mappée (exemple : mem.mapped). Si cette valeur est plus grande que la quantité de mémoire système,
certaines opérations vont nécessiter des accès disques (erreurs de pagination) pour lire les données de la mémoire virtuelle et affecter négativement les performances.</p>

<p>Erreurs de pagination : Une erreur de pagination se produit lorsque MongoDB nécessite des données qui ne sont pas situées dans la mémoire physique et qui doivent
se lire depuis la mémoire virtuelle. Pour vérifier l'existense d'erreurs de pagination, vérifier la valeur extra_info.page_faults avec les informations
retournées avec la commande serverStatus. Ces données sont disponibles uniquement sur les systèmes Linux.
Une simple erreur de pagination se termine rapidement et ne pose pas trop de problèmes. Par contre, de larges volumes d'erreurs de pagination indiquent
que MongoDB est en train de lire trop de données depuis le disque dur. Dans plusieurs situations, les verrous de lectures de MongoDB vont "céder"
après une erreur de pagination afin d'autoriser les autres processus à lire et éviter d'être bloqués en attendant que la prochaine page lise en mémoire.
Cette façon améliore la concurrence et améliore surtout le débit général au sein de système contenant de gros volumes d'informations.

Augmenter la quantité de mémoire RAM accessible par MongoDB devrait réduire les erreurs de pagination. Si cela n'est pas possible, vous voudrez alors prendre
la décision de déployer un sharded cluster et/ou ajouter des shards à votre déploiement pour distribuer les charges de travail sur les différentes instances mongod.


Nombre de connexions : Dans la plupart des cas, le nombre de connexions entre la couche applicative (par exemple : client) et la base de données peut
submerger la capacité du serveur à intercepter les requêtes et cela peut produire des irrégularités de performance. Les champs suivants, provenant du 
document retourné par la commande serverStatus fournit les informations suivantes :

- globalLock.activeClients : contient le nombre total de clients ayant des opérations actives dans une queue ou en cours d'exécution.
- connections : regroupe les deux champs suivants :
	- current : le nombre total de clients actuels connectés à la base de données.
	- available : le nombre total de connexions inutilisées qui sont disponibles pour de nouveaux clients.</p>

<div class="spacer"></div>
	
<div class="alert alert-info">
	<u>Note</u> : MongoDB a une limite du nombre de connexions définie à 20 000 si celle-ci n'est pas restrainte.
	Vous pouvez modifier les limites du système en utilisant la commande ulimit, ou alors en éditant le fichier /etc/sysctl de votre système.
</div>

<p>Si il y a un grand nombre de requêtes car il y a plusieurs applications concurrentes envoyant des requêtes, la base de données aura probablement
du mal à répondre à la demande. Si c'est le cas, vous aurez besoin d'augmenter la capacité de votre déploiement. Pour les applications
qui effectuent énormement d'oprétations de lecture, augmentez la taille de votre Replica Set et distribuez les opérations de lecture sur les membres
secondaires. Pour les applications lourdes en écritures, déployez le sharding et ajoutez un ou plusieurs shards à votre sharded cluster pour distribuer la charge
à travers les différentes instances mongod.

Des pointes dans le nombres de connexions peuvent aussi être la cause de multiples erreurs dans votre application ou même, votre driver. Tous les drivers
MongoDB officiels implémentent un système de file d'attente (queuing), ce qui permet aux clients d'utiliser et de ré-utiliser les connexions de manière plus efficace.
Pour les nombres extrêmement élevés de connexions, particulièrement si la charge de travail ne correspond pas, cela est souvent un indicateur d'une erreur de configuration
(driver ou autre).


Profiling de base de données : Le profiler de MongoDB est un système de profiling de base de données qui peut aider à identifier les requêtes et les opérations
peut optimisées.
Les niveaux de profiling disponibles sont les suivants :</p>

<table>
	<tr>
		<th>Niveau</th>
		<th>Paramètre</th>
	</tr>
	<tr>
		<td>0</td>
		<td>Off. Pas de Profiling</td>
	</tr>
	<tr>
		<td>1</td>
		<td>On. Inclut uniquement les opérations lentes</td>
	</tr>
	<tr>
		<td>2</td>
		<td>On. Inclut toutes les opérations</td>
	</tr>
</table>

<div class="spacer"></div>

<p>Activez le profiler en définissant la valeur "profile" en utilisant la commande suivante dans un shell mongo :</p>

<pre>db.setProfilingLevel(1)</pre>

<p>Le paramètres slowms définit ce qui constitue une opération "lente". Pour définir le seuil au dessus duquel le profiler considère une opération comme lente,
vous pouvez configurer le paramètres slowms au démarrage de l'opération db.setProfilingLevel().
Par défaut, mongod enregistre toutes les requêtes lentes dans son fichier log. Contrairement aux données de log, les données dans la collection system.profile
ne persistent pas entre les redémarrage du mongod.</p>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Parce-que le Profiler peut avoir un impacte négatif sur ls performances de la base de données, activez le profiling
	de façon minimalistique sur les systèmes de production.
	Vous devez activer le profiling par instance mongod et ce paramètre ne se propagera pas au sein d'un Replica Set ou d'un Sharded Cluster.
</div>

<p>Vous pouvez voir le résultat du profiler dans la collections system.profile de votre base de données en utilisant la commande showprofile dans un shell mongo, ou l'opération
suivante :</p>

<pre>db.system.profile.find( { millis : { $gt : 100 } } )</pre>

<p>Cela retourne toutes les opérations qui ont durées plus de 100 millisecondes. Assurez-vous que la valeur (ici 100) est au dessus du seuil slowms.</p>
<a name="repl"></a>

<div class="spacer"></div>

<p class="titre">V) [ Réplication et Monitoring ]</p>

<p>Parmis tous les besoins de monitoring pour toute instance MongoDB, pour les Replica Sets, les administrateurs doivent superviser le lag de réplication.
Le lag de réplication fait référence au temps total que cela prend de copier/répliquer une opéraiton d'écriture du membre primaire vers un membre secondaire.
Un court délai peut être acceptable bien sûr, mais deux problèmes majeurs apparaîssent lorsque le lag est trop conséquent :

- Premièrement, les opérations apparues pendant cette période de lag ne sont pas répliquées sur un ou plusieurs membres secondaires. Si vous utilisez la
réplication pour garantir l'autenticité des données, les longs délais devraient avoir un mauvais impacte sur l'intégrité de vos données.

- Dans un second temps, si le lag de réplication excède la longueur de l'Oplog, alors MongoDB va devoir effectuer une synchronisation initiale sur le secondaire,
en copiant toutes les données du primaire et reconstruire tous les indexes. Ce genre de situation n'est pas communes durant de normales circonstances, mais si
configurez l'Oplog pour être plus petit que la valeur par défaut, cette erreur a plus de risques de se produire.</p>


<div class="alert alert-info">
	<u>Note</u> : La taille de l'Oplog est paramètrable seulement pendant la première exécution d' l'instance mongod avec le paramètre --oplogSize,
	ou préférablement, le paramètre oplogSize dans un fichier de configuration MongoDB. Si vous ne spécifiez pas ce paramètre en ligne de commande
	avant d'exécuter avec l'option --replSet, mongod va créer une taille d'Oplog par défaut.
	Par défaut, la taille de l'Oplog représente 5% de l'espace disque libre sur les systèmes 64bits.
</div>

<p>Les erreurs de réplication sont souvent le résultat d'erreurs de connectivité réseau entre les membres, ou le résultat d'un membre primaire qui n'a pas
les ressources suffisantes pour supporter le traffic de réplication et de votre application. Pour vérifier le statut d'un Replica Set, utilisez la commande
replSetGetStatus ou la méthode suivante dans un shell mongo :</p>

<pre>rs.status()</pre>

<p>Le <a href="http://docs.mongodb.org/manualreference/command/replSetGetStatus" target="_blank">document</a> fournit une vue d'ensemble plus approfondie du résultat
de la commande précédente. En général, observez la valeur de optimeDate, et portez votre attention particulièrement à la différence entre le temps le membre
primaire et les membres secondaires.</p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Sharding et Monitoring ]</p>

<p>Dans la plupart de cas, les composants d'un sharded cluster bénéficient du même monitoring et de la même analyse que toutes les autres instances MongoDB.
Additionnellement, les clusters ont besoin de plus d'observation pour être sûr que les données sont bien distribuées à travers les noeuds et que les opérations
de sharding fonctionnent parfaitement.

Les serveurs de configuration : La base de données de configuration comporte un mapping des quels documents sont sur quels shards. Le cluster met à jour ce mapping
quand les chunks se déplacent entre les shards. Quand un serveur de configuration devient inaccessible, certaines opérations de sharding deviennent indisponibles,
telles que le déplacement de chunks et le démarrage d'instances mongos. Par contre, les clusters restent accessibles si les instances mongos sont déjà
en cours d'exécution.
Des serveurs de configuration inaccessibles peuvent avoir un sérieux impacte au sein de votre sharded cluster, vous devrez donc superviser vos serveurs de configuration
pour vous assurer que le cluster détient une balance équilibrée et que les instances mongos puissent redémarrer.
L'outil MMS Monitoring supervise les serveurs de configuration et peut créer des notifications si un serveur de configuration devient indisponible.

Balancement et distribution de chunks : Les déploiements de sharded cluster les plus efficaces balancent les chunks de façon équilibrée sur les différents shards.
Pour faciliter cela, MongoDB a un processus en arrière-plan, le balanceur, qui va distribuer les données afin de s'assurer que les chunks soient toujours
distribués de façon optimale sur les shards.
Servez-vous de la méthode db.printShardingStatus() ou sh.status() via un shell mongo. Cela retourne une vue d'ensemble du cluster entier en incluant
le nom de la base de données ainsi que la liste des chunks.

Etat verrouillé (lock) : Dans presque tous les cas, tous les verrous utilisés par le balanceur sont automatiquement relâchés quand ceux-ci deviennent obsolète.
En revanche, parce-que les longs verrous peuvnt bloquer les futurs balancements, il est important de vérifier que tous les verrous sont légitimes.
Pour vérifier l'état du verrou de la base de données, connectez-vous à une instance mongos via un shell mongo. Utilisez la commande suivante pour basculer sur la
base de données config et afficher tous les verrous sur le shard :</p>

<pre>
use config
db.locks.find()
</pre>

<p>Le processus de balancement, qui est hébergé par une seule instance mongos aléatoirement, réserve un verrou balanceur spécial qui empêche les autres	activités
de balancement d'agir. Utilisez la commande suivante sur la base de données de configuration pour vérifier l'état du verrou du Balanceur :</p>

<pre>db.locks.find( { _id : "balancer" } )</pre>

<p>Si le verrou existe, alors assurez-vous que le balanceur est activement en train de l'utiliser.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur la <a href="configuration_bdd.php">"Configuration d'Exécution de Base de Données" >></a>.</p>

<?php

	include("footer.php");

?>