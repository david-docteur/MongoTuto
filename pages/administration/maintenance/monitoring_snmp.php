<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Monitoring MongoDB avec SNMP</li>
</ul>

<p class="titre">[ Monitoring MongoDB avec SNMP ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pr">I) Pré-Requis</a></p>
	<p class="elem"><a href="#conf">II) Configurer SNMP</a></p>
	<p class="elem"><a href="#diag">III) Diagnostique</a></p>
</div>

<p>Nouvelle dans la version 2.2 de MongoDB, l'extension SNMP est disponible uniquement pour la version de MongoDB Enterprise.</p>
<a name="pr"></a>

<div class="spacer"></div>

<p class="titre">I) [ Pré-Requis ]</p>

<p>Installez MongoDB Entreprise :
Inclure les fichiers du package Enterprise :
MONGO-MIB.txt qui décrit les données pour la sortie de SNMP avec MongoDB.
mongod.conf qui est le fichier de configuration SNMP pour lire la sortie de SNMP avec MongoDB. SNMP configure les noms de communauté, les permissions,
les contrôles d'accès etc ...

Packages requis : Pour utiliser SNMP, vous devez installer plusieurs pré-requis. Les noms des packages peuvent varier selon les distributions :
- Ubuntu 11.04 nécessite libssl0.9.8, snmp-mibs-downloader, snmp et snmpd. Utilisez la commande suivante pour installer les packages :</p>

<pre>sudo apt-get install libssl0.9.8 snmp snmpd snmp-mibs-downloader</pre>

<p>Linux RedHat Enterprise 6.x et Linux Amazon AMI nécessitent libssl, net-snmp, net-snmp-libs et net-snmp-utils :</p>

<pre>sudo yum install libssl net-snmp net-snmp-libs net-snmp-utils</pre>

<p>Linux SUSE Enterprise a besoin de libopenssl0_9_8, libsnmp15, slessp1-libsnmp15 et snmp-libs :</p>

<pre>sudo zypper install libopenssl0_9_8 libsnmp15 slessp1-libsnmp15 snmp-mibs</pre>
<a name="conf"></a>

<div class="spacer"></div>

<p class="titre">II) [ Configurer SNMP ]</p>

<p>Installer les fichiers de configuration MIB : Assurez-vous que le répertoire MIB /usr/share/snmp/mibs existe. Sinon :</p>

<pre>sudo mkdir -p /usr/share/snmp/mibs</pre>

<p>Utilisez la commande suivante pour créer un lien symbolique :</p>

<pre>sudo ln -s "path"MONGO-MIB.txt /usr/share/snmp/mibs</pre>

<p>Remplacez [/path/to/mongodb/distribution/] avec le chemin de votre fichier de configuration MONGO-MIB.txt. Copiez le fichier mongod.conf dans le répertoire
/etc/snmp avec la commande suivante :</p>

<pre>cp mongod.conf /etc/snmp/mongod.conf</pre>

<p>Démarrez : Vous pouvez contrôler MongoDB Enterprise en utilisant des scripts de contrôle par défaut ou personnalisés, comme tout autre mongod.
Utilisez la commande suivante pour voir toutes les options SNMP disponibles dans votre MongoDB :</p>

<pre>mongod --help | grep snmp</pre>

<p>Cette commande devrait alors retourner un résultat similaire à celui-ci :</p>

<pre>
Module snmp options:
	--snmp-subagent		run snmp subagent
	--snmp-master		run snmp as master
</pre>

<p>Assurez-vous que les répertoires suivants existent :
- /data/db (le chemin ou MongoDB stocke les fichiers de données)
- /var/log/mongodb/ (le chemin ou MongoDB écrit ses logs)

Si ceux-ci n'existent pas, utilisez la commande suivante :</p>

<pre>mkdir -p /var/log/mongodb/ /data/db/</pre>

<p>Démarrez l'instance mongod avec la commande suivante :</p>

<pre>mongod --snmp-master --port 3001 --fork --dbpath /data/db/ --logpath /var/log/mongodb/1.log</pre>

<p>Vous pouvez optionnellement définir ces options dans un fichier de configuration.
Pour vérifier si mongod est exécuté avec SNMP, utilisez la commande ci-dessous :</p>

<pre>ps -ef | grep 'mongod --snmp'</pre>

<div class="spacer"></div>

<p>Le résultat, indiquant que l'instance mongod est exécutée, retourne les informations suivantes :</p>

<pre>systemuser 31415 10260 0 Jul13 pts/16 00:00:00 mongod --snmp-master --port 3001 # [...]</pre>

<p>Tester SNMP : Vérifiez que le processus SNMP écoute sur le port 1161 avec la commande suivante :</p>

<pre>sudo lsof -i :1161</pre>

<p>Ce qui devrait retourner le résultat suivant dans le shell :</p>

<pre>
COMMAND  PID   USER      FD  TYPE DEVICE SIZE/OFF NODE NAME
mongod   9238  sysadmin  10u IPv4 96469  0t0      UDP  localhost:health-polling
</pre>

<p>De façon similaire, cette commande :</p>

<pre>netstat -anp | grep 1161</pre>

<p>devrait retourner le résultat suivante :</p>

<pre>udp	0	0	127.0.0.1:1161	0.0.0.0:*	9238/<path>/mongod</pre>

<div class="spacer"></div>

<p>Exécuter snmpwalk localement : snmpwalk fournit des outils pour récupérer et formatter les données SNMP en fonction du MIB. Si vous avez installé tous les
packages décrits plus haut, vote système aura donc snmpwalk. Utilisez la commande suivante pour récupérer les données avec mongod utilisant SNMP :</p>

<pre>snmpwalk -m MONGO-MIB -v 2c -c mongodb 127.0.0.1:1161 1.3.6.1.4.1.37601</pre>

<p>Vous voudrez sûrement besoin de spécifier le chemin du fichier MIB :</p>

<pre>snmpwalk -m /usr/share/snmp/mibs/MONGO-MIB -v 2c -c mongodb 127.0.0.1:1161 1.3.6.1.4.1.37601</pre>

<p>Utilisez cette commande uniquement pour vous assurer que vous pouvez récuper et valider des données SNMP depuis MongoDB.</p>
<a name="diag"></a>

<div class="spacer"></div>

<p class="titre">III) [ Diagnostique ]</p>

<p>Vérifiez toujours les logs d'erreur si quelquechose ne se déroule pas comme prévu. Vérifiez le fichier log /var/log/mongodb.1.log.
La présence de la ligne suivante indique que mongod ne peut pas lire le fichier /etc/snmp/mongod.conf :</p>

<pre>[SNMPAgent] warning: error starting SNMPAgent as master err:1</pre>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur les <a href="fichiers_logs.php">"Fichiers Logs" >></a>.</p>

<?php

	include("footer.php");

?>
