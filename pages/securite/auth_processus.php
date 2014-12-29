<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Authentification Inter-Processus</li>
</ul>

<p class="titre">[ Authentification Inter-Processus ]</p>

<p>Dans la plupart des cas, les administrateurs des <b>replica sets</b> et des <b>clusters fragmentés</b> n'ont pas à garder en tête d'autres considérations au-délà
des précautions normales de sécurité que tous les adminisrateurs MongoDB doivent prendre en compte. En revanche, assurez-vous que :</p>

<ul>
	<li><b>la configuration de votre réseau va autoriser chaque membre du replica set à pouvoir contacter tous les autres membres du même ensemble.</b></li>
	<li><b>si vous utilisez le système d'authentification MongoDB pour limiter l'accès à votre infrastructure, assurrez-vous de configurer un "keyFile" sur tous les membres
	pour permettre l'authentification.</b></li>
</ul>

<div class="small-spacer"></div>

Pour la plupart des instances, les moyens <b>les plus efficaces</b> de contrôler l'accès et de sécuriser les connexions entre les membres du replica set dépendent
de l'accès à votre réseau. Utilisez le firewall de votre environnement et votre routage réseau pour vous assurer que le traffic des clients et des autres membres, du replica set seulement, peuvent <b>atteindre vos instances mongod</b>.
Si besoin, utilisez un <b>réseau privé virtuel (VPN)</b> pour garantir des connexions sécurisées au sein de <b>réseaux larges (WAN)</b>.

<div class="spacer"></div>

<p class="titre">[ Activer l'Authentification dans les Replica Sets et les Sharded Clusters ]</p>

<p>Le support pour l'authentification dans déploiement de type replica sets a été ajouté <b>dans la version 1.8</b>.
Puis, ce même support a été ajouté pour les déploiements de type <b>replica set fragmenté</b> dans la version 1.9.1.
MongoDB fournit un mécanisme d'authentification pour les instances <b>mongod</b> et <b>mongos</b> qui se connectent à des replica sets. Ces instances
activent l'authentification mais spécifient <b>un fichier clé partagé</b> qui sert de <b>mot de passe partagé</b>. 
Pour activer l'authentification, <b>ajoutez l'option suivante</b> à votre fichier de configuration :</p>

<div class="small-spacer"></div>

<pre>keyFile = /srv/mongodb/keyfile</pre>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Vous voudrez probablement définir ces options de configuration en utilisant les options "--keyFile" (ou "mongos --keyFile")
	en ligne de commande.
</div>

<p>Définir un <b>"keyFile"</b> active l'authentification et spécifie un <b>fichier clé</b> que les membres du replica set vont utiliser lorsqu'ils vont se connecter entre eux.
Le contenu du fichier clé est arbitraire mais <b>doit être le même</b> sur tous les membres du replica set et sur toutes les instances mongos qui se connectent
à cet ensemble.

Le fichier clé doit contenir <b>entre 6 et 1024 caractères</b> et doit contenir des caractères qui respectent <b>l'encodage base64</b>. Celui-ci ne doit pas non plus
avoir de <b>permission "group" ou "world"</b> sur les systèmes UNIX.</p>  

<div class="spacer"></div>

<p>Qu'en est-il de <b>l'exposition sur le réseau</b> ? C'est ce que nous allons voir maintenant dans le chapitre sur la <a href="exposition_reseau.php">"Sécurité et Exposition du Réseau" >></a> sous MongoDB.</p>
	
<?php

	include("footer.php");

?>
