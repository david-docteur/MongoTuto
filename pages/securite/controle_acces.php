	<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Contrôle d'Accès</li>
</ul>

<p class="titre">[ Contrôle d'Accès ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#auth">I) Authentification</a></p>
	<p class="elem"><a href="#auto">II) Autorisation</a></p>
	<p class="elem"><a href="#coll">III) La Collection system.users</a></p>
</div>

<p>MongoDB fournit le support pour <b>l'authentification</b> et <b>l'autorisation</b> pour chaque base de données. Les utilisateurs existent dans le contexte <b>d'une seule base de données
logique</b>. Regardons de plus près la différence entre les deux.</p>
<a name="auth"></a>

<div class="spacer"></div>

<p class="titre">I) [ Authentification ]</p>

<p>MongoDB implémente <b>l'authentification</b>, ou la vérification de l'identité de l'utilisateur, par base de données. L'authentification empêche l'accès
anonyme à la base de données. Pour l'authentification basique, MongoDB stocke les identifiants utilisateur dans une collection <b>"system.users"</b> de la base de
données.

L'authentification est <b>désactivée par défaut</b>. Pour l'activer pour un <b>mongod</b> ou <b>mongos</b>, utilisez les paramètres de configuration <b>"auth"</b> et <b>"keyFile"</b>.
Pour les installations <b>MongoDB Enterprise</b>, l'authentification utilisant un service <b>Kerberos</b> est disponible.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Vous pouvez vous authentifier avec un seul utilisateur par base de données uniquement. Si vous vous authentifiez
	à une base de données en tant qu'utilisateur et que plus tard vous vous authentifiez sur la même base de données en tant qu'utilisateur différent,
	la seconde authentification invalide la première. Par contre, vous pouvez vous identifier sur une autre base de données avec un utilisateur différent sans
	invalider vos connexions sur les autres bases de données.
</div>
<a name="auto"></a>

<div class="spacer"></div>

<p class="titre">II) [ Autorisation ]</p>

<p>MongoDB fournit <b>l'autorisation</b>, ou l'accès aux bases de données et aux opérations, par base de données. MongoDB utilise <b>les rôles</b> pour l'autorisation
et stocke les rôles de chaque utilisateur dans un <b>document de privilèges</b> dans la collection <b>"system.users"</b> de votre base de données.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La base de données "admin" fournit les rôles indisponibles dans d'autres bases de données, incluant un rôle qui
	rend, de manière efficace, un utilisateur en un superutilisateur MongoDB.
</div>

<div class="small-spacer"></div>

<p>Pour assigner des rôles aux utilisateurs, vous devez être un utilisateur <b>avec un rôle administratif</b> sur cette base de données. Pour cela, vous devez créer,
 dans un premier temps, un utilisateur administratif.</p>
<a name="coll"></a>

<div class="spacer"></div>

<p class="titre">III) [ La Collection system.users ]</p>

<p>La collection <b>"system.users"</b> d'une base de données stocke les informations pour <b>l'authentification</b> et <b>l'autorisation</b> de cette base de données.
Spécifiquement, la collection stocke <b>les identifiants</b> des utilisateurs pour l'authentification ainsi que <b>les privilèges</b> de chacun pour l'autorisation.
MongoDB nécessite l'autorisation d'accéder à la collection <b>"system.users"</b> dans le but d'éviter les attaques de type <b>escalade de privilèges</b>. Pour accéder à cette collection,
vous devez avoir soit un rôle <b>"userAdmin"</b> soit <b>"userAdminAnyDatabase"</b>.
Depuis la version 2.4, le schéma de la collection "system.users" a changé de le but d'adopter une autorisation plus fiable utilisant un modèle de 
privilège utilisateur, appelé <b>document de privilèges</b>.</p> 

<div class="spacer"></div>

<p>Allez ! Assez discuté, maintenant passons au chapitre sur <a href="auth_processus.php">"Authentification Inter-Processus" >></a> sous MongoDB.</p>
	
<?php

	include("footer.php");

?>
