<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Introduction aux Replica Sets</li>
</ul>

<p class="titre">[ Introduction aux Replica Sets ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#buts">I) Buts de la Réplication</a></p>
	<p class="elem"><a href="#repl">II) Réplication avec MongoDB</a></p>
	<p class="elem"><a href="#asyn">III) Réplication Asynchrone</a></p>
	<p class="elem"><a href="#fo">IV) FailOver Automatique</a></p>
	<p class="elem"><a href="#fa">V) Fonctionnalités Additionnelles</a></p>
</div>

<p>Allez, commençons cette <b>introduction sur la réplication</b> ! Dans un premier temps, nous allons définir le mot <b>"réplication"</b> dans le contexte de MongoDB. La réplication est <b>le processus de synchronisation
d'un ensemble de données sur de multiples serveurs</b>. En bref, vous avez votre base de données (ou plusieurs) et vous allez vouloir <b>copier vos données
sur d'autres serveurs</b> pour des raisons de sauvegarde et de redondance. Passons au <b>premier paragraphe</b> afin de mieux s'imprégner du sujet.</p>
<a name="buts"></a>

<div class="spacer"></div>

<p class="titre">I) [ Buts de la Réplication ]</p>

<p>Nous allons voir <b>pourquoi</b> et <b>dans quels cas</b> nous souhaiterions affecter <b>une phase de réplication</b> pour un système de production sous MongoDB.
La réplication <b>améliore la redondance des informations</b>, c'est-à-dire la <b>recopie/multiplication</b> de ces mêmes données, et donc, en <b>améliorer
leur accessibilité</b>. En ayant une copie de votre base de données sur plusieurs serveurs de base de données, vous évitez <b>la perte totale</b> de vos informations
si le premier serveur crash ou si le disque dûr est affecté. Mais pas seulement, ce processus de réplication vous aide à <b>restituer vos données</b>
en cas d'échec matériel ou pour toute interruption de service. Avec plusieurs copies de vos données, vous pouvez dédier une réplique (un des serveurs de base de données)
à une opération de <b>récupération des données</b>, de <b>reporting</b> ou de <b>sauvgerade</b>.</p>
<p>Vous pouvez également utiliser le processus de réplication afin d'<b>accroître vos performances de lecture</b> de données. En effet, certains clients ont la possibilité
d'effectuer des <b>opérations de lectures et d'écriture</b> sur différents serveurs. De même, vous pouvez maintenir plusieurs copies dans <b>différents data centers</b> afin
d'<b>améliorer la localité</b> et <b>la disponibilité</b> des données pour les applications distribuées se situant dans des lieux différents.</p>
<a name="repl"></a>

<div class="spacer"></div>

<p class="titre">II) [ Réplication avec MongoDB ]</p>

<p>Comme nous l'avons brièvement décrit dans la page d'accueil, <b>un ensemble de répliques</b>, ou plus communément appelé <b>"Replica Set"</b>, correspond à <b>un ensemble de
processus mongod qui hébergent le même ensemble de données</b>. Le premier mongod, <b>le mongod primaire</b>, reçoit toutes les opérations de lecture. Les autres instances mongod, <b>les répliques
secondaires</b>, appliquent les instructions reçues par le membre primaire afin d'avoir <b>exactement le même ensemble de données</b>.
Les opérations d'écritures des clients sont aussi acceptées par le membre primaire du replica set. <b>Un replica set ne peut avoir qu'un seul
membre primaire</b> uniquement qui accepte les opérations d'écriture. Afin de supporter la réplication, le membre primaire enregistre <b>tous les changements
réalisés</b> sur son ensemble de données dans <b>son oplog</b>. Nous allons discuter de l'oplog un peu plus tard mais on peut globalement le qualifier <b>de journal
enregistrant toutes les opérations d'écritures</b> du membre. On sait donc tout ce qu'il s'y passe.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/introduction/intro_1.png" data-lightbox="intro_repl" title="Routage des Opérations de Lectures et d'Ecritures"><img src="/img/replication/introduction/intro_1.png" /></a>
<p><h6><b>Image 1.0</b> - Routage des Opérations de Lectures et d'Ecritures.</h6></p>

<div class="spacer"></div>

<p><b>Les membres secondaires</b>, en s'appuyant sur <b>l'oplog du membre primaire</b>, répliquent ces informations sur leur propre ensemble de données afin d'avoir une
<b>copie exacte</b> de celle du membre primaire. Si jamais le membre primaire devient <b>indisponible</b> pour quelconque raison (crash du serveur, panne matérielle etc ...), le replica
set va <b>élire un des membres secondaires</b> pour qu'il devienne primaire et prendre la place de celui qui ne répond plus. Par défaut, les clients
<b>lisent les données</b> depuis le membre primaire, par contre, les clients peuvent <b>modifier les préférences de lectures</b> afin de lire depuis l'un des membres
secondaires.</p>
<p>Afin d'élire efficacement un membre secondaire pour devenir un membre primaire, vous devriez <b>ajouter une instance mongod</b> définit pour jouer <b>le rôle d'arbitre</b>.
Les arbitres n'ont <b>aucun pouvoir sur l'ensemble des données</b>, ils ne sont là <b>uniquement pour voter</b> en cas d'élection requise. Si vous avez un nombre pair
de membres secondaires, l'arbitre peut <b>déterminer la majorité du vote</b> et <b>désigner le membre secondaire approprié</b>.
Un arbitre ne nécessite <b>aucun matériel</b> et <b>ne change jamais de rôle</b>, un secondaire peut passer en primaire, un primaire en tant que secondaire
mais l'arbitre <b>reste un arbitre</b> quoiqu'il arrive.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/introduction/intro_2.png" data-lightbox="intro_repl" title="Replica Set - 1 Primaire et 2 Secondaires"><img src="/img/replication/introduction/intro_2.png" /></a>
<p><h6><b>Image 1.1</b> - Replica Set - 1 Primaire et 2 Secondaires.</h6></p>

<div class="small-spacer"></div>

<a class="screenshot" href="/img/replication/introduction/intro_3.png" data-lightbox="intro_repl" title="Replica Set - 1 Primaire, 1 Secondaire et 1 Arbitre"><img src="/img/replication/introduction/intro_3.png" /></a>
<p><h6><b>Image 1.2</b> - Replica Set - 1 Primaire, 1 Secondaire et 1 Arbitre.</h6></p>

<div class="spacer"></div>

<p>La réplication avec MongoDB ne <b>supporte que 12 membres maximum</b>, sachant que <b>7 à la fois uniquement peuvent voter</b>. Si vous désirez <b>plus de 12 membres</b>
dans votre replica set, veuillez jetter un oeil à <b>la configuration Master-Slave</b>.
La <b>configuration minimale</b> requise est constituée d'<b>un membre primaire</b>, <b>un membre secondaire</b> et <b>un arbitre</b>. Mais la plupart des systèmes
sont configurés pour avoir <b>un membre primaire</b> et <b>deux secondaires</b> afin de stocker les données.</p>
<a name="asyn"></a>

<div class="spacer"></div>

<p class="titre">III) [ Réplication Asynchrone ]<p>

<p>Les membres secondaires du replica set <b>copient les opérations</b> du membre primaire, depuis l'oplog, <b>de manière asynchrone</b>, peut importe le temps que
prendra la copie d'une opération sur un des ensemble de données, le membre primaire et le/les autre(s) secondaires <b>continuerons d'exécuter leurs tâches</b>.
En conséquences, un membre secondaire ne retourne pas forcément les données <b>les plus récentes</b> immédiatement.</p>
<a name="fo"></a>

<div class="spacer"></div>

<p class="titre">IV) [ FailOver Automatique ]</p>

<p>Une situation de <b>failover</b> fait référence au fait qu'un serveur primaire <b>échoue et ne réponde plus</b>, on va donc <b>balancer les opérations</b> vers d'autres serveurs.
Ici, c'est ce qu'il se passe avec MongoDB, si le membre primaire ne répond plus <b>pendant 10 secondes</b>, une <b>élection</b> a lieue pour désigner un des membres secondaires en tant
que <b>nouveau primaire</b>. Le premier membre secondaire qui obtient <b>la majorité de votes</b> devient donc le membre primaire du replica set.
Les <b>battements de coeur</b> ou <b>"heartbeats"</b>, comme indiqués sur les images 1.1, 1.2 et 1.3, vont vérifier <b>continuellement</b> si un chaque membre
répond ou non.</p>

<div class="spacer"></div>

<a class="screenshot" href="/img/replication/introduction/intro_4.png" data-lightbox="intro_repl" title="Election d'un Nouveau Membre Primaire"><img src="/img/replication/introduction/intro_4.png" /></a>
<p><h6><b>Image 1.4</b> - Election d'un Nouveau Membre Primaire.</h6></p>
<a name="fa"></a>

<div class="spacer"></div>

<p class="titre">V) [ Fonctionnalités Additionnelles ]</p>

<p>MongoDB offre la possibilité de <b>configurer votre replica set</b> en fonction de vos besoins. Par exemple, vous désirez peut-être déployer un replica set
avec des membres <b>étalés sur plusieurs data centers</b>, ou même <b>contrôler les priorités</b> de chaque membre lors d'une élection.
Vous pouvez même <b>dédier une ou plusieurs machines</b> qui ne serviront que de <b>reporting</b>, de <b>restauration</b> ou de <b>sauvegarde</b>.</p>

<div class="spacer"></div>

<p>Voilà, la fin de l'introduction qui vous donne une vue d'ensemble sur <b>la réplication avec MongoDB</b>. Accédez maintenant aux <b>différents concepts</b> de réplication pour voir plus en détails les options disponibles et comment configurer
votre replica set avec les différents membres disponibles : <a href="concepts_membres.php">"Membres du Replica Set" >></a>.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
