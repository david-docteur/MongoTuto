<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Introduction au Sharding</li>
</ul>

<p class="titre">[ Introduction au Sharding ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#obj">I) Objectifs du Sharding</a></p>
	<p class="elem"><a href="#shar">II) Sharding Avec MongoDB</a></p>
	<p class="elem"><a href="#part">III) Partitionnement des Données</a></p>
	<p class="right"><a href="#cles">- a) Clés de Shard</a></p>
	<p class="right"><a href="#rbs">- b) Range Based Sharding</a></p>
	<p class="right"><a href="#parb">- c) Partitionnement Basé Hash</a></p>
	<p class="right"><a href="#dist">- d) Distinctions de Performances entre le Partitionnement basé Range et basé Hash</a></p>
	<p class="elem"><a href="#main">IV) Maintenir une Distribution des Données Equilibrée</a></p>
	<p class="right"><a href="#sepa">- a) Séparation</a></p>
	<p class="right"><a href="#bala">- b) Le Balancing</a></p>
	<p class="right"><a href="#ajou">- c) Ajout et Suppression de Shard dans le Cluster</a></p>
</div>

<p>Cette partie du tutoriel va introduire le concept de Sharding, ou fragmentation des données, en insistant sur les aspects de scalabilité horizontale, de
partitionnement des informations ainsi que de Clusters partagés avec MongoDB.
Le Sharding est une méthode pour stocker des données à travers plusieurs machines. MongoDB utilise le Sharding afin d'être préparé et de contrôller
les ensembles très larges de données ainsi que les débits faramineux (rappelez-vous l'éthymologie de MongoDB dans l'introduction) de lecture et écriture.</p>
<a name="obj"></a>

<div class="spacer"></div>

<p class="titre">I) [ Objectifs du Sharding ]</p>

<p>Les systèmes de bases de données avec de larges ensembles de données ainsi que de gros débits en lecture et en écriture peuvent défier la puissance d'un seul
serveur, aussi peformant soit-il. Les requêtes gourmandes et multiples peuvent fatiguer la capacité des CPU d'un serveur. De plus, les larges ensembles de données
peuvent excéder les capacités de stockage. Mais encore, travailler avec des ensembles qui excèdent la capacité de la RAM stressent les capacités
d'entrée/sortie des disques durs.
Afin de répondre à ce genre de besoin, les systèmes de bases de données ont deux approches : la scalabilité verticale et le Sharding, que nous allons décrire de suite.</p>

<p>La scalabilité verticale consiste à ajouter plus de processeurs ainsi que de ressources de stockage pour augmenter les capacités. Mais tout cela a des limites :
les systèmes ultra-performants ayant de nombreux CPUs et un nombre conséquent de mémoire RAM sont vraiment beaucoup plus onéreux que de plus petits systèmes.
De plus, les fournisseurs basés dans le cloud autorisent en général les utilisateurs à ne gérer que de petites instances. Par rapport à ces multiples raisons,
il y a une limites à la scalabilité verticale.
Le Sharding, ou la scalabilité horizontale, divise l'ensemble de données et le distribue sur de multiples serveurs, ou plus communéments appelés shards.
Chaque shard est une base de données indépendante, et collectivement, les shars forment une seule base de données logique.</p>

<div class="spacer"></div>

[ schéma mongodb ]

<div class="spacer"></div>

<p>Le Sharding répond au besoin de scalabilité afin de supporter de gros débits et d'énormes ensembles de données :
- Le Sharding réduit le nombre d'opérations que chaque shard doit gérer. Plus le Cluster grossit, moins les shards doivent individuellement gérer d'informations.
  En conclusion, les clusters partagés peuvent augmenter la capacité et les débit de façon horizontale.
  Par exemple, pour insérer des données, l'application a besoin d'accéder uniquement qu'au shard responsable de ces enregistrements.
- Le Sharding réduit aussi la quantité de données que chaque serveur doit stocker. Plus le cluster grossit, moins chaque serveur est chargé.
  Par exemple, si une base de données doit gérer 1to de données, et qu'il y a 4 shards, alors chaque shard doit gérer 256go de données. 
  S'il y a 40 shards, alors chaque shard devra gérer 25go.
</p>
<a name="shar"></a>

<div class="spacer"></div>

<p class="titre">II) [ Sharding avec MongoDB ]</p>

[ schéma mongodb ]

<div class="spacer"></div>

<p>Les Clusters partagés ont les composants suivants : les shards, les routeurs de requêtes et les configurations de serveurs.
Les Shards contiennent les données. Afin de fournir une haute accessibilité des données au sein d'un cluster partagé de production, chaque shard est un Replica Set.
Les Routeurs de Requêtes, ou instances mongos, opèrent avec les applications clientes et redirige les opérations au(x) shard(s) approprié(s).
Le Routeur de Requêtes traite et dirige les informations aux bons shards et termine en retournant un résultat aux clients. Un Cluster partagé peut contenir
plus d'un Routeur de Requêtes afin de diviser le charge de requêtes clientes. Un client envoie ses demandes à un routeur de requêtes. La plupart des clusters
partagés ont plusieurs routeurs de requêtes.
Les configurations de serveurs stockent les metainformations du cluster. Ces données contiennent une représentation de l'ensemble de données du Cluster
et comment celui-ci est répartit à travers les shards. Le routeur de requête utilise ces informations afin de rediriger les informations à un shard spécifique.
Les Clusters Partagés de production ont exactement 3 configurations de serveurs.</p> 

<div class="alert alert-danger">
	Attention : Pour des environnements de tests ou de développement uniquement, chaque shard peut être une instance mongod plutôt qu'un Replica Set.
	Ne jamais déployer des Clusters de production sans trois configurations de serveurs.
</div>
<a name="part"></a>

<div class="spacer"></div>

<p class="titre">III) [ Partionnement des Données ]</p>

<p>MongoDB distribue les données. Ces données sont partitionnées par le processus de Sharding avc une clé de Shard.</p>
<a name="cles"></a>

<div class="spacer"></div>

<p class="small-titre">a) Clés de Shard</p>

<p>Pour fragmenter une collection, vous devez sélectionner une clé de shard. Une clé de shard est soit un champ indexé ou un champ composé indexé qui existe dans
chaque document de la collection. MongodB divise les valeurs de la clé de shard en fragments et distribue ces fragments à travers les différents shards.
Pour diviser les valeurs de la clé de shard en fragments, MongoDB utilise soit le partitionnement ranged based et le partionnement basé hash.</p>
<a name="rbs"></a>

<div class="spacer"></div>

<p class="small-titre">b) Range Based Sharding</p>

<p>Pour du rangd-based sharding, MongoDB divise l'ensemble de données en ranges déterminés par par les valeurs de la clé de shard afin de fournir du partitionnement
de données ranged based. Considérons une clé de partage numérique: si vous visualisez une ligne nombre qui va de l'infinie négative à l'infinie positive, chaque valeur
de la clé de shard se situe sur cette ligne. MongoDB partitionne cette ligne en plus petits ranges, appelés morceaux (chunks) qui ne se chevauchent pas, ou un morceau
est un range de valeurs d'un valeur minimale à une valeur maximale.
Prenons un système de partitionnement basé range, les documents avec des valeurs proches de clé de shard auront plus de chance d'être dans le même chunk et bien sûr,
dans le même shard.</p>

[schéma mongodb] 
<a name="parb"></a>

<div class="spacer"></div>

<p class="small-titre">c) Partionnement Basé Hash</p>

<p>Pour du partitionnement basé hash, MongoDB calcule un hash de la valeur d'un champ, et ensuite utilise ces hash afin de créer des morceaux.
Avec du partitionnement basé hash, deux documents avec des valeurs proches de clé de shard auront moins de chance de se trouver sur le même morceau.
Cela assure une meilleure distribution aléatoire d'une collection sur le cluster.</p>
<a name="dist"></a>

<div class="spacer"></div>

<p class="small-titre">d) Distinctions de Performances entre le Partitionnement basé Range et basé Hash</p>

<p>Le partitionnement basé range supporte des requêtes basées range. Ayant une requête range sur la clé de shard, le routeur de requête peut facilemet déterminer
quel morceau chevauche ce range et route la requêtes à ces shards uniquement qui contiennent ces morceaux.
Par contre, le partitionnement basé range peut résulter en une distribution impaire des données, ce qui ferait perdre certains avantages du sharding.
Par exemple, si la clé de shard est un champ s'incrémentant de façon linéaire, comme l'heure, alors toutes les requêtes pour un range de temps donné va s'associer
au même chunk, et donc au même shard. Dans cette situation, un petit ensemble de shards va recevoir la majorité des requêtes et le système ne serait pas
vraiment scalable.</p>

[schéma mongodb]

<div class="spacer"></div>

<p>Le partitionnement basé hash assure une distribution paire des données. Les valeurs de clé hashées causent une distribution aléatoire des données
à travers les chunks et donc les shards.</p>
<a name="main"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Maintenir une Distribution des Données Equilibrée ]</p>

<p>L'ajout de nouvelles données ou de nouveaux serveurs peuvent causer une mauvaise balance de distribution des données au sein de votre Cluster, 
comme certains shards qui vont contenir beaucoup plus de chunks que d'autres, ou alors qu'un shard ait une taille beaucoup plus importante que tous les autres.
MongoDB s'assure qu'un Cluster ait une balance adéquate en utilisant deux processus d'arrière-plan : la séparation et le balanceur.
Regardons-ça d'un peu plus près.</p> 
<a name="sepa"></a>

<div class="spacer"></div>

<p class="small-titre">a) Séparation</p>

<p>La séparation, ou splitting, est un processus de fond qui empêche les chunks de grossir de trop. Quand un chunk dépasse une certaine taille (que nous verrons un peu plus tard),
MongoDB sépare le chunk en deux. Les INSERT et les UPDATES déclenchent des séparations. Pour créer des séparations, MongoDB ne migre pas de données
et n'affecte pas les shards.</p>

[schéma mongodb]
<a name="bala"></a>

<div class="spacer"></div>

<p class="small-titre">b) Le Balancing</p>

<p>Le Balanceur, ou Balancer, est un processus d'arrière-plan tout comme le processus de séparation, qui gère les migrations de chunks. Le Balanceur
s'exécute au sein de tous les Routeurs de Requêtes dans un Cluster.
Quand la distribution d'une collection shardée, au sein d'un Cluster, est impaire, le Balanceur migre les chunks depuis les shards les plus gros vers les
shards ayant le moins de chunks. Par exemple, si la collection "users" a 100 chunks sur shard 1 et 50 chunks sur shard 2, le Balanceur va migrer
des chunks de shard 1 vers shard 2 jusqu'à ce que la collection ait une balance correcte.</p>

<p>Les shards gèrent la migration de chunks comme une opération en arrière-plan. Pendant une migration, toutes les requêtes concernant des chunks de données,
indiquent le shard d'origine.
Lors d'une migration de chunk, le shard de destination reçoit tous les documents dans un chunk depuis le chunk de départ. Ensuite, le shard de destination
capture et applique tous les changements réalisés sur les données pendant le processus de migration.
Enfin, le shard de destination met à jour les meta-informations en fonction de la location du serveur de configuration.
S'il y a une erreur durant le processus de migration, le Balanceur arrête le processus en laissant le chunk sur son shard d'origine. MongoDB supprime
les chunks de données depuis le shard d'origine une fois le processus de migration terminé avec succès.</p>

[ schéma mongodb ]
<a name="ajou"></a>

<div class="spacer"></div>

<p class="small-titre">c) Ajout et Suppression de Shard dans le Cluster</p>

<p>Ajouter un shard à votre Cluster va créer un déséquilibre vu que ce nouveau shard n'a aucun chunks. Pendant que MongoDB commence la migration des données
sur un nouveau shard, cela peut prendre du temps avant que le Cluster balance correctement les données.
En supprimant un shard, le Balanceur migre tous les chunks de données vers les autres shards existants. Après avoir migré et mis à jour
toutes les méta-informations, vous pouvez facilement supprimer le shard.</p>

<div class="spacer"></div>

<p>Okay, maintenant passons aux différents concepts du Sharding de Collections avec MongoDB en commençant par les <a href="concepts_composants.php">Composants d'un Cluster Shardé >></a>.

<div class="spacer"></div>

<?php

	include("footer.php");

?>