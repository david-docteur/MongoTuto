<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Diagnostiques</li>
</ul>

<p class="titre">[ Diagnostiques du Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#stat">I) Vérifier le Statut du Replica Set</a></p>
	<p class="elem"><a href="#late">II) Vérifier la Latence de Replication</a></p>
	<p class="elem"><a href="#test">III) Tester les Connexions Entre les Membres</a></p>
	<p class="elem"><a href="#exce">IV) Exceptions de Sockets quand plusieurs Secondaires Redémarrent</a></p>
	<p class="elem"><a href="#tail">V) Verifier la Taille de l'Oplog</a></p>
	<p class="elem"><a href="#time">VI) Erreur de Timestamp pour une Entrée d'Oplog</a></p>
	<p class="elem"><a href="#dupl">VII) Erreur de Clé Dupliquée sur "local.slaves"</a></p>
</div>

<p>Dans cette rubrique, nous allons diagnostiquer certaines situations qui peuvent générer des erreurs ou porter à confusion lors de l'installation et de la maintenance
du Replica Set.</p>
<a name="stat"></a>

<div class="spacer"></div>

<p class="titre">I) [ Vérifier le statut du Replica Set ]</p>

<p>Pour afficher le statut du Replica Set ainsi que l'état de chaque membre, la méthode rs.status() permet de retourner l'état lorsque vous êtes connecté via un shell mongo
au membre primaire. Pour plus d'informations sur les statuts renvoyés : <a href="http://docs.mongodb.org/manualreference/command/replSetGetStatus" target="_blank">http://docs.mongodb.org/manualreference/command/replSetGetStatus</a>.</p>
<a name="late"></a>

<div class="spacer"></div>

<p class="titre">II) [ Vérifier la Latence de Réplication ]</p>

<p>La latence (lag) de réplication correspond au délai entre une opération sur le membre primaire et l'application de cette opération de l'Oplog vers un secondaire.
La latence de réplication peut devenir un problème majeur et peut sérieusement affecter les déploiements MongoDB.
Une latence excessive rends les membres "laggués" inéligible pour rapidement devenir primaire et augmenter la possibilité que les opérations de
lecture deviennent non pertinentes.</p>

<p>Pour vérifier le niveau de latence de réplication :
- Dans un shell mongo connecté au primaire, appelez la méthode db.printSlaveReplicationInfo().
Le document retourné affiche la valeur syncedTo pour chaque membre, ce qui vous indique la dernière lecture depuis l'Oplog de chaque membre :</p>

<pre>
source: m1.example.net:30001
	synced	To: Tue Oct 02 2012 11:33:40 GMT-0400 (EDT)
		= 7475 secs ago (2.08hrs)
source: m2.example.net:30002
	syncedTo: Tue Oct 02 2012 11:33:40 GMT-0400 (EDT)
		= 7475 secs ago (2.08hrs)
</pre>

<div class="spacer"></div>

<p>-Vérifiez le taux de réplication en observant le temps d'Oplog dans le graphe de Replica dans le MMS (MongoDB Management Service).

Les causes de latence possibles incluent :

	- Latence du réseau : vérifiez les routes du réseau entre les membres de votre ensemble qu'il n'y ai pas de packets perdus ou des problèmes de routages.
	  Utilisez des outils comme ping pour tester la latence entre les membres de l'ensemble et un traceroute pour afficher le routage des packets.
	- Le débit du disque dur : Si le système de fichiers et le disque dur d'un secondaire est incapable nettoyer les données du disque aussi rapidement
      que le membre primaire, alors le secondaire aura des difficultés à garder l'état du Replica Set le plus à jour. Utilisez des outils systèmes pour accéder
      au statut d'un disque dur, incluant iostat ou vmstat.
	- Parrallèlisme : Dans certains cas, les opérations trop longues sur le membre primaire peuvent bloquer la réplication sur les membres secondaires.
	  Pour de meilleurs résultats, configurez Write Concern afin d'exiger la confirmation de réplication vers les membres secondaires. Cela empêche les opérations
	  d'écriture de revenir si la réplication ne peut pas faire face à la charge d'écriture. Utilisez un profiler de base de données pour voir s'il y a des
	  requêtes lentes ou des opérations longues qui s'avéreraient être la raison du lag.
	- Write Concern approprié : Si vous générez des données de masse qui requierent un large nombre d'écritures sur le primaire, particulièrement avec un
	  Write Concern unacknoweledge, les secondaires ne vont pas pouvoir lire depuis l'Oplog assez rapidement afin de rester à jour avec les modifications.
	  Pour éviter cela, demandez un Write Concern de type aknoweledgment ou journale après chaque 100, 1000 ou autre intervale afin de fournir une opportunité
	  aux secondaires de se rattraper avec le membre primaire.</p>
<a name="test"></a>

<div class="spacer"></div>
	  
<p class="titre">III) [ Tester les Connexions entre les Membres ]</p>

<p>Tous les membres du Replica Set doivent pouvoir se connecter les uns les autres afin de supporter la réplication. Vérifiez toujours les connexion dans les deux
directions. Les topologies réseau et les configurations pare-feu empêche les connctivités normales et requisent, ce qui peut bloquer la réplication.
Considérons l'exemple suivant :</p>

<p>Nous avons un Replica Set avec 3 membres exécutés chacun sur leur hôte :
- m1.example.net
- m2.example.net
- m3.example.net

1) Testez la connexion depuis m1.example.net avec les autres hôtes :</p>

<pre>
mongo --host m2.example.net --port 27017

mongo --host m3.example.net --port 27017
</pre>

<div class="spacer"></div>

<p>2) Testez la connexion depuis m2.example.net vers les deux autres hôtes :</p>

<pre>
mongo --host m1.example.net --port 27017

mongo --host m3.example.net --port 27017 
</pre>

<div class="spacer"></div>

<p>Vous avez maintenant testé la connexion entre m2.example.net et m1.example.net dans les deux directions.
Testez maintenant depuis m3.example.net :</p>

<pre>
mongo --host m1.example.net --port 27017

mongo --host m2.example.net --port 27017
</pre>

<div class="spacer"></div>

<p>Si une connexion dans nimporte quelle direction échoue, vérifiez votre pare-feu et votre réseau et reconfigurez votre environement afin d'accepter les
connexions relatives à MongoDB.</p>
<a name="exce"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Exceptions de Sockets quand plusieurs Secondaires Redémarrent ]</p>

<p>Quand vous redémarrez des membres du Replica Set, assurez-vous que le Replica Set est en mesure d'élire un membre primaire. Cela signifie que la majorité
des votes de l'ensemble est disponible. Quand les membres actifs de l'ensemble ne peuvent former une majorité, le primaire de l'ensemble rétrograde et devient
secondaire. Le primaire ferme alors toutes les connexions ouvertes des applications clientes. Les clients qui tentent d'écrire à ce membre primaire
reçoivent une exception de socket et une erreur de reset de connexion jusqu'à ce que l'ensemble élise un nouveau primaire.</p>

<p>Par exemple, prenons un Replica Set de 3 membres ou chaque membre a un vote, l'ensemble peut élire un primaire tant qu'au moins deux membres peuvent se
connecter entre eux. Si vous redémarrez les secondaires en même temps, le primaire rétrograde et devient un secondaire. Jusqu'à ce qu'il y ai au moins un secondaire
disponible, l'ensemble n'a pas de primaire et ne peut en élire un nouveau.</p>
<a name="tail"></a>

<div class="spacer"></div>

<p class="titre">V) [ Vérifier la Taille de l'Oplog ]</p>

<p>Un Oplog plus large peut apporter au Replica Set une plus grande tolérance au lag et donc, le rend plus résistant.
Pour vérifier la taille de l'Oplog pour un membre donné du Replica Set, exécutez la commande suivante dans un shell mongo :</p>

<pre>db.printReplicationInfo()</pre>

<p>Le résultat en sortie affiche la taille de l'Oplog ainsi que le rang des dates des opérations contenues dans l'Oplog. Dans l'exemple suivant,
l'Oplog est d'environ 10mo et peut contenir environ 26h (94400 secondes) d'opérations :</p>

<pre>
configured oplog size: 10.10546875MB
log length start to end: 94400 (26.22hrs)
oplog first event time: Mon Mar 19 2012 13:50:38 GMT-0400 (EDT)
oplog last event time: Wed Oct 03 2012 14:59:10 GMT-0400 (EDT)
now: Wed Oct 03 2012 15:00:21 GMT-0400 (EDT)
</pre>

<div class="spacer"></div>

<p>L'Oplog devrait être assez long pour contenir toutes les opérations pour le temps d'arrêt le plus long auquel vous pourriez vous attendre pour un secondaire.
Au minimum, un Oplog devrait être capable de contenir un minimum de 24h d'opérations. La plupart des utilisateurs préfèrent avoir 72h ou même une semaine entière
d'opérations. Normalement, vous souhaiterez avoir un Oplog de la même taille pour chaque membre, si vous en changez la taille sur un membre, faîtes-le
sur tous les autres.</p>
<a name="time"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Erreur de Timestamp pour une Entrée d'Oplog ]</p>

<p>Considérez l'erreur suivante dans mongod :</p>

<pre>
replSet error fatal couldn't query the local local.oplog.rs collection.
Terminating mongod after 30 <timestamp> [rsStart] bad replSet oplog entry?
</pre>

<div class="spacer"></div>

<p>Souvent, une valeur non correctement tapée dans le champ "ts" dans la dernière entrée de l'Oplog cause cette erreur. Le type de données correcte
est Timestamp. Vérifiez le type du champ "ts" avec les requêtes suivantes :</p>

<pre>
db = db.getSiblingDB("local")
db.oplog.rs.find().sort({$natural:-1}).limit(1)
db.oplog.rs.find({ts:{$type:17}}).sort({$natural:-1}).limit(1)
</pre>

<div class="spacer"></div>

<p>La première ligne retourne le dernier document de l'Oplog tandis que la deuxième retourne le dernier document de l'Oplog ou le champ "ts" est de type
Timestamp. L'opérateur $type vous permet de selectionner le type BSON numéro 17, ce qui représente un Timestamp. Si les requêtes ne retournent pas les mêmes documents,
alors le dernier document dans l'Oplog a le mauvais type de données dans le champ "ts".
Par exemple, si la dernière entrée est :</p>

<pre>
{ "ts" : {t: 1347982456000, i: 1},
"h" : NumberLong("8191276672478122996"),
"op" : "n",
"ns" : "",
"o" : { "msg" : "Reconfig set", "version" : 4 } }
</pre>

<div class="spacer"></div>

<p>et que la seconde requête retourne :</p>

<pre>
{ "ts" : Timestamp(1347982454000, 1),
"h" : NumberLong("6188469075153256465"),
"op" : "n",
"ns" : "",
"o" : { "msg" : "Reconfig set", "version" : 3 } }
</pre>

<div class="spacer"></div>

<p>Alors la valeur du champ ts est du mauvaise type de données.
Pour résoudre ce problème et mettre à jour le type de données du champ ts :</p>

<pre>
db.oplog.rs.update( { ts: { t:1347982456000, i:1 } },
{ $set: { ts: new Timestamp(1347982456000, 1)}})
</pre>

<div class="spacer"></div>

<p>Modifiez la valeur en Timestamp. Cette opération devrait prendre du temps car la mise à jour doit scanner et mettre tout l'Oplog en mémoire.</p>
<a name="dupl"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Erreur de Clé Dupliquée sur "local.slaves" ]</p>

<p>Cette erreur de clé dupliquée arrive quand un secondaire ou un esclave change son nom d'hôte et le primaire ou le master essaye de mettre à jour 
sa collection local.slaves avec le nouveau nom. La mise à jour échoue car elle contient le même _id que le document contenant l'ancien nom d'hôte.
L'erreur va ressembler à :</p>

</pre>exception 11000 E11000 duplicate key error index: local.slaves.$_id_ dup key: { : ObjectId('object ...</pre>

<div class="spacer"></div>

<p>Cette erreur est vraiment bénine et n'affecte pas les opérations de réplication sur le secondaire ou le slave.
Pour empêcher cette erreur d'arriver, supprimez la collection local.slaves du primaire ou du master :</p>

<pre>
use local
db.slaves.drop()
</pre>

<p>La prochaine fois que le primaire ou le slave interroge le primaire ou le master, le primaire ou le master va recréer la collection local.slaves.</p>

<div class="spacer"></div>

<p>Je vous invite maintenant à passer au chapitre suivant sur le Sharding. Le chapitre sur la réplication est maintenant terminé ! : <a href="../sharding.php">"Sharding" >></a>.

<?php

	include("footer.php");

?>