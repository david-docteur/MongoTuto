<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Architectures de Cluster Partagé</li>
</ul>

<p class="titre">[ Architectures de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#shar">I) Besoins de Sharded Cluster</a></p>
	<p class="right"><a href="#bes">- a) Besoins pour la Quantité de Données</a></p>
	<p class="elem"><a href="#prod">II) Architecture d'un Cluster de Production</a></p>
	<p class="elem"><a href="#test">III) Architecture d'un Cluster de Test</a></p>
</div>

<p></p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="titre">I) [ Besoins de Sharded Cluster ]</p>

<p>Alors que le Sharding est une fonctionnalité puissante de MongoDB, les Sharded Clusters recquierent une infrastructure conséquente mais augmente aussi la complexité
d'un déploiement. En déduction, ne déployez seulement des Sharded Clusters que lorsque votre application et vos opérations en ont besoin.

Le Sharding est la seule et l'unique solution pour certains types de déploiements, utilisez-les si :

- votre ensemble de données approche ou excède la capacité de stockage d'une seule instance mongod
- la taille de votre espace de travail de votre système va bientôt dépasser la capacité en RAM que votre système détient
- une simple instance mongodb ne peut pas faire face à votre demande en opérations d'écritures, on imagine bien qu'un ticket de concert par exemple qui est
  absolument complet en 9 secondes (trouver un exemple), ça doit demander beaucoup de ressources !.
</p>

<p>Si ces attributs ne sont pas présents dans votre système, le Sharding va uniquement ajouter de la compléxité à votre système sans vraiment ajouter
quelconques bénéfices.</p>

<div class="spacer"></div>
<div class="alert alert-danger">
	<u>Attention</u> : Cela prend du temps et des ressources afin de déployer un Sharding. Si vous système a déjà atteind ou excédé sa capacité,
	cela va être difficile de déployer un Sharding sans avoir un minimum d'impact sur votre application. En conséquences, si vous pensez que vous aurez besoin de
	partitioner votre base de données dans le futur, n'attendez surtout pas que votre système soit complètement débordé pour activer le Sharding.
</div>

<div class="spacer"></div>

<p>Lorsque vous concevez votre modèle de données, prenez déjà le Sharding en considération.</p>
<a name="bes"></a>

<div class="spacer"></div>

<p class="small-titre">a) Besoins pour la Quantité de Données</p>

<p>Votre Cluster devrait gérer une énorme quantité de données pour que le Sharding ait assez d'effets. La taille par défaut d'un chunk est de 64mo et le Balanceur
ne va pas bouger de données à travers les différents shards avant que l'imbalance des chunks sur les shards dépasse le seuil de migration dont nous allons
parler un peu plus tard. En pratique, tant que votre Cluster n'a pas des centaines de megaoctets de données, vos données vous rester sur un seul et même shard.
Dans certaines situations, vous aurez peut-être besoin de sharder une collection ayant peu de données. Mais pour la plupart du temps, sharder une petite collection
ne vaux pas la complexité qui s'ajoute au déploiement et tant que vous n'aurez pas besoin de plus de ressources pour les besoins en écriture.
Si vous avez un petit ensemble de données, une simple instance MongoDB bien configurée ou alors un Replica Set seront bien souvent suffisants pour ce que
vous avez à faire.
La taille d'un Chunk est configurable par l'utilisateur. Pour la plupart des déploiements, la valeur de 64mo par défaut est idéale.</p>
<a name="prod"></a>

<div class="spacer"></div>

<p class="titre">II) [ Architecture d'un Cluster de Production ]</p>

<p>Dans un Cluster de production, vous devez vous assurer de la redondance des données et que vos systèmes soient hautement disponibles et accessibles.
Une fois que vous êtes sûrs de cela, un Cluster de production a les critères suivants :

- Trois serveurs de configuration. Chaque serveur doit être sur sa propre machine individuelle. Un simple sharded Cluster doit avoir l'utilisation exclusive
  de ses serveurs de configuration. Si vous avez plusieurs clusters, vous aurez besoin d'un groupe de serveurs de configuration pour chaque Cluster.
- Deux Replica Sets ou plus. Ces Replica Sets sont le shards.
- Une instance mongos ou plus. mongos est le routeur du cluster. En général, les déploiements ont une instance mongos sur chaque serveur d'application. Vous
  aurez sûrement besoin aussi de déployer un groupe d'instances mongos et utiliser un proxy/load balancer entre votre application et les mongos.</p>
  
[ schéma mongodb ]
<a name="test"></a>

<div class="spacer"></div>

<p class="titre">III) [ Architecture d'un Cluster de Test ]</p>

<div class="alert alert-danger">
	<u>Attention</u> : N'utilisez cette architecture uniquement pour du test et/ou du développement, jamais de la production.
</div>

<div class="spacer"></div>

<p>Pour du test ou du développement, selon vos besoins, vous pouvez déployer un sharded cluster minimal. Ces clusters de tests ont les composants suivants :

- un seul serveur de configuration
- au moins un shard, en sachant que les shards sont soient un Replica Set soit des instances mongod standalone
- une seule instance mongos</p>

<div class="spacer"></div>

[ schéma mongodb ]

<div class="spacer"></div>

<p>La suite va concerner le <a href="concepts_comportement.php">comportement d'un Sharded Cluster >></a> ainsi que les étapes fondementales de celui-ci.</p>

<?php

	include("footer.php");

?>