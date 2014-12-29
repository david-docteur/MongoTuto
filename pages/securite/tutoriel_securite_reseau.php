<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Tutoriel de Sécurité Réseau</li>
</ul>

<p class="titre">[ Tutoriel de Sécurité Réseau ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#ipt">I) Configurer le Firewall iptables de GNU/Linux pour MongoDB</a></p>
	<p class="right"><a href="#ve">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#conc">- b) Conception</a></p>
	<p class="right"><a href="#pol">- c) Changer le Politique par Défaut en DROP</a></p>
	<p class="right"><a href="#conf">- d) Gérer et Maintenir la Configuration d'iptables</a></p>
	<p class="elem"><a href="#nets">II) Configurer le Firewall netsh de Windows pour MongoDB</a></p>
	<p class="right"><a href="#ven">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#concn">- b) Conception</a></p>
	<p class="right"><a href="#confn">- c) Gérer et Maintenir les Configurations du Pare-feu Windows</a></p>
	<p class="elem"><a href="#ssl">III) Se Connecter à MongoDB avec SSL</a></p>
	<p class="right"><a href="#confs">- a) Configurer mongod et mongos pour SSL</a></p>
	<p class="right"><a href="#cli">- b) Configuration SSL pour les Clients</a></p>
</div>

<p></p>
<a name="ipt"></a>

<div class="spacer"></div>

<p class="titre">I) [ Configurer le Firewall iptables de GNU/Linux pour MongoDB ]</p>

<p>Sur les systèmes <b>GNU/Linux</b> d'aujourd'hui, le programme <b>iptables</b> fournit des méthodes pour gérer <b>netfilter</b> ou les utilitaires de filtrage de packets
que le noyau <b>Linux</b> détient. Ces règles de pare-feu permettent aux administrateurs de <b>contrôler</b> les hôtes qui peuvent se connecter au système et
<b>limiter</b> les risques d'exposition en limitant les hôtes qui peuvent se connecter à un système.
Dans cette page, nous allons voir les grandes lignes de la configuration basique du pare-feu <b>iptables</b> de GNU/Linux. Utilisez ces approches comme un point
de départ pour votre réseau qui peut-être plus large.</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Les règles dans les configurations d'iptables sont représentées par des chaînes, qui décrivent le <b>processus de filtrage du traffic</b>. Ces chaînes ont un ordre
et les packets doivent passer à travers les premières règles de la chaîne avant d'atteindre les dernières. Ce tutoriel prend en compte uniquement les <b>deux chaînes</b> suivantes :</p>

<p><b>INPUT : contrôle le traffique entrant</b></p>
<p><b>OUTPUT : contrôle le traffique sortant</b></p>

<div class="small-spacer"></div>

<p>En considérant les ports par défaut de tous les processus de MongoDB, vous devez <b>configurer les règles</b> qui autorisent <b>uniquement les communications requises</b>
entre votre application et les instances mongod et mongos appropriées.
Soyez conscient que, par défaut, la politique par défaut d'iptables est <b>d'autoriser toutes les connexions et traffics jusqu'à ce qu'ils soient bloqués</b>.
Les modifications de configuration de ce tutoriel vont créer des règles qui vont autoriser le traffic venant d'adresses spécifiques sur des ports spécifiques,
utilisant une politique qui <b>refuse tout le traffic qui n'est pas explicitement autorisé</b>. Quand vous aurez correctement configuré vos règles sous iptables
pour autoriser uniquement le traffic que vous désirez, vous pouvez <b>changer la politique par défaut en DROP</b>.</p>
<a name="conc"></a>

<div class="spacer"></div>

<p class="small-titre">b) Conception</p>

<p>Cette section comporte un nombre de modèles et d'exemples pour <b>configurer iptables</b> que vous pourrez utiliser avec vos déploiements MongoDB.
Si vous avez configuré différents ports en utilisant le paramètre de configuration <b>"port"</b>, vous aurez besoin de modifier les règles en conséquences.</p>

<p><b>Traffic "depuis" et "vers" les instances mongod</b> : ce modèle est applicable pour toutes les instances mongod exécutées en mode standalone ou en tant que
replica set.
Le but de ce modèle est <b>d'autoriser explicitement</b> le traffic à l'instance mongod depuis le serveur d'application. Dans les exemples suivants, remplacez <b>"adresse-ip"</b>
par l'adresse IP du serveur de configuration :</p>

<pre>
iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 27017 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A OUTPUT -d "adresse-ip" -p tcp --source-port 27017 -m state --state ESTABLISHED -j ACCEPT
</pre>

<div class="spacer"></div>

<p>La première règle autorise tout le traffic entrant de l'adresse IP <b>"adresse-ip"</b> sur le port <b>27017</b>, ce qui permet au serveur d'application de se connecter
à l'instance mongod en question. La seconde règle <b>autorise tout le traffic</b> sortant de l'instance mongod vers le serveur d'application.</p>

<div class="small-spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Si vous avez uniquement un seul serveur d'application, vous pouvez remplacer "adresse-ip" avec l'adresse ip elle-même
	telle que : <b>198.51.100.55</b>. Vous pouvez également utiliser la notation CDIR <b>198.51.100.55/32</b>. Si vous voulez autoriser un block plus large d'adresses ip, vous pouvez autoriser
	le traffic depuis /24 en utilisant une des spécifications possibles suivantes :
</div>

<div class="small-spacer"></div>

<pre>
10.10.10.10/24
10.10.10.10/255.255.255.0
</pre>

<div class="small-spacer"></div>

<p><b>Traffic "depuis" et "vers" les instances mongos</b> : Les instances mongos fournissent le routage de requêtes pour les clusters fragmentés . Les clients se connectent
aux instances mongos qui se comportent <b>comme des instances mongod</b> d'un point de vue du client. L'instance mongos se connecte ensuite à toutes les instances mongod
qui sont <b>des composants du cluster</b>.

Utilisez les mêmes commandes <b>iptables</b> pour autoriser le traffic vers et depuis ces instances comme vous le désirez pour ces instances mongod qui font partie
des membres du <b>replica set</b>. Reprenez-la configuration ci-dessus pour le traffic <b>vers</b> et <b>depuis</b> les instances mongod.</p>

<p><b>Traffic depuis et vers un serveur de configuration MongoDB</b> : Les serveurs de configurations hébergent la base de données de configuration qui
stocke <b>les méta-informations</b> du cluster fragmenté. Chaque cluster de production a <b>3 serveurs de configuration</b>, initialisés avec l'option
mongod <b>"--configsrv"</b>. Les serveurs de configuration écoutent les connexions sur le port <b>27019</b>. Ajoutez donc les règles <b>iptables</b> suivantes au serveur de configuration
pour autoriser les connexions entrantes et sortantes sur le port <b>27019</b> pour les connexions vers les autres serveurs de configuration :</p>

<pre>
iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 27019 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A OUTPUT -d "adresse-ip" -p tcp --source-port 27019 -m state --state ESTABLISHED -j ACCEPT
</pre>

<div class="spacer"></div>

<p>Remplacez <b>"adresse-ip"</b> avec l'adresse de tous les mongod que fournissent les serveurs de configuration.
De plus, les serveurs de configuration doivent autoriser les connexions entrantes depuis toutes les instances mongos dans le cluster et toutes les instances mongod
dans le cluster. L'ajout d'une règle ressemble à ceci :</p>

<pre>iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 27019 -m state --state NEW,ESTABLISHED -j ACCEPT</pre>

<p>Remplacez <b>"adresse-ip"</b> avec l'adresse des instances mongos et des instances mongod fragmentées.</p>

<p><b>Traffic vers et depuis un Shard MongoDB</b> : Pour les serveurs fragmentés qui s'exécutent avec mongod <b>"--shardsrv"</b> sur le port <b>27018</b> par défaut, vous devez configurer
les règles pour iptables pour autoriser le traffic vers et depuis chaque shard :</p>

<pre>
iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 27018 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -A OUTPUT -d "adresse-ip" -p tcp --source-port 27018 -m state --state ESTABLISHED -j ACCEPT
</pre>

<div class="spacer"></div>

<p>Remplacer <b>"adresse-ip"</b> avec l'adresse IP de tous les mongod. Cela va vous permettre d'autoriser les traffic entrants et sortants entre tous les shards
en incluant les membres du replica set :

- toutes les instances mongod dans le shards replica sets
- toutes les instances mongod dans les autres shards 

Plus loin encore, les shards ont besoin d'effectuer des connexions sortantes vers :

- toutes les instances mongos
- toutes les instances mongod dans les serveurs de configurations.

Créez une règle qui ressemble à la suivante, et remplacez "adresse-ip" avec les adresses des serveurs de configuration et des instances mongos :</p>

<pre>iptables -A OUTPUT -d "adresse-ip" -p tcp --source-port 27018 -m state --state ESTABLISHED -j ACCEPT...........</pre>

<p>Fournir un accès pour les systèmes de monitoring : 

1) L'outil de diagnostique mongostat a besoin de pouvoir atteindre tous les composants d'un cluster (incluant les serveurs de configuration, les serveurs shardés 
et les instances mongos) lorsque celui-ci est exécutez avec le paramètre --discover.

2) Si votre système de monitoring a besoin d'accéder à l'interface, ajoutez la règle suivante à la chaîne :</p>

<pre>iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 28017 -m state --state NEW,ESTABLISHED.........</pre>

<p>Remplacez "adresse-ip" par l'adresse de l'instance qui a besoin d'accéder à l'interface HTTP ou REST.
Pour tous les déploiements, vous devriez restreindre l'accès à ce port seulement à l'instance de monitoring.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Pour les instances mongod shardées exécutées avec l'option shardsrv, la règle ressemblerait à la suivante :
</div>

<div class="spacer"></div>

<pre>iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 28018 -m state --state NEW,ESTABLISHED........</pre>

<p>Pour les instances mongod de serveur de configuration exécutées avec l'option configsrv :</p>

<pre>iptables -A INPUT -s "adresse-ip" -p tcp --destination-port 28019 -m state --state NEW,ESTABLISHED.........</pre>
<a name="pol"></a>

<div class="spacer"></div>

<p class="small-titre">c) Changer le Politique par Défaut en DROP</p>

<p>La politique par défaut des chaînes d'iptables est d'autoriser tous les traffiques. Une fois avoir complété toutes les modifications de configuration,
vous devez changer la politique par défaut à DROP de manière ce que tout le traffic qui n'est pas explicitement autorisé come ci-dessus ne sera pas autorisé
à atteindre les composants du déploiement MongoDB. Utilisez les commandes suivantes pour changer la politique :</p>

<pre>
iptables -P INPUT DROP
iptables -P OUTPUT DROP
</pre>
<a name="conf"></a>

<div class="spacer"></div>

<p class="small-titre">d) Gérer et Maintenir la Configuration d'iptables</p>

<p>Cette section va contenir un nombre d'opérations de base pour gérer et utiliser iptables. Il y a de multiples outils graphiques pour automatiser
quelques aspects de la configuration d'iptables, mais dans le fond, tous les outils graphiques iptables fournissent les mêmes fonctionnalités de base :

Rendre toutes les règles d'iptables persistantes : Par défaut, toutes les règles d'iptables sont stockées uniquement dans la mémoire. Quand votre
système redémarre, les règles de votre pare-feu vont se remettre à zéro. Lorsque vous avez testé un ensemble de règles et avoir eu la garantie que ces règles
contrôlent bien votre traffique, vous pouvez utiliser les opérations suivantes pour être sûr que ces règles vont être bin persistantes.

Sur un Linux Red Hat Enterprise, Fedora ou tout autre système relaté, vous pouvez utiliser la commande suivante :</p>

<pre>service iptables save</pre>

<p>Sur Debian, Ubuntu et les autres distributions relatives, vous pouvez utiliser la commande suivante pour exporter les règles d'iptables vers le fichier
de configuration /etc/iptables.conf :</p>

<pre>iptables-save > /etc/iptables.conf</pre>

<p>Pour restaurer les règles, utilisez cette commande :</p>

<pre>iptables-restore < /etc/iptables.conf</pre>

<div class="spacer"></div>

<p>Placez cette commande dans votre fichier rc.local ou dans le fichier /etc/network/if-up.d/iptables.

Vous pouvez lister toutes les règles d'iptables avec la commande suivante :</p>

<pre>iptables --L</pre>

<p>Supprimer toutes les règles d'iptables, s'il vous arrive commettre une erreur dans votre configuration lorque vous entrez vos règles dans iptables
ou si vous aveez simplement besoin de remettre iptables par défaut :</p>

<pre>iptables --F</pre>

<p>Si vous avez déjà rendu vos règles d'iptables persistantes, vous aurez besoin de répéter la procédure appropriée vue dans la section "Rendre toutes les règles d'iptables
persistantes".</p>
<a name="nets"></a>

<div class="spacer"></div>

<p class="titre">II) [ Configurer le Firewall netsh de Windows pour MongoDB ]</p>

<p>Sur les systèmes Windows Server, le programme netsh fournit des méthodes pour gérer le Firewall Windows. Ces règles de firewall permettent aux administrateurs
de contrôler quels hôtes peuvent se connecter au système, et limiter les risques d'exposition en limitant les hôtes qui peuvent se connecter au système.

Cette partie du tutoriel va parler des grandes lignes sur les configurations basiques du pare-feu Windows. Utilisez ces approches comme un point de départ
pour votre organisation qui sera plus large.</p>
<a name="ven"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Le pare-feu Windows traite les règles dans un ordre déterminé par type de règle, et définit dans l'ordre suivant :

1) Windows Service Hardening
2) Connection Security Rules
3) Authenticated Bypass Rules
4) Blocks Rules
5) Allow Rules
6) Default Rules

Par défaut, la politique du pare-feu Windows autorise toutes les connexions sortantes et bloque toutes les connexions entrantes.

En considérant les ports par défaut de tous les processus MongoDB, vous devez configurer les règles qui autorisent uniquement les communications nécessaires
entre votre application et les instances mongod.exe et les instances mongos.exe appropriées.
Les changements de configuration démontrés dans ce document vont créer des règles qui autorisent le traffic depuis des addresses spécifiques sur des ports spécifiques,
utilisant une politique par défaut qui bloque tout le traffic qui n'est pas explicitement autorisé.

Vous pouvez configurer le pare-feu Windows en utilisant l'outil de ligne de commande netsh ou alors par l'intermédiaire d'une application Windows.
Sur un Windows Server 2008, cette application se nomme "Windows Firewall with advanced security" dans "Administrative Tools".
Si les versions antérieures de Windows Server, accédez à l'application du pare-feu Windows dans le panneau "System And Security".</p>
<a name="concn"></a>

<div class="spacer"></div>

<p class="small-titre">b) Conception</p>

<p>Cette section comporte un nombre de concepts et d'exemples pour configurer le pare-feu windows à utiliser lors des déploiements MongoDB. Si vous avez utilisé
différents ports en utilisant le paramètre de configuration "port", vous devrez alors modifier les règles en conséquences.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Si vous souhaitez accéder à la documentation officielle du firewall Windows, c'est ici que ça se passe : <a href="http://technet.microsoft.com/en-us/network/bb545423.aspx" target="_blank">
	Documentation Officielle du Firewall Windows</a>.
</div>

<div class="spacer"></div>

<p>Traffic "vers" et "depuis" les instances mongod.exe : Ce concept est applicable à tous les mongod.exe exécutées en mode standalone ou en tant que part d'un
Replica Set. L'objectif de ce concept est d'autoriser explicitement le traffic vers l'instance mongod.exe depuis le serveur d'application.</p>

<pre>netsh advfirewall firewall add rule name="Open mongod port 27017" dir=in action=allow protocol=TCP localport..........</pre>

<p>Cette règle autorise tout le traffic entrant sur le port 27017, qui autorise le serveur d'application à se connecter à l'instance mongod.exe.
Le pare-feu Windows autorise aussi l'activation de l'accès au réseau pour une application entière plutôt qu'un port spécifique, comme dans l'exemple suivant :</p>

<pre>etsh advfirewall firewall add rule name="Allowing mongod" dir=in action=allow program=" C:\mongodb\bin....</pre>

<p>Vous pouvez autoriser tous les accès pour un serveur mongod.exe avec la commande suivante :</p>

<pre>netsh advfirewall firewall add rule name="Allowing mongos" dir=in action=allow program=" C:\mongodb\bin..........</pre>

<p>Traffic "vers" et "depuis" les instances mongos.exe : Les instances mongos.exe fournissent le routage de requête pour les shardec clusters.
Les clients se connectent aux instances mongos.exe, qui se comportent comme des instances mongod.exe aux yeux des clients.
Ensuite, l'instance mongos.exe se connecte à toutes les instances mongod.exe qui sont des composants du sharded cluster.
Utilisez cette même commande du pare-feu Windows pour autoriser le traffic "vers" et "depuis" ces instances comme vous le désirez pour ces instances mongod qui font partie
des membres du replica set.</p>

<pre>netsh advfirewall firewall add rule name="Open mongod shard port 27018" dir=in action=allow protocol=.........</pre>

<div class="spacer"></div>

<p>Traffic "depuis" et "vers" un serveur de configuration MongoDB : Les serveurs de configuration hébergent la base de données de configuration qui stocke
les méta-informations du sharded cluster. Chaque cluster de production a trois serveur de configuration initialisés en utilisant l'option mongod --configsrv.
Les serveurs de configuration écoutent sur le port 27019. Par conséquent, ajoutez les règles du pare-feu Windows suivantes pour que le serveur de configuration
autorise les connexions entrantes et sortantes sur le port 27019 afin qu'il se connecte aux autres serveurs de configuration.</p>

<pre>netsh advfirewall firewall add rule name="Open mongod config svr port 27019" dir=in action=allow protocol........</pre>

<p>De plus, les serveurs de configuration nécessitent l'autorisation du traffic entrant venant de toutes les instances mongos.exe ainsi que toutes les instances
mongod.exe du cluster. Ajoutez des règles qui ressemblent à ceci :</p>

<pre>netsh advfirewall firewall add rule name="Open mongod config svr inbound" dir=in action=allow protocol=.........</pre>

<p>Remplacez "adresse-ip" avec les adresses des instances mongos.exe et les instances mongod.exe des shards.

Traffic "vers" et "depuis" un serveur shard MongoDB : Pour les serveurs shard, exécutés tels que mongod --shardsrv ont leur port par défaut sur 27018, vous
devez configurer les règles du pare-feu Windows suivantes pour autoriser le traffic vers depuis chaque shard :</p>

<pre>
netsh advfirewall firewall add rule name="Open mongod shardsvr inbound" dir=in action=allow protocol=
netsh advfirewall firewall add rule name="Open mongod shardsvr outbound" dir=out action=allow protocol=
..........
</pre>

<div class="spacer"></div>

<p>Remplacez "adresse-ip" avec les adresses IP de toutes les instances mongod.exe. Cela vous permet d'autoriser tout le traffic entrant et sortant
entre les shards en incluant les membres de replica set vers :

- toutes les instances mongod.exe dans les replica sets du shard
- toutes les instances mongod.exe des autres shards (tous les shards d'un cluster doivent pouvoir communiquer avec les autres afin de faciliter la migration de chunks)

<p>Plus loin encore, les shards doivent pouvoir effectuer des connexions sortantes vers :

- toutes les instances mongos.exe
- toutes les instances mongod.exe des serveurs de configuration

Créez une règle qui ressemble à la suivante et remplacez "adresse-ip" avec les adresses IP des serveurs de configuration et des instances mongos.exe :</p>

<pre>netsh advfirewall firewall add rule name="Open mongod config svr outbound" dir=out action=allow protocol.............</pre>

<p>Founir l'accès pour les Systèmes de Monitoring :

1) L'outil de diagnostique mongostat exécuté avec l'option --discover doit pouvoir atteindre tous les composants du cluster, en incluant les serveurs de configuration
les serveurs shards et les instances mongos.exe.

2) Si votre système de monitoring a besoin d'accéder à l'interface HTTP, insérez cette règle suivante :</p>

<pre>netsh advfirewall firewall add rule name="Open mongod HTTP monitoring inbound" dir=in action=allow........</pre>

<div class="spacer"></div>

<p>Remplacez "adresse-ip" avec l'adresse IP de l'instance qui a besoin d'accéder à l'interface HTTP ou l'interface REST.
Pour tous les déploiements, vous devriez restreindre l'accès sur ce port à l'instance de monitoring uniquement.</p>

En opion, pour les instances mongod.exe de serveur shard (--shardsrv), la règle ressemblerait à :</p>

<pre>netsh advfirewall firewall add rule name="Open mongos HTTP monitoring inbound" dir=in action=allow......</pre>

<p>Pour les instances mongod.exe de serveur de configuration (--configsrv) la règle ressemblerait à :</p>

<pre>netsh advfirewall firewall add rule name="Open mongod configsvr HTTP monitoring inbound" dir=in....</pre>
<a name="confn"></a>

<div class="spacer"></div>

<p class="small-titre">c) Gérer et Maintenir les Configurations du Pare-feu Windows</p>

<p>Cette section comporte un nombre d'opérations basiques pour gérer et utiliser netsh. Pendant que vous pouvez utiliser des interfaces graphiques
pour gérer le firewall Windows, toutes les fonctionnalités principales sont accessibles depuis netsh.

Supprimer toutes les règles du pare-feu Windows : Pour supprimer toutes les règles qui autorisent le traffic de mongod.exe :</p>

<pre>
netsh advfirewall firewall delete rule name="Open mongod port 27017" protocol=tcp localport=27017
netsh advfirewall firewall delete rule name="Open mongod shard port 27018" protocol=tcp localport=27018
</pre>

<div class="spacer"></div>

<p>Lister toutes les règles du pare-feu :</p>

<pre>netsh advfirewall firewall show rule name=all</pre>

<p>Remettre à zéro le firewall :</p>

<pre>netsh advfirewall reset</pre>

<p>Sauvegarder et Restaurer les règles de pare-feu windows : Pour simplifier l'administration d'une collection de systèmes plus larges, vous pouvez exporter
ou importer les règles de firewall facilement sur Windows. Pour exporter les règles :</p>

<pre>netsh advfirewall export "C:\temp\MongoDBfw.wfw"</pre>

<p>Remplacez "C:\temp\MongoDBfw.wfw" avec le chemin de votre choix. Vous pouver utiliser une commande pour importer un fichier créé à partir de cette opération :</p>

<pre>netsh advfirewall import "C:\temp\MongoDBfw.wfw"</pre>
<a name="ssl"></a>

<div class="spacer"></div>

<p class="titre">III) [ Se Connecter à MongoDB avec SSL ]</p>

<p>Dans cette partie du tutoriel, nous allons apprendre à nous connecter à MongoDB avec SSL (Secure Socket Layer) qui est un protocole qui fournit plus
de sécurité. SSL va permettre aux clients de se connecter aux instances mongod de MongoDB via une connexion cryptée.</p>

<div class="alert alert-info">
	<u>Note</u> : La distribution par défaut de MongoDB ne supporte pas SSL. Pour utiliser SSL, vous devez soit exécuter MongoDB en local
	en passant l'option --ssl à scons ou utiliser MongoDB Enterprise.
</div>

<p>Ces instructions partent du principe que vous avez déjà installé une version de MongoDB qui supporte le SSL, et que votre driver le supporte également.</p>
<a name="confs"></a>

<div class="spacer"></div>

<p class="small-titre">a) Configurer mongod et mongos pour SSL</p>

<p>Combiner un certificat SSL et un fichier clé : Avant que vous puissiez utiliser SSL, vous devez un fichier .pem qui contient le clé publique (le certificat)
et la clé privée. MongoDB peut utiliser n'importe quel certificat SSL valide. Pour générer un certificat auto-signé et une clé privée, utiliser la commande :</p>

<pre>
cd /etc/ssl/
openssl req -new -x509 -days 365 -nodes -out mongodb-cert.crt -keyout mongodb-cert.key
</pre>

<p>Cette opération va générer un nouveau certificat auto-signé sans passphrase qui sera valide pendant 365 jours. Une fois que vous possédez le certificat,
concaténez le certificat et la clé privée à un fichier .pem comme l'indique l'exemple suivant :</p>

<pre>cat mongodb-cert.key mongodb-cert.crt > mongodb.pem</pre>

<p>Installer mongod et mongos avec un certificat SSL et une clé : Pour utiliser SSL avec votre déploiement MongoDB, ajoutez les options d'exécution à vos
instances mongod et mongos :

- sslOnNormalPorts
- sslPEMKeyFile avec le fichier .pem qui contient le certificat SSL et la clé privée.

Considérez la syntaxe suivante :</p>

<pre>mongod --sslOnNormalPorts --sslPEMKeyFile "pem"</pre>

<p>Par exemple, prenons un certificat SSL situé à /etc/ssl/mongodb.pem, configurez mongod afin qu'il utilise SSL pour toutes les connexions :</p>

<pre>mongod --sslOnNormalPorts --sslPEMKeyFile /etc/ssl/mongodb.pem</pre>

<div class="spacer"></div>

<p>- spécifiez "pem" avec le chemin complet de votre certificat
- si la clé privée de "pem" est cryptée, spécifiez le mot de passe de cryptage avec l'option sslPEMKeyPassword
- vous voudrez peut-être spécifier ces options dans un fichier de configuration commande dans l'exemple suivant :</p>

<pre>
sslOnNormalPorts = true
sslPEMKeyFile = /etc/ssl/mongodb.pem
</pre>

<p>Pour votre connecter aux instances mongod ou mongos avec SSL, le shell mongo et les outils MongoDB doivent inclure l'option --ssl.

Installer mongod et mongos avec la validation du certificat : Pour définir les mongod et mongos pour le cryptage SSL en utilisant un certificat signé par
une autorité, ajoutez les options d'exécution suivantes :

- sslOnNormalPorts
- sslPEMKeyFile avec le nom du fichier .pem qui contient le certificat et la clé privée.
- sslCAFile avec le nom du fichier .pem qui contient le certificat root depuis l'autorité.

Considérons la syntaxe suivante pour mongod :</p>

<pre>mongod --sslOnNormalPorts --sslPEMKeyFile "pem" --sslCAFile "ca"</pre>

<p>Par exemple, prenons un certificat SSL signé situé à /etc/ssl/mongodb.pem et le fichier de l'autorité dans /etc/ssl/ca.pem, vous pouvez configurer
mongod pour le cryptage SSL comme dans l'exemple suivant :</p>

<pre>mongod --sslOnNormalPorts --sslPEMKeyFile /etc/ssl/mongodb.pem --sslCAFile /etc/ssl/ca.pem</pre>

<div class="spacer"></div>

<p>- spécifiez "pem" et "ca" avec les chemins relatifs à ces fichiers.
- Si "pem" est crypté, spécifiez le mot de passe de cryptage avec l'option sslPEMKeyPassword
- Vous voudrez probablement créer un fichier de configuration :</p>

<pre>
sslOnNormalPorts = true
sslPEMKeyFile = /etc/ssl/mongodb.pem
sslCAFile = /etc/ssl/ca.pem
</pre>

<p>Pour vous connecter aux mongod ou mongos en utilisant SSL, le shell mongo et les outils MongoDB vont devoir inclure les options --ssl et --PEMKeyFile.

Bloquer les certificats périmés pour les Clients : Pour empêcher les clients ayant des certificats interdits de se connecter, ajoutez l'option sslCRLFile
pour spécifier un fichier .pem qui contient les certificats révoqués.

Par exemple, la configuration SSL pour le mongod suivant :</p>

<pre>mongod --sslOnNormalPorts --sslCRLFile /etc/ssl/ca-crl.pem --sslPEMKeyFile /etc/ssl/mongodb.pem --sslCAFile...........</pre>

<p>Les clients ayant un certificats révoqué faisant partis de la liste /etc/ssl/ca-crl.pem ne pourront pas se connecter à cette instance mongod.

Valider uniquement si un client présente le certificat : Dans la plupart des cas, il est important de s'assurer que les clients présentent un certificat valide.
En revanche, si vous avez des clients qui ne sont pas en mesure de présenter un certificat, ou utilisent une autorité de certificat, vous voudrez sûrement
valider ces certificats uniquement des clients qui peuvent en présenter un.

Si vous voulez bypasser l'autorisation pour les clients qui ne présentent pas de certificats, utilisez l'option d'exécution sslWeakCertificateValidation avec
votre mongod ou mongos. Si le client ne présente pas de certificat, aucune validation n'est effectuée. Ces connexions, même non validées, sont cryptées en
utilisant SSL.

Par exemple, considérongs le mongod suivant avec sa configuration SSL qui inclut cette dernière option :</p>

<pre>mongod --sslOnNormalPorts --sslWeakCertificateValidation --sslPEMKeyFile /etc/ssl/mongodb.pem --sslCAFile..........</pre>

<div class="spacer"></div>

<p>Donc, les clients peuvent se connecter soit avec l'option --ssl et sans certificat, ou avec l'option --ssl et un certificat valide.</p>

<div class="alert alert-info">
	<u>Note</u> : Si le client présente un certificat, le certificat doit être valide. Toutes les connexions, incluant celles qui
	n'ont pas présentées de de certificats, sont cryptées en utilisant SSL.
</div>

<p>Exécuter en mode FIPS : Si votre mongod ou mongos est exécuté sur un système utilisant une librairie OpenSSL configurée avec le module FIPS 140-2,
vous pouvez exécuter mongod ou mongos en mode FIPS avec le paramètre sslFIPSMode.</p>
<a name="cli"></a>

<div class="spacer"></div>

<p class="small-titre">b) Configuration SSL pour les Clients</p>

<p>Les clients doivent supporter le SSL pour pouvoir travailler avec les instances mongod et mongos qui ont SSL activé. Les versions actuelles des drivers
Python, Java, Ruby, Node.js, .NET et C++ incluent le support SSL. Le support total de SSL dans pour d'autres drivers est en cours et arrivera bientôt.

Configuration SSL pour mongo : Pour les connexions SSL, vous devez utiliser le shell mongo implémentant le support SSL ou avec MongoDB Enterprise.
Pour supporter le SSL, mongo a les paramètres suivants : 

- --ssl
- --sslPEMKeyFile : avec le fichier .pem qui comporte le certificat et la clé privée.
- --sslCAFile : avec le fichier qui comporte le certificat et l'autorité de certificat
- --sslPEMKeyPassword : si le fichier de clé de certificat du client est crypté.

Se connecter à une instances MongoDB avec le cryptage SSL : Pour se connecter à une instance mongod ou mongos qui nécessite seulement un mode de cryptage SSL,
exécutez un shell mongo avec l'option --ssl :</p>

<pre>mongo --ssl</pre>

<p>Se connecter à une instande MongoDB qui nécessite les certificats Client : Pour vous connecter à une instance mongod ou mongos qui nécessite
des certificats de clients signés par une autorité (CA), démarrez un shell mongo avec les options --ssl et --sslPEMKeyFile pour spécifier 
le fichier de clé de certificat signé :</p>

<pre>mongo --ssl --sslPEMKeyFile /etc/ssl/client.pem</pre>

<div class="spacer"></div>

<p>Se connecter à une instance MongoDB qui valide quand un certificat est présenté : pour vous connectr à une instance mongod ou mongos qui nécessite uniquement
les certificats valides lorsque le client présente un certificat, démarrez un shell mongo soit avec l'option --ssl et sans certificat, ou alors avec l'option --ssl
et un certificat signé valide.

Par exemple, si votre mongod est exécuté avec une validation faible de certificat, les deux shell clients vont pouvoir se connecter à ce mongod :</p>

<pre>
mongo --ssl
mongo --ssl --sslPEMKeyFile /etc/ssl/client.pem
</pre>

<div class="alert alert-danger">
	<u>Attention</u> : Si le client présente un certificat, le certificat doit être valide.
</div>

<div class="spacer"></div>

<p>L'agent de Monitoring MMS : L'agent de monitoring va aussi devoir se connecter par SSL dans le but de récupérer ses statistiques. Vu que l'agent utilise
déjà SSL pour ses communications avec le serveur MMS, il faut juste activer le support SSL dans MMS lui-même par hôte.

Utilisez le boutton "Edit" (exemple : représenté par un crayon) sur la page Hosts dans la console MMS poru activer le SSL.

PyMongo, le driver Python pour MongoDB, ajoutez le paramètre ssl="True" à la classe MongoClient de PyMongo pour créer une connexion MongoDB vers à une instance MongoDB
utilisant SSL :</p>

<pre>
from pymongo import MongoClient

c = MongoClient(host="mongodb.example.net", port=27017, ssl=True)
</pre>

<p>Pour vous connecter à un Replica Set :</p>

<pre>
from pymongo import MongoReplicaSetClient

c = MongoReplicaSetClient("mongodb.example.net:27017", 
	replicaSet="mysetname", ssl=True)
</pre>

<div class="spacer"></div>

<p>PyMongo supporte aussi l'option "ssl=true" pour l'URI MongoDB :</p>

<pre>mongodb://mongodb.example.net:27017/?ssl=true</pre>

<p>Java : considérons l'exemple suivant du fichier SSLapp.java :</p>

<pre>
import com.mongodb.*;
import javax.net.ssl.SSLSocketFactory;

public class SSLApp {
	public static void main(String args[]) throws Exception {
		MongoClientOptions o = new MongoClientOptions.Builder()
		.socketFactory(SSLSocketFactory.getDefault())
		.build();
		
		MongoClient m = new MongoClient("localhost", o);
		DB db = m.getDB( "test" );
		DBCollection c = db.getCollection( "foo" );
		
		System.out.println( c.findOne() );
	}
}
</pre> 

<div class="spacer"></div>

<p>Ruby : Les versions récentes du driver Ruby supportent les connexions vers les serveurs SSL. Installez la dernière version de Ruby avec cette commande :</p>

<pre>gem install mongo</pre>

<p>Ensuite, connectez-vous à une instance standalone :</p>

<pre>
require 'rubygems'
require 'mongo'

connection = MongoClient.new('localhost', 27017, :ssl => true)
</pre>

<p>Remplacez la variable connection par l'exemple suivant pour vous connecter à un Replica Set :</p>

<pre>
connection = MongoReplicaSetClient.new(['localhost:27017'],
	['localhost:27018'],
	:ssl => true)
</pre>

<div class="spacer"></div>

<p>Ici, l'instance mongod s'exécute sur "localhost:27017" et "localhost:27018".

Node.js (node-mongodb-native) : Dans le driver node-mongodb-native, utilisez la commande suivante pour vous connecter à une instance mongod ou mongos
via SSL :</p>

<pre>
var db1 = new Db(MONGODB, new Server("127.0.0.1", 27017,
	{ auto_reconnect: false, poolSize:4, ssl:ssl } );
</pre>

<p>Pour vous connecter à un Replica Set via SSL :</p>

<pre>
var replSet = new ReplSetServers(
	[
		new Server( RS.host, RS.ports[1], { auto_reconnect: true } ),
		new Server( RS.host, RS.ports[0], { auto_reconnect: true } ),
	],
	{rs_name:RS.name, ssl:ssl}
);
</pre>

<p>.NET : Depuis la version 1.6, le driver .NET supporte les connexions SSL avec les instances mongod et mongos. Pour vous connecter en utilisant SSL, vous devez
ajouter l'option au Connection String, en spécifiant ssl=true comme dans l'exemple suivant :</p>

<pre>
var connectionString = "mongodb://localhost/?ssl=true";
var server = MongoServer.Create(connectionString);
</pre>

<div class="spacer"></div>

<p>Le driver .NET va valider le certificat avec le stockage local de certificats connus, et va fournir le cryptage au serveur. Ce comportement devrait
produire des erreurs pendant des tests si le serveur utilise un certificat auto-signé. Si vous rencontre une erreur, ajoutez l'option sslverifycertificate=false
au Connection String pour empêcher le driver .NET de valider le certificat :</p>

<pre>
var connectionString = "mongodb://localhost/?ssl=true&sslverifycertificate=false";
var server = MongoServer.Create(connectionString);
</pre>

<div class="spacer"></div>

<p>Allez ! Assez discuté, maintenant passons au chapitre sur <a href="tutoriel_controle_acces.php">"Tutoriel de Gestion du Contrôle d'Accès" >></a>.</p>
	
<?php

	include("footer.php");

?>	
