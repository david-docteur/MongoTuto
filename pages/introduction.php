<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Introduction</li>
</ul>

<div class="titre">[ Introduction ]</div>

<p class="first-line">Vous voilà dans la <b>première partie</b> de ce tutoriel, <b>l'introduction</b>, qui va vous expliquer ce qu'est <b>MongoDB</b>, l'entreprise en question, d'ou vient cette technologie, les auteurs ainsi que tout autre information, juste pour la <b>culture</b> :)</p>

<div class="spacer"></div>

<div class="titre">[ Quoi ? ]</div>

<p>Qu'est-ce que c'est que <b>MongoDB</b> ? MongoDB est un <b>Système de Gestion de Bases de Données</b> tout comme MySQL, Oracle, PostGreSql et bien d'autres ... Seulement, MongoDB est un SGBD <b>orienté Documents</b>, et non <b>relationnel</b> comme la plupart d'entre vous connaissent déjà.
Qu'est-ce que cela veut dire ? C'est très simple, dans un <b>SGBD Relationnel (SGBDR)</b>, les données vont êtres stockées dans des <b>tables sous forme de lignes/tuples</b> afin de mémoriser les informations. Avec MongoDB c'est différent
car nous allons stocker les informations dans, non pas des tables, mais des <b>collections</b>. Ces collections vont contenir des <b>documents JSON</b>.
Nous allons revenir sur cette notion un peut plus loin.</p><p>Ce système est classé <b>NoSQL</b>, ce qui veut dire que MongoDB n'utilise pas de langage <b>Structured Query Language (SQL)</b> normalisé comme la plupart des SGBD classiques.
Le nom MongoDB vient du mot Anglais <b>"humongous"</b> qui signifie <b>"faramineux"</b>. Pourquoi ? car MongoDB est destiné au <b>stockage de données de masses</b> et a été conçu pour gérer de <b>grosses quantités d'opérations</b>.
Bien sûr, cela ne veut pas dire que l'on ne peut pas l'utiliser pour de <b>petites bases de données</b>, bien au contraire ! L'entreprise <b>10gen</b>, fondatrice de MongoDB, est dirigée par <b>Mr Dwight Merriman</b>, qui était le co-fondateur de <b>Double-Click</b> en 1995, qui ensuite a été rachetée par <b>Google</b>.
Créé initialement en 2007 et offrant sa sortie officielle en 2009, MongoDB est totalement <b>OpenSource</b> ! Vous pouvez aller faire un tour sur le <a href="https://jira.mongodb.org" target="_blank">JIRA</a> du projet pour ceux qui sont adeptes de la méthode de management <b>AGILE</b>.</p>

<div class="spacer"></div>

<div class="titre">[ Les chiffres ]</div>

<p class="first-line">Qui utilise MongoDB et quelles en sont les statistiques aujourd'hui ? Il y a, <b>aujourd'hui plus de 5 000 000 de téléchargements</b>, <b>100 000 inscriptions à l'<a href="https://education.mongodb.com/" target="_blank">Université MongoDB</a> en ligne</b>, <b>20 000 groupes d'utilisateurs</b>, <b>600 clients comprenant d'importantes entreprises</b> et bien d'autres ...</p>

<div class="spacer"></div>

<div class="titre">[ Les Utilisateurs ]</div>

<p class="first-line">De grandes entreprises vous dîtes ?</p>

<div class="small-spacer"></div>

<table id="logosEntreprises">
	<tr>
		<td><a href="http://www.sap.com" target="_blank"><img alt="SAP" src="/img/logos/logo-sap.jpg" height="80" width="150"></a></td>
		<td><a href="http://www.cisco.com" target="_blank"><img alt="Cisco" src="/img/logos/logo-cisco.png" height="80" width="150"></a></td>
		<td><a href="http://www.ea.com" target="_blank"><img alt="EA" src="/img/logos/logo-ea.png" height="80" width="150"></a></td>
		<td><a href="http://www.ebay.com" target="_blank"><img alt="eBay" src="/img/logos/logo-ebay.jpg" height="80" width="150"></a></td>
		<td><a href="http://www.forbes.com" target="_blank"><img alt="Forbes" src="/img/logos/logo-forbes.gif" height="80" width="150"></a></td>
	</tr>
	<tr>
		<td><a href="https://www.foursquare.com" target="_blank"><img alt="Foursquare" src="/img/logos/logo-foursquare.png" height="80" width="150"></a></td>
		<td><a href="http://www.leroymerlin.com" target="_blank"><img alt="Leroy Merlin" src="/img/logos/logo-leroy-merlin.png" height="80" width="140"></a></td>
		<td><a href="http://www.mcafee.com" target="_blank"><img alt="McAfee" src="/img/logos/logo-mcafee.jpg" height="80" width="150"></a></td>
		<td><a href="http://www.orange.com" target="_blank"><img alt="Orange" src="/img/logos/logo-orange.jpg" height="100" width="110"></a></td>
		<td><a href="http://www.mtv.com" target="_blank"><img alt="MTV" src="/img/logos/logo-mtv.jpg" height="100" width="150"></a></td>
	</tr>
</table>

<p>Autant dire que ce n'est pas rien ! <b>MongoTuto.com</b> aussi utilise MongoDB pour les <a href="news.php">"News"</a> par exemple. Bon, ce n'est pas aussi impressionnant que la <b>liste du dessus</b>, mais quand même ! :)</p>
<p>Si vous souhaitez consulter la liste complète des clients, c'est par ici => <a href="http://www.mongodb.com/customers" target="_blank">http://www.mongodb.com/customers</a>.</p>

<div class="spacer"></div>

<div class="titre">[ Où ? ]</div>
<p class="first-line">Des offices un peu partout : New York City, San Fransisco, Palo Alto, Atlanta, Londres et Dublin.</p>

<div class="spacer"></div>

<div class="titre">[ Pourquoi MongoDB ? ]</div>

<ul>
	<li><p class="un-list">Gratuit et OpenSource</p>Totalement <b>gratuit</b>, qui dit mieux ? En revanche, pour ceux qui souhaitent accéder à une <b>assistance</b>
	et un niveau de <b>sécurité</b> plus poussés, la version <b>MongoDB Enterprise</b> est payante.</li><br />
	<li><p class="un-list">Interopérabilité</p>MongoDB est disponible sous <b>Microsoft Windows</b>, <b>GNU/Linux</b> et <b>Apple Mac OS</b></li><br />
	<li><p class="un-list">Supporte les architectures 32 et 64bits</p>Attention car la version 32 bits est réservée pour du <b>déploiement de test uniquement</b>.</li><br />
	<li><p class="un-list">Réalisé en C++ :</p>L'un des des langages de programmation le plus <b>performant</b> et <b>reconnu</b> au monde entier.</li><br />
	<li><p class="un-list">Sécurité :</p>Les injections SQL ? <b>Vous oubliez</b> ! MongoDB ne rigole pas avec la <b>sécurité des données</b>.</li><br />
	<li><p class="un-list">Flexibilité des données :</p>Le schéma des données est <b>dynamique</b> ! Plus de détails dans la suite du tutoriel ...</li><br />
	<li><p class="un-list">Documents JSON/BSON</p>Un format de données <b>léger</b> au niveau du stockage et <b>encodable/décodable</b> facilement.</li><br />
	<li><p class="un-list">Big Data</p>Vous souhaitez gérer et manipuler des <b>données de masse</b> et vous devez supporter des <b>opérations lourdes</b> ? MongoDB est fait pour ça !</li><br />
	<li><p class="un-list">Rapidité/Performances</p>Décuplez vos <b>performances</b> et déployez un environnement dont vous pourrez augmenter facilement les <b>capacités</b>.</li><br />
	<li><p class="un-list">API pour divers langages de programmation</p>MongoDB fournit plusieurs drivers afin de supporter <b>plusieurs langages</b> (C, C++, Php, Java, Python, Ruby, .NET etc ...).</li><br />
</ul>

<div class="spacer"></div>

<div class="titre">[ A qui s'adresse ce tutoriel ? ]</div>

<p">A tout le monde ! MongoDB se veut <b>simple</b>, <b>ouvert</b> à tous et même jusqu'aux plus <b>experts</b> dans le domaine :</p>

<div class="small-spacer"></div>

<p><b>Etudiants ?</b></p>

<ul>
	<li><p>L'occasion d'apprendre une <b>nouvelle technologie</b> et d'<b>explorer d'autres horizons</b>.</p></li><br />
	<li><p>L'opportunité d'<b>impressionner</b> et de vous <b>démarquer</b> aux yeux de votre profs !</p></li><br />
	<li><p>D'attirer l'attention sur votre <b>CV</b> bien sûr, <b>être original</b>, <b>sortir du lot</b> après, ou même <b>avant</b>, la fin de vos études.</p></li><br />
</ul>

<div class="small-spacer"></div>

<p><b>Professionnels ?</b></p>

<ul>
	<li><p>Ayez une <b>vision différente</b> du <b>Big Data</b> et collaborez avec une équipe d'<b>experts confirmés</b>.</p></li><br />
	<li><p>Accédez à une <b>sécurité</b> plus accrue et <b>protégez</b> vos données d'une autre façon.</p></li><br />
	<li><p>Libérez plus de <b>performances</b> et augmentez la <b>redondance</b> de vos informations.</p></li><br />
</ul>

<div class="spacer"></div>

<div class="titre">[ Principes de Base et Différences avec MySQL ]</div>

<p>Bien, avant de poursuivre sur le chapitre d'<a href="installation.php">"Installation"</a> du tutoriel, vous allez devoir apprendre quelques principes de base
qui vont vous changer "un peu". Il faut savoir que, comme MongoDB est un SGBD orienté Documents, il adopte une <b>syntaxe NoSQL</b> et non normalisée SQL comme avec MySQL, Oracle ou PostGreSql par exemple.</p>

<p>Les principes <b>changent</b> et vous devez vous familiariser avec. Etant développeur Java qui utilise MongoDB, on s'y fait <b>très vite</b>.
Bien entendu, <b>à gauche</b>, le jargon des SGBDR SQL classiques, et <b>à droite</b>, notre nouveau jargon MongoDB, regardez :</p>

<table id="sqlToMongo">
	<tr>
		<th>SGDB SQL</th>
		<th>MongoDB</th>
	</tr>
	<tr>
		<td>Base de données</td>
		<td>Base de données</td>
	</tr>
	<tr>
		<td>Table</td>
		<td>Collection</td>
	</tr>
	<tr>
		<td>Ligne/tuples</td>
		<td>Document ou Document JSON/BSON</td>
	</tr>
	<tr>
		<td>Colonne</td>
		<td>Champs</td>
	</tr>
	<tr>
		<td>Indexe</td>
		<td>Indexe</td>
	</tr>
	<tr>
		<td>Clé primaire</td>
		<td>Clé primaire (champ_id automatique)</td>
	</tr>
	<tr>
		<td>Aggrégation (group by etc ...)</td>
		<td>Pipeline d'aggrégation</td>
	</tr>
</table>

<p>Attendez-vous au départ à dire : <b>"Alors on va insérer dans la table ... oops ... la collection ...."</b>. Cela prouve que ça fonctionne !</p>

<div class="spacer"></div>

<p class="titre">[ Exemple de Document JSON ]</p>

<p>Vous pouvez voir ici un exemple de <b>document JSON</b>.</p>

<pre>
{
  _id: ObjectID("306a8fb2e3f4878bd2f983f8"),
  id_utilisateur: "abc123",
  age: 24,
  statut: 'Salarié'
}
</pre>

<div class="small-spacer"></div>

<p>En fait pas tout à fait ... <b>je vous ai mentit</b> ! Ici c'est un <b>document BSON</b> ! Alors la différence ?
Nous allons voir ça dans le chapitre sur les <a href="operations_crud.php">"Opérations CRUD"</a> après le chapitre des différentes installations de MongoDB.
JSON et BSON sont globalement <b>la même chose</b>, excepté le fait que BSON offre certains <b>types de données en plus</b> (comme le type ObjectID ci-dessus par exemple).
Vous pouvez donc employer l'un des deux termes comme bon vous semble.</p>

<div class="spacer"></div>

<p class="titre">[ Exécutables ]</p>

<p>Ici aussi, <b>très rapidement</b>, pour que vous voyez, globalement, à quoi correspondent les <b>exécutables principaux</b> de MongoDB par rapport à MySQL :</p>

<table id="sqlToMongo">
	<tr>
		<th>MySQL</th>
		<th>MongoDB</th>
	</tr>
	<tr>
		<td>Serveur	mysqld/oracle</td>
		<td>mongod</td>
	</tr>
	<tr>
		<td>Client mysql/sqlplus</td>
		<td>mongo</td>
	</tr>
</table>

<div class="spacer"></div>

<p>Vous pouvez maintenant passer à l'étape d'<b>installation de MongoDB</b> sur votre ordinateur dans le chapitre suivant <a href="installation.php">"Installation" >></a></p>
<p>Votre MongoDB est <b>déjà installé</b> ? Très bien ! Alors passons directement au chapitre sur les <a href="operations_crud.php">"Opérations CRUD" >></a>.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Ah oui, j'oubliais ! Comme tout tutoriel, les textes de MongoTuto.com comportent probablement de légers détails
	qui manquent, des fautes d'orthographe, des points qui ne vous paraissent pas clairs ou même avec lesquels vous allez être en total désaccord. Aucun soucis, je suis ouvert
	à toute critique sur le site, c'est comme ça que MongoTuto progressera. Donc surtout, si vous avez des questions ou des remarques, même celles qui vous semblent
	les plus bêtes au possible, contactez-moi, les questions idiotes ça n'existe pas ! Allez, bon courage !
</div>

<?php

	include("footer.php");

?>
