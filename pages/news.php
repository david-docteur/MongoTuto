<?php

	set_include_path("../");
	include("header.php");

	// Nouvelle connexion à MongoDB
	// On s'identifie, on ne vas pas laisser libre accès comme certains :)
	$connexion = new MongoClient("fake")) or Die("Impossible de se connecter à la base de données MongoDB.");
	
	// Obtenir la collection
	$collection = $connexion->mongotuto->news;
	
	// Les documents dans l'ordre d'insertion (mieux d'afficher par date/heure)
	$documents = $collection->find()->sort(array('$natural' => -1));
	$nbrDocs = $documents->count();

?>

<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">News</li>
</ul>
<div class="titre">[ News ]</div>

<p>Vous êtes sur <b>la page de news</b> ! Retrouvez ici <b>toutes les actualités</b> en relation avec MongoTuto ainsi que MongoDB en général.
Revenez régulièrement pour savoir <b>ce qu'il se passe</b> ! Vous souhaitez proposer une news ? <a href="contact.php">"contactez-moi"</a> !</p>

<div class="spacer"></div>

<table id="tableNews">

<?php
	
	// On vérifie si l'on a au moins une actualité
	if($nbrDocs > 0) { 
	
		$cpt = $nbrDocs;
		
		foreach($documents as $document) {
			echo "<tr><td><div class='news'><span class='number'>#" . $cpt . "</span><span class='titreNews'>" . $document['titre'] . "</span><span class='dateNews'>" . date('d M Y, g:i', strtotime($document['date'])) . "</span><hr><div class='body'>" . $document['description'] . "</div><hr><div class='linkNews'><a href='" . $document['link'] . "' target='_blank'>" . $document['link'] . "</a></div></div></td></tr>";
			$cpt--;
		}
	
	// Sinon on affiche un message d'erreur
	} else {
		echo "Aucune news à afficher pour le moment ...";
	}

?>

</table>

<p>Je posterai des news <b>régulièrement</b>, en vous proposant les articles les plus intéressants tels que <b>les news explosives</b> de MongoDB,
<b>les conférences</b> à travers le monde entier (surtout en Français), <b>les tutoriaux</b> ainsi que les différentes entreprises racontant <b>leurs expériences</b> avec MongoDB. <b>Restez à l'écoute :)</b>.</p>

<?php

	include("footer.php");

?>
