<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Membres du Replica Set</li>
</ul>
<p class="titre">[ Membres du Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#pri">I) Membre Primaire</a></p>
	<p class="elem"><a href="#sec">II) Membres Secondaires</a></p>
	<p class="elem"><a href="#zero">III) Membres à Priorité 0</a></p>
	<p class="right"><a href="#sb">- a) Membres à Priorité 0 en StandBy</a></p>
	<p class="right"><a href="#fo">- b) Membres à Priorité 0 et FailOver</a></p>
	<p class="elem"><a href="#cac">IV) Membres Cachés</a></p>
	<p class="elem"><a href="#dec">V) Membres Décalés</a></p>
	<p class="elem"><a href="#arb">VI) Arbitre</a></p>
	<p class="elem"><a href="#secu">VII) Sécurité</a></p>
</div>

<p>Dans cette section, nous allons parler de chaque <b>type de membre</b> pouvant faire partie d'un <b>replica set</b>, ainsi que leur <b>rôle</b> et <b>fonctions
principales</b>. N'oublions pas que votre replica set peut contenir <b>jusqu'à 12 membres</b> maximum dont <b>7 votants</b> uniquement.
Il y a au total <b>6 types</b> de membre et nous allons commencer par le plus important, <b>le membre primaire</b>.</p>
<a name="pri"></a>

<div class="spacer"></div>

<p class="titre">I) [ Membre Primaire ]</p>

<p>Le premier type de membre, <b>le membre primaire</b> dans un replica set est <b>unique</b>, il ne peut y en avoir plusieurs et celui-ci est <b>le seul</b> qui reçoit les opérations d'écritures.
MongoDB va exécuter <b>les opérations d'écritures</b> et ensuite laisser <b>une trace</b> dans l'oplog du membre primaire. De cette façon, <b>les membres secondaires</b> vont pouvoir
s'appuyer sur cet oplog afin de <b>répliquer les mêmes opérations</b> sur leur propre ensemble de données.
Tous les membres peuvent accepter les opérations de lecture mais, par défaut, <b>le primaire s'en occupe</b>, bien sûr cela est <b>paramétrable</b>.
En cas de <b>non réponse</b> de la part du membre primaire courant, <b>une élection sera déclenchée</b> afin d'en élire un autre qui prendra sa place.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_1.png" data-lightbox="concepts_mbr" title="Routage des Opérations de Lectures et d'Ecritures"><img src="/img/replication/concepts_membres/concepts_1.png" /></a>
<p><h6><b>Image 1.0</b> - Routage des Opérations de Lectures et d'Ecritures.</h6></p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_2.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_2.png" /></a>
<p><h6><b>Image 1.1</b> - Election d'un Nouveau Membre Primaire.</h6></p>
<a name="sec"></a>

<div class="spacer"></div>

<p class="titre">II) [ Membres Secondaires ]</p>

<p>Un <b>membre secondaire</b> maintient <b>une copie conforme</b> des données du membre primaire en se servant de son oplog via <b>un processus asynchrone</b>.
Un replica set peut avoir <b>un ou plusieurs membres secondaires</b>. Les clients ne peuvent pas <b>écrire</b> sur les membres secondaires mais peuvent
<b>lire</b> depuis ces membres selon votre configuration. Un membre secondaire peut <b>devenir un membre primaire</b> lors d'une élection si le membre primaire actuel
ne répond plus et qu'il a besoin d'être remplacé.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_7.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_7.png" /></a>
<p><h6><b>Image 1.2</b> - Election d'un Nouveau Membre Primaire.</h6></p>

<div class="spacer"></div>

<p>Vous pouvez aussi configurer un second membre afin qu'il devienne <b>un membre à priorité 0</b>, <b>un membre caché</b> ou alors <b>un snapshot historique</b>.
Nous allons maintenant détailler tous ces types ...</p>
<a name="zero"></a>

<div class="spacer"></div>

<p class="titre">III) [ Membres à Priorité 0 ]</p>

<p>Un <b>membre à priorité 0</b> est tout simplement un membre secondaire qui <b>ne peut devenir primaire</b>. Ceux-ci ne peuvent <b>déclencher d'élection</b> non plus.
Sinon, les membres secondaires à priorité 0 restent <b>de simple membres secondaires</b>, ils <b>maintiennent le même ensemble de données</b>, <b>acceptent les opérations de lectures</b>
et <b>peuvent voter lors d'une élection</b>. Ce genre de situation peut être très utile si vous déployez avec <b>plusieurs data centers</b>.
Voir le schéma suivant indiquant deux membres, <b>un primaire</b> et <b>un secondaire</b> pour le premier data center, et un autre à <b>priorité 0</b> dans le deuxième data center :</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_3.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_3.png" /></a>
<p><h6><b>Image 1.4</b> - Election d'un Nouveau Membre Primaire.</h6></p>
<a name="sb"></a>

<div class="spacer"></div>

<p class="small-titre">a) Membres à Priorité 0 en StandBy</p>

<p>Dans certains replica sets, il n'est parfois pas possible <b>d'ajouter un nouveau membre</b> dans un laps de temps raisonnable. Un membre à priorité 0
<b>en standby</b> permet de garder une copie de la base de données afin d'<b>être prêt à remplacer un membre non disponible</b>.
Parfois, vous n'avez pas à <b>définir la priorité 0 du membre en standby</b>. En revanche, dans des replica sets ayant du <b>hardware différent</b> ou une <b>localité
différente</b>, définir la priorité 0 du membre en standy s'assure que <b>seuls les membres qualifiés peuvent devenir primaires</b>.
Un membre standby à priorité 0 peut aussi être utile pour certains membres du replica set ayant du <b>matériel différent</b>. Dans ce cas, déployez un membre
à priorité 0 afin qu'il <b>ne devienne pas primaire</b>. Vous pouvez aussi utiliser <b>les membres cachés</b> pour cela. Si votre replica set comporte déjà <b>7 membres</b> pouvant
voter, configurez se membre afin qu'il <b>ne soit pas en mesure de voter</b>.</p>
<a name="fo"></a>

<div class="spacer"></div>

<p class="small-titre">b) Membres à Priorité 0 et FailOver</p>

<p>Lorsque vous <b>configurez un membre à priorité 0</b>, considérez l'éventualité de <b>failover</b>, incluant toutes les partitions du réseau : assurez vous <b>toujours</b>
que votre data center principal est constitué de membres <b>pouvant voter</b> et un ensemble de membres pouvant être <b>éligible en tant que primaire</b>.</p>
<a name="cac"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Membres Cachés ]</p>

<p>Un membre caché d'un replica set <b>recopie l'ensemble de données</b> du membre primaire, mais est <b>complètement invisible des applications clientes</b>. Ces membres
sont aussi des membres <b>à priorité 0</b> et <b>ne peuvent pas devenir primaires</b>. La méthode <b>db.isMaster()</b> n'affiche pas les membres cachés. Par contre, ceux-ci <b>votent
durant une élection</b>.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_4.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_4.png" /></a>
<p><h6><b>Image 1.7</b> - Election d'un Nouveau Membre Primaire.</h6></p>

<div class="spacer"></div>

<p>Les opérations de lectures sur les membres secondaires <b>n'atteignent pas le membre caché</b>, donc celui-ci <b>ne reçoit aucun traffic</b>.
Garder un membre caché peut être utile pour <b>garder une sauvegarde</b> ou pour <b>des logs</b> d'un ensemble de données.
Concernant <b>les sauvegardes dédiées</b>, assurez-vous que le membre caché ait <b>une latence faible sur le réseau</b> comme celle du membre primaire ou presque.
Assurez-vous que le lag de réplication soit <b>très faible ou inexistant</b>.
Evitez d'<b>arrêter le processus mongod</b> d'un membre caché. A la place, pour les snapshots de système de fichiers, utilisez la fonction <b>fsynclock()</b>
pour flusher toutes les opérations d'écritures et <b>les verrous</b> de l'instance mongod pendant la durée de la restauration.</p>
<a name="dec"></a>

<div class="spacer"></div>

<p class="titre">V) [ Membres Décalés ]</p>

<p>Les <b>membres décalés</b> contiennent des copies de l'ensemble de données primaire. Par contre, un membre décalé <b>reflette un ensemble de données plus tôt ou décalé</b>
que celui qui existe. Par exemple, s'il est <b>9h52</b> et que le membre a <b>une heure de décalage</b>, les données contenues daterons du même jour mais dans l'état où elles étaient à <b>8h52</b>.
Ce genre de membre peut aider <b>en cas d'erreur humaine</b> ou alors <b>d'opération plus lourdes</b>.</p>

<p>Il y a des conditions requises :
Ces membres doivent être à <b>priorité 0</b>, pour qu'ils ne deviennent pas primaire (sinon les données retournées seront trop vieilles).
Devraient être cachés, il faut toujours <b>empêcher les applications</b> de voir et d'utiliser les membres décalés.
Mais ceux-ci <b>peuvent voter</b> lors d'une élection pour élire un nouveau primaire.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_5.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_5.png" /></a>
<p><h6><b>Image 1.8</b> - Election d'un Nouveau Membre Primaire.</h6></p>

<div class="spacer"></div>

<p>D'après le schéma suivant, le replica set <b>contient un membre décalé</b> ré-appliquant l'oplog du primaire avec <b>un décalage de 3600 secondes</b> ou <b>1h</b>.
Voici un exemple de configuration pour un membre décalé :</p>

<pre>
{
	"_id" : num,
	"host" : hostname:port,
	"priority" : 0,
	"slaveDelay" : seconds,
	"hidden" : true
}
</pre>
<a name="arb"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Arbitre ]</p>

<p><b>Un arbitre</b> ne contient <b>aucune sauvegarde</b> de l'ensemble de données du replica set et <b>ne peut pas</b> devenir un membre primaire.
Il est préférable d'avoir un arbitre afin de <b>déterminer le nombre de votes</b> pour déterminer le prochain membre primaire en cas de failover.
Ils permettent d'avoir <b>un nombre impair</b> de votes sans pour autant avoir la surchage d'un autre membre secondaire si l'on en a <b>pas besoin</b> d'un supplémentaire.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : N'exécutez pas l'instance de l'arbitre sur une machine comportant le membre primaire ou un membre secondaire.
</div>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/concepts_membres/concepts_6.png" data-lightbox="concepts_mbr" title="Election d'un Nouveau Membre Primaire."><img src="/img/replication/concepts_membres/concepts_6.png" /></a>
<p><h6><b>Image 1.6</b> - Election d'un Nouveau Membre Primaire.</h6></p>

<div class="spacer"></div>

<p>N'ajoutez un arbitre qu'aux replica sets ayant <b>un nombre pair de membres</b>, sinon le <b>processus d'élection</b> ne serait pas efficace.</p>
<a name="secu"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Sécurité ]</p>

<p>Pour <b>l'authentificaton</b>, les arbitres, <b>exécutés avec auth</b>, échanges les identifiants avec les autres membres du replica set à authentifier. MongoDB <b>crypte</b>
le processus d'authentification. Les arbitres utilisent des <b>fichiers clés (keyfiles)</b> pour s'identifier sur les replica sets.
Par l'aspect <b>communication</b>, les seules communications entre l'arbitre et les autres membres du replica set concernent <b>les votes</b>, <b>les battements de coeur</b> et
<b>les configurations</b>. Ces échanges <b>ne sont pas cryptés</b>.
Par contre, si vous déployez avec <b>SSL</b>, MongoDB va <b>crypter toutes les communications</b> entre tous les membres du replica set.</p>

<div class="spacer"></div>

<p>Vous êtes arrivés au bout de la page sur les <b>types de membres</b> qu'un replica set peut avoir, maintenant, la prochaine étape va être de voir quelles sont
<b>les architectures de déploiement</b> possibles. Idem ici, si vous ne comprenez pas <b>certains points</b> ou alors si vous détectez <b>une information
incohérente</b>, <a href="../contact.php">"contactez-moi"</a>. Direction la page des <a href="concepts_deploiement.php">"Architectures de Déploiements de Replica Set" >></a>.</p>

<?php

	include("footer.php");

?>
