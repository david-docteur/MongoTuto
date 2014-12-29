<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Diagnostique de Cluster Partagé</li>
</ul>

<p class="titre">[ Diagnostique de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#err">I) Erreur de String de la Base de Données de Configuration</a></p>
	<p class="elem"><a href="#cur">II) Le Curseur a Echoué en Raison de Données de Configuration Obsolètes</a></p>
	<p class="elem"><a href="#evi">III) Eviter le Temps d'Arrêt lors d'un Déplacement des Serveurs de Configuration</a></p>
</div>

<p>Dans cette dernière rubrique du tutoriel sur le Sharding, vous allons discuter des erreurs et petits soucis les plus fréquents que l'on peut rencontrer
avec le sharding.</p>
<a name="err"></a>

<div class="spacer"></div>

<p class="titre">I) [ Erreur de String de la Base de Données de Configuration ]</p>

<p>Démarrez toutes les instances mongos dans un sharded cluster avec une chaîne de caractères configdb identique. Si une instance mongos tente de se connecter
au sharded cluster avec un string configdb qui ne correspond pas exactement aux strings utilisés par les autres instances mongos, en incluant l'ordre des hôtes,
l'erreur suivante apparaît :</p>

<pre>could not initialize sharding on connection</pre>

<p>Et :</p>

<pre>mongos specified a different config database string</pre>

<p>Afin de résoudre cette erreur, redémarrez l'instance mongos avec le bon string.</p> 
<a name="cur"></a>

<div class="spacer"></div>

<p class="titre">II) [ Le Curseur a Echoué en Raison de Données de Configuration Obsolètes ]</p>

<p>Une requête retourne l'avertissement suivant lorsque l'une des instances mongos ou plus n'a pas encore mis à jour son cache des méta-informations du cluster
depuis la base de données de configuration :</p>

<pre>could not initialize cursor across all shards because : stale config detected</pre>

<p>Cet avertissement devrait se répéter jusqu'à ce que toutes les instances mongos aient actualisés leurs caches. Pour forcer une instance à mettre à jour son cache,
utilisez commande flushRouteurConfig.</p>
<a name="evi"></a>

<div class="spacer"></div>

<p class="titre">III) [ Eviter le Temps d'Arrêt lors d'un Déplacement des Serveurs de Configuration ]</p>

<p>Utilisez les CNAMEs pour identifier vos serveurs de configuration du cluster. De cette façon, vous allez pouvoir les renommer ou renuméroter sans devoir les arrêter
et provoquer un downtime.</p>

<div class="spacer"></div>

<p>C'est ici que se termine le chapitre sur le Sharding, pas toujours évident de vous l'accorde mais qui sait prouver son utilité lorsqu'il s'agit de gérer
des informations de masses. En espérant que cela vous a plus. Surtout, n'hésitez pas à me <a href="../contact.php">"Contacter"</a> si vous avez la moindre question.
Passez au chapitre suivant : <a href="../administration.php">"Administration" >></a>.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>