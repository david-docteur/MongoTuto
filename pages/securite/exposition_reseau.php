<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Sécurité et Exposition du Réseau</li>
</ul>

<p class="titre">[ Sécurité et Exposition du Réseau ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#opti">I) Options de Configuration</a></p>
	<p class="right"><a href="#http">- a) nohttpinterface</a></p>
	<p class="right"><a href="#rest">- b) rest</a></p>
	<p class="right"><a href="#bind">- c) bind_ip</a></p>
	<p class="right"><a href="#port">- d) port</a></p>
	<p class="elem"><a href="#fire">II) Firewalls</a></p>
	<p class="elem"><a href="#vpn">III) Réseaux Privés Virtuels</a></p>
</div>

<p>Par défaut, <b>les programmes MongoDB</b> (par exemple : mongos et mongod) vont se lier à <b>toutes les interfaces réseau</b> (par exemple : adresse IP)
de votre système.
Dans cette partie du tutoriel, nous allons voir <b>quelques options</b> qui vont permettre de <b>limiter l'accès</b> aux programmes MongoDB.</p>
<a name="opti"></a>

<div class="spacer"></div>

<p class="titre">I) [ Options de Configuration ]</p>

<p>Vous pouvez <b>limiter l'exposition au réseau</b> avec les options de configurations (mongod et mongos) suivantes : <b>nohttpinterface</b>, <b>rest</b>, <b>bind_ip</b> et <b>port</b>.
Vous pouvez utiliser un <b>fichier de configuration</b> pour spécifier ces paramètres.</p>
<a name="http"></a>

<div class="spacer"></div>

<p class="small-titre">a) nohttpinterface</p>

<p>Le paramètre <b>"nohttpinterface"</b> pour des instances mongod ou mongos <b>désactive la page</b> principale de statut, qui devrait s'exécuter sur le port <b>28017</b> par défaut.
L'interface statut est <b>read-only</b> par défaut. Vous voudrez aussi spécifier cette option en ligne de commande tel que :</p>

<div class="small-spacer"></div>

<pre>mongod --httpnointerface</pre>

<p><b>Ou :</b></p>

<pre>mongos --httpnointerface</pre>

<div class="small-spacer"></div>

<p>L'authentification <b>n'affecte pas</b> l'accès à cette interface.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Désactivez cette option pour les environnements de production. Si vous laissez cette interface activée, vous devrez alors
	autoriser uniquement les clients de confiance pour accéder à ce port.
</div>
<a name="rest"></a>

<div class="spacer"></div>

<p class="small-titre">b) rest</p>

<p>Le paramètre <b>"rest"</b>, pour une instance mongod, <b>active une interface REST</b> entièrement administrable, qui est <b>désactivée par défaut</b>. L'interface de statut, qui est activée
par défaut, est <b>read-only</b>. Cette configuration rend cette interface <b>entièrement interractive</b>. L'interface REST ne supporte aucune authentification et vous
devrez toujours restreindre l'accès à cette interface pour <b>autoriser uniquement les clients de confiance</b> à se connecter à ce port.
Vous voudrez probablement aussi activer cette interface en ligne de commande comme ceci :</p>

<div class="small-spacer"></div>

<pre>mongod --rest</pre>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Désactivez cette option pour les déploiements de production. Si vous laissez cette interface activée, vous devrez
	alors autoriser uniquement les clients de confiance accéder à ce port.
</div>
<a name="bind"></a>

<div class="spacer"></div>

<p class="small-titre">c) bind_ip</p>

<p>Le paramètre <b>"bind_ip"</b> pour une instances mongod ou mongos limite les interfaces réseau sur lesquelles les programmes MongoDB vont écouter afin d'autoriser
les connexions entrantes. Vous pouvez aussi <b>spécifier un certain nombre d'interfaces</b> en passant l'option "bind_ip" avec une liste d'adresses IP séparées par une
virgule. Vous pouvez utiliser cette option <b>en ligne de commande</b> pour limiter l'accessibilité réseau d'un programme MongoDB :</p>

<div class="small-spacer"></div>

<pre>mongod --bind_ip</pre>

<p><b>Ou :</b></p>

<pre>mongos --bind_ip</pre>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Soyez sûr que vos instances mongod et mongos sont accessibles uniquement sur des réseaux de confiance. Si votre système
	a plus d'une seule interface réseau, liez les programmes MongoDB à l'interface réseau privée ou interne.
</div>
<a name="port"></a>

<div class="spacer"></div>

<p class="small-titre">d) port</p>

<p>Le paramètre <b>"port"</b> des instances mongod ou mongos <b>change le port principal</b> sur lequel ces instances vont écouter. Le port par défaut, comme vous le savez probablement
déjà est <b>le port 27017</b>. Changer ce port ne réduit pas forcément les risques ou ne limite pas forcément l'exposition sur le réseau. Vous pourrez aussi
spécifier cette option en ligne de commande :</p>

<div class="small-spacer"></div>

<pre>mongod --port</pre>

<p><b>Ou :</b></p>

<pre>mongos --port</pre>

<div class="small-spacer"></div>

<p>Utiliser <b>"port"</b> définit indirectement le port pour <b>l'interface de statut HTTP</b>, qui est toujours disponible sur le port : <b>port du mongod primaire + 1000</b>, et donc par défaut
<b>28017</b>.
N'autorisez uniquement que <b>les clients de confiance</b> à se connecter sur le port des instances mongod et mongos.</p>
<a name="fire"></a>

<div class="spacer"></div>

<p class="titre">II) [ Firewalls ]</p>

<p>Les pare-feu permettent aux administrateurs de <b>filtrer les connexions et contrôler l'accès</b> à un système en surveillant les communications sur le réseau.
Pour les administrateurs de MongoDB, les capacités suivantes sont importantes : limiter le traffic entrant sur un port spécifique et limiter le traffic
entrant venant d'hôtes inconnus.

Sur les systèmes <b>GNU/Linux</b>, l'interface <b>iptables</b> fournit l'accès au firewall <b>netfilter</b>. Sur les systèmes de <b>Microsoft Windows</b>, l'interface de la ligne de commande
<b>netsh</b> fournit l'accès au firewall <b>Windows</b>.
Pour de meilleurs résultats ainsi que pour <b>minimisr l'exposition générale</b>, assurez-vous que seul le traffic des <b>hôtes de confiance</b> puissent avoir accès
aux instances <b>mongod</b> et <b>mongos</b> et que ces instances puissent uniquement se connecter aux hôtes de confiance.

De plus, pour les déploiements MongoDB sur les <b>Web Services d'Amazon</b>, jettez un oeil à la page pour <a href="http://docs.mongodb.org/ecosystem/platforms/amazon-ec2/" target="_blank">"Amazon EC2"</a>,
qui fournit plus de détails de fonctionnalité de sécurité des <b>Amazon's Security Groups</b>.</p>
<a name="vpn"></a>

<div class="spacer"></div>

<p class="titre">III) [ Réseaux Privés Virtuels ]</p>

<p>Les réseaux privés virtuels, ou VPN, rendent possible la liaison de deux réseaux <b>via un réseau de confiance crypté</b> et ayant un accès limité sécurisé.
En général, les utilisateurs MongoDB qui utilisent des VPNs utilisent <b>SSL</b> plutôt que <b>IPSEC VPNs</b> pour des raisons de performances.

En fonction de leur configuration et de leur implémentation, les VPNs fournissent <b>la validation de certificat</b> et un choix de protocol de cryptage, ce qui nécessite
un niveau d'authentification et d'identification rigoureux <b>pour tous les clients</b>. De plus, vu que les VPNs fournissent un tunnel sécurisé, vous pouvez éviter les attaques
de type <b>"man-in-the-middle"</b> en les utilisant pour contrôler l'accès à votre instance MongoDB.</p> 

<div class="spacer"></div>

<p>Le chapitre sur la <a href="interfaces_api.php">"Sécurité et Interfaces de l'API MongoDB" >></a> va nous apporter des éléments
sur la <b>sécurité de l'API</b> MongoDB ainsi que <b>certaines interfaces</b>.</p>
	
<?php

	include("footer.php");

?>
