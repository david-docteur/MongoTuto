<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Aggrégations</li>
</ul>

<p class="titre">[ Aggrégations ]</p>

<p>On utilise très souvent en <b>SQL normalisé</b> les requêtes <b>HAVING</b>, <b>GROUP BY</b>, <b>COUNT</b> et bien d'autres ... Bien sûr, MongoDB
offre <b>son ensemble</b> de commandes et fonctionalités.
Les aggrégations avec MongoDB consistent à <b>regrouper les valeurs de plusieurs documents ensemble</b> puis effectuer des opérations sur l'ensemble des informations retournées
pour n'obtenir <b>qu'un seul résultat</b>.
Cela <b>simplifie le code</b> de votre application puis <b>allège les besoins en ressources</b>.</p>
<p>Pour cela, il y a <b>trois possibilités</b> d'effectuer ce genre d'opérations : <b>le pipeline d'aggregation</b>, <b>la fonction map-reduce</b> 
ainsi que <b>les simples commandes d'aggrégations</b>, que nous allons toutes détailler dans les pages suivantes.</p>

<div class="spacer"></div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>I) Le Pipeline d'Aggrégation</h3>
        <p>Le mode d'aggrégation le plus utilisé avec MongoDB, le pipeline !</p>
        <p><a href="aggregations/aggregation_pipeline.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>II) La fonction Map-Reduce</h3>
        <p>La fonction Map-Reduce est toujours une méthode d'aggrégation utilisée, mais est jugée plus compliquée que le pipeline.</p>
        <p><a href="aggregations/aggregation_mapreduce.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>III) Simples Commandes d'Aggrégation</h3>
        <p>Toutes les commandes de type count, distinct etc ...</p>
        <p><a href="aggregations/aggregation_simple.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>IV) Optimisations et Limites</h3>
        <p>Ici, comment optimiser vos requêtes d'aggrégations ainsi que les limites de celles-ci.</p>
        <p><a href="aggregations/aggregation_optimisations_limites.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>
  
<div class="spacer"></div>

<p>Avant de quitter ce chapitre, gardez-bien à l'esprit que les aggrégations doivent particulièrement <b>se travailler</b> et <b>plus vous vous entraînerez, mieux
ça sera</b>. De mon point de vue, <b>passer l'examen</b> de la <a href="https://university.mongodb.com/" target="_blank">"MongoDB University"</a> m'a <b>beaucoup aidé</b> (et en plus c'est gratuit), pourquoi pas vous y atteler ? La chapitre suivant porte sur <b>les indexes</b>.
Les indexes permettent <b>d'optimiser fortement vos requêtes</b> lorsque vous avez beaucoup de données. C'est par là <a href="indexes.php">"Les Indexes" >></a></p>
  
<?php

	include("footer.php");

?>
