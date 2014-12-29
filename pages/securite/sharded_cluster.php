<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Sécurité de Sharded Cluster</li>
</ul>

<p class="titre">[ Sécurité de Sharded Cluster ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#priv">I) Privilèges de Contrôle d'Accès dans les Clusters Fragmentés</a></p>
	<p class="elem"><a href="#acce">II) Accéder à un Cluster Fragmenté avec l'Authentification</a></p>
	<p class="elem"><a href="#rest">III) Restrictions sur l'Interface localhost</a></p>
</div>

<p>Les clusters fragmentés utilisent <b>le même fichier clé</b> et <b>contrôle d'accès</b> que tous les autres déploiements MongoDB. En revanche, il y a d'autres considérations
à prendre en compte lorsque vous utilisez l'authentification avec les clusters fragmentés.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : En addition aux mécanismes décrits dans cette section, exécutez toujours vos clusters fragmentés dans un environnement sécurisé.
	Assurez-vous que le réseau autorise seulement le traffic approprié à atteindre les instances mongod et mongos.
</div>
<a name="priv"></a>

<div class="spacer"></div>

<p class="titre">I) [ Privilèges de Contrôle d'Accès dans les Clusters Fragmentés ]</p>

<p>Dans les clusters fragmentés, MongoDB fournit des <b>privilèges administratifs</b> séparés pour le cluster fragmenté et chaque shard.</p>
<p><b>Authentification de cluster fragmenté</b> : Lorsque vous êtes connectés à une instance mongos, vous pouvez avoir accès à la base de données <b>"admin"</b> du cluster.
Ces identifiants se situent <b>sur les serveurs de configuration</b>. Les identifiants pour les bases de données autre que la base de données <b>"admin"</b> se situent
sur l'instance mongod (ou le cluster) qui est <b>le shard primaire</b> de la base de données.
Les utilisateurs peuvent avoir accès au cluster <b>en fonction de leurs permissions</b>. Pour recevoir des privilèges pour le cluster, vous devez vous identifier
tout en étant connecté à une instance mongos.</p>

<p><b>Authentificaton de Serveur Shard</b> : Pour autoriser les administrateurs à se connecter et s'authentifier <b>directement à un shard spécifique</b>, créez un utilisateur
dans la base de données <b>"admin"</b> sur l'instance mongod, ou le replica set, que fournit chaque shard.
Ces utilisateurs ont accès <b>uniquement à un seul shard</b> et sont complètement distincts des identifiants s'étendant au cluster entier.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Connectez-vous et authentifiez-vous toujourrs aux sharded clusters via une instance mongos.
</div>

<p>Au sein de ces propriétés, les privilèges pour les clusters fragmentés sont <b>les mêmes que n'importe quel autre déploiement</b> MongoDB.</p>
<a name="acce"></a>

<div class="spacer"></div>

<p class="titre">II) [ Accéder à un Cluster Fragmenté avec l'Authentification ]</p>

<p>Pour accéder à un cluster fragmenté en tant qu'utilisateur <b>authentifié</b>, en ligne de commande, utilisez les options d'authentification lorsque vous vous connectez
à une instance mongos. Ou alors vous pouvez d'abord vous connecter et ensuite vous authentifier avec la commande <b>"authenticate"</b> ou la méthode
<b>"db.auth()"</b>.

Pour fermer une session authentifiée, utilisez la commande <b>"logout"</b>.</p>
<a name="rest"></a>

<div class="spacer"></div>

<p class="titre">III) [ Restrictions sur l'Interface localhost ]</p>

<p>Les clusters fragmentés ont des restrictions sur <b>l'interface localhost</b>. Si l'hôte identifié pour une instance MongoDB est soit <b>localhost</b> ou <b>127.0.0.1</b>,
alors vous devez utiliser <b>localhost</b> ou <b>127.0.0.1</b> pour identifier toutes les instances MongoDB de votre déploiement. Cela s'applique à l'argument <b>"host"</b>
de la commande <b>"addShard"</b> aussi bien que l'option <b>"--configdb"</b> pour les instances mongos. Si vous mixez <b>localhost</b> avec une addresse à distance, les clusters
ne vont pas fonctionner correctement.</p>

<div class="spacer"></div>

<p>Passons maintenant à un peu de <b>configuration</b> et de <b>pratique</b> sur la sécurité réseau avec MongoDB.
Direction le chapitre suivant sur <a href="tutoriel_securite_reseau.php">"Tutoriel de Sécurité Réseau" >></a>.
Encore une fois, si vous avez des questions, <a href="../contact.php">"contactez-moi"</a>.</p>
	
<?php

	include("footer.php");

?>
