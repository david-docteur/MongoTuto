<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Indexes</li>
</ul>
<p class="titre">[ Indexes ]</p>

<p>Bienvenue sur <b>la page des indexes</b> avec MongoDB. Vous souhaitez <b>optimiser vos requêtes</b> lourdes et répétitives ? Vous êtes à la bonne page
et vous allez pouvoir trouver ici <b>tout ce qu'il vous faut</b>. Il existe <b>plusieurs types d'indexes</b> en fonction des données que vous allez vouloir
optimiser. La différence de performance peut-être <b>parfois bluffante</b>, nous allons voir pourquoi, mais en attentant, je vous invite à <b>commencer par l'introduction</b>.</p>

<div class="spacer"></div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>I) Introduction</h3>
        <p>Découvrez ce que sont les indexes et pourquoi ils constituent une part importante avec MongoDB.</p>
        <p><a href="indexes/indexes_introduction.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>II) Types d'Indexes</h3>
        <p>Ayez une vue d'ensemble des différents types d'indexes disponibles avec MongoDB.</p>
        <p><a href="indexes/indexes_types.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>III) Gestion d'Indexes et Options</h3>
        <p>Comment gérer vos indexes ainsi que découvrir les différentes options disponibles pour ceux-ci.</p>
        <p><a href="indexes/indexes_gestion.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>IV) Indexes GéoSpatiaux</h3>
        <p>Découvrir ici les indexes géospatiaux ainsi que comment gérer vos données 2D.</p>
        <p><a href="indexes/indexes_geo.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  
  <div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <div class="caption">
        <h3>V) Recherche de Texte</h3>
        <p>Les indexes de texte vont vous permettre d'effectuer des recherches sur des mots-clés ou tableaux de mots-clés.</p>
        <p><a href="indexes/indexes_recherchetexte.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="spacer"></div>

<p>Bon, vous n'aurez pas besoin d'utiliser <b>tous les types d'indexes</b>, ni même de les utiliser pour vos petites bases de données, mais lorsque vous
devez faire face à <b>un déploiement lourd</b> ou alors même <b>par anticipation</b>, là, cela vous sera utile. Le chapitre suivant ne concerne que ceux qui vont
vouloir se préoccuper de <b>la redondance et la sauvegarde des leurs données</b>, le chapitre sur la <a href="replication.php">"Réplication" >></a> va
vous expliquer comment vous allez pouvoir <b>créer un ensemble de répliques</b> afin de déployer des <b>sauvegardes autonomes</b> de vos bases de données.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
