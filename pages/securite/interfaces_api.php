<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Sécurité et Interfaces de l'API MongoDB</li>
</ul>

<p class="titre">[ Sécurité et Interfaces de l'API MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#secu">I) Sécurité de Javascript et du Shell mongo</a></p>
	<p class="right"><a href="#expr">- a) Expression Javascript ou Fichier Javascript</a></p>
	<p class="right"><a href="#fich">- b) Le Fichier .mongorc.js</a></p>
	<p class="elem"><a href="#inter">II) L'Interface de Statut HTTP</a></p>
	<p class="elem"><a href="#rest">III) L'API REST</a></p>
</div>

<p>La section suivante contient des stratégies pour <b>limiter les risques</b> concernant les interfaces disponibles de MongoDB incluant <b>Javascript</b>, <b>HTTP</b> et <b>REST</b>.</p>
<a name="secu"></a>

<div class="spacer"></div>

<p class="titre">I) [ Sécurité de Javascript et du Shell mongo ]</p>

<p>Un peu de <b>Javascript</b> ! Comme vous le savez, le shell mongo <b>implémente Javascript</b>. Il va donc falloir garder un oeil sur <b>certains points</b>.</p>
<a name="expr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Expression Javascript ou Fichier Javascript</p>

<p>Le programme mongo peut <b>évaluer des expressions Javascript</b> en utilisant l'option de ligne de commande <b>"--eval"</b>. Aussi, le programme mongo
peut évaluer un fichier <b>Javascript (.js)</b> passé directement en paramètre (par exemple, "mongo unFichier.js").

Vu que le programme mongo évalue le Javascript <b>sans valider la saisie</b>, cela représente une vulnérabilité.</p>
<a name="fich"></a>

<div class="spacer"></div>

<p class="small-titre">b) Le Fichier .mongorc.js</p>

<p>Si un fichier <b>.mongorc.js</b> existe, le shell mongo va évaluer ce fichier avant de démarrer. Vous pouvez désactiver ce comportement en passant l'option
<b>"--norc"</b> comme ceci :</p>

<div class="small-spacer"></div>

<pre>mongo --norc</pre>
<a name="inter"></a>

<div class="spacer"></div>

<p class="titre">II) [ L'Interface de Statut HTTP ]</p>

<p>L'interface de statut HTTP fournit une <b>interface web</b> qui inclut une variété de données d'opérations, de logs et de rapports concernant les instances mongod
et mongos. L'interface HTTP est toujours accessible <b>sur le port numéroté 1000 de plus que le port du mongod primaire</b> qui est 27017 par défaut, l'interface, elle, sera
donc par défaut 28017 (<b>quel calcul phénoménal hein ?</b>).
Sans le paramètre <b>"rest"</b>, cette interface est entièrement <b>read-only</b> et limitée, en contre-partie, cette interface peut présenter <b>un risque</b>. Pour désactiver
cette interface, définissez l'option <b>"nohttpinterface"</b> à votre instance ou alors en ligne de commande, <b>"--nohttpinterface"</b>.</p>
<a name="rest"></a>

<div class="spacer"></div>

<p class="titre">III) [ L'API REST ]</p>

<p>L'<b>API REST</b> de MongoDB fournit des informations additionnelles et un accès en écriture pour l'interface de statut HTTP. Tandis que l'API REST
ne fournit <b>aucun support pour les opérations Create, Update et Remove</b>, elle fournit un accès administratif, et de ce fait, son accessibilité représente
une vulnérabilité dans un environnement sécurisé. Cette interface REST est <b>désactivée par défaut</b> et n'est pas recommandée pour l'utilisation en production.
Si vous devez utiliser cette API REST, vous devez <b>contrôler</b> et <b>limiter</b> son accès. Celle-ci ne fournit aucun support pour l'authentification non plus, même
si <b>"auth"</b> est activé.</p>

<div class="spacer"></div>

<p>Pour ceux qui ont un <b>cluster fragmenté</b>, le prochain chapitre leur sera utile : <a href="sharded_cluster.php">"Sécurité de Sharded Cluster" >></a>.</p>
	
<?php

	include("footer.php");

?>
