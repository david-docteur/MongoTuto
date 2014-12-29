<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Tutoriel de Gestion du Contrôle d'Accès</li>
</ul>

<p class="titre">[ Tutoriel de Gestion du Contrôle d'Accès ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#acti">I) Activer l'Authentification</a></p>
	<p class="right"><a href="#proc">- a) Procédures</a></p>
	<p class="right"><a href="#inte">- b) Interroger les Utilisateurs Authentifiés</a></p>
	<p class="elem"><a href="#cree">II) Créer un Utilisateur Administrateur</a></p>
	<p class="right"><a href="#adm">- a) Créer un Administrateur</a></p>
	<p class="right"><a href="#iden">- b) S'identifier avec un Accès Administratif Total via localhost</a></p>
	<p class="elem"><a href="#ajou">III) Ajouter un Utilisateur à une Base de Données</a></p>
	<p class="elem"><a href="#chan">IV) Changer le Mot de Passe d'un Utilisateur</a></p>
	<p class="elem"><a href="#gene">V) Générer un Fichier Clé</a></p>
	<p class="right"><a href="#fcle">- a) Générer un Fichier Clé</a></p>
	<p class="right"><a href="#prop">- b) Propriétés du Fichier Clé</a></p>
	<p class="elem"><a href="#depl">VI) Déployer MongoDB avec l'Authentification Kerberos</a></p>
	<p class="right"><a href="#ve">- a) Vue d'Ensemble</a></p>
	<p class="right"><a href="#oper">- b) Opérations</a></p>
	<p class="right"><a href="#diag">- c) Diagnostique</a></p>
</div>

<p></p>
<a name="acti"></a>

<div class="spacer"></div>

<p class="titre">I) [ Activer l'Authentification ]</p>

<p>Vous pouvez activer l'authentification avec les paramètres auth ou keyFile. Utilisez auth pour les instances standalone et keyFile avec les Replica Sets
et les Sharded Clusters. keyFile implique auth  et autorise les membres d'un déploiement MongoDB à s'authentifier en interne.
L'authentification nécessite au moins un utilisateur administrateur dans la base de données "admin". Vous pouvez créer l'utilisateur avant ou après avoir activé
l'authentification.</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="small-titre">a) Procédures</p>
  
<p>Vous pouvez activer l'authentification en utilisant l'une de ces procédures :

Créer les identifiants administrateur et ensuite activer l'authentification :

1) Démarrez l'instance mongod ou mongos sans les paramètres auth ou keyFile
2) Créez l'utilisateur administrateur comme décrit un peu plus bas
3) Redémarrez l'instance mongod ou mongos avec le paramètre auth ou keyFile

Activer l'authentification et ensuite créer l'administateur :

1) Démarrez l'instance mongod ou mongos avec les paramètres auth ou keyFile
2) Connectez-vous à l'instance sur le même système de manière à ce que vous puissiez vous connecter en utilisant l'exception localhost
3) Créez l'utilisateur administrateur comme décrit un peu plus bas
<a name="inte"></a>

<div class="spacer"></div>

<p class="small-titre">b) Interroger les Utilisateurs Authentifiés</p>

<p>Si vous avez le role userAdmin ou userAdminAnyDatabase sur une base de données, vous pouvez interroger les utilisateurs authentifiés dans la base de données
avec l'opération suivante :</p>

<pre>db.system.users.find()</pre>
<a name="cree"></a>

<div class="spacer"></div>

<p class="titre">II) [ Créer un Utilisateur Administrateur ]</p>

<p>Dans un déploiement MongoDB, les utilisateurs ayant, soit le rôle userAdmin ou le rôle userAdminAnyDatabase sont des "super-utilisateurs" ayant des droits
administratifs. Les utilisateurs ayant l'un de ces rôles peut créer et modifier n'importe quel autre utilisateur et peuvent lui assigner des privilèges.
Il peut également s'attribuer des privilièges. Dans les déploiements de production, cet utilisateur ne devrait pas avoir d'autres rôles et devrait administrer
uniquement les utilisateurs et le privilèges.

Cela devrait être le premier utilisateur créé pour un déploiement MongoDB. Cet utilisateur peut ensuite créer tous les autres utilisateurs dans le système.

<div class="alert alert-danger">
	<u>Attention</u> : L'utilisateur ayant le rôle userAdminAnyDatabse peut s'attribuer à lui-même ainsi qu'à d'autres utilisateurs l'accès à l'instance
	MongoDB entière. Les identfiants de connexion pour cet utilisateurs devraient être contrôlés de façon minutieuse.
	Les utilisateurs avec les privilèges userAdmin ou userAdminAnyDatabase ne sont pas les mêmes que le super-utilisateur "root" pour les systèmes UNIX.
	Ces utilisateurs ne peuvent pas effectuer d'opérations administratives ou lire ou écrire des données sans s'accorder des permissions additionnelles.
</div>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Le rôle userAdmin est un privilège spécifique à une seule base de données, et autorise un utilisateur à seulement administrer les
	utilisateurs d'une seule et même base de données. En revanche, pour la base de données "admin", userAdmin permet à un utilisateur d'obtenir la permission
	userAdminAnyDatabase. Donc, pour la base de données "admin", ces rôles sont exactement les même.
</div>
<a name="adm"></a>

<div class="spacer"></div>

<p class="small-titre">a) Créer un Administrateur</p>

<p>1) Connectez-vous soit à votre mongod ou mongos :
- identifiez-vous avec un utilisateur existant ayant le privilège userAdmin ou userAdminAnyDatabase
- 	Lorsque vous créez le premier utilisateur dans un déploiement, vous devez vous identifier en utilisant l'exception localhost.
2) Choisissez la base de données "admin" :</p>

<pre>db = db.getSiblingDB('admin')</pre>

<p>3) Ajouter l'utilisateur avec l'un des deux rôles userAdmin ou userAdminAnyDatabase comme dans l'exemple suivant en remplaçant bien évidement les valeur
de "username" et "password" par les vôtres :</p>

<pre>
db.addUser(
	{ 
		user: "username",
		pwd: "password",
		roles: [ "userAdminAnyDatabase" ] 
	}
)
</pre>

<p>Pour vous identifier avec cet utilisateur, vous devez le faire sur la base de données "admin"</p>
<a name="iden"></a>

<div class="spacer"></div>

<p class="small-titre">b) S'identifier avec un Accès Administratif Total via localhost</p>

<p>S'il n'y a pas d'utilisateurs pour la base de données "admin", vous pouvez vous connecter avec un accès adminisratif total via l'interface localhost.
Ce bypass existe pour l'amorçage de nouveaux déploiements. Cette approche est très utile, par exemple, si vous souhaitez exécuter un mongod ou mongos
avec l'authentification avant de créer le premier utilisateur.

Pour vous identifier via localhost, connectez-vous à l'instance mongod ou mongos depuis un client exécuté sur le même système. Votre connexion un accès
administratif total.
Pour désactiver le bypass localhost, définissez le paramètre enableLocalhostAuthBypass avec l'option setParameter au démarrage :</p>

<pre>mongod --setParameter enableLocalhostAuthBypass=0</pre>

<div class="alert alert-info">
	<u>Note</u> : Pour les versions antérieures à MongoDB 2.2 avant la 2.2.4, si l'instance mongos est exécutée avec l'option keyFile, alors
	tous les utilisateurs se connectant avec l'interface localhost doivent s'identifier, même s'il n'y a aucun utlilisateur définit dans la base de données
	"admin". Les connexions via localhost ne garantissent pas un accès complet sur les systèmes shardés qui implémentent ces versions.
	MongoDB résoud ce problème à partir de la version 2.2.4.
</div>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Dans la version 2.2, vous ne pouvez pas ajouter le premier utilisateur à un sharded cluster en utilisant l'intrface localhost.
	Si vous exécutez un sharded cluster avec la version 2.2 et que vous voulez activer l'authentification, vous devez déployer le cluster et ajouter
	le premier utilisateur à la base de données "admin" avant de redémarrer le cluster exécuté avec le paramètre keyFile.
</div>
<a name="ajou"></a>

<div class="spacer"></div>

<p class="titre">III) [ Ajouter un Utilisateur à une Base de Données ]</p>

<p>Pour ajouter un utilisateur à une base de données, vous devez vous identifier sur cette base de données avec un utilisateur ayant le rôle userAdmin
ou userAdminAnyDatabase. Si vous n'avez pas déjà créé d'utilisateur ayant l'un de ces rôles, revenez à l'étape précédente sur comment créer un administrateur.

Lorsque vous ajoutez un utilisateur à de multiples bases de données, vous devez définir l'utilisateur pour chaque base de données.
Pour ajouter un utilisateur, utilisez la méthode db.addUser() ayant pour paramètre un document qui contient les identifiants et les pribilèges de l'utilisateur.
La méthode db.addUser() ajoute le document à la collection "system.users".

Dans les versions antérieures à la version 2.4 de MongoDB, vous pouviez changer le mot de passe d'un utilisateur existant en invoquant cette méthode db.addUser()
avec le nom d'utilisateur et le nouveau mot de passe. N'importe quelle information spécifiée dans la méthode db.addUser() aurait écrit par dessus les informations
existantes pour cet utilisateur. Dans les nouvelles versions de MongoDB, cette opération retournerait une erreur de type clé dupliquée.
Pour changer le mot de passe d'un utilisateur proprement, passez au paragraphe suivant.

Par exemple, la commande suivante va créer un utilisateur nommé Alice dans la base de données products et lui donne les privilèges readWrite et dbAdmin.</p>

<pre>
use products
db.addUser(
	{
		user: "Alice",
		pwd: "Moon1234",
		roles: [ "readWrite", "dbAdmin" ]
	}
)
</pre>

<div class="spacer"></div>

<p>L'exemple suivant va créer un utilisateur nommé Bob dans la base de données "admin". Le document privilèges utilise les identifiants de Bob de la base
de donnés products et lui assigne le droit userAdmin :</p>

<pre>
use admin
db.addUser( 
	{ 
		user: "Bob",
		userSource: "products",
		roles: [ "userAdmin" ]
	}
)
</pre>

<p>L'exemple suivant va créer un utilisateur nommé Carlos dans la base de données "admin" et lui donne le droit readWrite à la base de données "config",
ce qui lui permet de changer certains paramètres pour les clusters partagés, comme désactivr le balanceur par exemple :</p>

<pre>
db = db.getSiblingDB('admin')
db.addUser(
	{
		user: "Carlos",
		pwd: "Moon1234",
		roles: [ "clusterAdmin" ],
		otherDBRoles: {
			config: [ "readWrite" ]
		}
	}
)
</pre>

<p>Seulement la base de données supporte le champ otherDBRoles.</p>
<a name="chan"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Changer le Mot de Passe d'un Utilisateur ]</p>

<p>Depuis la version 2.4, vous devez avoir le privilège userAdmin sur la même base de données ou se trouve l'utilisateur dont vous souhaitez modifier le mot de passe.
Pour mettre à jour son mot de passe, passez en paramètre le nom d'utilisateur et son nouveau mot de passe à la méthode db.changeUserPassword().
Par exemple, l'opération suivante change le mot de passe de l'utilisateur reporting en "SOhSS3TbYhxusooLiW8ypJPxmt1oOfL" :</p>

<pre>
db = db.getSiblingDB('records')
db.changeUserPassword("reporting", "SOhSS3TbYhxusooLiW8ypJPxmt1oOfL")
</pre>

<div class="alert alert-info">
	<u>Note</u> : Comme expliqué un peut plus haut, dans les anciennes version de MongoDB, vous deviez utiliser la méthod db.addUser() pour modifier
	le mot de passe d'un utilisateur.
</div>
<a name="gene"></a>

<div class="spacer"></div>

<p class="titre">V) [ Générer un Fichier Clé ]</p>

<p>Ici, nous allons voir comment générer un fichier clé pour stocker les informations d'authentification. Après avoir généré un fichier clé, spécifiez ce fichier clé
en utilisant l'option keyFile lorsque vous démarrez une instance mongod ou mongos.
La taille d'une clé doit être comprise entre 6 et 1024 caractères et doit contenir seulement des caractères appartenant à l'ensemble base64. Le fichier clé
ne doit pas avoir de permissions de type group ou world sur les systèmes UNIX. Les permissions du fichier clé ne sont pas vérifiées sur les systèmes Windows.</p>
<a name="fcle"></a>

<div class="spacer"></div>

<p class="small-titre">a) Générer un Fichier Clé</p>

<p>Utilisez la commande openssl suivante sur un shell système pour générer un contenu pseudo-aléatoire pour votre fichier clé :</p>

<pre>openssl rand -base64 741</pre>

<div class="alert alert-info">
	<u>Note</u> : Les permissions sur le fichier clé ne sont pas vérifiées sur les systèmes Windows.
</div>
<a name="prop"></a>

<div class="spacer"></div>

<p class="small-titre">b) Propriétés du Fichier Clé</p>

<p>Vous devez savoir que MongoDB supprime les espaces (par exemple x0d, x09 et x20) pour des raisons d'interopérabilité. En sachant cela, les opérations
suivantes produisent les mêmes clés :</p>

<pre>
echo -e "my secret key" > key1
echo -e "my secret key\n" > key2
echo -e "my secret key" > key3
echo -e "my\r\nsecret\r\nkey\r\n" > key4
</pre>
<a name="depl"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Déployer MongoDB avec l'Authentification Kerberos ]</p>

<p>Depuis la version 2.4, MongoDB Enterprise supporte l'authentification en utilisant le service Kerberos. Kerberos est en fait un protocole d'authentification
standart pour les systèmes client/serveur largement étendus. Avec Kerberos, MongoDB et les applications peuvent prendre l'avantage de processus et d'une infrastructure
existants pour l'authentification. Vous allez apprendre dans ce tutoriel comment configurer et déployer Kerberos. Pour utiliser MongoDB avec Kerberos,
vous devez avoir un déploiement Kerberos correctement configuré et la possibilité de générer un fichier keytab pour chaque instance mongod faisant partie
de votre déploiement MongoDB.</p>

<div class="alert alert-info">
	<u>Note</u> : La partie de ce chapitre qui va suivre va considérer que vous avez un fichier keytab Kerberos valide pour votre système.
	L'exemple suivant par du principe que le fichier keytab est valide et situé /opt/mongodb/mongod.keytab et est accessible uniquement par l'utilisateur
	qui exécute le processus mongod.
</div>
<a name="ve"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vue d'Ensemble</p>

<p>Pour exécuter MongoDB en supportant Kerberos, vous devez :

- Configurer un service Kerberos principal pour chaque instance mongod et mongos dans votre déploiement MongoDB.
- Générer et distribuer les fichiers keytab pour chaque composant MongoDB (mongod et mongos) dans votre déploiement. Assurez-vous de transmettre les fichiers
  keytab uniquement par un réseau/canal sécurisé.
- Optionel : Démarrez l'instance mongod sans l'option auth et créez les utilisateurs dans MongoDB que vous pourrez utiliser pour amorçer votre déploiement.
- Démarrez les instances mongod et mongos avec la variabl d'environnement KRB5_KTNAME ainsi que les autres options d'exécution requises.
- Si vous n'avez pas créé de comptes utilisateur Kerberos, vous pouvez utiliser l'exception localhost pour créer des utilisateurs jusqu'à ce que vous créez
  le premier utilisateur dans la base de données "admin".
- Identifiez les clients, en incluant le shell mongo, en utilisant Kerberos.</p>
<a name="oper"></a>

<div class="spacer"></div>

<p class="small-titre">b) Opérations</p>

<p>Créer des utilisateurs et les documents privilèges : Pour chaque utilisateur que vous voudrez identifier en utilisant Kerberos, vous devez créer des documents
privilèges correspondants dans la collection system.users. Considérons le document suivant :</p>

<pre>
{
	user: "application/reporting@EXAMPLE.NET",
	roles: ["read"],
	userSource: "$external"
}
</pre>

<div class="spacer"></div>

<p>Cela permet à l'utilisateur Kerberos principal "application/reporting@EXAMPLE.NET la lecture seule sur une base de données. La référence userSource : "$external"
autorise mongod à consulter des sources externes (par exemple, Kerberos) pour identifier cet utilisateur.

Dans le shell mongo, vous pouvez passer un document de privilège utilisateur à la méthode db.addUser() pour attribuer les droits nécessaires à cet utilisateur comme dans
l'exemple suivant :</p>

<pre>
db = db.getSiblingDB("records")
db.addUser(
	{
		"user": "application/reporting@EXAMPLE.NET",
		"roles": [ "read" ],
		"userSource": "$external"
	}
)
</pre>

<p>Ces opérations autorisent l'utilisateur Kerberos "application/reporting@EXAMPLE.NET" à accéder à la base de données "records".
Pour supprimer l'accès à un utilisateur, utilisez la méthode remove() :</p>

<pre>db.system.users.remove( { user: "application/reporting@EXAMPLE.NET" } )</pre>

<p>Pour modifier un document utilisateur, utilisez l'opération update sur les documents de la collection system.users.

Démarrer un mongod avec le support Kerberos : Une fois que vous avez attribué les pribilèges aux utilisateurs correspondants dans le mongod, et obtenu un fichier
keytab valide, vous devez démarrer mongod en utilisant la variable d'environnement suivante :</p>

<pre>env KRB5_KTNAME="path to keytab file" "mongod invocation"</pre>

<div class="spacer"></div>

<p>Pour que tout se déroule bien avec votre instance mongod, utilisez les opétions d'exécution en plus de vos options habituelles :

- --setParameter avec l'argument authenticationMechanisms=GSSAPI pour activer le support de Kerberos
- --auth pour activer l'identification
- --keyFile pour permettre aux composants d'un seul déploiement MongoDB à communiquer avec les autres, si besoin pour supporter les opérations
  d'un replica set ou d'un sharded cluster. keyFile implique auth.
  
Par exemple :</p>

<pre>
env KRB5_KTNAME=/opt/mongodb/mongod.keytab \
/opt/mongodb/bin/mongod --dbpath /opt/mongodb/data \
--fork --logpath /opt/mongodb/log/mongod.log \
--auth --setParameter authenticationMechanisms=GSSAPI
</pre>

<p>vous pouvez aussi spécifier ces options en utilisant un fichier de configuration :</p>

<pre>
# /opt/mongodb/mongod.conf, Example configuration file.
fork = true
auth = true
dbpath = /opt/mongodb/data
logpath = /opt/mongodb/log/mongod.log
setParameter = authenticationMechanisms=GSSAPI
</pre>

<div class="spacer"></div>

<p>Pour utiliser ce fichier de configuration, démarrez mongod comme suivant :</p>

<pre>
env KRB5_KTNAME=/opt/mongodb/mongod.keytab \
/opt/mongodb/bin/mongod --config /opt/mongodb/mongod.conf
</pre>

<p>Pour démarrer une instance mongos avec Kerberos, vous devez créer un service principal Kerberos et déployer un fichier keytab pour cette instance.
Ensuite, vous devez démarrer l'instance mongos :</p>

<pre>
env KRB5_KTNAME=/opt/mongodb/mongos.keytab \
/opt/mongodb/bin/mongos
--configdb shard0.example.net,shard1.example.net,shard2.example.net \
--setParameter authenticationMechanisms=GSSAPI \
--keyFile /opt/mongodb/mongos.keyfile
</pre>

<div class="alert alert-success">
	<u>Astuce</u> : Si vous avez installé MongoDB Enterprise en utilisant l'un des packages officiel .deb ou .rpm et que ceux-ci contrôlent l'instance mongod
	en utilisant les scripts init/upstart inclus, vous pouvez définir la variable KR5_KTNAME dans le fichier d'environnement par défaut. Pour les packages
	.rpm, ce fichier est situé dans /etc/sysconfig/mongod. Pour les packages .deb, celui-ci est situé dans /etc/default/mongodb. Définissez sa valeur avec une ligne
	qui ressemble à export KRB5_KTNAME="setting".
</div>

<div class="spacer"></div>

<p>Si vous rencontrez des problèmes en essayant de démarrer votre mongod ou votre mongos, passez à la dernière page du tutoriel sur les Diagnostiques de Sécurité.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Avant que les utilisateurs puissent se connecter à MongoDB en utilisant Kerberos, vous devez créer des utilisateurs et leur
	attribuer des privilèges à l'intérieur de MongoDB. Si vous n'avez pas créé d'utilisateurs lorsque que vous démarrez MongoDB avec Kerberos, vous pouvez
	utiliser l'exception localhost pour ajouter des des utilisateurs.
</div>

<div class="spacer"></div>

<p>Identifier le shell mongo avec Kerberos : Pour se connecter à une instance mongod en utilisant le shell mongo, vous devez commencer par utiliser le programme
kinit pour initialiser et identifier une session Kerberos. Ensuite, démarrez une instance mongo et utilisez la méthode db.auth() pour vous identifier
sur la base de données spéciale $external :</p>

<pre>
use $external
db.auth( { mechanism: "GSSAPI", user: "application/reporting@EXAMPLE.NET" } )
</pre>

<p>D'une autre façon, vous pouvez vous identifier en utilisant l'option en ligne de commande avec mongo :</p>

<pre>
mongo --authenticationMechanism=GSSAPI
--authenticationDatabase='$external' \
--username application/reporting@EXAMPLE.NET
</pre>

<div class="spacer"></div>

<p>Ces opérations identifient l'utilisateur principal application/reporting@EXAMPLE.NET sur le mongod connecté et va automatique trouver les privilèges
nécessaires.

Utiliser les drivers MongoDB pour l'identification avec Kerberos : Les drivers C++, Java, C# et Python fournissent tous les support pour l'identification
avec Kerberos pour MongoDB. Considérez les trois tutoriaux suivants :

- <a href="http://docs.mongodb.org/ecosystem/tutorial/authenticate-with-java-driver/" target="_blank">S'identifier sur MongoDB avec Kerberos avec le driver Java</a>
- <a href="http://docs.mongodb.org/ecosystem/tutorial/authenticate-with-csharp-driver/" target="_blank">S'identifier sur MongoDB avec Kerberos avec le driver C#</a>
- <a href="http://docs.mongodb.org/ecosystem/tutorial/authenticate-with-cpp-driver/" target="_blank">S'identifier sur MongoDB avec Kerberos avec le driver C++</a>
- <a href="http://api.mongodb.org/python/current/examples/authentication.html" target="_blank">S'identifier sur MongoDB avec Kerberos avec le driver Python</a>

Une dernière chose, Kerberos ne fonctionne pas pour la console HTTP.</p>
<a name="diag"></a>

<div class="spacer"></div>

<p class="small-titre">c) Diagnostique</p>

<p>Le checklist de configuration de Kerberos :si vous rencontrez des problèmes en démarrant mongod avec Kerberos, il y a un nombre d'erreurs connues qui empêchent
le démarrage de mongod avec Kerberos. Assurez-vous que :

- Le mongod est de la version MongoDB Enterprise
- Vous n'utilisez pas la console HTTP. MongoBB Enterprise ne supporte pas l'identification Kerberos pour l'interface de la console HTTP.
- Vous avez un fichier keytab valide spécifié dans l'environnement exécutant le mongod. Pour une instances mongod exécutée sur l'hôte db0.example.net,
  le service principal devrait être mongodb/db0.example.net.
- Les DNS autorise mongod à résoudre les composants de l'infrastructure Kerberos. Vous devriez avoir les enregistrements A et PTR (forward et reverse DNS) pour le système
  exécutant cette instance mongod.
- Le nom d'hôte du système qui exécute l'instance mongod est le domaine résolvable de cet hôte. Testez la résolution de nom d'hôte avec la commande
  hostname -f.
- Kerberos KDC et l'instance mongod exécutée doivent être capable de résoudre leur DNS respectifs.
- Les horloges des systèmes exécutant les instances mongod ainsi que l'infrastructure Kerberos sont sunchronisées. Les différences d'heures de plus de 5 minutes
  vont poser problèmes lors du démarrage du mongod.
  
Si vous rencontrez encore des problèmes avec Kerberos, vous pouvez démarrez mongod et mongo (ou un autre client) avec la variable d'environnement
KRB5_TRACE définie pour que les différents fichiers produisent plus d'informations sur l'identification (mode verbose) avec Kerberos pour plus de diagnostique :</p>

<pre>
env KRB5_KTNAME=/opt/mongodb/mongod.keytab \
KRB5_TRACE=/opt/mongodb/log/mongodb-kerberos.log \
/opt/mongodb/bin/mongod --dbpath /opt/mongodb/data \
--fork --logpath /opt/mongodb/log/mongod.log \
--auth --setParameter authenticationMechanisms=GSSAPI
</pre>

<div class="spacer"></div>

<p>Messages d'erreurs courants : Dans certaines situations, MongoDB va retourner des messages d'erreurs depuis l'interface GSSAPI si il y a un problème avec
le service Kerberos.</p>

<pre>GSSAPI error in client while negotiating security context.</pre>

<p>Cette erreur arrive sur le client et reflette des identifiants insuffisants ou alors une tentative frauduleuse d'accès.
Si vous recevez cette erreur, assurez-vous que vous utilisez les bons identifiants ainsi que le bon DNS lorsque vous vous connectez à l'hôte.</p>

<pre>GSSAPI error acquiring credentials.</pre>

<p>Cette erreur apparaît uniquement lorsque vous tentez de démarrer mongod ou mongos et reflette une mauvaise configuration du nom d'hôte du système
ou alors un fichier keytab manquant ou incorrecte. Si vous rencontrez ce problème, revoyez la checklist de Kerberos entière un peu plus haut, en particulier :

- examinez le fichier keytab avec la commande suivante :</p>

<div class="spacer"></div>

<pre>klist -k "keytab"</pre>

<p>Remplacez "keytab" avec le chemin entier du votre.
Vérifier le nom d'hôte configuré de votre système :</p>

<pre>hostname -f</pre>

<p>Assurez-vous que nom corresponde au nom indiqué dans le fichier keytab, ou utilisez l'option saslHostName pour passer le nom d'hôte correcte à MongoDB.


Activer le mécanisme d'identification traditionnelle avec MongoDB : Pour un environnement de test ou de développement, vous pouvez activer les deux, 
Kerberos (GSSAPI) et la méthode d'identificaiton traditionnelle de MongoDB (MONGODB-CR) en utilisant l'option d'exécution setParameter comme ceci :</p>

<pre>mongod --setParameter authenticationMechanisms=GSSAPI,MONGODB-CR</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Toute identification interne avec un keyFile, entre les membres d'un Replica Set ou d'un Sharded Cluster, utilise
	encore le mécanisme MONGODB-CR pour l'identification, même si MONGODB-CR n'est pas activé. Touts les identifications des clients vont utiliser Kerberos.
</div>

<div class="spacer"></div>

<p>Allez ! Assez discuté, maintenant passons au chapitre sur <a href="rapport_vulnerabilite.php">"Créer un Rapport de Vulnérabilité" >></a>.</p>
	
<?php

	include("footer.php");

?>