<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations UPDATE</li>
</ul>
<p class="titre">[ Les Opérations UPDATE ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#upd">I) La Méthode update()</a></p>
	<p class="elem"><a href="#save">II) La Méthode save()</a></p>
</div>

<p>Chapitre suivant, <b>les opérations UPDATE</b> ! Ces opérations vont vous permettre de <b>mettre à jour</b> et <b>modifier</b> les documents de votre choix.
Il y a deux méthodes pour cela, <b>les méthodes update() et save()</b>.
Nous allons voir les différences de chacune.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Lisez bien la page des <a href="create.php">Opérations CREATE</a> car celle-ci fournit des informations
	essentielles à la compréhension de ce tutoriel.
</div>
<a name="upd"></a>

<div class="spacer"></div>

<p class="titre">I) [ La Méthode update() ]</p>

<p><b>Premier exemple</b> avec la fonction <b>update()</b> qui va être la fonction <b>principale</b> pour <b>mettre à jour</b> vos documents MongoDB.
La structure de la fonction <b>update()</b> est la suivante :</p>

<pre>db.maCollection.update(sélection, modification, options)</pre>

<div class="spacer"></div>

<p>J'explique : <b>la sélection</b> correspond aux documents que vous souhaitez <b>cibler</b> lors de votre mise à jour,
par exemple, si vous souhaitez <b>mettre à jour</b> une collection <b>"Etudiants"</b> et modifier les champs <b>"moyenne"</b> de ceux qui ont une moyenne égale à 20
(comme ce que j'ai toujours eu dans toutes les matières bien sûr ! hum hum ...), vous allez passer en paramètre <b>un document BSON</b> comme celui ci <b>{ 'moyenne': 20 }</b>.</p>

<p><b>Le paramètre "modification"</b> correspond aux champs que vous allez vouloir <b>modifier</b>, pour cela il y aura différentes possibilités que je vous invite à lire
dans <b>les prochaines lignes</b> de ce paragraphe.</p>

<p>Enfin, <b>les options</b> vous permettrons de définir si vous souhaitez réaliser <b>un UPSERT</b> et/ou alors si vous souhaitez effectuer <b>une mise à jour MULTI</b>.
Passons maintenant aux <b>exemples</b> :</p>

<div class="small-spacer"></div>

<pre>
db.universite.update(

	{ "type" : "etudiant", "prenom" : "David" },
	{ $set : { "note" : 13 } },
	{ upsert : true,
	  multi: false
	}
)
</pre>

<div class="spacer"></div>

<p>Supposons ici que nous travaillons sur <b>une collection nommée "Universite"</b>, et nous voulons attribuer <b>une note de 13/20 à tous les étudiants
s'appelant David</b>, la requête ci-dessus va permettre de le faire.
Vous voyez que vous sélectionnez <b>tous les étudiants appelés David</b> dans la sélection, puis, que l'on souhaite <b>modifier la note de chaque étudiant nommé David</b>
en 13/20.
Maintenant, regardons <b>les options</b> : <b>upsert</b> et <b>multi</b>. Qu'est-ce que cela veut bien vouloir dire ?

Et bien un <b>UPSERT</b> est <b>un mélange</b> entre le mot <b>UPDATE</b> et <b>INSERT</b>. En fait, lors de votre mise à jour, si <b>'upsert'</b> est définit à <b>true</b>, MongoDB va <b>automatiquement</b>
créer le/les document(s) <b>si ceux-ci n'existent pas déjà</b>. En bref, si dans ce cas, la mise à jour ne trouve <b>aucun étudiant nommé David</b>, alors un document
sera <b>créé</b> pour cet étudiant avec la note spécifiée à 13/20.

L'option <b>MULTI</b>, comme vous vous en doutez probablement, va permettre de spécifier si l'on souhaite mettre à jour/modifier <b>un seul document ou plusieurs</b>.
Si <b>'multi'</b> est définit à <b>false</b>, alors <b>le premier document</b> trouvé par l'opération UPDATE sera <b>mit à jour</b>, par contre, si <b>'multi'</b> est définit à <b>true</b>,
<b>tous les documents</b> concernés seront <b>modifiés</b>.</p>

<div class="spacer"></div>

<p>Dans le cas où vous décideriez de <b>mettre à jour le champ "_id"</b> d'un document dans le <b>deuxième paramètre "modification"</b>, voici ce que renvoi MongoDB si l"_id", que vous spécifiez, existe déjà :</p>

<pre>E11000 duplicate key error index: maBDD.maCollection.$_id_  dup key: ...</pre>

<div class="spacer"></div>

<p>Et l'opérateur <b>$set</b> alors, <b>à quoi sert-il</b> ? L'opérateur <b>$set</b> va vous permettre de <b>spécifier les champs</b> que vous souhaitez <b>modifier</b>.
En bref, <b>uniquement les champs</b> que vous passerez en paramètre seront mis à jour <b>mais pas les autres</b>.</p>

<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Si vous oubliez l'opérateur $set, votre document sera intégralement remplacé par le document, que vous
	passez en tant que second paramètre de la fonction update(), plutôt que de mettre à jour
	les champs indiqués !
</div>
<a name="save"></a>

<div class="spacer"></div>

<p class="titre">II) [ La Méthode save() ]</p>

<p>La méthode <b>save()</b>, elle, a <b>deux fonctions</b>. Celle-ci va <b>remplacer le document intégral</b> si un document <b>possède le champ "_id"</b> correspondant à celui passé dans la requête.
En revanche, si <b>aucun champ "_id"</b> n'est donné, ou si le <b>champ "_id" n'existe pas</b>, alors MongoDB va le <b>créer</b>.</p>

<div class="spacer"></div>

<p>Voici un <b>exemple de mise à jour</b> où le champ <b>"_id"</b> n'est pas spécifié, le document sera donc créé :</p>

<pre>
db.maCollection.save(
	{ 
		'type' : 'livre',
		'sujet' : 'ordinateur',
		'qte' : 23
	}
)
</pre>

<div class="spacer"></div>

<p>Puis un autre exemple de <b>mise à jour</b> où le champ <b>"_id"</b> est spécifié et <b>existe</b> dans la collection, on <b>remplace</b> le document <b>existant</b> par le document <b>donné</b>.</p>

<pre>
db.maCollection.save(
	{ 
		"_id" : "10224144",
		"type" : "livre",
		"sujet" : "ordinateur",
		"qte" : 23
	}
)
</pre>

<div class="small-spacer"></div>

<p>Ici, le document, <b>ayant pour "_id" 10224144</b>, sera remplacé par le contenu de celui <b>spécifié par la requête</b>.</p>

<div class="spacer"></div>

<p>Voilà c'est terminé pour <b>les opérations UPDATE</b> !
Je vous conseille de bien vous entraîner à <b>insérer et mettre à jour</b> vos propres
documents BSON, et de surtout <b>observer la différence</b> entre une opération UPDATE <b>avec l'opérateur $set</b>
et une autre <b>sans</b> car la différence <b>est importante</b>.
Vous pouvez maintenant passer à la suite sur les <a href="delete.php">"Les Opérations DELETE" >></a>, le dernier chapitre sur <b>les Opérations CRUD</b>.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
