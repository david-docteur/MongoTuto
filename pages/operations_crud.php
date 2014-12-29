<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Opérations CRUD</li>
</ul>

<p class="titre">[ Opérations CRUD ]</p>
	
<p>Bienvenue sur la page des <b>Opérations CRUD</b> ! La première page de <b>MongoTuto</b> qui va vous faire rentrer <b>dans le vif du sujet</b>.
Vous avez vu dans <b>le chapitre précédent</b> comment <b>installer MongoDB</b> pour votre système d'exploitation. C'était déjà fait ?
<b>Tant mieux !</b> L'important est que vous soyez prêt à démarrer. Par <b>"prêt"</b>, j'entends bien sûr le fait que vous puissiez
<b>exécuter votre instance mongod</b> ainsi que d'utiliser <b>le client mongo</b> pour s'y connecter.</p>

<div class="small-spacer"></div>

<p>Nous allons maintenant voir ensemble les différentes <b>opérations CRUD</b>, dont l'accronyme signifie <b>CREATE, READ, UPDATE, DELETE</b> 
(comme INSERT, SELECT, UPDATE et DELETE en SQL normalisé).
Ces opérations vont vous permettre de réaliser <b>presque tout</b> sur vos données (<b>les documents</b>).
Regardons plus en <b>détails</b> ce qu'offre ce chapitre :</p>

<div class="spacer"></div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/bson.png" alt="...">
      <div class="caption">
        <h3>I) Documents BSON</h3>
        <p>Découvrez ce qu'est le format BSON et pourquoi celui-ci constitue les documents au sein de votre base de données MongoDB.</p>
        <p><a href="operations_crud/bson.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/dbinit.png" alt="...">
      <div class="caption">
        <h3>II) Initialisation</h3>
        <p>Apprenez ici comment démarrer et initialiser votre version de MongoDB une fois que votre installation est terminée.</p>
        <p><a href="operations_crud/init_bdd.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/dbcreate.png" alt="...">
      <div class="caption">
        <h3>III) Opérations CREATE</h3>
        <p>Le premier type d'opération CRUD, les opérations CREATE, avec lesquelles vous pourrez ajouter des données à MongoDB.</p>
        <p><a href="operations_crud/create.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/dbfind.png" alt="...">
      <div class="caption">
        <h3>IV) Opérations READ</h3>
        <p>Les opérations READ pour MongoDB :</p>
        <p class="un-list"><a href="operations_crud/read.php">- Sélection et Opérateurs</a></p>
		<p class="un-list"><a href="operations_crud/read2.php">- Sous-Documents et Tableaux</a></p>
		<p class="un-list"><a href="operations_crud/read3.php">- Limites de projection et Curseurs</a></p>
        <p><a href="operations_crud/read.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/dbupdate.png" alt="...">
      <div class="caption">
        <h3>V) Opérations UPDATE</h3>
        <p>Cette section va vous expliquer différentes options et fonctionnalités afin de mettre à jouer et modifier vos documents MongoDB.</p>
        <p><a href="operations_crud/update.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="thumbnail">
      <img src="../img/operations_crud/dbremove.png" alt="...">
      <div class="caption">
        <h3>VI) Opérations DELETE</h3>
        <p>Enfin, dernier bloc des opérations CRUD, les opérations DELETE qui, vous l'aurez deviné, vous permettrons de supprimer vos données.</p>
        <p><a href="operations_crud/delete.php" class="btn btn-primary centeredSection" role="button">Accéder</a></p>
      </div>
    </div>
  </div>
</div>

<div class="spacer"></div>

<p>Une fois terminée, la lecture de ces chapitres vont vous permettre de <b>manipuler vos données</b>, c'est-à-dire de les <b>insérer</b>, de les <b>interroger</b>, de les <b>modifier</b>
ainsi que de les <b>supprimer</b>. Vous vous sentez à l'aise maintenant avec <b>la manipulation</b> de vos données ?
Pour ceux qui viennent de <b>SQL</b>, ne vous inquiètez pas, les <b>aggrégations</b> avec les clauses <b>HAVING</b>, <b>GROUP BY</b>, <b>DISTINCT</b> et autres vont venir dans le
chapitre des <a href="aggregations.php">"Aggrégations"</a>. Allez, maintenant, il est temps de vous apprendre <b>comment structuer et modéliser vos données</b>. Pour cela,
le chapitre suivant sur les <a href="modelisation_donnees.php">"Modélisations des Données" >></a>.</p>

<?php

	include("footer.php");

?>
