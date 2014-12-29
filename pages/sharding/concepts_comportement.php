<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../sharding.php">Sharding</a></li>
	<li class="active">Comportement de Cluster Partagé</li>
</ul>
<p class="titre">[ Comportement de Cluster Partagé ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#cles">I) Clés de Shard</a></p>
	<p class="right"><a href="#hash">- a) Clés de Shards Hashées</a></p>
	<p class="right"><a href="#impa">- b) Impactes des Clés de Shards sur votre Cluster</a></p>
	<p class="elem"><a href="#hd">II) Haute-Disponibilité d'un Sharded Cluster</a></p>
	<p class="right"><a href="#indi">- a) Indisponibilité des Serveurs d'Application ou des Instances mongos</a></p>
	<p class="right"><a href="#mong">- b) Indisponibilité d'un Simple mongod dans un Shard</a></p>
	<p class="right"><a href="#tous">- c) Indisponibilité de Tous les Membres d'un Replica Set</a></p>
	<p class="right"><a href="#bdd">- d) Indisponibilité de Un ou Deux Bases de Données de Configuration</a></p>
	<p class="right"><a href="#reno">- e) Renommage de Serveur de Configuration et Disponibilité du Cluster</a></p>
	<p class="elem"><a href="#rout">III) Routage de Requêtes d'un Sharded Cluster</a></p>
	<p class="right"><a href="#proc">- a) Processus de Routage</a></p>
	<p class="right"><a href="#dete">- b) Detecter les Connexions aux Instances mongos</a></p>
	<p class="right"><a href="#oper">- c) Opérations de Diffusion et Opération Ciblées</a></p>
	<p class="right"><a href="#donn">- d) Données Shardées et Non-Shardées</a></p>
</div>

<p></p>
<a name="cles"></a>

<div class="spacer"></div>

<p class="titre">I) [ Clés de Shard ]</p>

<p>MongoDB utilise une clé de shard afin de diviser les données d'une Collection sur les différents shard d'un Cluster.
Celle-ci est soit un champ indexé ou alors un indexe sur un champ composé qui existe dans chaque document de la collection.
En effet, MongoDB partitionne les données de la collection en utilisant une portée, une gamme de valeurs de clé de shard. Chaque portée, ou chunk,
définit une portée non chevauchante de valeurs de clé de shard. MongoDB distribue les chunks et leur documents sur les différents shards du Cluster.</p>

[ schéma mongodb ]

<div class="spacer"></div>

<p>Quand un chunk grossis au-délà la taille d'un chunk, MongoDB coupe se chunk en plusieurs petits chunks, petits morceaux, toujours basés sur la portée de valeurs
de la clé de shard.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Les clés de shard sont immutables et ne peuvent donc être modifiées après leur insertion. Veuillez-vous référer au paragraphe
	des limites de sharded cluster.
</div>
<a name="hash"></a>

<div class="spacer"></div>

<p class="small-titre">a) Clés de Shards Hashées</p>

<p>Nouveau dans la version 2.4, les clés de shard hashées utilisent les indexes hashés d'un simple champ en tant que clé de shard afin de partitionner les données
sur votre sharded cluster.
Le champ que vous choisissez comme clé de shard doit avoir une bonne cardinalité ou un nombre large de valeurs. Les clés hashées fonctionnent bien avec
les champs qui s'incrémentent monotonement que les ObjectId ou timestamps.
Si vous shardez une collection vide en utilisant une clé de shard hashée, MongoDB va automatiquement créer et migrer les chunks afin que chaque shard ait deux chunks.
Vous pouvez contrôler le nombre de chunks que MongoDB va créer avec le paramètre numInitialChunks de shardCollection ou en créeant manuellement des chunks
sur la collection vide en utilisant la commande split.</p>

<div class="alert alert-success">
	<u>Astuce</u> : MongoDB calcule les hash automatiquement en utilisant les requêtes se servant d'indexes hashés. Vos applications n'ont pas besoin
	de calucler de hash.
</div>
<a name="impa"></a>

<div class="spacer"></div>

<p class="small-titre">b) Impactes des Clés de Shards sur votre Cluster</p>

<p>La clé de Shard va affecter les performances des requêtes en déterminant comment MongoDB partitionne les données dans le cluster et
comment les instances mongos vont peuvent router efficacement les opérations au cluster. Considérez les impactes suivants :</p>

<p>Echelle d'écriture : Certaines clés de shard vont autoriser votre application à tirer les avantages de l'augmentation de la capacité de lecture que le cluster
peut fournir, alors que les autres ne peuvent pas. Considérons l'exemple suivant ou l'on shard par les valeur du champ _id par défaut, qui est un ObjectId.
MongoDB génère des valeurs ObjectId lors de la création d'un Document afin de produire un identifiant unique pour cet objet. Par contre, les bits de données les plus 
représentatifs dans cette valeur représentent un timestamp, ce qui montre qu'ils s'incrémentent de manière régulière et prédictable. Même si cette valeur a une
haute cardinalité, quand vous l'utilisez, n'importe qu'elle date ou tout autre nombre s'incrémentant de façon monotone en tant que clé de shard, toutes les opérations
d'insertion vont stocker les données dans un seul chunk et donc, un simple shard. En conséquences, la capacité d'écriture de ce shard va définir la bonne
capacité d'écriture du cluster.</p>

<p>Une clé de shard ayant une valeur qui s'incrémente de façon monotone ne va pas gêner les performances si vous avez un taux d'insertion plutôt faible, ou alors si la
plupart de vos opérations d'écritures correspondent à des update() distribuéesà travers tout votre ensemble de données. De manière générale, choisissez vos clés
de shard ayant une forte cardinalité et qui vont distribuer les opérations d'écriture à travers le cluster entier.
Typiquement, une clé de shard calculée étant générée de façon aléatoire, comme celles qui incluent un hash cryptographique (ex : MD5 ou sha1) dans un document,
va autoriser le cluster à avoir des opérations d'écritures plus scalables. En revanche, les clés de shard aléatoire ne fournissent pas l'isolation de requête,
ce qui est une charactéristique importeant pour les clés de shard. Nous allons revenir un peu plus tard sur l'isolation de requêtes.
Nouveau dans la version 2.4, MongoDB permet le sharding de collection sur un indexe hashé. Cela peut grandement augmenter la scalabilité de vos écritures.</p>

<p>Requêtage : Les instances mongos fournissent une interface qui masque la complexité du partitionnement des données pour les applications qui intéreagissent
avec des sharded clusters. Une instance mongos reçoit des requêtes depuis vos applications, et utilise les méta-information des serveurs de configuration afin
de router les requêtes vers l'instance mongod comportant les données appropriées. Pendant que tous les mongos ont le rôle de mener le requêtage dans les environnements
shardés, la clé de shard que vous allez sélectionner peut sérieusement affecter les performances de vos requêtes.</p>

<p>Isolation de requête : Les requêtes les plus rapides dans un environnement shardé sont celles que que l'instance mongos va router vers un seul shard en utilisant
la clé de shard ainsi que la configuration du cluster définie par les méta-informations contenues par les serveurs de configuration. Pour les requêtes qui n'incluent
pas la clé de shard, mongos doit interroger tous les shard, attendre leur réponse et ensuite retourner le résultat de à l'application. Ces requêtes appelées
"scatter/gather" (pour disperser/rassembler) peuvent parfois être longues.
De plus, si votre requête inclut le premier composant d'une clé de shard composée (on peut penser la clé de shard comme un indexe sur le cluster tout entier),
les mongos peuvent router immédiatement à un simple shard , ou alors un nombre réduit de shards, ce qui apporte de meilleures performances. Même si vous interroger
des valeurs d'une clé de shard qui résident dans différents chunks, les mongos vont router les requêtes directemet aux shards spécifiques.
Afin de sélectionner une clé de shard pour une collection : 
- déterminez les champs les plus inclus dans le requêtes de votre application
- trouvez lesquelles de ces opérations sont le plus dépendantes des performances

Si ce champ a une faible cardinalité (par exemple : pas suffisament utilisé dans vos requêtes), vous devriez ajouter un second champ à la clé de shard,
ce qui la rend composée. Les données devraient devenir plus séparables avec une clé de shard composée.</p>

<p>Le tri : Dans les systèmes shardés, les instances mongos effectuent un étape de tri de tous les résultats triés de requêtes depuis vos shards.</p>
<a name="hd"></a>

<div class="spacer"></div>

<p class="titre">II) [ Haute-Disponibilité d'un Sharded Cluster ]</p>

<p>Un Cluster de production ne peut pas échouer en un seul point (une seule machine). Nous allons discuter dans cette section de la disponibilité des données
concernant les déploiements MongoDB en général et mettre en avant les possibles situtations d'échecs, ainsi que leur résolutions.</p> 
<a name="indi"></a>

<div class="spacer"></div>

<p class="small-titre">a) Indisponibilité des Serveurs d'Application ou des Instances mongos</p>

<p>Si chaque serveur d'application a sa propre instance mongos, les autres serveurs d'application peuvent continuer d'accéder à la base de données.
De plus, les instances mongos peuvent redémarrer et devenir indisponible sans perdre quelconque donnée. Quand une instance mongos démarre, elle récupère une copie
du serveur de configuration et peut commencer à router les requêtes.</p>
<a name="mong"></a>

<div class="spacer"></div>

<p class="small-titre">b) Indisponibilité d'un Simple mongod dans un Shard</p>

<p>Les Replica Sets permettent aux shards d'être hautement accessibles. Si le mongod indisponible est primaire, alors le Replica Set va élire un nouveau primaire.
Si le mongod indisponible est un secondaire et qu'il se déconnecte, le primaire et le secondaire vont continuer de maintenir toutes les données. Dans un Replica
Set à trois membres, même si un des membres de l'ensemble rencontre une catastrophe, deux des autres membres ont une copie complète des données.
Faîtes toujours attention aux interruption de disponibilité ou aux situations de d'échec. Si un système ne peut pas du tout se restaurer, remplacez-le
et créez un nouveau membre du Replica Set aussi vite que possible afin de remplacer la redondance perdue.</p>
<a name="tous"></a>

<div class="spacer"></div>

<p class="small-titre">c) Indisponibilité de Tous les Membres d'un Replica Set</p>

<p>Si tous les membres d'un Replica Set à l'intérieur d'un Shard deviennent indisponibles, toutes les données détenues par ce shard deviennent indisponibles.
En revanche, toutes les autres données des autres shards vont rester disponibles, et il est possible de lire et d'écrire des données sur les autres shards.
En revanche, votre application doit être capable de coopérer avec des résultats partiels, et vous devrez d'identifier la cause de l'interruption ainsi que
d'essayer de remettre le shard en bon état de fonctionnement.</p>
<a name="bdd"></a>

<div class="spacer"></div>

<p class="small-titre">d) Indisponibilité de Un ou Deux Bases de Données de Configuration</p>

<p>Trois instances mongod bien distinctes fournissent la base de données de configuration en utilisant un commit à deux phases afin de maintenant un état consistent
entres ces instances mongod. Les opérations du Cluster vont continuer normalement sauf la migration de chunks ainsi que la création de nouveaux chunks. Remplacez
le Serveur de Configuration aussi vite que possible. Si tous les Serveurs de configuration deviennent indisponibles, le Cluster devient inutile.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Tous les serveurs de configuration doivent être en cours d'exécution et disponibles lorsque vous
	instanciez un sharded cluster.
</div>
<a name="reno"></a>

<div class="spacer"></div>

<p class="small-titre">e) Renommage de Serveur de Configuration et Disponibilité du Cluster</p>

<p>Si le nom ou l'adresse qu'un sharded cluster utilise, afin de se connecter à un serveur de configuration, change, vous devrez redémarrer toutes les instances
mongod et mongos se trouvant dans votre sharded cluster. Evitez le temps d'arrêt en utilisant CNAMEs afin d'identifier les serveurs de configuration sous votre
déploiement MongoDB.</p>
<a name="rout"></a>

<div class="spacer"></div>

<p class="titre">III) [ Routage de Requêtes d'un Sharded Cluster ]</p>

<p>Ici, nous allons parler de comment MongoDB route/redirige les différentes opérations de lectures et d'écritures aux shards spécifiques.
Les instances mongos fournit l'interface du sharded cluster à l'application. L'application ne se connecte jamais ou ne communique jamais directement avec 
les shards. Les mongos traquent quelles données sont sur quel shard en métant en cache les méta-informations des serveurs de configuration. Les mongos
utilisent les méta-informations afin de router les opérations des applications et des clients vers les instances mongod. Une instance mongos
consume peu de ressources système. La pratique la plus courante est d'exécuter les instances mongos sur les mêmes systèmes que ceux de vos serveurs d'application,
mais vous pouvez maintenir les mongos sur les shards ou alors sur des ressources dédiées.
Depuis la version 2.1, certaines opérations d'aggrégation utilisant la commande d'aggrégation : db.collection.aggregate(), vont créer une plus forte demande
en ressources CPU pour les instancs mongos que dans les versions précédentes de MongoDB. Ce changement de besoin en ressources pourrait changer votre type
d'architecture si vous utilisez activement le framework d'aggrégation.</p>
<a name="proc"></a>

<div class="spacer"></div>

<p class="small-titre">a) Processus de Routage</p>

<p>Une instance mongos utilise les processus suivants afin de router les requêtes et de retourner les résultats.

Comment mongos détermine quels shards reçoivent une requête : Une instance mongos route une requête à un cluster en : 
1) Déterminant la liste de shards qui doivent recevoir la requête
2) Etablir un curseur sur tous les shards ciblés

Dans certains cas, quand la clé de shard ou un préfix de la clé de shard est une partie de la requête, le mongos peut router la requête à un sous-ensemble des shards.
Sinon, le mongos doit impérativement rediriger la requête à tous les shards qui contiennent des documents pour cette collection.</p>

<p>Par exemple, prenons la clé de shard suivante :</p>

<pre>{ zipcode: 1, u_id: 1, c_date: 1 }</pre>

<div class="spacer"></div>

<p>En fonction de la distribution des chunks sur votre cluster, le mongos devrait être capable d'envoyer la requête à un sous-ensemble de shards, 
si la requête contient les champs suivants :</p>

<pre>
{ zipcode: 1 }
{ zipcode: 1, u_id: 1 }
{ zipcode: 1, u_id: 1, c_date: 1 }
</pre>

<div class="spacer"></div>

<p>Comment le mongos contrôle les modifieurs de requête : Si le résultat de la requête n'est pas trié, l'instance mongos ouvre un curseur qui tri
les résultats de tous ls curseurs situés sur les shards.
Si la requête spécifié un ordre de tri en utilisant la commande sort() du curseur, l'instance mongos ajoute l'option $orderby aux shards. Quand le mongos reçoit
les résultats, celui-ci effectue un tri de regroupement des résultats pendant qu'ils sont retournés au client.
Si la requête limite la taille de l'ensemble de résultats en utilisant la méthode limit() du curseur, le mongos passe cett limitation aux shards et
re-applique la limite au résultat avant de retourner les résultats au client.
Si la requête spécifie un nombre de résultat à ignorer en utilisant skip(), le mongos ne peut pas passer le skip aux shards, mais récupère plutôt les résultats
non ignorés depuis les shards et les ignore avant de construire le résultat final. Par contre, quand vous utilisez cette méthode en parrallèle avec 
avec une limit(), le mongos va passer la limite additionnée à la valeur du skip() aux shards afin d'améliorer l'efficacité de ces opérations.</p>
<a name="dete"></a>

<div class="spacer"></div>

<p class="small-titre">b) Detecter les Connexions aux Instances mongos</p>

<p>Pour savoir si l'instance MongoDB à laquelle votre client est connecté est un mongos, utilisez la commande isMaster(). Quand un client se connecte à un mongos,
isMaster retourne un document avec un champ "msg" qui a pour valeur un string "isdbgrid" :</p>

<pre>
{
	"ismaster" : true,
	"msg" : "isdbgrid",
	"maxBsonObjectSize" : 16777216,
	"ok" : 1
}
</pre>

<p>Si par contre, l'application est connectée à un mongod, le document retourné ne contient pas la valeur "isdbgrid".</p>
<a name="oper"></a>

<div class="spacer"></div>

<p class="small-titre">c) Opérations de Diffusion et Opération Ciblées</p>

<p>En général, les opérations au sein d'un environnement shardé sont soit : 
- Diffusées à tous les shards du cluster qui comportent des documents dans la collection
- ciblé vers un seul shard ou un groupe de shards limité , basé sur la clé de shard

Pour de meilleures performances, utilisez les opérations ciblées dès que possible. Alors que certaines opérations doivent être diffusées sur tous les shards,
vous pouvez vous assurez que MongoDB utilise des opérations ciblées dès que possible en incluant toujours la clé de shard.</p>

<p>Opération de broadcast : les instances mongos diffusent les requêtes à tous les shards pour la collection jusqu'à ce que les mongos puissent
déterminer quel shard ou sous-ensemble de shards stockent ces données :</<p>

[ schéma mongodb ]

<div class="spacer"></div>

<p>Les multi-updates sont toujours des opérations diffusées. L'opération remove() est toujours diffusée également jusqu'à ce que l'opération spécifie pleinement
la clé de shard.</p>

<p>Opérations ciblées : Toutes les opérations de type insert() sont ciblées vers un seul shard. Toutes les simples update() (incluant les opérations upsert) et remove()
doivent impérativement cibler un shard.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Chaque opération de type update() ou remove() doit inclure la clé de shard ou le champ _id dans la spécification de la requête.
	Les opérations update() ou remove() qui affectent un document dans une collection shardée sans la clé de shard ou le champ _id retournent une erreur.
</div>
<div class="spacer"></div>

<p>Pour les requêtes qui incluent la clé de shard ou une portion de celle-ci, les mongos peuvent cibler la requête vers un shard spécifique ou un sous-ensemble
de shards. C'est le cas uniquement si la portion de la clé de shard inclue dans la requête est un préfixe de la clé de shard. Par exemple, si la clé de shard est :</p>

<pre>{ a: 1, b: 1, c: 1 }</pre>

<p>Le programme mongos peut router les requêtes qui incluent la clé de shard entière ou les préfixes suivants à un shard spécifique ou un sous-ensemble de shards :</p>

<pre>
{ a: 1 }
{ a: 1, b: 1 }
</pre>

<div class="spacer"></div>

[schéma mongodb]

<div class="spacer"></div>

<p>En fonction de la distribution des données dans le cluster et la sélectivité de la requête, le mongos devra encore avoir à contacter plusieurs shards afin d'exécuter
ces requêtes.</p>
<a name="donn"></a>

<div class="spacer"></div>

<p class="small-titre">d) Données Shardées et Non-Shardées</p>

<p>Le Sharding opère au niveau d'une collection. Vous pouvez sharder plusieurs collections d'une base de données ou avoir plusieurs bases de données avec le Sharding
activé. En revanche, en production, certaines bases de données et collections vont utiliser le sharding, alors que d'autres bases de données et collections vont
être stockées sur un seul shard uniquement.</p>

[schéma mongodb]

<div class="spacer"></div>

<p>Sans regarder l'architecture des données de votre sharded cluster, assurez-vous que toutes les requêtes et opérations utilisent le routeur mongos 
pour accéder aux données du cluster. Utilisez le mongos même pour les opérations qui n'ont pas d'impacte sur les données shardées.</p>

<div class="spacer"></div>

<p>La suite va concerner les <a href="concepts_mecaniques.php">"mécaniques d'un Sharded Cluster" >></a> ainsi que les étapes fondementales de celui-ci.</p>

<?php

	include("footer.php");

?>