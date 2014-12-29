<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Utiliser les Commandes de Base de Données</li>
</ul>

<p class="titre">[ Utiliser les Commandes de Base de Données ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#form">I) Forme d'une Commande de Base de Données</a></p>
	<p class="elem"><a href="#util">II) Utiliser les Commandes</a></p>
	<p class="elem"><a href="#comm">III) Commandes admin de Base de Données</a></p>
	<p class="elem"><a href="#repo">IV) Réponses des Commandes</a></p>
</div>

<p>L'interface de commande MongoDB fournit un accès à toutes les opérations non CRUD de base de données. Récupérer des statistiques du serveur, initialiser
un Replica Set et exécuter une Map-Reduce par exemple sont tout à fait réalisable avec des commandes.
Vous désirez accéder à toute la liste des commandes ? La voici : <a href="http://docs.mongodb.org/manual/reference/command" target="_blank">Liste des Commandes
de Base de Données MongoDB</a>.</p>
<a name="form"></a>

<div class="spacer"></div>

<p class="titre">I) [ Forme d'une Commande de Base de Données ]</p>

<p>Vous spécifiez une commande en commençant par construire le document BSON dont la première clé est le nom de la commande. Par exemple, 
spécifiez la commande isMaster en utilisant le document BSON suivant :</p>

<pre>{ isMaster: 1 }</pre>
<a name="util"></a>

<div class="spacer"></div>

<p class="titre">II) [ Utiliser les Commandes ]</p>

<p>Le shell mongo offre une méthode qui va permettre d'exécuter ces commandes et qui se nomme db.runCommand(). L'opération suivante dans le shell mongo exécute
la commande précédente :</p>

<pre>db.runCommand( { isMaster: 1 } )</pre>

<p>Plusieurs drivers fournissent un équivalent pour la méthode db.runCommand(). Internallement, exécuter des commandes avec la méthode db.runCommand()
est équivalent à une requête spéciale contre la collection $cmd.
Plusieurs commandes courantes ont leur propre méthodes dans le shell mongo telle que la méthode db.isMaster() dans le shell JavaScript mongo.</p>
<a name="comm"></a>

<div class="spacer"></div>

<p class="titre">III) [ Commandes admin de Base de Données ]</p>

<p>Vous aurez probablement besoin d'effectuer des commandes sur la base de données admin. Normallement, ces opérations ressemblent à :</p>

<pre>
use admin
db.runCommand( {buildInfo: 1} )
</pre>

<p>Mais vous avez aussi une autre méthode qui vous permet d'exécuter une commande directement sur la collection admin :</p>

<pre>db._adminCommand( {buildInfo: 1} )</pre>
<a name="repo"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Réponses des Commandes ]</p>

<p>Toutes les commandes retournent, au moins, un document avec le champ ok indiquant si le commande a réussit ou non :</p>

<pre>{ 'ok': 1 }</pre>

<p>Les commandes qui échouent retournent ok avec la valeur 0.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="gerer_mongod.php">"Gérer les Processus mongod" >></a>.</p>

<?php

	include("footer.php");

?>
