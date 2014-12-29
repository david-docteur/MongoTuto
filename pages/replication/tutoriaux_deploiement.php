<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Déploiement de Replica Set</li>
</ul>

<p class="titre">[ Déploiement de Replica Set ]</p> 

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#depl">I) Déployer un Replica Set</a></p>
	<p class="right"><a href="#dpr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#dp">- b) Procédure</a></p>
	<p class="elem"><a href="#td">II) Déployer un Replica Set de Test ou de Développement</a></p>
	<p class="right"><a href="#tdpr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#tdp">- b) Procédure</a></p>
	<p class="elem"><a href="#geo">III) Déployer un Replica Set Géographiquement Redondant</a></p>
	<p class="right"><a href="#geopr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#geop">- b) Procédure</a></p>
	<p class="right"><a href="#trois">- c) Replica Set Géographique Redondant à 3 Membres</a></p>
	<p class="right"><a href="#quatr">- d) Replica Set Géographique Redondant à 4 Membres</a></p>
	<p class="right"><a href="#plus">- e) Replica Set Géographique Redondant à Plus de 4 Membres</a></p>
	<p class="elem"><a href="#ajar">IV) Ajouter un Arbitre à un Replica Set</a></p>
	<p class="elem"><a href="#conv">V) Convertir un Standalone en un Replica Set</a></p>
	<p class="right"><a href="#convp">- a) Procédure</a></p>
	<p class="right"><a href="#conve">- b) Etendre le Replica Set</a></p>
	<p class="right"><a href="#cons">- c) Considérations Pour le Sharding</a></p>
	<p class="elem"><a href="#ajmbr">VI) Ajouter des Membres à un Replica Set</a></p>
	<p class="right"><a href="#ajmpr">- a) Pré-Requis</a></p>
	<p class="right"><a href="#ajmp">- b) Procédure</a></p>
	<p class="elem"><a href="#suppm">VII) Supprimer des Membres d'un Replica Set</a></p>
	<p class="right"><a href="#suppmr">- a) Supprimer un Membre en utilisant rs.remove()</a></p>
	<p class="right"><a href="#suppmc">- b) Supprimer un Membre en utilisant rs.reconfig()</a></p>
	<p class="elem"><a href="#remp">VIII) Remplacer un Membre du Replica Set</a></p>
</div>

<p>Bienvenue sur la page de <b>déploiement de replica sets</b> ! Ici encore, <b>un gros bloc à lire</b> mais pour avoir un ensemble de répliques fonctionnant
<b>parfaitement</b>, il est essentiel d'avancer au fur et à mesure <b>des différentes étapes</b>. De plus, vous avez <b>plusieurs types de déploiements</b> expliqués ici, donc
n'hésitez pas à aller <b>dans le vif du sujet</b>. Encore une fois, <b>je reste à votre disposition en cas de questions</b>.</p>
<a name="depl"></a>

<div class="spacer"></div>

<p class="titre">I) [ Déployer un Replica Set ]</p>

<p>Ce tutoriel va décrire <b>comment créer un replica set</b> à trois membres depuis 3 instances mongod existantes.
Si vous souhaitez déployer un replica set depuis une seule instance MongoDB, jettez un oeil au paragraphe sur la <b>conversion d'un standalone en un replica set</b>.</p>

<p>Les replica sets à trois membres <b>fournissent assez de redondance</b> des données pour survivre à de <b>multiples pannes</b>. Ces ensembles ont aussi assez de capacité
pour de <b>multiples opérations de lecture distribuées</b>. Les replica sets doivent toujours avoir un <b>nombre impair de membres</b>, ce qui assure une élection efficace.
La procédure de base est de <b>démarrer les instances mongod</b> qui vont devenir des membres de l'ensemble, <b>configurer l'ensemble lui-même</b> et ensuite <b>ajouter les instances
créées</b> à celui-ci.</p>
<a name="dpr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-Requis</p>

<p>Pour des déploiements de <b>production</b>, vous devez maintenant instaurer autant de séparation que possible entre les membres en <b>hébergeant chaque instance mongod sur
des machines séparées</b>. Quand vous utilisez des machines virtuelles pour la production, vous devriez placer chaque instance mongod <b>sur un serveur séparé</b>
définit par des chemins réseaux redondants (si un routeur est H.S, un autre prend le relais avec son chemin réseau) et une alimentation redondante.
Avant que vous puissiez déployer un replica set, vous devez installer MongoDB sur chaque système qui fera partie de votre replica set.
Si vous n'avez pas déjà installé MongoDB, dirigez-vous au chapitre sur l'<a href="../installation.php">Installation de MongoDB</a>.
Ensuite, avant de créer votre replica set, vous devriez vérifier que votre configuration réseau <b>autorise toutes les connexions entre chaque membres</b>.
Car, afin d'obtenir une réplication efficace, chaque membre doit être capable de se connecter à tous les autres. Nous détaillerons comment <b>tester la connexion</b>
entre chaque membre un peu plus tard.</p>
<a name="dp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>Chaque membre du replica set réside sur sa propre machine et tous les processus MongoDB sont <b>associés au port 27017</b> (le port par défaut).
Ensuite, chaque membre du replica set doit être accessible via un DNS ou nom d'hôte comme dans l'exemple suivant :</p>

<pre>
_ mongodb0.exemple.fr
– mongodb1.exemple.fr
– mongodb2.exemple.fr
– mongodbn.exemple.fr
</pre>

<div class="spacer"></div>

<p>Vous aurez besoin de soit vos DNS, ou alors de définir votre <b>fichier host "/etc/hosts" (Linux)</b> ou <b>"Windows/System32/Drivers/hosts" (Windows)</b>
afin de refletter cette configuration.
Assurez-vous ensuite que le traffic sur le réseau puisse passer entre les membres du réseau de manière <b>efficace</b> et <b>sécurisée</b>.</p>
<p>- Etablissez un <b>VPN</b>. Assurez-vous que votre topologie réseau route tous les traffics entre les membres à l'intérieur d'un seul site LAN.</p>
<p>- Configurez l'authentification utilisant <b>auth</b> et <b>keyFile</b> de manière à ce que les serveurs et processus authentifiés uniquement ne puissent rejoindre le
replica set.</p>
<p>- Configurez les règles de pare-feu de manière à ce que seulement le traffic (packets entrants et sortants) sur le port MongoDB (27017 par défaut) soit autorisé
au sein de votre déploiement.</p>

<p>Vous devez spécifier la configuration d'exécution sur <b>chaque système</b> dans un fichier de configuration stocké dans <b>"/etc/mongodb.conf"</b> ou dossier relaté.
Ne spécifiez pas la configuration de l'ensemble via le shell mongo.
Utilisez la configuration suivante pour chaque instance MongoDB que vous avez en définissant les valeurs propres à votre système :</p>

<div class="small-spacer"></div>

<pre>
port = 27017
bind_ip = 10.8.0.10
dbpath = /srv/mongodb/
fork = true
replSet = rs0
</pre>

<div class="spacer"></div>

<p>Le <b>"dbpath"</b> indique ou vous souhaitez stocker vos données et doit impérativement exister avant de démarrer votre instance. Si celui-ci n'existe pas, créez-le
et assurez-vous que MongoDB a la permission de lire et d'écrire sur ce dossier.
Modifier <b>"bind_ip"</b> vérifie que mongod va seulement écouter les connexions des applications sur cette adresse spécifiée.</p>

<p>Pour déployer un replica set de production :

1) <b>Démarrez</b> une instance mongod sur chaque machine qui fera partie de votre replica set. Spécifier le même nom de replica set sur chaque instance.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Si votre application se connecte à plus d'un seul replica set, chaque ensemble doit avoir un nom unique.
	Certains drivers regroupent les connexions de replica sets par noms de replica sets.
</div>

<div class="spacer"></div>

<p>Si vous utilisez un fichier de configuration, démarrez chaque instance mongod avec une commande ressemblant à la suivante :</p>

<pre>mongod --config /etc/mongodb.conf</pre>

<div class="spacer"></div>

<p>Bien sûr, changez le chemin en fonction de votre système.
2) Ouvrez un shell mongo connecté à l'un des hôtes :</p>

<pre>mongo</pre>

<div class="spacer"></div>

<p>3) Utilisez <b>rs.initiate()</b> afin d'initialiser un replica set
4) Affichez la configuration actuelle du replica set</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>ce qui va ressembler à :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 4,
	"members" : [
		{
			"_id" : 1,
			"host" : "mongodb0.exemple.fr:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>1) Dans le shell mongo connecté au <b>membre primaire</b>, ajoutez les membres restants utilisant la commande <b>rs.add()</b> au membre primaire (ici mongodb0.example.net).
Les commandes devraient ressembler à :</p>

<pre>
rs.add("mongodb1.exemple.fr")
rs.add("mongodb2.exemple.fr")
</pre>

<div class="spacer"></div>

<p>Une fois cela complété, vous devriez avoir un replica set fonctionnel ! Le nouvel ensemble va donc élire un primaire. Si vous désirez
vérifier l'état de votre ensemble, utilisez la commande <b>rs.status()</b>.</p>
<a name="td"></a>

<div class="spacer"></div>

<p class="titre">II) [ Déployer un Replica Set de Test ou de Développement ]</p>

<p>Les replica sets à 3 membres fournissent assez de redondance des données pour survivre à de multiples pannes. Ces ensembles ont aussi assez de capacité
pour de multiples opérations de lecture distribuées. Les replica sets doivent toujours avoir un <b>nombre impair</b> de membres, ce qui assure une élection efficace.
La procédure de base est de démarrer les instances mongod qui vont devenir des membres de l'ensemble, configurer l'ensemble lui-même et ensuite ajouter les instances
créées à celui-ci.</p>
<a name="tdpr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-requis</p>

<p>Pour du déploiement <b>de test ou de développement</b>, vous pouvez exécuter vos instances mongod sur votre machine en <b>local</b> ou alors sous une machine
<b>virtuelle</b>. Avant que vous puissiez déployer un replica set, vous devez installer MongoDB sur chaque système qui fera partie de votre replica set.
Si vous n'avez pas déjà installé MongoDB, référez-vous au chapitre sur l'<a href="../installation.php">Installation de MongoDB</a>.
Ensuite, avant de créer votre replica set, vous devriez vérifier que votre configuration réseau <b>autorise toutes les connexions entre chaque membres</b>.
Car, afin d'obtenir une réplication efficace, chaque membre doit être capable de se connecter à <b>tous les autres</b>. Nous détaillerons comment tester la connexion
entre chaque membre un peu plus tard.</p>
<a name="tdp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<div class="alert alert-danger">
	<u>Attention</u> : Les instructions suivantes ne sont à utiliser uniquement pour les déploiements de test et/ou de développement.
</div>

<div class="spacer"></div>

<p>Dans cet exemple, nous avons un replica set nommé <b>rs0</b>. Vous allez donc démarrer les 3 instances mongod en tant que membres du replica set rs0.

1) Créez les dossiers de données pour chaque membre en effectuant cette commande :</p>

<pre>mkdir -p /srv/mongodb/rs0-0 /srv/mongodb/rs0-1 /srv/mongodb/rs0-2</pre>

<div class="spacer"></div>

<p>Cela va créer les dossiers <b>rs0-0, rs0-1 et rs0-2</b> qui vont contenir les fichiers de données pour vos membres.
2) Démarrez vos membres en exécutant les instances mongod dans son propre shell/invite de commande :</p>

<pre>
// Premier Membre
mongod --port 27017 --dbpath /srv/mongodb/rs0-0 --replSet rs0 --smallfiles --oplogSize 128

// Deuxième Membre
mongod --port 27018 --dbpath /srv/mongodb/rs0-0 --replSet rs0 --smallfiles --oplogSize 128

// Troisième Membre
mongod --port 27019 --dbpath /srv/mongodb/rs0-0 --replSet rs0 --smallfiles --oplogSize 128
</pre>

<div class="spacer"></div>

<p>Cela va démarrer chaque instance du Replica Set <b>rs0</b>, chacune étant associée à un port différent et spécifiant le dossier de données approprié
que nous avons définit juste avant avec l'option <b>"--dbpath"</b>. Si vous utilisez déjà ces ports, choisissez-en d'autres.
Les options <b>"--smallfiles"</b> et <b>"--oplogSize"</b> réduisent l'espace disque que doivent utiliser les instances. Cette configuration est idéale pour un environnement
de test ou de déploiement car il ne surcharge pas la machine. Si vous souhaitez plus de détails sur cette configuration ou d'autres, je vous invite à consulter
cette page : <a href="http://docs.mongodb.org/manual/reference/configuration-options" target="_blank">http://docs.mongodb.org/manual/reference/configuration-options</a>.</p>

<div class="small-spacer"></div>

<p></p>3) Connectez-vous à l'une de vos instances avec un shell/invite de commande mongo. Vous allez devoir indiquer une instance en choisissant le port que vous
désirez, et pour des raisons de simplicité, vous pourrez choisir la première en utilisant <b>la commande suivante</b> :</p>

<pre>mongo --port 27017</pre>

<div class="spacer"></div>

<p>Dans le shell mongo, utilisez <b>rs.initiate()</b> afin de démarrer votre replica set. Vous pouvez créer un objet de configuration de votre replica set avec
le shell mongo :</p>

<pre>
rsconf = {
	_id: "rs0",
	members: [
		{
			_id: 0,
			host: "hostname:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>remplaçant <b>"hostname"</b> avec le nom de votre système et ensuite, passer l'objet <b>"rsconf"</b> à la commande <b>rs.initiate()</b> :</p>

<pre>rs.initiate( rsconf )</pre>

<div class="spacer"></div>

<p>Si vous souhaitez afficher la <b>configuration actuelle</b> de votre replica set :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>La configuration de votre replica set ressemblera à ceci :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 4,
	"members" : [
		{
			"_id" : 1,
			"host" : "localhost:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>6) Dans le shell mongo connecté au primaire, ajoutez le second et le troisième membre au replica set avec la fonction <b>rs.add()</b> :</p>

<pre>
rs.add("hostname:27018")
rs.add("hostname:27019")
</pre>

<div class="spacer"></div>

<p>Une fois complétée, cette opération devrait vous offrir un replica set <b>entièrement fonctionnel</b>, qui va élire un <b>membre primaire</b>.
Vous pouvez vérifier le statut de votre Replica Set avec la commande rs.status().
<a name="geo"></a>

<div class="spacer"></div>

<p class="titre">III) [ Déployer un Replica Set Géographiquement Redondant ]</p>

<p>Ce tutoriel va traiter des replica sets ayant des membres dans des <b>zones géographiques différentes</b>.
Nous allons voir des replica sets avec <b>3 membres</b>, <b>quatres membres</b> et <b>plus</b>.
Il est vivement conseillé d'avoir le paragprahe sur comment deployer un replica set au début de cette page afin d'avoir les bases
nécessaires pour cette partie du tutoriel.</p>

<p>Alors que les replica sets normaux fournissent une protection contre l'échec d'une seule instance, les replica sets se situant dans une seule
et même zone sont succeptibles d'être confronté à une échec lié à cette zone (coupure de courant générale, panne de connexion Internet, désastres
naturels ou autre ...). Afin d'avoir une <b>protection efficace</b> contre ce genre d'évênements, vous pouvez choisir de déployer un replica set
avec un ou plusieurs membres répartis dans d'autres endroits ou <b>data centers</b> afin de garantir une meilleure redondance de l'information.</p>
<a name="geopr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-Requis</p>

<p>En général, les recommandations de base pour un replica set géographiquement redondant sont les suivantes :

- Assurez-vous que la majorité des membres votants se trouvent dans votre site primaire, <b>le site A</b>. Cela inclus bien sûr les membres à <b>priorité 0</b> et
les arbitres. Déployer ensuite les autres membres dans des sites secondaires <b>B, C etc ...</b> afin d'ajouter des copies additionnelles à votre replica set.
- Si vous déployer un replica set avec un nombre pair de membres, déployez un arbitre sur <b>le site A</b>. Celui-ci doit impérativement être sur le site A
afin de garder la majorité des membres votants sur ce site.</p>

<p>Concernant les replica sets à 3 membres, vous devez avoir deux membres sur le site A et le dernier sur un autre site, le site B.
Le site A doit faire partie de la même zone ou être <b>très proche</b> de celle de votre application (applications serveurs, utilisateurs etc ...).</p>

<p>Un replica set à quatres membres doit avoir <b>au moins deux membres</b> sur le site A, avec le nombre de membres restants sur le site B, sans oublier un Arbitre
sur le site primaire A. Pour toutes les configurations de ce tutoriel, déployez chaque membres du replica set sur <b>une machine distincte</b>. Si vous choisissez
de déployer plus d'un membre du replica set sur un même système, cela réduit <b>la redondance</b> et <b>la capacité</b> du replica set, ce genre de déploiement est uniquement
réservés aux <b>tests et développement</b> comme décris auparavant dans ce tutoriel.</p>
<a name="geop"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédure</p>

<p>- Chaque membre du replia set se trouve sur sa propre machine et tous les processus MongoDB associés au port 27017 par défaut.
- Chaque membre doit être accessible par <b>DNS ou hostname</b> :</p>

<pre>
mongodb0.exemple.fr
mongodb1.exemple.fr
mongodb2.exemple.fr
mongodbn.exemple.fr
</pre>

<div class="spacer"></div>

<p>Vous aurez besoin soit de configurer vos DNS correctement ou alors définir votre fichier host /etc/hosts sous Linu ou /Windows/System32/Drivers/host
afin de refletter cette configuration.
- Assure-vous que le réseau passe entre chaque membre du Replica Set avec les procédures suivantes :</p>

<ul>
	<li><b>Etabissez un VPN. Assurez-vous que la topologie de votre réseau route tous les traffiques entre les membres à l'intérieur de votre LAN.</b></li>
	<li><b>Configurez l'authentication utilisant auth et keyFile de manière à ce qu'uniquement les serveurs et processus authentifiés puissent se connecter au Replica
	  set.</b></li>
	<li><b>Configurer les règles de pare-feu de manière à ce qu'uniquement le traffique (packets entrants et sortants) sur le port par défaut 27017 est autorisé.</b></li>
	<li><b>Vous devez spécifier une configuration sur chaque système dans un fichier de configuration stocké dans /etc/mongodb.conf ou autres. Ne spécifiez pas
	   la configuration du Replica Set dans le shell mongo.</b></li>
</ul>

<div class="spacer"></div>
	
<p>Utilisez la configuration suivante pour chaque instance MongoDB :</p>
	   
<pre>
port = 27017
bind_ip = 10.8.0.10
dbpath = /srv/mongodb/
fork = true
replSet = rs0
</pre>

<div class="spacer"></div>

<p>Ajustez bien entendu vos paramètres systèmes à cette configuration. Le <b>dbpath</b> indique l'endroit ou vous souhaitez que MongoDB stocke les fichiers de données.
S'il n'existe pas, créez le répertoire et assurez-vous que mongod ait les <b>permissions de lecture et d'écriture</b> sur ce dossier.
Modifiez <b>"bind_ip"</b> vérifié que mongod écoute seulement les connexions des applications sur l'adresse configurée.</p>
<a name="trois"></a>

<div class="spacer"></div>

<p class="small-titre">c) Replica Set Géographique Redondant à 3 Membres</p>

<p>1) <b>Démarrez une instance</b> mongod sur chaque machine qui fera partie de votre replica set. Spécifiez le même nom de l'ensemble sur chaque instance.

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Si votre application se connecte à plus d'un Replica Set, chaque Set devrait avoir un nom distinct. Certains drivers
	regroupent les connexions aux Replica Set par noms de Replica Set.
</div>

<div class="small-spacer"></div>

<p>Si vous utilisez un fichier de configuration, veuillez démarrer <b>chaque instance mongod</b> avec cette commande :</p>

<pre>mongod --config /etc/mongodb.conf</pre>

<div class="spacer"></div>

<p>2) Ouvrez un shell/terminal mongo sur <b>l'une des machines</b> avec la commande :</p>

<pre>mongo</pre>

<div class="spacer"></div>

<p>Utilisez <b>rs.initiate()</b> afin de démarrer le replica set et d'utiliser la configuration par défaut :</p>

<pre>rs.initiate()</pre>

<div class="spacer"></div>

<p>4) Vous pouvez afficher la <b>configuration actuelle</b> :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>Ou <b>l'objet de configuration</b> devrait ressembler à :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 4,
	"members" : [
		{
			"_id" : 1,
			"host" : "mongodb0.exemple.fr:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>5) Dans le shell/terminal mongo connecté au membre primaire, ajoutez les autres membres au replica set utilisant la méthode <b>rs.add()</b> :</p>

<pre>
rs.add("mongodb1.exemple.fr")
rs.add("mongodb2.exemple.fr")
</pre>

<div class="spacer"></div>

<p>Une fois terminé, vous obtenez un replica set fonctionnel qui va <b>élire un membre primaire</b>.
6) Soyez sûr que vous avez configuré le membre situé sur le site B (dans cette exemple, mongodb2.exemple.fr) comme un membre à priorité 0.
	a) Effectuez la commande suivante pour déterminer la position du membre :<p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

	<p>b) Dans le tableau de membres, <b>sauvegardez la position du membre</b> auquel vous souhaiteriez changer la priorité. Dans l'étape suivante, nous avons la valeur
	   2 pour la troisième item dans la liste. Vous devez impérativement enregistrer la position du tableau et non <b>l'_id</b> vu que ces valeurs seront différentes
	   si vous supprimez un membre.
	 c) Dans le shell/terminal mongo connecté au membre primaire :</p>
	 
<pre>
cfg = rs.conf()
cfg.members[2].priority = 0
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Quand l'opération est terminée, mongodb2.exemple.fr à la priorité 0, il <b>ne peut pas</b> devenir primaire.</p>

<div class="alert alert-success">
	<u>Astuce</u> : La méthode rs.reconfig() peut forcer le membre primaire à s'arrêter, déclenchant une élection. Quand le membre primaire
	est stoppé, tous les clients sont déconnectés. La plupart des élection se termine en moins d'une minute.
</div>

<p>Après avoir utilisé ces commandes, vous obtenez un <b>replica set géographiquement redondant</b>. Comme toujours, vérifiez le statut de votre Replica set
avec la commande rs.status().</p>
<a name="quatr"></a>

<div class="spacer"></div>

<p class="small-titre">d) Replica Set Géographique Redondant à 4 Membres</p>

<p>Un replica set géographiquement redondant à <b>4 membres</b> à deux critères additionnels à prendre en compte :

- Un hôte doit être un arbitre (par exemple : <b>mongodb3.exemple.fr</b>). Cet hôte peut être exécuté sur un système qui implémente déjà un serveur d'application
ou sur la même machine qu'un autre processus MongoDB.
- Vous devez choisir comment <b>distribuer votre système</b>. Il y a 3 architectures possibles avec le replica set à 4 membres :</p>

<ul>
	<li><b>3 membres dans le site A, 1 membre a priorité 0 sur B et l'arbitre dans A</b></li>
	<li><b>2 membres dans A, 2 membres à priorité 0 sur B et l'arbitre dans A</b></li>
	<li><b>2 membres dans A un à priorité 0 dans B, 1 à priorité 0 dans C et l'Arbitre dans A</b></li>
</ul>
	
<p>Dans la plupart des cas, l'architecture A <b>est privilégiée</b> car c'est la moins complexe de toutes.</p>

<p>1) Démarrez une instance mongod sur chaque machine qui fera partie de votre replica set. Spécifiez le même nom de l'ensemble sur <b>chaque instance</b>.

<div class="alert alert-danger">
	<u>Attention</u> : Si votre application se connecte à plus d'un replica set, chaque Set devrait avoir un nom distinct. Certains drivers
	regroupent les connexions aux seplica set par noms de replica set.
</div>

<div class="spacer"></div>

<p>Si vous utilisez un fichier de configuration, veuillez <b>démarrer chaque instance mongod</b> avec cette commande :</p>

<pre>mongod --config /etc/mongodb.conf</pre>

<div class="spacer"></div>

<p>2) Ouvrez un shell/terminal mongo sur <b>l'une des machines</b> avec la commande :</p>

<pre>mongo</pre>

<div class="spacer"></div>

<p>Utilisez <b>rs.initiate()</b> afin de démarrer le replica set et d'utiliser la configuration par défaut :</p>

<pre>rs.initiate()</pre>

<div class="spacer"></div>

<p>4) Vous pouvez afficher la <b>configuration actuelle</b> :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>Ou <b>l'objet de configuration</b> devrait ressembler à :</p>

<pre>
{
	"_id" : "rs0",
	"version" : 4,
	"members" : [
		{
			"_id" : 1,
			"host" : "mongodb0.exemple.fr:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>5) Dans le shell/terminal mongo connecté au membre primaire, ajoutez les autres membres au replica set utilisant la commande <b>rs.add()</b> :</p>

<pre>
rs.add("mongodb1.exemple.fr")
rs.add("mongodb2.exemple.fr")
rs.add("mongodb3.exemple.fr")
</pre>

<div class="spacer"></div>

<p>Après cela, vous aurez un replica set parfaitement fonctionnel qui élira un <b>nouveau primaire</b>.
6) Dans le même terminal/shell, effectuez cette commande afin d'<b>ajouter un arbitre</b> :</p>

<pre>rs.addArb("mongodb4.exemple.fr")</pre>

<div class="spacer"></div>

<p>7) Soyez sûrs d'avoir configuré chaque membre situé <b>à l'extérieur du site A</b> (par exemple, mongodb3.exemple.fr) en tant que membre <b>à priorité 0</b> :
	a) Effectuez la commande suivante pour déterminer la position du membre :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

	<p>b) Dans le tableau de membres, sauvegardez la position du membre auquel vous souhaiteriez <b>changer la priorité</b>. Dans l'étape suivante, nous avons la valeur
	   2 pour la troisième item dans la liste. Vous devez impérativement enregistrer la position du tableau et non <b>l'_id</b> vu que ces valeurs seront différentes
	   si vous supprimez un membre.
	 c) Dans le shell/terminal mongo connecté au membre primaire :</p>
	 
<pre>
cfg = rs.conf()
cfg.members[2].priority = 0
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Quand l'opération est terminée, <b>mongodb2.exemple.fr</b> a la <b>priorité 0</b>, il ne peut pas devenir primaire.<p>

<div class="alert alert-success">
	<u>Astuce</u> : La méthode rs.reconfig() peut forcer le membre primaire à s'arrêter, déclenchant une élection. Quand le membre primaire
	est stoppé, tous les clients sont déconnectés. La plupart des élection se termine en moins d'une minute.
</div>

<div class="spacer"></div>

<p>Après ces commandes terminées, vous obtenez un seplica set <b>géographiquement redondant à 4 membres</b>.
Exécutez la méthode <b>rs.status()</b> pour vérifier le statut de votre replica set.</p>
<a name="plus"></a>

<div class="spacer"></div>

<p class="small-titre">e) Replica Set Géographique Redondant à Plus de 4 Membres</p>

<p>Les procédures des paragraphes précédents détaillent les étapes nécessaires pour déployer un Replica Set Géographiquement Redondant. Les déploiements 
de Replica Sets plus larges suivent les même étapes mais considèrent les options suivantes :</p>

<ul>
<li><b>Ne déployez jamais plus de 7 membres</b></li>
<li><b>Si vous avez un nombre pair de membres, utilisez la procédure décrite dans le paragraphe sur les Repica Sets à 4 membres. Assurez-vous que le site primaire A
  a toujours la majorité de membres votants en déployant un Arbitre sur ce même site. Par exemple, si vous avez 6 membres, déployez-en au moins 3 sur le site A
  en addition avec l'Arbitre, puis, les autres membres sur les autres sites.</b></li>
<li><b>Si vous avez un nombre impaire de membres, utilisez la procédure sur les Replica Sets à 3 membres. Assurez-vous que le site primaire A
  a toujours la majorité de membres votants en déployant un Arbitre sur ce même site. Par exemple, si vous avez 5 membres, déployez-en 3 sur le site A
  puis, les deux autres membres sur les autres sites.</b></li>
<li><b>Si vous avez une majorité de membres à l'extérieur du site A, et que les partitions du réseau empêchent la communication entre les sites, le membre primaire
  du site A va s'arrêter, même si aucun des membres à l'extérieur du site A ne peuvent devenir primaires.</b></li>
</ul>
<a name="ajar"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Ajouter un Arbitre à un Replica Set ]</p>
  
<p>Les arbitres sont des instances mongod qui prennent part du replica set mais qui ne contiennent <b>aucunes données</b>. Les arbitres <b>participent aux élections</b>
dans le but de casser les égalités de votes. Si un replica set a un <b>nombre pair de membres</b>, ajoutez un arbitre.
Les arbitres nécessitent de <b>légères ressources</b> et ne récessitent <b>aucun matériel</b>. Vous pouvez déployer un arbitre sur un serveur d'application.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Ne déployez jamais un arbitre sur un des membres de votre Replica Set.
</div>

<div class="spacer"></div>

<p>1) Créez un répertoire de données pour l'arbitre <b>(dbpath)</b>. L'instance mongod de l'arbitre utilise ce dossier pour les données de configuration, et ne contiendra
aucunes données. Par exemple, créez le répertoire <b>"/data/arb"</b> :</p>

<pre>mkdir /data/arb</pre>

<div class="spacer"></div>

<p>2) Démarrez l'arbitre et spécifiez-lui le dossier des données et le nom du replica set comme avec la commande suivante :</p>

<pre>mongod --port 30000 --dbpath /data/arb --replSet rs</pre>

<div class="spacer"></div>

<p>ou le répertoire des données se situe dans <b>"/data/arb"</b> et le nom du replica set est <b>rs</b>. L'Arbitre est associé au port 30000.
3) Connectez le membre primaire et ajoutez l'arbitre au replica set. Utilisez la fonction <b>rs.useArb()</b> :</p>

<pre>rs.addArb("m1.exemple.fr:30000")</pre>

<p>L'opération ajoute l'arbitre au replica set, l'arbitre étant associé <b>au port 30000</b> sur l'hôte <b>m1.exemple.fr</b>.</p>
<a name="conv"></a>
  
<div class="spacer"></div>

<p class="titre">V) [ Convertir un Standalone en un Replica Set ]</p>

<p>Ce tutoriel va décrire comment transformer <b>une simple instance mongod en un replica set à 3 membres</b>. Utilisez les instances standalone pour du test ou
du développement, mais utilisez <b>toujours</b> les replica set en production. Pour installer une instance standalone, veuillez vous référer au chapitre sur
l'<a href="../installation.php">Installation MongoDB</a>.</p>
<a name="convp"></a>

<div class="spacer"></div>

<p class="small-titre">a) Procédure</p>

<p>1) <b>Arrêter l'instance</b> mongod standalone
2) <b>Redémarrez l'instance</b> avec l'option --replSet afin de définir le nom du replica set existant.
   Par exemple, la commande suivante démarre l'instance standalone en tant que nouveau membre du replica set nommé <b>rs0</b>.
   La commande utilise le <b>dbpath</b> existant de l'instance standalone <b>/srv/mongodb/db0</b>.

<pre>mongod --port 27017 --dbpath /srv/mongodb/db0 --replSet rs0</pre>

<div class="spacer"></div>

<p>3) <b>Connectez-vous</b> à l'instance mongod
4) Utilisez la commande <b>rs.initiate()</b> pour initialiser le nouveau replica set :</p>

<pre>rs.initiate()</pre>

<p>Le replica set est maintenant <b>opérationnel</b>. Pour en vérifier sa configuration, <b>rs.conf()</b>, pour son statut, <b>rs.status()</b>.</p>
<a name="conve"></a>

<div class="spacer"></div>

<p class="small-titre">b) Etendre le Replica Set</p>

<p>En parlant d'extension du replica set, on veut bien sûr <b>ajouter des membres</b> au replica set.
1)Sur deux systèmes distincts, démarrez deux instances mongod standalones.
2) Avec votre connexion sur le <b>mongod original</b>, saisissez la commande suivante afin d'ajouter un membre au replica set :</p>

<pre>rs.add("hostname:port")</pre>

<p>Remplacez <b>hostname</b> et <b>port</b> avec votre DNS et le port de l'instance mongod à laquelle on veut ajouter le membre.
<a name="cons"></a>

<div class="spacer"></div>

<p class="small-titre">c) Considérations Pour le Sharding</p>

<p>Si le replica set fait partie d'un <b>cluster fragmenté</b>, changez les informations de l'hôte dans la configuration de la base de données :
	1) Connectez-vous à l'une des instances mongos du cluster fragmenté et saisissez la commande suivante :</p>
	
<pre>db.getSiblingDB("config").shards.save( {_id: "name", host: "replica-set/member,member,..." } )</pre>

<p>Remplacez name avec le nom du shard, remplacez replica set avec le nom du replica set et remplacez <b>member</b> avec la liste des membres de votre replica set.
2) Redémarrez toutes les instances mongos et si possible, <b>redémarrez tous les composants</b> du replica set (tous les mongo et tous les mongod).</p>
<a name="ajmbr"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Ajouter des Membres à un Replica Set ]</p>

<p>Afin d'ajouter un membre à votre Replica Set, il va falloir prendre plusieurs facteurs en compte :</p>

<ul>
<li><b>Le nombre maximum de membres votants : Un replica set peut avoir un maximum de 7 membres votants au total. Pour ajouter un membre qui a déjà 7 votes,
vous devez soit ajouter ce membre en mode non votant ou alors en supprimer un existant.</b></li>
<li><b>Les scripts de contrôle : Dans les déploiements de production, vous pouvez configurer un script de contrôle pour gérer les processus membres.</b></li>
<li><b>Membres existants : Vous pouvez utiliser ces procédures pour ajouter des membres à un Replica Set existant. Vous piouvez également utiliser la même procédure
pour re-ajouter un membre supprimé. Si les données du membre supprimé sont relativements récentes, il peut se restaurer et se remettre à niveau facilement.</b></li>
<li><b>Fichiers de données : Si vous avez une sauvegarde ou une image d'un membre existant, vous pouvez bouger les fichiers de données (dossier dbpath) à un nouveau
système et les utiliser afin d'instancier rapidement un membre. Ces fichiers doivent être :
	<ul>
		<li><b>une copie de la base de données d'un membre du même replica set.</b></li>
		<li><b>Plus récents que la plus vieille opération de l'Oplog du membre primaire. Le nouveau membre doit pouvoir devenir actuel en appliquant les opérations
		depuis l'oplog du membre primaire.</b></li>
	</ul>
	</b></li>
</ul>
<a name="ajmpr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Pré-requis</p>

<p>1) Un replica set <b>actif</b></p>
<p>2) Un nouveau système MongoDB capable de supporter votre ensemble de données, accessible par le replica set actuel <b>à travers le réseau</b>.</p>
<a name="ajmp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Procédures</p>

<p>Tout d'abord, vous allez devoir préparer le répertoire des données avant d'ajouter un membre à votre Replica Set :</p>

<ul>
	<li><b>Soyez sûr que le dossier de données du nouveau membre ne contienne pas de données. Le nouveau membre va copier les données depuis un membre existant.
	Si le nouveau membre est dans un état de récupération "recovering", il doit impérativement s'arrêter et devenir secondaire avant que MongoDB puisse copier
	toutes les données durant le processus de réplication. Ce processus prend du temps, mais ne recquiert pas d'intervention de la part d'un Administrateur.</b></li>
	<li><b>Copiez manuellement le dossier de données depuis un membre existant. Le nouveau membre devient secondaire et va se mettre à niveau de l'état du replica set.
	Copier les données de cette façon peut réduire le temps que ce membre met à devenir à jour.
	Assure-vous que vous pouvez copier le répertoire des données au nouveau membre et commencer la réplication sous la fenêtre autorisée par l'oplog.
	Sinon, la nouvelle instance va devoir effectuer une synchronisation initiale, ce qui re-synchronise complètement les données.
	Utilisez db.printReplicationInfo() pour vérifier l'état actuel des membres du Replica Set en fonction de l'oplog.</b></li>
</ul>

<div class="spacer"></div>

<p>Ensuite, vous allez devoir <b>ajouter un membre</b> à votre replica set existant :
1) Démarrez la nouvelle instance mongod et spécifiez le dbpath  et le nom du replica set. L'exemple suivant spécifie le dbpath <b>"/srv/mongodb/db0"</b> et le replica set
rs0 :</p>

<pre>mongod --dbpath /srv/mongodb/db0 --replSet rs0</pre>

<div class="small-spacer"></div>

<p>Prenez notre du nom d'hôte et du port de l'instance mongod.</p>

<div class="alert alert-warning">
	<u>Optionnel</u> : Vous pouvez spécifier le répertoire des données et le Replica Set dans le fichier de configuration mongo.conf et
	démarrer l'instance mongod avec la commande suivante :
</div>

<div class="small-spacer"></div>

<pre>mongod --config /etc/mongodb.conf</pre>

<p>2) Connectez-vous au primaire du replica set. Vous pouvez ajouter des membres seulement quand vous êtes connecté au primaire. Si vous ne savez pas
quel membre est le primaire, connectez-vous à l'un des membres et utilisez la fonction <b>db.isMaster()</b>.</p>
<p>3) Utilisez <b>rs.add()</b> pour ajouter un nouveau membre au replica set. Par exemple, si vous souhaitez ajouter mongodb3.exemple.fr :</p>

<pre>rs.add("mongodb3.exemple.fr")</pre>

<div class="small-spacer"></div>

<p>Vous pouvez même inclure le port :</p>

<pre>rs.add("mongodb3.exemple.fr:27017")</pre>

<div class="spacer"></div>

<p>4) Vérifiez que le membre fait bien partie du replica set, appelez la méthode <b>rs.conf()</b> qui affiche la configuration du replica set. Pour vérifier le statut
de votre replica set, appelez la méthode <b>rs.status()</b>.</p>

<p>Si vous souhaitez configurer et ajouter un membre avec sa configuration, vous pouvez passer un document membre à la méthode <b>rs.add()</b>. Ce document
doit avoir la forme d'un document de type <b>"local.system.replset.members"</b>.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Spécifiez une valeur pour le champ _id du document. MongDB n'attribue pas d'_id automatiquement dans ce cas.
	Ce document doit impérativement déclarer une valeur "host", ous les autres champs sont optionnels.
</div>

<div class="spacer"></div>

<p>Par exemple, afin d'ajouter un membre ayant cette configuration :</p>

<ul>
	<li><b>un _id de 1</b></li>
	<li><b>un nom d'hôte et un numéro de port de mongodb3.example.net:27017</b></li>
	<li><b>une priorité à 0</b></li>
	<li><b>configuré en tant que caché</b></li>
</ul>

<p>effectuez la commande suivante :</p>

<pre>rs.add({_id: 1, host: "mongodb3.exemple.fr:27017", priority: 0, hidden: true})</pre>
<a name="suppm"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Supprimer des Membres d'un Replica Set ]</p>

<p>Après savoir comment ajouter des membres au replica set, vous allez sans doute vouloir savoir comment <b>en supprimer</b>. C'est ce que nous allons voir dès maintenant.</p>
<a name="suppmr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Supprimer un Membre en utilisant rs.remove()</p>

<p>Comme vous vous en doutiez probablement, la méthode pour supprimer un membre d'un replica set est <b>rs.remove()</b>. Voici la procédure à effectuer
lorsque vous voulez <b>supprimer un membre particulier</b> :</p>

<div class="small-spacer"></div>

<p>1) Arrêtez l'instance du membre que vous souhaitez supprimer, pour cela, connectez-vous à un shell mongo et <b>exécutez la commande db.shutdownServer()</b>.</p>

<p>2) Conectez-vous au membre primaire du replica set. Pour savoir quel membre est primaire, utilisez la commande <b>db.isMaster()</b> depuis n'importe quel membre
du replica set.</p>

<p>3) Utilisez <b>rs.remove()</b> dans une des formes suivantes, au choix :</p>

<div class="small-spacer"></div>

<pre>
rs.remove("mongod3.exemple.fr:27017")
rs.remove("mongod3.exemple.fr")
</pre>

<div class="spacer"></div>

<p>MongDB <b>déconnecte alors le shell brièvement</b> vu que le replica set vote pour un primaire. Ensuite, le shell se reconnecte de lui-même. Le shell mongo
retounre cette erreur suivante, même si l'opération <b>a bien été effectuée</b> :</p>

<pre>DBClientCursor::init call() failed</pre> 
<a name="suppmc"></a>

<div class="spacer"></div>

<p class="small-titre">b) Supprimer un Membre en utilisant rs.reconfig()</p>

<p>Voici une autre méthode qui va permettre de supprimer un Membre de votre replica set mais en reconfigurant celui-ci :</p>

<div class="small-spacer"></div>

<p>1) Arrêtez l'instance du membre que vous souhaitez supprimer, pour cela, connectez-vous à un shell mongo et exécutez la commande <b>db.shutdownServer()</b>.</p>

<p>2) Connectez-vous au membre primaire du replica set. Pour savoir quel membre est primaire, utilisez la commande <b>db.isMaster()</b> depuis n'importe quel membre
du replica set.</p>

<p>3) Effectuez la commande <b>rs.conf()</b> afin de vérifier le document de configuration et déterminer la position du membre dans le tableau de membres que l'on
veut supprimer. Par exemple, <b>mongod_C.exemple.fr</b> se trouve en deuxième position dans le document de configuration suivant :</p>

<div class="small-spacer"></div>

<pre>
{
	"_id" : "rs",
	"version" : 7,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongod_A.exemple.fr:27017"
		},
		{
			"_id" : 1,
			"host" : "mongod_B.exemple.fr:27017"
		},
		{
			"_id" : 2,
			"host" : "mongod_C.exemple.fr:27017"
		}
	]
}
</pre>

<div class="spacer"></div>

<p>4) Assignez la configuration actuelle à une variable <b>cfg</b> :</p>

<pre>cfg = rs.conf()</pre>

<div class="spacer"></div>

<p>5) Modifiez <b>l'object cfg</b> afin de supprimer le membre. Par exemple, pour supprimer le membre <b>mongod_C.exemple.fr:27017</b>, utilisez :</p>

<pre>cfg.members.splice(2,1)</pre>

<div class="spacer"></div>

<p>6) Ecrivez par dessus le <b>document de configuration</b> du replica set avec la nouvelle :</p>

<pre>rs.reconfig(cfg)</pre>

<div class="spacer"></div>

<p>Une fois cela effectué, le shell va se déconnecter pendant que le replica set est <b>en train d'élire un nouveau membre primaire</b>. Le shell affiche
ensuite :</p>

<pre>DBClientCursor::init call() failed</pre>

<div class="spacer"></div>

<p>Pas de panique, malgré ce message, votre opération <b>a bien été effectuée</b> avec succès et le shell se reconecte.

7) Pour confirmer que votre nouvelle configuration a bien été prise en compte, veuillez rappeler la méthode <b>rs.conf()</b> :</p>

<div class="small-spacer"></div>

<pre>
{
	"_id" : "rs",
	"version" : 8,
	"members" : [
		{
			"_id" : 0,
			"host" : "mongod_A.exemple.fr:27017"
		},
		{
			"_id" : 1,
			"host" : "mongod_B.exemple.fr:27017"
		}
	]
}
</pre>

<p>Voilà, vous pouvez constater que votre configuration <b>a bien été modifiée</b> et que votre nouveau membre <b>a été ajouté</b>.</p>
<a name="remp"></a>

<div class="spacer"></div>

<p class="titre">VIII) [ Remplacer un Membre du Replica Set ]</p>

<p>Si vous avez besoin de <b>changer le nom d'hôte</b> d'un membre du replica set sans changer la configuration de ce membre ou de l'ensemble, vous pouvez utiliser
la séquence qui va suivre.</p>

<p>Pour changer le nom d'hôte (hostname) d'un membre du replica set, modifiez le champ <b>"host"</b>. La valeur de <b>l'_id</b> ne va pas changer quand vous aller reconfigurer
l'ensemble.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Toute modification de la configuration d'un Replica Set peut déclencher un arrêt du membre primaire, ce qui
	force une élection. Pendant l'élection, le shell sur lequel vous êtes ainsi que tous les clients sont déconnectés, ce qui produit une erreur, même si l'opération
	a été effectuée correctement.
</div>

<div class="small-spacer"></div>

<p>Par exemple, pour changer le nom d'hôte de <b>mongo2.exemple.fr</b> du membre du replica set configuré en members[0], effectuez la commande suivante :</p>

<pre>
cfg = rs.conf()
cfg.members[0].host = "mongo2.exemple.fr"
rs.reconfig(cfg)
</pre> 

<div class="spacer"></div>

<p>Nous venons de voir comment <b>configurer certains types de replica sets</b>, maintenant, il va vous falloir apprendre comment
<b>configurer chaque membre faisant partie de votre replica set</b>. Passons donc à la page suivante : <a href="tutoriaux_configuration.php">"Configuration de Membre" >></a>.

<?php

	include("footer.php");

?>
