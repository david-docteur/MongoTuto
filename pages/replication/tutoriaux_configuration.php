<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Configuration de Membre</li>
</ul>

<p class="titre">[ Configuration de Membre ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#prio">I) Définir la Priorité d'un Membre d'un Replica Set</a></p>
	<p class="elem"><a href="#emp">II) Empêcher un Secondaire de devenir Primaire</a></p>
	<p class="elem"><a href="#cach">III) Configurer un Membre Caché de Replica Set</a></p>
	<p class="elem"><a href="#deca">IV) Configurer un Membre Décalé de Replica Set</a></p>
	<p class="elem"><a href="#nv">V) Configurer un Membre Non-Votant de Replica Set</a></p>
	<p class="elem"><a href="#arbr">VI) Convertir un Membre Secondaire en Arbitre</a></p>
	<p class="right"><a href="#reut">- a) Convertir un Membre Secondaire en Arbitre et Ré-utiliser son numéro de port</a></p>
	<p class="right"><a href="#nouv">- b) Convertir un Membre Secondaire en Arbitre avec un nouveau numéro de port</a></p>
</div>

<p>Ici, nous allons voir comment <b>configurer chaque type de membre</b> qu'un ensemble de répliques peut contenir. De cette manière, vous aurez un <b>aperçu
global</b> sur ce que vous pouvez faire avec chacun.</p>
<a name="prio"></a>

<div class="spacer"></div>

<p class="titre">I) [ Définir la Priorité d'un Membre d'un Replica Set ]</p>

<p>Pour <b>changer la valeur de la priorité</b> d'un membre du replica set, utilisez la séquence suivante dans un shell mongo :</p>

<pre>
cfg = rs.conf()
cfg.members[0].priority = 0.5
cfg.members[1].priority = 2
cfg.members[2].priority = 2
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>La première opération utilise <b>rs.conf()</b> afin de définir une variable locale <b>"cfg"</b>, celle-ci va contenir un document qui va être la configuration
actuelle du replica set. Les <b>trois opérations</b> suivantes définissent les priorités des membres étant dans le <b>tableau de membres</b>. Puis, l'opération finale
appelle la fonction <b>rs.reconfig()</b> afin d'affecter la nouvelle configuration <b>"cfg"</b> du replica set.
Quand vous mettez à jour la configuration du replica set, accédez aux membres du replica set dans le tableau de membres avec l'indexe du tableau. <b>L'indexe commence
à 0</b>. Ne confondez pas cet indexe avec la valeur du champ <b>"_id"</b> de chaque document.</p>

<p>Si un membre a une priorité définie à 0, il <b>ne peut pas devenir primaire</b> et <b>ne va pas déclencher d'élection</b>. Les membres cachés, décalés et les arbitres ont
<b>tous une priorité à 0</b> alors que tous les autres ont une priorité définie <b>par défaut à 1</b>.
La valeur d'une priorité est un nombre flottant <b>définit entre 0 et 1000</b> et ne servent uniquement à déterminer les préférences lors d'une élection.
Plus un membre aura une priorité élevée et plus il aura donc de chances d'être élu primaire.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La reconfiguration du replica set peut forcer le membre primaire actuel à s'arrêter et déclencher une élection au sein du replica
	set. Une élection ferme toutes les connexions clientes ouvertes.
</div>
<a name="emp"></a>

<div class="spacer"></div>

<p class="titre">II) [ Empêcher un Secondaire de devenir Primaire ]</p>

<p>Si l'on ne veut pas qu'un membre secondaire devienne secondaire lors d'un failover, assignez-lui la priorité 0 comme décrit ci-dessus. Vous pouvez
définir ce mode "secondary-only" pour tous les membres du Replica Set à l'exception du membre primaire bien sûr. Afin de configurer un membre en mode "secondary-only",
définissez sa priorité à 0 dans Document members dans la configuration de son Replica Set. Tout membre ayant une priorité définie à 0 ne va jamais déclencher
d'élection et ne sera jamais élu primaire.</p>

<pre>
{
	"_id" : num,
	"host" : hostname:port,
	"priority" : 0
}
</pre>

<div class="spacer"></div>

<p>MongoDB n'autorise pas le membre primaire à avoir une priorité de 0. Si vous souhaitez empêcher le membre primaire actuelle de devenir primaire encore, 
vous devez d'abord l'arrêter avec la commande rs.stepDown() et vous allez ensuite devoir reconfigurer le Replica Set avec rs.conf() et rs.reconfig().
Par exemple, si nous avns un Replica Set de 4 membres, identifiez chaque membre par son indice dans le tableau :</p>

<pre>
cfg = rs.conf()
cfg.members[0].priority = 2
cfg.members[1].priority = 1
cfg.members[2].priority = 0.5
cfg.members[3].priority = 0
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Cette séquence d'opérations va configurer le Replica Set de telle sorte :</p>

<ul>
	<li><b>- Le membre 0 a une priorité à 2, alors il peut devenir primaire dans n'importe-qu'elle circonstance</b></li>
	<li><b>- Le membre 1 a une priorité de 1, ce qui est la valeur par défaut. Celui-ci peut devenir primaire si aucun autre membre n'a de plus grosse priorité</b></li>
	<li><b>- Le membre 2 a une priorité de 0.5, ce qui le rend moins important que les autres membres lors d'une élection mais il peut quand même le devenir</b></li>
	<li><b>- Le membre 3 a une priorité de 0 donc il ne pourra jamais devenir primaire avec cette priorité</b></li>
</ul>

<p>Quand vous mettez à jour la configuration du Replica Set, accédez aux membres du Replica Set dans le tableau de Membres avec l'indexe du tableau. L'indexe commence
à 0. Ne confondez pas cet indexe avec la valeur du champ "_id" de chaque Document.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La reconfiguration du Replica Set peut forcer le membre primaire actuel à s'arrêter et déclencher une élection au sein du Replica
	Set. Une élections ferme toutes les connexions clientes ouvertes (cela prend entre 10 et 20 secondes). Afin que la configuration s'achève avec succès, 
	une majorité des membres doit être accessible. Si votre Replica Set a un nombre pair de membres, ajoutez un arbitre.
</div>
<a name="cach"></a>

<div class="spacer"></div>

<p class="titre">III) [ Configurer un Membre Caché de Replica Set ]</p>

<p>Les membres cachés font partie du Replica Set mais ne peuvent pas devenir primaires et sont invisibles des applications clientes. En revanche, ces membres
peuvent voter comme les autres. Si le paramètre "chainingAllowed" autorise les membres secondaires à se synchroniser depuis d'autres secondaires, MongoDB
privilégie par défault les membres non-cachés lors de la sélection d'une cible de synchronisation, MongoDB ne les choisira qu'en cas de dernier recours.
Si vous souhaitez qu'un secondaire se synchronise sur un membre caché, utilisez le paramètre "replSetSyncFrom" afin de définir la cible de synchronisation
souhaitée. Pour configurer un membre en tant que caché, attribuez-lui la priorité 0 et sont paramètre hidden à true :</p>

<pre>
{
	"_id" : num
	"host" : hostname:port,
	"priority" : 0,
	"hidden" : true
}
</pre>

<div class="spacer"></div>

<p>Par exemple, la séquence suivante rend le membre de l'indice 0, du tableau de Membres, en caché. Afin de configurer ce membre, utilisez l'exemple suivant
dans le shell mongo du membre primaire :</p>

<pre>
	cfg = rs.conf()
	cfg.members[0].priority = 0
	cfg.members[0].hidden = true
	rs.reconfig(cfg)
</pre>

<p>Après cette reconfiguration, ce membre a une priorité à 0, il ne peut donc pas devenir primaire, puis il devient caché. Les autres membres
ne le mentionneront pas lors d'une commande du genre isMaster ou db.isMaster().Quand vous mettez à jour la configuration du Replica Set, accédez aux membres du Replica Set dans le tableau de Membres avec l'indexe du tableau. L'indexe commence
à 0. Ne confondez pas cet indexe avec la valeur du champ "_id" de chaque Document.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La reconfiguration du Replica Set peut forcer le membre primaire actuel à s'arrêter et déclencher une élection au sein du Replica
	Set. Une élections ferme toutes les connexions clientes ouvertes (cela prend entre 10 et 20 secondes). Afin que la configuration s'achève avec succès, 
	une majorité des membres doit être accessible. Si votre Replica Set a un nombre pair de membres, ajoutez un arbitre.
</div>
<a name="deca"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Configurer un Membre Décalé de Replica Set ]</p>

<p>Pour configurer un membre en tant que caché, définissez-le avec une priorité de 0, hidden à true et son paramètre "saveDelay" avec le nombre de secondes
souhaité.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La valeur de "slaveDelay" du membre secondaire doit se tenir dans la fenêtre de l'Oplog. Si l'Oplog
	est plus court que le saveDelay, le membre décalé ne pourra pas répliquer correctement les informations.
</div>

<div class="spacer"></div>

<p>Lorsque vous configurez un membre décalé, ce délais s'applique à la réplication et à l'Oplog des autres membres. Par exemple,
dans la séquence suivante nous définissions 1h de délais sur le membre secondaire à l'indice 0 du tableau de membres. Effectuez cette opération
dans le shell mongo du membre primaire :</p>

<pre>
cfg = rs.conf()
cfg.members[0].priority = 0
cfg.members[0].hidden = true
cfg.members[0].slaveDelay = 3600
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Une fois que le Replica Set est reconfiguré, le second membre décalé ne peut plus devenir primaire et est caché des applications clientes. L'indice du tableau
commence à 0 et il ne faut pas confondre la valeur de cet indexe avec la valeur du champ "_id" se situant dans chaque Document du tableau de membres.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La reconfiguration du Replica Set peut forcer le membre primaire actuel à s'arrêter et déclencher une élection au sein du Replica
	Set. Une élections ferme toutes les connexions clientes ouvertes (cela prend entre 10 et 20 secondes). Afin que la configuration s'achève avec succès, 
	une majorité des membres doit être accessible. Si votre Replica Set a un nombre pair de membres, ajoutez un arbitre.
</div>
<a name="nv"></a>

<div class="spacer"></div>

<p class="titre">V) [ Configurer un Membre Non-Votant de Replica Set ]</p>

<p>Les membres non-votants vous autorise à ajouter des membres additionnels pour la distribution des lectures au delà de la limite des 7 membres imposée
par MongoDB. Pour configurer un membre de la sorte, définissez la valeur de son paramètres "votes" à 0. Par exemple, si vous souhaitez désactiver l'abilité
à voter pour les membres 4, 5 et 6 du Replica Set, utilisez les commandes suivantes dans le terminal/shell mongo du membre primaire :</p>

<pre>
cfg = rs.conf()
cfg.members[3].votes = 0
cfg.members[4].votes = 0
cfg.members[5].votes = 0
rs.reconfig(cfg)
</pre>

<div class="spacer"></div>

<p>Cette séquence précédente donne le droit à 0 votes pour le membre 4, 5 et 6, depuis le tableau de membres retourné par la commande rs.conf().
Cette configuration autorise ces membres à devenir primaire mais ceux-ci ne pourront jamais voter.
Une fois que le Replica Set est reconfiguré, l'indice du tableau commence à 0 et il ne faut pas confondre la valeur de cet indexe avec la valeur 
du champ "_id" se situant dans chaque Document du tableau de membres.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La reconfiguration du Replica Set peut forcer le membre primaire actuel à s'arrêter et déclencher une élection au sein du Replica
	Set. Une élections ferme toutes les connexions clientes ouvertes (cela prend entre 10 et 20 secondes). Afin que la configuration s'achève avec succès, 
	une majorité des membres doit être accessible. Si votre Replica Set a un nombre pair de membres, ajoutez un arbitre.
</div>

<div class="spacer"></div>

<p>En général et dès que possible, tous les membres doivent avoir 1 vote uniquement. Cela évite les erreurs et empêche les mauvais membres de devenir primaires.
Utilisez le paramètre "priority" pour déterminer quel membre serait plus apte à devenir primaire.</p>
<a name="arbr"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Convertir un Membre Secondaire en Arbitre ]</p>

<p>Si vous avez un membre secondaire dans votre Replica Set qui n'a plus besoin de retenir des données mais qui est essentiel afin de définir un membre primaire
en cas de vote, vous voudriez surement convertir ce membre en Arbitre en utilisant l'une des deux procédures suivantes :</p>

<p>_ Vous voudrez garder le même numéro de port du membre secondaire. Pour cela, vous allez devoir arrêter le membre et supprimer ses données avant de le redémarrer
et de le configurer en tant qu'Arbitre.
_ Vous voudrez changer de numéro de port ou vous pourrez reconfigurer le serveur en tant qu'Arbitre avant d'arrêter l'instance du secondaire.</p>
<a name="reut"></a>

<div class="spacer"></div>

<p class="small-titre">a) Convertir un Membre Secondaire en Arbitre et Ré-utiliser son numéro de port</p>

<p>1) Si votre application se connecte directement au secondaire, modifiez votre application de manière à ce que les requêtes n'atteignent pas le secondaire.
2) Arrêtez le membre secondaire.
3) Supprimez le secondaire du Replica Set en appelant la méthode rs.remove(). effectuezz cette opération en étant connecté au shell mongo du membre primaire.</p>

<pre>rs.remove("hostname:port")</pre>

<div class="spacer"></div>

<p>4) Vérifiez que votre Replica Set n'inclut plus le membre secondaire en appelant la méthode rs.conf() dans mongo.
5) Bougez les données du secondaire dans un dossiers d'archives par exemple :</p>

<pre>mv /data/db /data/db-old</pre>

<div class="spacer"></div>

<p>Ou alors, supprimez ces données, comme vous le voulez.
6) Créez un nouveau répertoire des données, vide, sur lequel vous allez redémarrer votre instance mongod :</p>

<pre> mkdir data/db</pre>

<div class="spacer"></div>

<p>7) Redémarrez l'instance mongod pour le membre secondaire en spécifiant le numéro de port, le dossier vide des données et le Replica Set. Vous pouvez utiliser
le même numéro de port que le membre secondaire utilisait auparavant :</p>

<pre>mongod --port 27021 --dbpath /data/db --replSet rs</pre>

<div class="spacer"></div>

<p>8) Dans le shell mongo, convertissez le membre secondaire en Arbitre en utilisant la commande rs.addArb() :</p>

<pre>rs.addArb("hostname:port")</pre>

<div class="spacer"></div>

<p>9) Vérifiez que l'Arbitre appartienne au Replica Set avec la méhode rs.conf() dans le shell mongo :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>Le membre Arbitre doit include le paramètre :</p>

<pre>"arbiterOnly" : true</pre>
<a name="nouv"></a>

<div class="spacer"></div>

<p class="small-titre">b) Convertir un Membre Secondaire en Arbitre avec un nouveau numéro de port</p>

<p>1) Si votre application est directement connectée au secondaire, ou alors a un connection string référençant le secondaire, modifiez l'application
afin que les requêtes MongoDB ne l'atteignent pas.
2)Créez un nouveau dossier de données, vide, qui sera utilisé pour le nouveau numéro de port :</p>

<pre>mkdir /data/db-temp</pre>

<div class="spacer"></div>

<p>3) Démarrez une instance mongod pour le membre secondaire en spécifiant le nouveau numéro de port, le dossier vide des données et le Replica Set.
Vous pouvez utiliser le même numéro de port que le membre secondaire utilisait auparavant :</p>

<pre>mongod --port 27021 --dbpath /data/db-temp --replSet rs</pre>

<div class="spacer"></div>

<p>4) Dans le shell mongo connecté au membre primaire, convertissez la nouvelle instance mongod en Arbitre :</p>

<pre>rs.addArb("hostname:port")</pre>

<div class="spacer"></div>

<p>5) Vérifiez que l'Arbitre appartienne au Replica Set avec la méhode rs.conf() dans le shell mongo :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>Le membre Arbitre doit include le paramètre :</p>

<pre>"arbiterOnly" : true</pre>

<div class="spacer"></div>

<p>6) Arrêtez le membre secondaire.
7)Supprimez le secondaire du Replica Set :</p>

<pre>rs.remove("hostname:port")</pre>

<div class="spacer"></div>

<p>8) Vérifiez que le Replica Set n'inclut plus le vieux membre secondaire en appelant rs.conf() depuis le shell mongo :</p>

<pre>rs.conf()</pre>

<div class="spacer"></div>

<p>9) Bougez les données du secondaire dans un dossier d'achives :</p>

<pre>mv /data/db /data/db-old</pre>

<div class="spacer"></div>

<p>Vous pouvez aussi supprimer les données si vous le souhaitez.</p>

<div class="spacer"></div>

<p>Je vous invite maintenant à passer à la page suivante : <a href="tutoriaux_maintenance.php">"Maintenance de Replica Set" >></a>.

<?php

	include("footer.php");

?>
