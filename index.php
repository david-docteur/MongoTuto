<!DOCTYPE html>
<html><head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" href="/img/favicon.png">

		<title>MongoTuto.Com - La référence Francophone de MongoDB.</title>
    
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
		<link href="css/offcanvas.css" rel="stylesheet" type="text/css">
		<link href="css/style.css" rel="stylesheet" type="text/css">

    
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
    
    <style>
    /* Prevents slides from flashing */
    #slides {
      display:none;
    }
  </style>
    
    <style>
    body {
      -webkit-font-smoothing: antialiased;
      font: normal 15px/1.5 "Helvetica Neue", Helvetica, Arial, sans-serif;
      color: #232525;
      padding-top:70px;
    }

    #slides {
      display: none
    }

    #slides .slidesjs-navigation {
      margin-top:3px;
    }

    #slides .slidesjs-previous {
      margin-right: 5px;
      float: left;
    }

    #slides .slidesjs-next {
      margin-right: 5px;
      float: left;
    }

    .slidesjs-pagination {
      margin: 6px 0 0;
      float: right;
      list-style: none;
    }

    .slidesjs-pagination li {
      float: left;
      margin: 0 1px;
    }

    .slidesjs-pagination li a {
      display: block;
      width: 13px;
      height: 0;
      padding-top: 13px;
            background-image: url(../img/pagination.png);
      background-position: 0 0;
      float: left;
      overflow: hidden;
    }

    .slidesjs-pagination li a.active,
    .slidesjs-pagination li a:hover.active {
      background-position: 0 -13px
    }

    .slidesjs-pagination li a:hover {
      background-position: 0 -26px
    }

    #slides a:link,
    #slides a:visited {
      color: #333
    }

    #slides a:hover,
    #slides a:active {
      color: #9e2020
    }

    .navbar {
      overflow: hidden
    }
  </style>
  <!-- End SlidesJS Optional-->

  <!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
  <style>
    #slides {
      display: none
    }

    .container {
      margin: 0 auto
    }

    /* For tablets & smart phones */
    @media (max-width: 767px) {
      body {
        padding-left: 20px;
        padding-right: 20px;
      }
      .container {
        width: auto
      }
    }

    /* For smartphones */
    @media (max-width: 480px) {
      .container {
        width: auto
      }
    }

    /* For smaller displays like laptops */
    @media (min-width: 768px) and (max-width: 979px) {
      .container {
        width: 724px
      }
    }

    /* For larger displays */
    @media (min-width: 1200px) {
      .container {
        width: 1170px
      }
    }
  </style>
	</head>

	<body>
        <?php include_once("analyticstracking.php") ?>
		<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
		  <div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <div id="mongo-logo-header">&nbsp;</div><a class="navbar-brand" href="index.php">MongoTuto.Com</a>
			</div>
			<div class="collapse navbar-collapse">
			  <ul class="nav navbar-nav">
				<li><a href="pages/news.php">News</a></li>
				<li><a href="pages/liens.php">Liens</a></li>
				<li><a href="pages/apropos.php">MongoTuto, c'est quoi ?</a></li>
				<li><a href="pages/contact.php">Contact</a></li>
			  </ul>
			</div><!-- /.nav-collapse -->
		  </div><!-- /.container -->
		</div><!-- /.navbar -->
		
			<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
			  <div class="list-group">
				<a href="#" class="list-group-item active">Tutoriels</a>
				<a href="pages/bienvenue.php" class="list-group-item"><b>:: B</b>ienvenue</a>
				<a href="pages/introduction.php" class="list-group-item"><b>:: I</b>ntroduction</a>
				<a href="pages/installation.php" class="list-group-item"><b>:: I</b>nstallation</a>
				<a href="pages/operations_crud.php" class="list-group-item"><b>:: O</b>pérations CRUD</a>
				<a href="pages/modelisation_donnees.php" class="list-group-item"><b>:: M</b>odélisations de Données</a>
				<a href="pages/aggregations.php" class="list-group-item"><b>:: A</b>ggrégations</a>
				<a href="pages/indexes.php" class="list-group-item"><b>:: I</b>ndex</a>
				<a href="pages/replication.php" class="list-group-item"><b>:: R</b>éplication</a>
				<a href="pages/sharding.php" class="list-group-item"><b>:: S</b>harding</a>
                <a href="pages/administration.php" class="list-group-item"><b>:: A</b>dministration</a>
                <a href="pages/securite.php" class="list-group-item"><b>:: S</b>écurité</a>
                <a href="pages/exemples_code.php" class="list-group-item"><b>:: E</b>xemples de Code</a>
				<a href="pages/outils.php" class="list-group-item"><b>:: O</b>utils</a>
			  </div>
			</div><!--/span-->
		  </div><!--/row-->
		  
		<div class="container">
		  <div class="row row-offcanvas row-offcanvas-right">
			<div class="col-xs-12 col-sm-9">
			  <p class="pull-right visible-xs">
				<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
			  </p>
				  <div class="jumbotron">
                      <div id="slides">
                            <img src="img/carroussel/carroussel1.png">
                            <img src="img/carroussel/carroussel2.png">
                            <img src="img/carroussel/carroussel3.png">
                          <a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
                          <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
                      </div>
					
				</div><!--/span-->
				<div class="row">
                    <div class="col-md-4">
                      <h2>MongoDB, c'est quoi ?</h2>
                      <p>MongoDB est un système de gestion de bases de données NoSQL orienté Big Data. L'objectif principal de MongoDB est de gérer les données de masses ainsi que de faciliter la scalabilité de vos déploiements en fonctions de vos besoins.</p>
                    </div>
                    <div class="col-md-4">
                      <h2>Qui utilise MongoDB ?</h2>
                      <p>MongoDB est utilisé et déployé par certaines Entreprises bien connues sous le nom de SAP, eBay, Orange, Foursquare ainsi que bien d'autres ...</p>
                   </div>
                    <div class="col-md-4">
                      <h2>MongoDB et la Securité</h2>
                      <p>La sécurité est avant tout un point essentiel afin de protéger et de garantir l'intégrité de vos informations. MongoDB propose plusieurs solutions et tutoriaux que vous pourrez consulter sur le site afin de réduire les risques au maximum.</p>
                    </div>
                  </div>
			<hr>
			
		  <div id="footerIndex">
			<p>
			© MongoTuto.com 2014&nbsp;
			<a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/4.0/80x15.png" /></a>
			</p>
		  </div>
		</div><!--/.container-->

		<script src="js/jquery-1.10.2.min.js" type="text/javascript"></script>
		<script src="js/bootstrap.js" type="text/javascript"></script>
		<script src="js/offcanvas.js" type="text/javascript"></script>
    <script src="js/jquery.slides.min.js"></script>
    <script>
    $(function(){
      $("#slides").slidesjs({
        width: 800,
        height: 380,
          navigation: false,
          play: {
              auto: true
          }
      });
    });
  </script>
	
	</body>
</html>
