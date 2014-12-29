<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../securite.php">Sécurité</a></li>
	<li class="active">Introduction</li>
</ul>

<p class="titre">[ Introduction ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#def">I) Défense en Profondeur</a></p>
	<p class="elem"><a href="#env">II) Environnements Sécurisés</a></p>
	<p class="elem"><a href="#ris">III) Pratiques Courantes pour Réduire les Risques</a></p>
	<p class="elem"><a href="#cry">IV) Cryptage des Données</a></p>
	<p class="elem"><a href="#str">V) Stratégies Additionnelles de Sécurité</a></p>
	<p class="elem"><a href="#ide">VI) Identification de Vulnérabilité</a></p>
	<p class="elem"><a href="#inj">VII) Injections SQL</a></p>
</div>

<p>Comme toute application exécutée au sein d'un réseau, les administrateurs MongoDB doivent <b>prendre en compte l'aspect sécurité</b> ainsi que l'exposition
aux risques pour un déploiement MongoDB. Il n'y a pas de <b>solutions miracles</b> pour faire face aux différents risques, et maintenanir un déploiement MongoDB
sécurisé est un <b>processus continu</b>.</p>
<a name="def"></a>

<div class="spacer"></div>

<p class="titre">I) [ Défense en Profondeur ]</p>

<p>Les documents de ce chapitre prennent une approche de <b>"Défense en Profondeur"</b> afin de sécuriser vos déploiements MongoDB et traitent de <b>différentes méthodes</b>
pour <b>gérer les risques</b> et en <b>réduire l'exposition</b>.
Le but principal de l'approche par "Défense en Profondeur" est de s'assurer <b>qu'il n'y a pas de points exploitables</b> dans votre déploiement qui pourraient
permettre à un intrus <b>d'accéder à vos données</b> stockées dans votre base de données MongoDB. Le moyen le plus facile et le plus efficace de réduire les risques
d'exploitation est d'exécuter MongoDB <b>dans un environnement sécurisé</b>, de <b>limiter l'accès</b>, de <b>réduire les privilèges système</b> et de <b>suivre les meilleures
pratiques de déploiement et de développement</b>.</p>
<a name="env"></a>

<div class="spacer"></div>

<p class="titre">II) [ Environnements Sécurisés ]</p>

<p>Le moyen <b>le plus efficace</b> de réduire les risques pour les déploiements MongoDB est d'exécuter votre déploiement entier, incluant tous les composants MongoDB
tels que les instances <b>mongod</b> et <b>mongos</b> ainsi que les instances de votre application, dans un environnement <b>sécurisé</b>. Les environnements sécurisés utilisent les 
principes suivants pour gérer le contrôle d'accès :</p>

<ul>
	<li><b>utilisez un filtre réseau (par exemple : un firewall) ayant des règles qui bloquent toutes les connexions de systèmes inconnus à vos composants MongoDB.</b></li>
	<li><b>liez vos instances mongod et mongos à une adresse IP spécifique pour limiter l'accessibilité.</b></li>
	<li><b>limiter les programmes MongoDB aux réseaux locaux non-publiques et VPN.</b></li>
</ul>
<a name="ris"></a>

<div class="spacer"></div>

<p class="titre">III) [ Pratiques Courantes pour Réduire les Risques ]</p>

<p>Vous pourrez réduire les risques davantage en <b>contrôlant l'accès</b> à la base de données en employant <b>l'authentification et l'autorisation</b>.
Exigez l'authentification pour l'accès aux instances MongoDB en utilisant des identifiants <b>les plus complexes possible</b>. Cela devrait faire partie
des règles internes de votre organisation. Employez l'autorisation et déployez un modèle nécessitant <b>le moins de privilèges possible</b>, où tous les utilisateurs
ont uniquement les droits nécessaires à ce dont ils ont besoin pour effectuer leurs tâches quotidiennes.
En suivant les meilleures pratiques de développement et de déploiement, qui incluent : <b>validation des saisies</b>, <b>gestion des sessions</b> et <b>contrôle d'accès
au niveau de l'application</b>.</p>

<p>Exécutez les processus <b>mongod</b> et <b>mongos</b> sous un seul utilisateur uniquement ayant <b>les permissions et les accès minimums</b>. N'exécutez jamais un programme
MongoDB en tant que <b>root</b> ou ayant <b>des privilèges administratifs</b>. Les utilisateurs système qui exécutent les processus MongoDB doivent garantir des identifiants
<b>solides</b> et <b>difficiles</b> à deviner afin d'empêcher un accès non-autorisé.

Pour limiter votre environnement encore plus, vous pouvez exécuter les processus mongos ou mongod dans un <b>environnement chroot</b>. Les restrictions d'accès
aux utilisateurs et la configuration de chroot suivent des conventions recommandées pour administrer tous les <b>processus démons</b> sur les systèmes de type <b>Unix</b>.</p>
<a name="cry"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Cryptage des Données ]</p>

<p>Afin de supporter des besoins d'audits, vous aurez probablement besoin de <b>crypter</b> les données stockées dans MongoDB. Pour de meilleurs résultats, 
vous pouvez crypter ces données dans la couche applicative en cryptant le contenu des champs qui contiennent des <b>données sensibles</b> (par exemple, crypter
les mots de passe en SHA1).

De plus, MongoDB a un partenariat avec <a href="https://www.mongodb.com/partners/technology/gazzang" target="_blank">"Gazzang"</a> pour crypter et sécuriser
les données sensibles dans MongoDB. Cette solution crypte les données en <b>temps réel</b> et Gazzang fournit une gestion de <b>clés</b> avancée qui s'assure que seulement
les processus autorisés puissent accéder aux données. Le logiciel Gazzang vérifie que les clés cryptographiques restent sécurisées et assure une 
<b>compatibilité</b> avec certains standards tels que <b>HIPAA</b>, <b>PCI-DSS</b> et <b>FERPA</b>.
Vous pouvez obtenir plus d'informations ici <a href="http://www.gazzang.com/images/datasheet-zNcrypt-for-MongoDB.pdf" target="_blank">"Présentation"</a> et un <a href="http://gazzang.com/resources/videos/partner-videos/item/209-gazzang-zncrypt-on-mongodb" target="_blank">"Webinar"</a>.</p>
<a name="str"></a>

<div class="spacer"></div>

<p class="titre">V) [ Stratégies Additionnelles de Sécurité ]</p>

<p>MongoDB fournit de <b>multiples stratégies</b> pour réduire les risques au sein du réseau, comme <b>configurer MongoDB</b> ou <b>configurer votre firewall</b>.
De plus, vous pouvez considérer le chapitre sur la <a href="interfaces_api.php">"Sécurité et Interfaces de l'API MongoDB"</a> pour réduire les risques
liés à l'interface du shell mongo, de l'interface HTTP et de l'API REST.
MongoDB Enterprise supporte l'authentification utilisant <b>Kerberos</b> également.</p> 
<a name="ide"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Identification de Vulnérabilité ]</p>

<p><b>MongoDB prend la sécurité très au sérieux</b>.
Si vous découvrez une <b>vulnérabilité</b> dans MongoDB ou si vous voulez en savoir plus sur le reporting de vulnérabilités et le processus de réponse,
passez au chapitre sur la <a href="rapport_vulnerabilite.php">"Création d'un Rapport de Vulnérabilité"</a>. En effet, MongoDB est open-source, ce qui lui permet
de <b>profiter du savoir-faire des experts du monde entier</b>.</p>
<a name="inj"></a>

<div class="spacer"></div>

<p class="titre">VII) [ Injections SQL ]</p>

<p>Ce qui est pratique avec MongoDB, c'est que les attaques de type <b>injection SQL</b> traditionnelles, qui sont très répandues, sont absolument <b>inefficaces</b>
avec MongoDB donc aucun risque à avoir à ce niveau là. En effet, la requête reçoit un document BSON plutôt qu'une chaîne de caractères interprétée
liée à une <b>syntaxe SQL normalisé</b>.
Peut-importe le caractère, spécial ou non, rien ne sera pris en compte comme les simples ou doubles guillemets par exemple.</p>

<div class="spacer"></div>

<p>Allez ! Assez discuté, maintenant passons au chapitre sur le <a href="controle_acces.php">"Contrôle d'Accès" >></a> sous MongoDB.</p>

<div class="spacer"></div>
	
<?php

	include("footer.php");

?>
