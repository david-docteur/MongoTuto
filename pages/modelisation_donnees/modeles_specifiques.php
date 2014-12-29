<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../modelisation_donnees.php">Modélisation de Données</a></li>
	<li class="active">Contextes Spécifiques d'Applications</li>
</ul>

<p class="titre">[ Contextes Spécifiques d'Applications ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#oa">I) Données pour Opérations Atomiques</a></p>
	<p class="elem"><a href="#mc">II) Données pour la Recherche de Mots-Clés</a></p>
</div>

<p>Quelques spécifications sur <b>la gestion des opérations atomiques</b> ainsi que sur <b>la recherche de mots-clés</b>. Je n'en dirai pas plus pour l'instant,
on passe <b>directement</b> aux explications :</p>
<a name="oa"></a>

<div class="spacer"></div>

<p class="titre">I) [ Données pour Opérations Atomiques ]</p>

<p>Imaginons qu'un <b>étudiant</b> veuille <b>emprunter un livre</b> depuis la bibliothèque de l'Université : Il va falloir <b>trouver le livre</b> que l'étudiant
souhaite emprunter, puis <b>modifier les informations d'emprunt</b>, c'est-à-dire <b>décrémenter de 1</b> le nombre d'exemplaires disponibles, puis, <b>ajouter des informations</b>
sur l'étudiant dans <b>un tableau</b> prévu à cet effet. Normalement, <b>deux requêtes</b> sont requises pour ce genre d'opération mais ici, <b>une seule requête</b> suffira. Si de <b>multiples transactions</b> se produisent
, les informations pourraient <b>s'entre-mêler</b> si un autre étudiant essaye de réserver <b>le même livre au même moment</b> !
Pour corriger cela, il y a la méthode <b>findAndModify()</b> qui va permettre d'effectuer <b>une requête atomique</b> en sélectionnant et modifiant les informations du livre.
Les données seront <b>synchronisées</b> pour <b>plus de cohérence</b> et <b>éviter les erreurs</b> de réservation.
Prenons le livre suivant qui est disponible en<b> 2 exemplaires</b>, et qui a déjà été emprunté par <b>Jean le 27 Septembre 2013</b> :</p>

<div class="small-spacer"></div>

<pre>
livre = {
	_id: 123456789,
	titre: "L'Astronomie pour les Nuls",
	auteur: [ "Stephen Maran", "Pascal Bordé" ],
	date_publication: ISODate("2007-06-01"),
	pages: 354,
	langue: "Français",
	id_editeur: "Editions Générales First",
	disponibles: 2,
	reservations: [ { par: "jean", date: ISODate("2013-09-27") } ]
}
</pre>

<div class="spacer"></div>

<p>Pour que Jean puisse réserver son livre, sans qu'un autre étudiant le réserve <b>au même moment que lui</b>, tout en gardant les données synchronisées :</p>

<pre>
db.livres.findAndModify ( {
   query: {
            _id: 123456789,
            disponibles: { $gt: 0 }
          },
   update: {
             $inc: { disponibles: -1 },
             $push: { reservations: { par: "jean", date: new Date() } }
           }
} )
</pre>

<div class="small-spacer"></div>

<p>Voilà, de cette manière, chaque fois qu'une réservation aura lieu, il sera <b>impossible d'effectuer une réservation</b> sur un livre qui n'est plus disponible
car celui-ci a été réservé <b>juste avant que la transaction</b> soit terminée.</p>
<a name="mc"></a>

<div class="spacer"></div>

<p class="titre">II) [ Données pour la Recherche de Mots-Clés ]</p>

<p>Cette fonctionnalité va vous permettre de <b>rechercher des mots-clés</b> dans un tableau de mots dans un document, pour satisfaire <b>la fonction recherche</b> de votre application/site web
par exemple. Vous pouvez ensuite <b>créer un indexe multi-clés</b> (voir le chapitre sur les indexes pour plus de détails).</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : La recherche de mots-clés n'est pas utilisable pour de la recherche dans une phrase ou un block de texte.
</div>

<div class="spacer"></div>

<p>Si vous souhaitez <b>ajouter des mots-clés</b> à votre collection de livres par exemple :</p>

<pre>
{
	titre : "Moby-Dick" ,
	auteur : "Herman Melville" ,
	publication : 1851 ,
	ISBN : 0451526996 ,
	sujets : [
			"baleine", "vengeance", "Americain",
			"novel", "nautique", "voyage", "mer"
		 ]
}
</pre>

<div class="spacer"></div>

<p>Ensuite, créez <b>l'indexe multi-clés</b> afin d'optimiser la sélection de mots :</p>

<pre>db.volumes.ensureIndex( { sujets: 1 } )</pre>

<div class="small-spacer"></div>

<p>L'indexe multi-clés s'appelle de cette façon car il va <b>créer un indexe pour chaque mot-clé</b> contenu dans le tableau <b>"sujets"</b>, où chaque mot est une clé.
Par exemple, il y aura un indexe de créé pour le mot <b>"baleine"</b> et pour <b>"nautique"</b> ainsi que tous les autres.
Pour, ensuite, trouver les titres de livres ayant le mot-clé <b>"voyage"</b> dans leur liste de sujets, effectuez la requête suivante :</p>

<pre>db.volumes.findOne( { sujets : "voyage" }, { titre: 1 } )</pre>

<div class="small-spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Les tableaux comprenant plusieurs centaines, voir des milliers, de mots clés vont induire un coût plus important lors de l'ajout
	d'un mot clé, car l'indexe créé devra se mettre a jour.
</div>

<div class="spacer"></div>

<p>Devinez quoi ? Le chapitre sur <b>la modélisation des données touche à sa fin</b> ! Vous commencez à bien progresser avec <b>MongoDB</b>
et vous allez arriver sur un chapitre important : <a href="../aggregations.php">"Les Aggrégations" >></a>. Ce chapitre vous montrera comment
<b>effectuer des requêtes plus précises</b>, <b>regrouper/compter/distinguer</b> des données en tous genres. <b>Bravo d'en être arrivé jusque là</b> !
Encore une fois, si vous avez des questions ou des remarques, <a href="../contact.php">"contactez-moi"</a>.</p>

<?php

	include("footer.php");

?>
