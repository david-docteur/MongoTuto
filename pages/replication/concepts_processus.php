<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Processus de Réplication</li>
</ul>

<p class="titre">[ Processus de Réplication ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#oplo">I) Oplog du Replica Set</a></p>
	<p class="right"><a href="#tail">- a) Taille de l'Oplog</a></p>
	<p class="right"><a href="#cg">- b) Oplog de Grande Taille</a></p>
	<p class="right"><a href="#stat">- c) Statut de l'Oplog</a></p>
	<p class="elem"><a href="#sync">II) Synchronisation des Données du Replica Set</a></p>
	<p class="right"><a href="#init">- a) Synchronisation Initiale</a></p>
	<p class="right"><a href="#repl">- b) Réplication</a></p>
	<p class="right"><a href="#cohe">- c) Cohérence et Durabilité</a></p>
	<p class="right"><a href="#mt">- d) Réplication Multi-Threadée</a></p>
	<p class="right"><a href="#ps">- e) Pré-Sélection des Indexes Pour Améliorer le Débit de Réplication</a></p>
</div>

<p>Les membres du replica set <b>répliquent continuellement les données</b> depuis le membre primaire. Au début, un membre effectue <b>une synchronisation initiale</b>
afin de <b>capturer l'ensemble de données</b>. Ensuite, le membre <b>enregistre et applique continuellement les opérations</b> qui modifient l'ensemble des données.
Chaque membre enregistre <b>ses opérations dans son oplog</b>, qui est une <b>collection plafonnée (ou Capped)</b>.</p> 
<a name="oplo"></a>

<div class="spacer"></div>

<p class="titre">I) [ Oplog du Replica Set ]</p>

<p>L'oplog sert à <b>enregistrer toutes les opérations qui modifient l'ensemble des données</b>.
L'oplog <b>(operations logs)</b> est une <b>collection plafonnée spéciale</b> qui va garder un enregistrement continu de <b>toutes les opérations qui modifient les données
stockées</b> dans vos bases de données. MongoDB applique les opérations via le membre primaire et ensuite enregistre chaque opération sur l'oplog du membre
primaire. Ensuite, les seconds membres <b>copient et appliquent ces opérations de façon asynchrone</b>. Tous les membres du replica set contiennent une copie
de l'oplog, <b>leur permettant de maintenir l'état actuel de la base de données</b>.
Afin de faciliter la réplication, tous les membres du replica set <b>envoient des pings à tous les autres membres</b>. Nimporte quel membre peut importer des informations
de l'oplog <b>depuis n'importe quel autre membre</b>.
Qu'elle soit appliquée une fois ou plusieurs sur l'ensemble des données, une opération dans l'oplog <b>produit les même résultats</b>, <b>chaque opération
dans l'oplog est idempotente</b> :</p>

<ul>
	<li><b>Synchronisation initiale</b></li>
	<li><b>Remise à niveau après un RollBack</b></li>
	<li><b>Migration des chunks de Sharding</b></li>
</ul>
<a name="tail"></a>

<div class="spacer"></div>

<p class="small-titre">a) Taille de l'Oplog</p>

<p>Lorsque vous démarrez un membre du replica set <b>pour la première fois</b>, MongoDB <b>créé un oplog avec une taille par défaut</b>. La taille <b>dépend de l'architecture de
votre système d'exploitation</b>. Dans la plupart des cas, la taille par défaut de l'oplog est suffisante. Par exemple, si un oplog <b>représente 5% de l'espace libre
de votre disque dur</b> et se remplit <b>après 24h d'opérations</b>, les membres secondaires peuvent arrêter de copier les entrées de l'oplog <b>pendant 24h sans devenir
obsolètes</b>. Par contre, la plupart des replica sets ont <b>un volume d'opérations beaucoup plus petit</b> et leurs oplogs peuvent contenir beaucoup plus d'opérations.</p>

<p>Avant que mongod ne créé un oplog, vous pouvez <b>spécifier sa taille avec l'option "oplogSize"</b>. Par contre, une fois que vous avez démarré un membre
du replica set pour la première fois, vous pouvez <b>changer la taille de l'oplog uniquement en utilisant la procédure de changement de taille d'un oplog</b> que nous
allons détailler un peu plus loin.
Par défaut, la taille de l'oplog est la suivante :</p>

<ul>
	<li><b>Pour Linux 64Bits, Solaris, FreeBSD et Windows, MongoDB alloue 5% de l'espace disponible du disque dur de votre machine à l'Oplog. Si cet espace
	est inférieur à 1go alors MongoDB attribue 1go.</b></li>
	<li><b>Pour OS X 64Bits, MongoDB alloue 183mo pour l'Oplog.</b></li>
	<li><b>Pour les systèmes 32Bits, MongoDB alloue 48mo pour l'Oplog.</b></li>
</ul>
<a name="cg"></a>

<div class="spacer"></div>

<p class="small-titre">b) Oplog de Grande Taille</p>
 
<p>Certaines <b>charges de travail (workloads)</b> pourraient avoir besoin d'un oplog plus gros, comme les situations suivantes :</p>

<ul>
<li><b>Mises à jour de plusieurs Documents en même temps : l'Oplog doit traduire les updates multiples en opérations individuelles afin de maintenir
  l'idempotence.</b></li>
<li><b>Suppressions aussi conséquentes que les Insertions : Si vous supprimer globalement le même montant de données que ce que vous en insérez, la base de données
  ne sera pas énorme, mais l'oplog lui pourrait être assz grand.</b></li> 
<li><b>Nombre considérable d'updates In-Place : si une importante part de la charge de travail représente des updates In-Place, la base de données
  enregistre un large nombre d'opérations mais ne change pas la quantité de données sur le disque.</b></li>
</ul>
<a name="stat"></a>

<div class="spacer"></div>

<p class="small-titre">c) Statut de l'Oplog</p>
  
<p>Afin de vérifier le statut de l'oplog en incluant sa taille et des détails sur les opérations, <b>exécutez la méthode db.printReplicationInfo()</b>.
Sous des situations exceptionnelles et variées, les updates sur les oplogs des membres secondaires devraient lagger comparé au temps de performances désiré.
Utilisez <b>db.getReplicationInfo()</b> depuis un membre secondaire et le résultat replication status pour accéder à <b>l'état courant de réplication</b> et déterminer
s'il y aurait des délais de réplication <b>non attendus</b>.</p> 
<a name="sync"></a>

<div class="spacer"></div>
  
<p class="titre">II) [ Synchronisation des Données du Replica Set ]</p>

<p>Les membres secondaires doivent <b>répliquer toutes les modifications acceptées par le membre primaire</b>. Ce processus est la base des opérations du replica set.
MongoDB offre deux façons de garantir <b>la synchronisation des données</b> : <b>"initial sync"</b> ou <b>la synchronisation initiale</b> lors de l'ajout d'un
nouveau membre au replica set, puis, <b>la réplication afin de continuer à répliquer</b> les nouvelles opérations sur l'ensemble de données du membre.</p>
<a name="init"></a>

<div class="spacer"></div>

<p class="small-titre">a) Synchronisation Initiale</p>

<p>La synchronisation initiale va <b>copier toutes les données</b> depuis un membre vers un autre. Un membre utilise la synchronisation initiale lorsque
<b>le membre n'a aucune données</b>, quand il est nouveau par exemple ou alors quand il a des données mais ou il manque un historique de l'ensemble de réplication.
Quand vous effectuez une synchronisation initiale, MongoDB réalise :</p>

<ul>
<li><b>1) Clone toutes les bases de données. Pour clôner, mongod interroge chaque collection dans chaque base de données et insert toutes les données
dans ses propres répliques des collections existantes.</b></li>
<li><b>2) Applique toutes les modifications à l'ensemble des données. Utilisant l'Oplog depuis la source, mongod met à jour son ensemble de données
afin de refletter l'état actuel de l'ensemble de répliques.</b></li>
<li><b>3) Construit tous les indexes sur toutes les collections. Quand mongod finit de créer tous les indexes, le membre peut migrer vers un statut normal
, par exemple secondary.</b></li>
<a name="repl"></a>

<div class="spacer"></div>

<p class="small-titre">b) Réplication</p>

<p>Les membres du replica set <b>répliquent continuellement les données après la synchronisation initiale</b>. Ce processus tient à jour les membres de l'ensemble
avec tous les changements effectués sur le replica set. Dans la plupart des cas, les secondaires <b>se synchronisent depuis le primaire</b>. Les secondaires
pourraient changer automatiquement <b>leur cibles de synchronisation</b> si besoin en fonction du ping et des états des autres membres.
Pour qu'un membre se synchronise depuis un autre, le paramètre <b>"buildIndexes"</b> doit avoir exactement la même valeur pour les deux membres (true ou false).
Initié depuis la verson 2.2, les secondaires <b>évitent de se synchroniser depuis les membres décalés</b> et <b>les membres cachés</b>.</p>
<a name="cohe"></a>

<div class="spacer"></div>

<p class="small-titre">c) Cohérence et Durabilité</p> 

<p>Dans un replica set, le primaire <b>accepte uniquement les opérations d'écriture</b>. Ecrire sur le membre primaire assure la cohérence stricte des données.
<b>Le journaling fournit une durabilité d'écriture en single-instance</b>, sans cela, si une instance de mongo ne se termine pas correctement, vous pourrez vous retrouver
avec <b>une base de données corrompue</b>.</p>
<a name="mt"></a>

<div class="spacer"></div>

<p class="small-titre">d) Réplication Multi-Threadée</p>

<p>MongoDB effectue des opérations d'écriture par lots en utilisant <b>le multi-threading afin d'améliorer le parallélisme de ces opérations</b>.
MongoDB <b>regroupe chaque lot par espace de nom</b> et applique les opérations en utilisant un groupe de threads, mais applique toujours les opérations d'écriture
dans un espace de nom dans l'ordre. Pendant qu'un lot est traité, MongoDB <b>bloque toutes les opérations de lecture</b>. Par conséquence, tous les secondaires <b>ne peuvent
jamais retourner un état des données</b> qui n'a jamais existé dans le membre primaire.</p>
<a name="ps"></a>

<div class="spacer"></div>

<p class="small-titre">e) Pré-sélection des Indexes pour améliorer le débit de Réplication</p>

<p>Afin d'améliorer les performances concernant l'application des entrées d'oplog, MongoDB <b>parcourt des pages de mémoire</b> qui contiennent les données
affectées et les indexes. Le stage de pré-sélection <b>minimalise le temps pendant lequel MongoDB tient le verrou d'écriture</b> pendant qu'il applique les entrées de l'oplog.
Par défaut, les secondaires vont pre-sélectionner <b>tous les indexes</b>.</p>

<div class="spacer"></div>

<div class="alert alert-warning">
	<u>Optionnel</u> : Vous pouvez désactiver la pré-sélection ou pré-sélectionner seulement les indexe sur le champ "_id".
	Renseigez-vous sur l'option "replIndexPrefetch" pour plus d'informations.
</div>

<div class="spacer"></div>

<p>Voilà, ici s'achève la partie du tutoriel sur <b>le processus de réplication</b>. Nous avons vu qu'un replica set normal
peut contenir <b>jusqu'à 12 membres maximum</b>. Et si vous souhaitez <b>en avoir plus</b> ? Comment faire ? La réponse se trouve
dans la page suivante : <a href="concepts_masterslave.php">"Réplication Master-Slave" >></a>.

<?php

	include("footer.php");

?>
