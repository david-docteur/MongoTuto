<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Haute Disponibilité du Replica Set</li>
</ul>

<p class="titre">[ Haute Disponibilité du Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#elec">I) Elections du Replica Set</a></p>
	<p class="elem"><a href="#fact">II) Facteurs Affectant une Election</a></p>
	<p class="right"><a href="#batt">- a) Battements de Coeur</a></p>
	<p class="right"><a href="#comp">- b) Comparaison de Priorités</a></p>
	<p class="right"><a href="#opti">- c) L'Optime</a></p>
	<p class="right"><a href="#conn">- d) Connexions</a></p>
	<p class="right"><a href="#pr">- e) Parties sur le Réseau</a></p>
	<p class="elem"><a href="#fonc">III) Fonctionnement d'une Election</a></p>
	<p class="right"><a href="#decl">- a) Déclenchement</a></p>
	<p class="right"><a href="#part">- b) Participation</a></p>
	<p class="right"><a href="#dv">- c) Droit de Véto</a></p>
	<p class="right"><a href="#nv">- d) Membres Non Votants</a></p>
	<p class="elem"><a href="#rbfo">IV) Rollbacks Durant un FailOver</a></p>
	<p class="right"><a href="#coll">- a) Collecter les Données du Rollback</a></p>
	<p class="right"><a href="#evit">- b) Eviter les Rollbacks de l'Ensemble</a></p>
	<p class="right"><a href="#limi">- c) Limites du Rollback</a></p>
</div>

<p>Les ensembles de répliques fournissent une <b>haute disponibilité des données</b> en utilisant le principe du <b>FailOver</b> qui, lorsque votre membre primaire
est indisponible et ne répond plus, va <b>tout basculer sur un des membres secondaire</b> sélectionné lors après <b>une élection</b>. Presque dans tous les cas, le FailOver
ne nécessite <b>aucune intervention manuelle</b>.
Tous les membres du replica set <b>contiennent tous les même données</b> mais restent indépendants. Dans certains cas, le FailOver a besoin d'effectuer <b>un retour en
arrière ou Rollback</b> que nous allons détailler un peu plus loin.</p>

<p>Le type de déploiement de votre replica set va <b>affecter les situations de FailOver</b>. Afin de supporter un FailOver efficace, assurez-vous qu'une instance
puisse <b>élire un membre primaire si besoin</b>.
<b>Placez la majorité des membres votants</b> ainsi que <b>tous les membres pouvant devenir primaire</b> dans un mêe site. Dans le cas contraire, les différentes parties sur le réseau
pourraient <b>empêcher l'ensemble de déterminer une majorité</b>.</p>
<a name="elec"></a>

<div class="spacer"></div>

<p class="titre">I) [ Elections du Replica Set ]</p>

<p>Ces élections ont lieues lorsqu'un replica set <b>démarre</b>, mais surtout quand <b>le membre primaire de votre ensemble devient injoignable</b>. Ensuite, les membres de l'ensemble vont <b>voter automatiquement</b>
afin d'élire un <b>nouveau membre primaire</b>. Comme déjà expliqué auparavant, le membre primaire est <b>le seul membre pouvant
accepter des opérations d'écriture</b>.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Les élections sont essentielles pour l'indépendance des opérations du replica set, par contre, les élections prennent du temps
	avant d'être terminées. Pendant qu'une élection est en cours, le replica set n'a aucun membre primaire et ne peut pas accepter les opérations
	d'écriture. MongoDB va donc éviter les élections un maximum.
</div>
<a name="fact"></a>

<div class="spacer"></div>

<p class="titre">II) [ Facteurs Affectant une Election ]</p>

<p>Voici une liste de <b>certaines causes</b> pouvant affecter ou empêcher le processus d'une élection.</p>
<a name="batt"></a>

<div class="spacer"></div>

<p class="small-titre">a) Battements de Coeur</p>

<p>Les membres du replica set s'envoient des <b>battements de coeur</b> ou plus communément appelés <b>Pings</b> à chacun d'entre-eux <b>toutes les deux secondes</b>.
Si le ping ne répond pas au bout de <b>10 secondes</b>, les autres membres définissent ce membre en tant <b>qu'inaccessible</b>.</p> 
<a name="comp"></a>

<div class="spacer"></div>

<p class="small-titre">b) Comparaison de Priorités</p>

<p>Le paramètre <b>"priority"</b> d'un membre du replica set, que nous allons découvrir un peu plus tard, <b>affecte les élections</b>. En effet, les membres vont préférer voter pour les membres
ayant <b>la priorité la plus forte</b>. Les membres ayant la <b>priorité 0</b> ne peuvent devenir primaire et par conséquence <b>ne font pas partie de l'élection</b>.
Un replica set ne va pas effectuer d'élections tant que le membre primaire actuel a <b>la plus haute priorité</b> et que sa dernière entrée dans l'oplog
<b>date de moins de 10 secondes</b>. Si un autre membre ayant une priorité plus haute rattrape <b>sous 10 secondes la dernière entrée de l'oplog</b> du membre primaire
actuel, l'ensemble déclenche une élection dans le but de <b>donner plus de chances</b> à ce membre de devenir primaire.</p>
<a name="opti"></a>

<div class="spacer"></div>

<p class="small-titre">c) L'Optime</p>

<p>L'Optime est un <b>Timestamp</b> de la dernière opération qu'un membre a appliqué depuis l'oplog. Un membre du replica set ne peut devenir primaire 
<b>s'il n'a pas le plus haut</b> (plus récent) optime parmis tous les membres visibles de l'ensemble.</p>
<a name="conn"></a>

<div class="spacer"></div>

<p class="small-titre">d) Connexions</p>

<p>Un membre du replica set ne peut pas devenir primaire tant qu'il ne peut pas <b>se connecter à la majorité des membres</b> de l'ensemble.
Dans le cas d'une élection, la majorité fait référence <b>au nombre total de votes</b>, plutôt qu'au nombre total de membres.</p>

<p>Si vous avez un ensemble de trois membres, ou chaque membre a un vote, le membre peut élire un primaire <b>du moment que deux membres peuvent se connecter entre eux</b>.
Si deux membres sont <b>indisponibles</b>, le dernier membre est secondaire <b>parce-qu'il ne peut pas se connecter</b> à la majorité des membres de l'ensemble. Si ce dernier
membre est déjà un primaire, alors celui-ci se <b>rétrograde en secondaire</b>.</p>
<a name="pr"></a>

<div class="spacer"></div>

<p class="small-titre">e) Parties sur le Réseau</p>

<p>Celles-ci affectent <b>la formation d'une majorité lors d'une élection</b>. Si un membre primaire s'arrête et que sa portion de l'ensemble <b>n'a pas la majorité</b>,
alors l'ensemble <b>ne pourra pas élire de membre primaire</b>. Le replica set devient <b>read only</b>.
Afin d'éviter ce genre de situations, <b>placez la majorité des instances dans un seul et même data center</b>, puis, <b>une minorité d'instances dans d'autres data centers</b>.</p>
<a name="fonc"></a>

<div class="spacer"></div>

<p class="titre">III) [ Fonctionnement d'une Election ]</p>

<p>Dans cette partie, nous allons voir <b>plus en détails</b> comment fonctionne une élection du membre primaire.</p>
<a name="decl"></a>

<div class="spacer"></div>

<p class="small-titre">a) Déclenchement</p>

<p>Une élection est <b>déclenchée par le replica set</b> lorsqu'un membre primaire est <b>introuvable</b> :</p>

<ul>
	<li><b>initialisation d'un nouveau replica set</b></li>
	<li><b>un membre secondaire perd contact avec le primaire (les secondaires appelent à l'élection quand ils ne voient plus le primaire)</b></li>
	<li><b>un membre primaire s'interromp</b></li>
</ul>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Les membres à priorité 0 ne peuvent déclencher d'élection, même si ceux-ci ne détectent plus le membre primaire.
</div>

<div class="spacer"></div>

<p>Un membre primaire va <b>s'interrompre</b> quand :</p>

<ul>
	<li><b>il va recevoir la commande replSetStepDown</b></li>
	<li><b>un membre secondaire est éligible pour l'élection et a une plus forte priorité</b></li>
	<li><b>le membre primaire ne peut contacter la majorité des membres de l'ensemble</b></li>
</ul>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Quand le membre primaire s'arrête, celui-ci ferme toutes les connexions clientes ouvertes afin d'empêcher
	les clients d'envoyer des opérations d'écriture à un membre secondaire. Cela aide les clients à garder une vue d'ensemble juste et précise du Replica Set,
	ainsi que d'éviter les Rollback.
</div>
<a name="part"></a>

<div class="spacer"></div>

<p class="small-titre">b) Participation</p>

<p>Chaque membre de l'ensemble a <b>une priorité qui va le définir</b> plus ou moins éligible afin de devenir primaire ou non. Le membre qui aura la plus forte priorité
<b>sera donc élu membre primaire</b>. Par défaut, <b>tous les membres ont une priorité de 1</b> et ont une chance égale d'être élu et de devenir primaire. Par défaut, ils peuvent
également aussi <b>déclencher une élection</b>.</p>

<p>Vous pouvez définir la priorité <b>(paramètre priority)</b> pour donner de l'importance à un membre (votre deuxième machine la plus puissante de l'ensemble par exemple).
Si vous avez un replica set distribué géographiquement, vous pouvez <b>ajuster les priorités</b> de manière à ce que les membres d'un data center uniquement puissent devenir
primaire.
En conséquences, le premier membre recevant <b>la majorité de votes devient primaire</b>. Par défaut, tous les membres ont <b>un seul vote</b> sauf si vous souhaiter modifier
certains paramètres et les membres qui ne votent pas en ont évidemment 0.</p>

<div class="small-spacer"></div>

<p>L'état (state) d'un membre <b>affecte aussi son éligibilité</b> au vote car seuls les membres ayant les statuts suivants peuvent voter :</p>

<ul>
	<li><b>PRIMARY</b></li>
	<li><b>SECONDARY</b></li>
	<li><b>RECOVERING</b></li>
	<li><b>ARBITER</b></li>
	<li><b>ROLLBACK</b></li>
</ul>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Dans un ensemble, ne modifiez pas le nombre de votes afin de contrôler le résultat d'une élection. Modifiez la valeur
	de la priorité à la place.
</div>
<a name="dv"></a>

<div class="spacer"></div>

<p class="small-titre">c) Droit de Véto</p>

<p>Tous les membres d'un replica set <b>peuvent donner leur droit de véto</b> lors d'une élection, incluant les membres non votants :</p>

<ul>
	<li><b>Si le membre visant une élection n'est pas un membre de l'ensemble votant</b></li>
	<li><b>Si le membre visant une élection n'est pas à jour au niveau des opérations les plus récentes et accessibles de l'ensemble</b></li>
	<li><b>Si le membre visant une élection a une priorité plus basse qu'un autre membre ayant une priorité plus haute que celui-ci dans l'ensemble</b></li>
	<li><b>Si un membre à priorité 0 (membre caché ou décalé) est le membre le plus à jour de l'ensemble. Dans ce cas, un autre membre éligible de l'ensemble va rattraper
	l'état de ce membre et ensuite essayer de devenir primaire</b></li>
	<li><b>Si le membre primaire en cours a plus de récentes opérations ou égale (optime plus haut ou égal) par rapport au membre qui vise une élection</b></li>
</ul>
<a name="nv"></a>

<div class="spacer"></div>

<p class="small-titre">d) Membres Non Votants</p>

<p>Les membres <b>non votants</b> contiennent des copies de l'ensemble des données et acceptent les opérations de lectures des applications clientes. Les membres
non votants <b>ne peuvent pas voter lors d'une élection</b> mais peuvent <b>donner leur droit de véto et devenir primaire</b>. En effet, un ensemble de répliques peut
avoir un nombre maximum de 12 membres mais uniquement 7 d'entre eux qui peuvent voter. Les membres non votants autorisent un replica set à avoir <b>plus
de 7 membres votants</b>.</p>

<div class="spacer"></div>

<p>Un membre non votant a son <b>paramètre "votes"</b> définit à 0 comme ceci :</p>

<pre>
{
	"_id" : num
	"hote" : hote:port,
	"votes" : 0
}
</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Ne changez pas le nombre de votes afin de contrôler quel membre va être primaire. A la place, changez le paramètre priority.
	Ne modifiez le nombre de votes uniquement en cas exceptionnels comme pour autoriser plus de 7 membres votants par exemple.
</div>

<div class="spacer"></div>

<p>Quand cela est possible, tous les membres ne doivent avoir <b>qu'un et un seul vote uniquement</b>. Changer le nombre de votes peut <b>créer de multiples confusions</b>
telles que l'élection de membres pouvant devenir primaires. Pour configurer un membre non-votant, nous allons voir cela un peut plus loin</p>
<a name="rbfo"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Rollbacks Durant un FailOver ]</p>

<p>Un Rollback, comme son nom l'indique, va <b>revenir en arrière sur les opérations d'écriture</b> sur le membre primaire qui ne répondait plus lorsqu'il aura rejoint l'ensemble
après un <b>FailOver</b>. Un Rollback est nécessaire seulement si un membre primaire a <b>accepté des opérations d'écritures</b> que les membres secondaires n'ont pas ré-appliqués
avant que le primaire s'arrête. Quand le membre primaire rejoint l'ensemble en tant que membre secondaire, <b>il revient ou "rolls back" sur les opérations
d'écritures</b> afin de garder la même structure de la base de données avec les autres membres.</p>

<p>Même si les rollbacks sont rares, MongoDB essaye de les éviter au maximum. Si un rollback est effectué, c'est souvent souvent à cause d'une partie du réseau.
Les membres secondaires qui ne peuvent pas répondre à temps avec les opérations effectuées sur le primaire <b>augmentent la probabilité de situation de rollback</b>.
Un rollback n'est pas exécuté si les opérations de lecture <b>sont répliquées sur un autre membre de l'ensemble</b> avant que le membre primaire s'arrête
et si ce membre reste disponible et accessible à la majorité de l'ensemble.</p>
<a name="coll"></a>

<div class="spacer"></div>
 
<p class="small-titre">a) Collecter les Données du Rollback</p>
 
<p>Quand un rollback est effectué, les administrateurs <b>doivent décider de garder ou d'ignorer les données du rollback</b>. MongoDB écrit les données du rollback
<b>dans des fichiers BSON dans le répertoire "rollback/"</b> dans le dossier du <b>dbpath</b> que vous avez configuré. Les fichiers rollback ont la forme suivante :</p>
 
<pre>base.collection.timestamp.bson</pre>

<div class="spacer"></div>
 
<p>Par exemple :</p>
 
<pre>librairie.auteurs.2011-05-09T18-10-04.0.bson</pre>

<div class="spacer"></div>
 
<p>Les administrateurs vont devoir appliquer les données du rollback <b>manuellement</b> une fois que le membre termine le processus de rollback et revienne
au statut de secondaire. Utilisez <b>bsondump</b> pour lire le contenu des fichiers du rollback. Utilisez ensuite <b>mongorestore</b> pour appliquer ces changements
au nouveau membre primaire.</p>
<a name="evit"></a>

<div class="spacer"></div>
 
<p class="small-titre">b) Eviter les Rollbacks de l'Ensemble</p>

<p>Afin d'éviter les situations de Rollback, utilisez le <b>write concern</b>, que nous allons voir un peu plus loin, de l'ensemble afin de garantir le fait que les
opérations d'écriture <b>se propagent bien à travers les membres</b> du replica set.</p>
<a name="limi"></a>

<div class="spacer"></div>
 
<p class="small-titre">c) Limites du Rollback</p> 

<p>Une instance mongod ne va jamais effectuer un rollback sur <b>plus de 300mo de données</b>. Si votre système doit impérativement dépasser cette limite,
vous serez obligé d'intervenir manuellement afin de restaurer toutes les données. Si c'est le cas, la ligne suivante va apparaître dans votre <b>log mongod</b> :</p>

<pre>[replica set sync] replSet syncThread: 13410 replSet too much data to roll back</pre>

<div class="spacer"></div>

<p>Dans cette situation, <b>sauvegardez les données directement</b> ou alors <b>forcez le membre à effectuer une synchronisation initiale</b>. Pour forcer cette
synchronisation, <b>synchronisez depuis un membre courant</b> de l'ensemble en supprimant le contenu du dossier <b>dbpath</b> pour le membre qui nécessite un rollback
plus gros.</p>
 
<div class="spacer"></div>

<p>Voilà, c'est terminé pour le cours sur <b>les élections</b>. Maintenant, passons à la partie sur le <b>Write Concern</b> ainsi que les <b>préférences de lecture</b> avec MongoDB.
C'est par ici : <a href="concepts_semantiques.php">"Semantiques de Lecture et d'Ecriture du Replica Set" >></a>.

<?php

	include("footer.php");

?>
