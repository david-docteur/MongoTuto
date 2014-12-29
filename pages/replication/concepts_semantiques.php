<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Sémantiques de Lecture et d'Ecriture du Replica Set</li>
</ul>

<p class="titre">[ Sémantiques de Lecture et d'Ecriture du Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#wc">I) Le Write Concern</a></p>
	<p class="right"><a href="#veri">- a) Vérifier les Opérations d'Ecriture</a></p>
	<p class="right"><a href="#modi">- b) Modifier le Write Concern par Défaut</a></p>
	<p class="right"><a href="#para">- c) Write Concerns Paramétrables</a></p>
	<p class="elem"><a href="#pref">II) Préférences de Lecture</a></p>
	<p class="right"><a href="#mode">- a) Modes de Préférence de Lecture</a></p>
	<p class="right"><a href="#tags">- b) Ensembles de Tags</a></p>
	<p class="elem"><a href="#proc">III) Processus de Préférences de Lecture</a></p>
	<p class="right"><a href="#sele">- a) Sélection de Membre</a></p>
	<p class="right"><a href="#dema">- b) Demande d'Association</a></p>
	<p class="right"><a href="#essa">- c) Ré-éssai Automatique</a></p>
	<p class="right"><a href="#sc">- d) Préférences de Lecture Dans un Cluster Fragmenté</a></p>
</div>

<p>D'un point de vue d'une application cliente, le fait que MongoDB soit sur <b>un seul serveur (mode standalone)</b> ou sur <b>un ensemble de répliques (Replica Set)</b>
est invisible. Par défaut, les opérations de lecture envoyées à un ensemble de répliques retournent des résultats depuis <b>le membre primaire de l'ensemble</b> et
sont cohérents <b>grâce à la dernière opération d'écriture</b>.</p>

<p>Les utilisateurs voudront peut-être configurer <b>la préférence de lecture</b> par connexion afin de préférer des opérations de lecture basées sur un membre secondaire.
Si les clients configurent la préférence de lecture afin d'autoriser <b>la lecture depuis un membre secondaire</b>, les opérations de lecture ne pourront retourner
de résultats depuis les membres <b>n'ayant pas répliqués les opérations les plus récentes</b>. Quand vous lisez depuis un membre secondaire, une requête peut
retourner de données reflettant leur état précédent.</p>

<p>Ce comportement est quelquefois caractérisé de <b>cohérence éventuelle</b> car l'état du second membre va éventuellement refletter l'état du membre primaire et MongoDB
ne peut garantir <b>une stricte cohérence</b> des données pour la lecture des données depuis un membre secondaire.
Afin de garantir l'intégrité et la cohérence des données les plus récentes lues depuis un second membre, vous pouvez <b>configurer le client et le driver</b>
pour s'assurer que toutes les opérations d'écriture réussissent sur tous les membres avant de <b>s'achever complètement</b>. Pour cela, veuillez lire un peu plus
sur le <b>write concern</b> dans le paragaprahe suivant.</p>
<a name="wc"></a>

<div class="spacer"></div>

<p class="titre">I) [ Le Write Concern ]</p>

<p>Ce que l'on appelle le <b>Write Concern</b> chez MongoDB est <b>la guarantie dont une application a besoin</b> afin de considérer une opération d'écriture réussie.
Ceci va confirmer les opérations d'écriture du membre primaire du repica set. La commande <b>getLastError</b> retourne des informations sur <b>la dernière 
opérations d'écriture effectuée</b>, ces informations correspondront à l'erreur s'il y en a une, ou alors aux informations comme quoi tout
s'est bien déroulé.</p>
<a name="veri"></a>

<div class="spacer"></div>

<p class="small-titre">a) Vérifier les Opérations d'Ecriture</p>

<p>Le write concern par défaut <b>confirme les opérations d'écriture</b> sur le membre primaire. Vous pouvez le configurer pour confirmer ces opérations sur d'autres
membres du replica set toujours en utilisant la fonction getLastError <b>avec l'option "w"</b>.
Cette option <b>"w"</b> confirme que les opérations d'écriture ont été répliqués sur le nombre de membres spécifié de l'ensemble de répliques, incluant
le membre primaire. Vous pouvez soit <b>spécifier un nombre</b>, soit <b>spécifier une majorité qui va s'assurer que l'écriture se propage sur la majorité des membres
de l'ensemble</b>.</p>

<div class="small-spacer"></div>

<p>Si vous spécifiez une valeur "w" <b>plus grande que le nombre de membres qui comportent une copie des données</b>, cette opération bloque jusqu'à ce que ces membres
deviennent disponibles. Cela peut causer un <b>blocage</b> de cette opération pour toujours ! Pour cela, vous pouvez spécifier un timeout pour l'opération
<b>getLastError</b>, utilisez l'argument <b>"wtimeout"</b>. Ce paramètre <b>définit à 0</b> va induire le faire que l'opération ne s'arrêtera jamais.</p>
<a name="modi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Modifier le Write Concern par Défaut</p>

<p>Vous pouvez configurer votre propre comportement pour la commande getLastError pour un replica set. Utilisez <b>le paramètre getLastErrorDefault</b> dans la
configuration du replica set que nous découvrirons un peu plus loin. La séquence suivante créée une configuration qui attend que l'opération d'écriture
<b>soit terminée sur la majorité des membres de l'ensemble</b> :</p>

<pre>
cfg = rs.conf()
cfg.settings = {}
cfg.settings.getLastErrorDefaults = {w: "majority"}
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Le paramètre <b>getLastErrorDefaults</b> concerne seulement les commandes <b>getLastError</b> qui n'ont pas d'autres arguments.</p>

<div class="alert alert-danger">	
	<u>Attention</u> : Utiliser un write concern peu efficace peut mener à des rollbacks dans le cas d'un failover d'un replica set.
	Assurez-vous toujours d'avoir le bon write concern pour votre application.
</div>
<a name="para"></a>

<div class="spacer"></div>

<p class="small-titre">c) Write Concerns Paramétrables</p>

<p>Vous pouvez <b>utiliser des tags du replica set</b> pour créer votre propre write concern en utilisant la commande getLastErrorDefaults et la commande
getLastErrorModes.</p>

<div class="alert alert-danger">	
	<u>Attention</u> : Les modes personnalisés de write concerns spécifient le nom du champ et un nombre de valeurs distinctes pour ce champ.
	En conséquences, les préférences de lecture utilisent les valeurs des champs dans <b>le document tag</b> afin de rediriger les opérations de lecture.
	Dans certains cas, vous devriez pouvoir utiliser <b>les mêmes tags</b> pour les préférences de lecture et les write concerns. Sinon, vous aurez besoin de
	créer des tags additionnels pour les write concerns <b>selon les besoins de votre application</b>.
</div>

<div class="spacer"></div>

<p>Prenons un exemple avec les write concerns à tag simple, considérons un replica set de 5 membres où chaque membre a un de ces tags :</p>

<pre>
{ "use": "reporting" }
{ "use": "backup" }
{ "use": "application" }
{ "use": "application" }
{ "use": "application" }
</pre>

<div class="spacer"></div>

<p>Vous devriez créer un mode de write concern personnalisé qui va s'assurer que les opérations d'écriture applicables ne vont pas retourner de résultat
avant que les membres ayant au moins deux valeurs du <b>tag "use"</b> ai bien pris en compte l'opération. Vous pouvez créer le mode suivant dans le shell
mongo avec <b>la séquence suivante</b> :</p>

<pre>
cfg = rs.conf()
cfg.settings = { getLastErrorModes: { use2: { "use": 2 } } }
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Afin d'utiliser ce mode, passez la chaîne de caractère <b>"use2" au paramètre "w"</b> de la commande getLastError :</p>

<pre>db.runCommand( { getLastError: 1, w: "use2" } )</pre>

<div class="spacer"></div>

<p>Dans ce cas, nous allons parler de write concerns particuliers, si vous avez un replica set avec 3 membres suivants :</p>

<pre>
{ "disk": "ssd" }
{ "disk": "san" }
{ "disk": "spinning" }
</pre>

<div class="spacer"></div>

<p>Vous ne pouvez pas spécifier de <b>valeur personnalisée</b> pour le paramètre getLastErrorModes afin d'être sûr que l'écriture s'est propagée à "san" avant de
revenir. En revanche, vous pouvez implémenter cette règle de write concern en <b>crééant les tags additionnels</b> suivants :</p>

<pre>
{ "disk": "ssd" }
{ "disk": "san", "disk.san": "san" }
{ "disk": "spinning" }
</pre>

<div class="spacer"></div>

<p>Ensuite, créez une valeur spécifiée de getLastErrorModes comme suivant :</p>

<pre>
cfg = rs.conf()
cfg.settings = { getLastErrorModes: { san: { "disk.san": 1 } } }
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Pour utiliser ce mode, <b>passez le string "san" à l'option "w"</b> de getLastError :</p>

<pre>db.runCommand( { getLastError: 1, w: "san" } )</pre>

<div class="spacer"></div>

<p>Cette opération ne sera pas retournée avant qu'un membre de l'ensemble ayant <b>le tag "disk.san"</b> ne retourne un résultat.
Vous voudrez définir alors un mode personnalisé de write concern en tant que mode par défaut utilisant <b>getLastErrorDefaults</b> :</p>

<pre>
cfg = rs.conf()
cfg.settings.getLastErrorDefaults = { ssd: 1 }
rs.reconfig(cfg)
</pre>
<a name="pref"></a>

<div class="spacer"></div>

<p class="titre">II) [ Préférences de Lecture ]</p>

<p>Cette préférence permet aux applications de contrôler comment MongoDB <b>redirige les opérations de lecture</b> aux bons membres du replica set.
Par défaut, une application redirige les opération de lecture sur le membre primaire du replica set. Lire les données depuis le membre primaire <b>garantie
que ces données vont être les plus récentes</b>. En revanche, vous pouvez, totalement ou partiellement, rediriger les opérations de lecture sur <b>un ou plusieurs
membres de l'ensemble de répliques</b> afin d'optimiser ces opérations et le temps de réponse si votre application ne nécessite pas forcément toujours des données
complètement à jour immédiatement.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Vous devez faire attention lorsque vous spécifiez des préférences de lecture, les modes autres que primaire peuvent retourner
	des données qui ne seront pas exactement les plus à jour car les requêtes redirigées vers les membres secondaires ne vont pas forcément inclure les données
	des opérations d'écriture les plus récentes que le membre primaire aura exécuté.
</div>

<div class="spacer"></div>

<p>Les cas suivants sont <b>courants</b> et <b>n'utilisent pas</b> de mode de préférence de lecture primaires :</p>

<div class="small-spacer"></div>

<p> - Exécuter des opérations système qui n'affecterons pas l'application front-end : <b>distribuer des opérations de lecture
à des membres secondaires</b> aide à partager les ressources et évitent de <b>surcharger le membre primaire</b>. Cela peut être un bon choix
pour faire du reporting ou pour de l'analyse de charge de travail.</p>

<div class="alert alert-success">
	<u>Astuce</u> : Les préférences de lecture ne sont pas faites pour pour rediriger les connexions à une seule instance mongod.
	Par contre, dans le but de rediriger les opérations de lecture sur un membre secondaire du Replica Set, vous devez définir une préférence de lecture
	comme "secondary".
</div>

<div class="small-spacer"></div>

<p> - Fournir des lectures locales pour les applications <b>géographiquement distribuées</b> : si vous avez des serveurs d'application dans <b>de multiples data centers</b>,
vous voudrez sûrement avoir un ensemble de répliques géographiquement distribué et d'utiliser une préférence de lecture non primaire ou la <b>"nearest"</b>.
Cela <b>réduit la latence réseau</b> en ayant le serveur d'application qui effectue ses opérations de lecture <b>sur les membres secondaires les plus proches</b>, plutôt que
sur un primaire distant.</p>

<div class="small-spacer"></div>

<p> - Maintenir la disponibilité durant un failover : Utilisez la propriété <b>"primaryPreferred"</b> si vous voulez que votre application effectue ses lectures depuis
le membre primaire en temps normal, mais d'autoriser des lectures <b>un peu moins récentes sur les membres secondaires</b> en cas d'urgence. Cela fournit un mode
<b>"read-only"</b> pour votre application quand un failover se produit.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : En général, ne pas utiliser les propriétés "primary" et "primaryPreferred" afin de fournir plus de capacité.
	Le Sharding augmente les capacités de lecture et d'écriture en distribuant ces opérations à travers un groupe de machines, et est souvent une meilleure
	stratégie afin d'ajouter de la capacité.
</div>
<a name="mode"></a>

<div class="spacer"></div>

<p class="small-titre">a) Modes de Préférence de Lecture</p>

<div class="alert alert-danger">
	<u>Attention</u> : Tous les modes de préférence de lecture, excepté le mode "primary", peuvent ne pas retourner les données les
	plus à jour car les membres secondaires répliquent les opérations du membre primaire avec un léger décalage. Soyez sûrs que votre application
	peut se permettre de tolérer des données ne reflettant pas toujours l'état le plus à jour.
</div>

<div class="spacer"></div>

<p>Nouveau dans la version 2.2, les drivers MongoDB <b>offrent 5 modes de préférence de lecture</b> :</p>

<table>
	<tr>
		<th>Mode Préférence Lecture</th><th>Description</th>
	</tr>
	<tr>
		<td>primary</td><td>Mode par défaut, toutes les opérations de lecture depuis le membre primaire de l'ensemble.</td>
	</tr>
	<tr>
		<td>primaryPreferred</td><td>Lecture par défaut sur le membre primaire, mais bascule sur les secondaires si primaire non disponible.</td>
	</tr>
	<tr>
		<td>secondary</td><td>Toutes les opérations de lecture avec les membres secondaires.</td>
	</tr>
	<tr>
		<td>secondaryPreferred</td><td>Lecture par défaut sur les membres secondaires, mais basculent sur le primaire si aucun secondaire disponible.</td>
	</tr>
	<tr>
		<td>nearest</td><td>Les lectures sont basculées sur le membre le plus proche, sans tenir compte du type.</td>
	</tr>
</table>

<div class="spacer"></div>

<p>Vous pouvez spécifier un mode de préférence de lecture sur <b>des objets de connexion</b>, <b>des objets de bases de données</b>, <b>des objects collection</b> ou bien <b>par
opération</b>. La syntaxe permettant de spécifier le mode <b>dépend du langage de programmation</b> que vous utilisez.
Les modes sont aussi disponibles pour les clients se connectant à un cluster fragmenté <b>à travers une instance mongos</b>. L'instance mongos
obéït à <b>des préférences de lecture spécifiques</b> quand une connexion au replica set (qui fournit chaque shard du cluster) est effectuée.</p>

<p>Dans un shell mongo, la commande curseur <b>readPref()</b> fournit un accès aux préférences de lecture. Si les opérations de lecture représentent un large pourcentage
de votre application, <b>distribuer les opérations de lecture sur les membres secondaires</b> semble être le meilleur choix. En revanche, dans la plupart des cas,
le sharding <b>fournit un meilleur support</b> pour ce genre d'opérations vu que les clusters peuvent distribuer les opérations de lecture et d'écriture à travers
<b>un groupe d'ordinateurs</b>.</p>
<a name="tags"></a>

<div class="spacer"></div>

<p class="small-titre">b) Ensembles de Tags</p>

<p>Les ensembles de tags vous donnent <b>la possibilité de personnaliser vos préférences de lecture</b> et write concerns afin que votre application redirige les
opérations sur des membres spécifiques.
Les préférences de lecture et les write concerns personnalisés vérifient <b>les tags</b> de différentes façons : les préférences de lecture prennent en compte
la valeur d'un tag <b>lors de la sélection d'un membre</b> à partir duquel on peut lire les données. Alors que les write concerns ignorent la valeur d'un tag
lors de la sélection d'un membre sauf pour vérifier si la valeur d'un tag <b>est unique ou non</b>.
Vous pouvez choisir un ensemble de tags avec les modes de préférence de lecture suivants :</p>

<p class="un-list">- primaryPreferred</p>
<p class="un-list">- secondary</p>
<p class="un-list">- secondaryPreferred</p>
<p class="un-list">- nearest</p>

<div class="spacer"></div>

<p>Les tags ne sont pas <b>compatibles avec le mode "primary"</b> et s'appliquent en général lors de la sélection d'un membre primaire de l'ensemble pour une opération
de lecture. En revanche, <b>le mode de lecture "nearest"</b>, combiné avec un ensemble de tags, va <b>sélectionner le membre le plus proche</b> qui correspond au tag spécifié,
ce qui devrait être un primaire et un secondaire.
Toutes les interfaces utilisent <b>la même logique de sélection de membre</b> afin de choisir le membre qui recevra les opérations de lecture, en se basant sur
le mode de préférence de lecture et les ensembles de tags.</p> 
<a name="proc"></a>

<div class="spacer"></div>

<p class="titre">III) [Processus de Préférences de Lecture ]</p>

<p>Avec les ensemble de répliques, les opérations de lecture peuvent avoir <b>un comportement et des principes différents</b>.
Les drivers de MongoDB utilisent les procédures suivantes afin de rediriger les opérations des replica sets et des clusters fragmentés.
Pour déterminer comment rediriger ces opérations, les applications <b>mettent à jour leur vue</b> selon l'état du replica set, identifiant quel membre
est <b>actif ou inactif</b>, quel membre est primaire et vérifient la latence de chaque instance mongod.</p>
<a name="sele"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sélection de Membre</p>

<p>Les clients, par l'intermédiaire de leur driver et des instances mongos pour les clusters fragmentés, mettent périodiquement à jour leur vue de l'état du
replica set. Quand vous choisissez la préférence de lecture non-primaire, le driver va <b>déterminer le membre à choisir</b> en suivant ce processus :</p>

<div class="small-spacer"></div>

<p>1) <b>Assembler la liste des membres disponibles</b> en prenant en compte leur type (primaire, secondaire ou autres).</p>
<p>2) <b>Exclure les membres ne correspondant pas aux ensembles de tags</b> si ceux-ci sont spécifiés.</p>
<p>3) <b>Déterminer quel membre est le plus proche du client</b>.</p>
<p>4) <b>Etablir une liste des membres définis sous le seuil d'une certaine distance de ping</b> (en millisecondes) du membre le plus proche <b>"nearest"</b>.
   La latence acceptable est de <b>15 millisecondes</b>, que vous pouvez modifier dans le rriver avec sa propre option <b>"secondaryAcceptableLatencyMS"</b>.
   Pour les instances mongos, vous pouvez utiliser les options <b>--localThreshold</b> ou <b>localThreshold</b> pour définir cette valeur.</p>
<p>5) <b>Sélectionner un membre de la liste aléatoirement</b>. Ce membre reçoit finalement les opérations de lecture.</p>

<div class="spacer"></div>

<p>Le driver peut ensuite associer <b>le thread ou la connexion avec le membre sélectionné</b>. Cette demande d'association est configurable par l'application.
Jettez un oeil à la configuration de demande d'association de votre driver.</p>
<a name="dema"></a>

<div class="spacer"></div>

<p class="small-titre">b) Demande d'Association</p>

<div class="alert alert-danger">
	<u>Attention</u> : La demande d'association est configurable par votre application. Veuillez consulter la documentation de votre
	driver afin d'obtenir plus de renseignements sur la configuration et le comportement par défaut de celle-ci.
</div>

<p>De part le fait que les membres secondaires subiront <b>une légère latence</b> derrière le membre primaire, les opérations de lecture sur ceux-ci vont
refletter des données ayant <b>un état différent dans le temps</b>. Pour empêcher les lectures séquentielles de se balader dans le temps, le driver peut associer
les <b>threads de votre application à un membre spécifique de l'ensemble</b> après la première lecture, et donc, empêcher la lecture depuis les autres membres.
La thread va continuer de lire <b>depuis le même membre</b> jusqu'à ce que :</p>

<ul>
	<li><b>L'application effectue une lecture avec une préférence de lecture différente</b></li>
	<li><b>La Thread se termine ou</b></li>
	<li><b>Le socket client reçoit une exception, ce qui est provoqué par une erreur sur le réseau ou alors quand un processus mongod ferme
	les connexion durant un FailOver. Cela déclenche un autre essai, invisible de l'application.</b></li>
</ul>

<p>Quand vous utilisez une requête d'association, si le client détecte que l'ensemble a élu un nouveau membre primaire, le driver va <b>annuler
toutes les associations entre les threads et les membres</b>.</p>   
<a name="essa"></a>

<div class="spacer"></div>

<p class="small-titre">c) Ré-éssai Automatique</p>

<p>Les connexions entre les drivers MongoDB et les instances mongod dans un ensemble de répliques doivent <b>renvoyer 2 concerns</b> :</p>
<p>1) Le client doit essayer de <b>préférer les résultats les plus à jour</b>, et toute connexion doit lire depuis le même membre de l'ensemble
tant que possible.</p>
<p>2) Le client doit <b>minimiser le temps où la base de données est inaccessible</b> à cause d'un problème de connexion, panne réseau ou failover du replica set.</p>

<div class="small-spacer"></div>

<p>Par conséquent, les drivers MongoDB et les instances mongod :</p>

<ul>
	<li><b>Réutilisent une connexion d'une instance mongod spécifique aussi longtemps que possible après avoir établit une connexion sur cette instance.</b></li>
	<li><b>Tenter de se reconnecter à un nouveau membre, respecter les modes de préférence de lecture existants, si la connecion au mongod est perdue.
	  Les reconnexions sont transparentes de votre application. Si la connexion est permise sur des membres secondaires après une reconnexion, votre
	  application pourra recevoir deux lectures séquentielles retournées depuis différents membres secondaires. En fonction de l'état des données répliquées
	  sur chaque membre secondaire, les Documents pourront refletter des données de votre base de données à différents moments.</b></li>
	<li><b>Retourner une erreur seulement après avoir essayé de se connecter à trois membres de l'ensemble qui correspondent au mode de préférence de lecture
	  et à l'ensemble de tags. S'il y a moins de 3 membres dans l'ensemble, le Client va retourner une erreur après avoir tenté de se connecter à tous les membres
	  de l'ensemble.
	  Après cette erreur, le Driver sélectionne un nouveau membre utilisant le mode de préférence de lecture. En l'absence d'un mode, le driver utilise le
	  membre primaire.</b></li>
	<li><b>Après détecter une situation de failover, le driver tente de rafraîchir l'état du replica set aussi vite que possible.</b></li>  
</ul>
<a name="sc"></a>

<div class="spacer"></div>

<p class="small-titre">d) Préférences de Lecture dans un Sharded Cluster</p>

<p>Avant la version 2.2, <b>mongos ne supportait pas les sémantiques de modes de préférence de lecture</b> que nous allons détailler un peu plus tard.
Dans la plupart des clusters fragmentés, chaque Shard est un replica set. Par conséquent, les préférences de lecture sont applicables.
En considérant les préférences de lecture, les opérations de lecture dans un cluster fragmenté sont <b>identiques aux replica sets</b> non fragmentés.</p>

<p>Contrairement aux simples replica sets, dans les clusters fragmentés <b>toutes les interractions avec les shards passent des clients aux instances
mongos</b> qui sont actuellement connectées aux membres de l'ensemble. mongos est donc responsable des préférences de lecture, <b>ce qui est invisible pour
vos applications</b>. Il n'y a pas de configuration spéciale requise pour le support total des modes de préférence de lecture en environnement fragmenté, du moment
que les mongos <b>soient au moins à la version 2.2</b>. Tous les mongos maintiennent leur propre <b>pool de connexion aux membres du replica set</b> :</p>

<ul>
	<li>Une requête sans préférence définie est primary, par défaut. Afin d'éviter les confusions, définissez explicitement votre mode de préférence de lecture.</li>
	<li>Tous les membres nearest et les calculs de latence reflettent la connexion entre les instances mongos et mongod, pas le client et les instances mongod.
	  Cela produit le résultat désiré car tous les résultats doivent passer par mongos avant de retourner au client.</li>
</ul>

<div class="spacer"></div>

<p>Passons à la page suivante afin d'en apprendre plus sur <b>le processus de réplication</b> : <a href="concepts_processus.php">"Processus de Réplication" >></a>.

<?php

	include("footer.php");

?>
