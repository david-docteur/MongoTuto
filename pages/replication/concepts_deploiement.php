<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../replication.php">Réplication</a></li>
	<li class="active">Architectures et Déploiements de Replica Set</li>
</ul>

<p class="titre">[ Architectures et Déploiements de Replica Set ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#stra">I) Stratégies</a></p>
	<p class="right"><a href="#nbr">- a) Nombre de Membres</a></p>
	<p class="right"><a href="#dist">- b) Distribution de Membres</a></p>
	<p class="right"><a href="#tags">- c) Cibler les Opérations avec des Tags</a></p>
	<p class="right"><a href="#jour">- d) Le Journaling Contre les Problèmes d'Alimentation</a></p>
	<p class="elem"><a href="#depl">II) Concepts de Déploiement</a></p>
	<p class="right"><a href="#trois">- a) Replica Sets à 3 Membres</a></p>
	<p class="right"><a href="#plus">- b) Replica Sets à 4 Membres ou Plus</a></p>
	<p class="right"><a href="#geo">- c) Replica Sets Distribués Géographiquement</a></p>
</div>

<p>On parle d'<b>architecture</b> de replica set car elle affecte <b>la capacité et les performances</b> de l'ensemble. Nous allons donc voir
<b>différentes facettes</b> d'architectures d'ensemble de répliques. Un ensemble standard consiste en <b>trois membres</b>. Ceux-ci fournissent
la <b>redondance</b> des données ainsi qu'un degré de <b>tolérance</b> à l'erreur Nous allons voir cela dans un instant.

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Si votre application se connecte à plus d'un replica set, veuillez tous leur attribuer un nom.
</div>
<a name="stra"></a>

<div class="spacer"></div>

<p class="titre">I) [ Stratégies ]</p>

<p>Ici, nous allons parler des <b>stratégies</b>, en bref, le nombre de membres ainsi que leur distribution au sein d'un replica set, mais encore
ce que sont les tags et le journaling.</p>
<a name="nbr"></a>

<div class="spacer"></div>

<p class="small-titre">a) Nombre de Membres</p>

<p>Vous devez <b>ajouter des membres</b> à votre ensemble de répliques en suivant les stratégies suivantes.
Optez pour un <b>nombre impair</b> de membres afin de <b>faciliter les votes</b> durant une élection pour élire un nouveau membre primaire.
On préfèrera <b>déployer un arbitre</b> sur un serveur d'application ou un processus partagé.</p>

<div class="spacer"></div>

<p>De plus, afin de considérer un degré de <b>tolérance à l'erreur</b>, le nombre de machine avec tolérance est égal au <b>nombre de membres de l'ensemble</b> auquel on déduit
<b>la majorité requise des membres</b> pour élire un nouveau membre primaire, observez le tableau ci-dessous :</p>

<table>
	<tr>
		<th>Nombre de Membres</th><th>Majorité requise pour élire nouveau Primaire</th><th>Tolérance à l'Erreur</th>
	</tr>
	<tr>
		<td>3</td><td>2</td><td>1</td>
	</tr>
	<tr>
		<td>4</td><td>3</td><td>1</td>
	</tr>
	<tr>
		<td>5</td><td>3</td><td>2</td>
	</tr>
	<tr>
		<td>6</td><td>4</td><td>2</td>
	</tr>
</table>

<p>Ajouter des membres à l'ensemble <b>n'augmente pas toujours</b> le degré de tolérance à l'erreur. Par contre, dans ce genre de situation,
ajouter des membres (cachés ou décalés) peut aider à <b>gérer la sauvegarde ou pour du reporting</b>.</p>

<p>En cas de traffic extrêmement lourd sur votre déploiement, le <b>Load Balancing</b> semble être la solution, il faut <b>distribuer les opérations de lecture
sur les membres secondaires</b> afin d'obtenir de meilleures performances.</p>

<p>Le fait d'avoir une capacité <b>plus grande que la demande permet</b> d'avoir assez d'espace lors de l'ajout d'un nouveau membre. Ajoutez toujours des membres
avant que la demande actuelle <b>ne sature la capacité actuelle de l'ensemble</b>.</p>
<a name="dist"></a>

<div class="spacer"></div>

<p class="small-titre">b) Distribution de Membres</p>

<p>Distribuez vos membres <b>géographiquement</b> pour protéger vos données si votre data center <b>ne répond plus</b>. Gardez au moins un membre dans un autre data center.
Pensez aussi à garder <b>une majorité de membres</b> dans une seule location quand un replica set a <b>plusieurs rembres dans de multiples data centers</b>. Pour répliquer
les données, les membres doivent <b>communiquer avec les autres</b>. Lors d'une élection, tous les membres pouvant voter doivent <b>se détecter</b> afin d'établir une majorité
pour un <b>nouveau membre primaire</b>. Pour être sûr qu'une élection sera menée à bien, gardez une majorité de membres dans <b>une seule location</b>.</p>
<a name="tags"></a>

<div class="spacer"></div>

<p class="small-titre">c) Cibler les Opérations avec des Tags</p>

<p>Utilisez <b>les tags de replica set</b> afin d'être sûrs que les données sont répliquées <b>sur des data centers spécifiques</b>. Les tags permettent aussi
de <b>rediriger les opérations de lectures</b> vers des machines spécifiques.</p>
<a name="jour"></a>

<div class="spacer"></div>

<p class="small-titre">d) Le Journaling Contre les Problèmes d'Alimentation</p>

<p>Lorsque vous avez une <b>interruption d'alimentation</b> (coupure de courant, reboot ou autre ...), <b>activez le journaling</b>. Sans cela, MongoDB <b>ne peut restaurer les données perdues</b> à cause
de ces évênements inattendus. Toutes les version de <b>MongoDB 64 bits</b>, après la 2.0, ont le journaling d'activé par défaut.</p> 
<a name="depl"></a>

<div class="spacer"></div>

<p class="titre">II) [ Concepts de Déploiement ]</p>

<p>Dans cette partie du tutoriel, nous allons décrire les situations de replica sets <b>les plus courantes</b>. Mais bien sûr, vous pouvez en créer d'autre selon les besoins
de votre application ! Vous pouvez coupler les bénéfices de chaque installation.</p>
<a name="trois"></a>

<div class="spacer"></div>

<p class="small-titre">a) Replica Sets à 3 Membres</p>

<p>Cette architecture est <b>la minimale recommandée</b> pour un replica set. Il peut y avoir <b>3 membres</b> ayant les données ou alors <b>2 avec un arbitre</b>.</p>

<p>1) <b>1 primaire avec 2 membres pouvant devenir primaires</b>.</p>

<div class="small-spacer"></div>

<p>2) <b>1 primaire + 1 secondaire + 1 arbitre</b> où l'arbitre n'est ici que pour voter en cas d'élection. Dans cette situation, grâce à l'arbitre, on est sûr que <b>au moins le primaire
ou au moins le secondaire</b> est toujours fonctionnel.</p>
<a name="plus"></a>

<div class="spacer"></div>

<p class="small-titre">b) Replica Sets avec 4 Membres ou Plus</p>

<p>4 Membres ou plus fournissent <b>une meilleure redondance</b> des données et peuvent supporter une <b>meilleure distribution des opérations de lectures</b>
et/ou de fonctionnalités dédiées comme <b>la sauvegarde, le reporting ou la restauration</b>. Ajoutez d'autres membres afin d'augmenter la redondance
et la capacitité à distribuer les opérations de lecture.
Dans ce cas, <b>vérifiez toujours que vous avec un nombre pair de membres</b>. Si vous avez un nombre pair, veuillez <b>ajouter un arbitre</b> qui fera la balance
en cas de vote. Un replica set peut avoir <b>jusqu'à 12 membres maximum dont 7 pouvant voter</b>.
Si vous en voulez plus, veuillez vous réferer au type de réplication master-slave.</p>
<a name="geo"></a>

<div class="spacer"></div>

<p class="small-titre">c) Replica Sets Distribués Géographiquement</p>

<p>Ces replica sets sont <b>distribués géographiquement</b>, c'est-à-dire qui ont <b>des membres dans différentes locations</b>, utile en cas de coupure de courant ou risques liés
à l'environnement. Ajouter des membres de cette façon augmente la redondance des données et fournis un degré de tolérance à l'erreur si un data
center est indisponible. Les membres de data centers additionnels doivent avoir <b>la priorité 0</b> afin de les empêcher de devenir primaires.
Par exemple, le membre primaire <b>est sur le data center principal</b>, de même pour le second membre qui peut devenir primaire,
et un autre secondaire à <b>priorité 0</b> sur un autre data center qui ne peut devenir primaire :</p>

<div class="spacer"></div>

<p>Si le membre primaire est <b>indisponible</b>, le secondaire <b>prend sa place</b>. Si les datas centers <b>ne communiquent plus</b>, le secondaire du data center 2 à priorité 0
<b>ne devient pas le primaire</b>. Si le data center 1 <b>ne répond plus</b>, vous pouvez manuellement <b>récupérer les données</b> depuis le data center 2 avec un downtime minimal.
Avec un <b>writeconcern suffisant</b>, il n'y aura aucune perte des données.
Afin de faciliter les élections, le data center principal doit comporter <b>la majorité des membres</b>, bien sûr, en étant assuré que l'ensemble de répliques a un nombre
<b>impair de membres</b>. Si vous déployez un autre membre dans un autre data center, où là le nombre de membres serait pair, veuillez <b>déployer un arbitre</b>.</p>

<div class="spacer"></div>

<p>Je vous invite maintenant à passer à la <b>page suivante</b> qui portera plus sur <b>le processus d'élection</b> et de <b>FailOver</b> : <a href="concepts_disponibilite.php">"Haute Disponiblité du Replica Set" >></a>.

<?php

	include("footer.php");

?>
