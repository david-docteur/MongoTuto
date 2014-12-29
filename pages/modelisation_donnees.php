<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Modélisations de Données</li>
</ul>

<div class="titre">[ Modélisation de Données ]</div>

<p><b>Nouveau chapitre</b>, <b>nouvelles notions</b>, ici nous allons découvrir <b>la modélisation des données avec MongoDB</b>.
En effet, cette notion est <b>très importante</b>, surtout dans les environnements qui ont pour tâche primaire <b>la gestion de données de masse</b>, mais aussi
tout en sachant que MongoDB adopte <b>un schéma flexible</b>.
Voici <b>le plan</b> qui vous est proposé et que <b>je vous invite à découvrir</b>. Une bonne structure reflette <b>toujours</b> une bonne base de données !</p>

<div class="spacer"></div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>I) Gestion des Relations de Données</h3>
        <p>Discutons ici principalement de la structure de vos documents ainsi que la flexibilité du schéma. Vous allez voir qu'avec MongoDB
        vous pouvez ajouter les informations que vous souhaitez, tout en ayant une structure différente pour chaque document.</p>
        <p><a href="modelisation_donnees/gestion_relation_donnees.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>II) Relations Entre Documents</h3>
        <p>Cela doit vous rappeler le modèle entités-relations comme en SQL normalisé. Les relations entre documents vont vous expliquer
        comment gérer la structure de vos collections ainsi que comment vous pouvez organiser vos documents de manière optimale.</p>
        <p><a href="modelisation_donnees/relations_documents.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>III) Structures d'Arbres</h3>
        <p>Les structures d'arbres vont correspondre à une méhode différente de stockage d'informations. Plutôt utile si vous avez une application
        ou un déploiement pour un arbre généalogique !</p>
        <p><a href="modelisation_donnees/structures_arbres.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>IV) Contextes Spécifiques d'Applications</h3>
        <p>D'autres points importants sur l'atomicité des opérations ainsi que pour la recherche de mots-clés. Pas utile pour tous les types
        de déploiements mais peut vous être très utile.</p>
        <p><a href="modelisation_donnees/modeles_specifiques.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="spacer"></div>

<p>Une fois que vous aurez vu comment <b>organiser et ranger</b> vos données, pourquoi pas vous diriger vers le chapitre suivant sur <b>les aggrégations</b> ?
Cela va vous apprendre à <b>effectuer des requêtes plus complexes</b> mais aussi <b>beaucoup plus précises</b> sur ce que vous désirez <a href="aggregations.php">"Aggrégations" >></a></p>

<?php

	include("footer.php");

?>
