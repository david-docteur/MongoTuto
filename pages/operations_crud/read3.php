<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations READ - Limites de projection et Curseurs</li>
</ul>

<p class="titre">[ Les Opérations READ - Limites de projection et Curseurs ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#limit">I) Limiter la Projection</a></p>
	<p class="right"><a href="#retall">- a) Retourner Tous les Champs</a></p>
	<p class="right"><a href="#retspe">- b) Retourner des Champs Spécifiques</a></p>
	<p class="right"><a href="#retexc">- c) Retourner Tout Sauf Champ Exclu</a></p>
	<p class="right"><a href="#proj">- d) Projection sur les Tableaux</a></p>
	<p class="elem"><a href="#cur">II) Les Curseurs</a></p>
</div>

<p>Bien, maintenant que vous savez comment <b>naviguer à travers les différentes données</b> de vos documents, vous allez probablement vouloir choisir
quel(s) champ(s) vous allez vouloir <b>voir apparaître dans le résultat</b> que va retourner votre requête. Regardez <b>les possibilités</b> qu'offre MongoDB.</p>
<a name="limit"></a>

<div class="spacer"></div>

<p class="titre">I) [ Limiter la projection ]</p>

<p>
    Tout comme en <b>SQL</b>, vous pouvez <b>contrôler la projection</b> de vos requêtes, c'est-à-dire les champs qui sont retournés lorsque votre résultat est traité.
    Par exemple, en SQL, la commande suivante retournerait <b>uniquement</b> les champs <b>'nomClient'</b> et <b>'numeroCompte'</b> :
</p>

<div class="small-spacer"></div>

<pre>SELECT nomClient, numeroCompte FROM clients:</pre>

<div class="spacer"></div>

<p>
    Avec MongoDB, cela se passe autrement, il s'agit d'ajouter à <b>la fonction find()</b> un paramètre qui sera sous la forme d'un <b>document BSON</b> lui aussi.
    Pour inclure un champ, on le spécifie avec <b>{ champ: 1 }</b>, ou alors si on souhaite l'exclure, on utilisera <b>{ champ: 0 }</b>.
</p>

<div class="small-spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Le champ "_id" est, par défaut, toujours inclus ! Si vous ne souhaitez pas le retourner dans votre résultat,
    ajoutez explicitement { _id: 0 }.
</div>

<div class="alert alert-danger">
	<u>Attention</u> : L'inclusion et l'exclusion de champs en même temps n'est pas possible, à l'exception du champ _id.
	C'est-à-dire que vous ne pouvez pas inclure et exclure deux champs différents à la fois dans votre projection.
</div>
<a name="retall"></a>

<div class="spacer"></div>

<p class="small-titre">a) Retourner tous les champs</p>

<p>
    <b>Facile !</b> Vous savez déjà le faire, on ne spécifie <b>rien</b> :
</p>

<div class="small-spacer"></div>

<pre>
db.alimentation.find(
    { 
        type: 'fruit'
    }
)
</pre>

<p>
    Ici, tous les champs du document sont retournés, <b>"_id" inclus</b>.
</p>
<a name="retspe"></a>

<div class="spacer"></div>

<div class="small-titre">b) Retourner champs spécifiés</div>

<pre>
db.alimentation.find(
    { 
        'type': 'fruit'
    },
    { 
        'nom': 1,
        'qte': 1
    }
)
</pre>

<p>
    Cette requête va retourner <b>tous les documents</b> de la collection <b>"alimentation"</b> étant de type <b>"fruit"</b>. Uniquement les champs <b>"nom"</b>, <b>"qte"</b> et <b>"_id"</b>(par défaut) seront
    retournés. Pour <b>exclure le champ "_id"</b> de la projection, <b>spécifiez le à 0</b>, rappelez-vous, dans le <b>deuxième paramètre</b> de la fonction find().
</p>
<a name="retexc"></a>

<div class="spacer"></div>

<p class="small-titre">c) Retourner Tout Sauf Champ Exlcu</p>

<p>
    Si vous souhaitez retourner <b>tous les champs</b> que vos documents peuvent contenir, <b>excepté celui/ceux spécifié(s)</b> :
</p>

<pre>
db.alimentation.find(
    {
        type: 'fruit'
    }, 
    { 
        nom: 0 
    }
)
</pre>

<p>
    La requête va <b>inclure tous les champs</b> dans le résultat de la projection <b>sauf</b> le champ <b>"type"</b> étant <b>définit à 0</b>.    
</p>
<a name="proj"></a>

<div class="spacer"></div>

<p class="small-titre">d) Projection sur les tableaux</p>

<p>
    Pour cela, veuillez vous référer aux opérateurs <b>$elemMatch</b> et <b>$slice</b> qui offrent <b>plus de contrôle</b>
    lorsque l'on veut <b>projeter une partie</b> d'un tableau.    
</p>
<a name="cur"></a>

<div class="spacer"></div>

<p class="titre">II) [ Les Curseurs ]</p>

<p>
    La fonction <b>find()</b> va retourer <b>un curseur</b>, qui, similairement aux curseurs SQL, va contenir <b>un ensemble de résultat</b> sur lequel on va pouvoir
    <b>parcourir chaque enregistrement</b> retourné par MongoDB.
    Si celui-ci n'est pas attribué avec <b>le mot clé Javascript "var"</b> dans un <b>shell mongo</b>, il retournera par défaut <b>20 occurences</b>.
</p>

<div class="small-spacer"></div>

<pre>
var monCurseur = db.banque.find( { 'compte': '123456' } );
monCurseur
</pre>

<div class="spacer"></div>

<p>
    La méthode <b>next()</b> va appeler <b>le prochain document</b> contenu dans le curseur. Cela va nous aider à parcourir <b>chaque document</b> comme ceci :
</p>

<div class="small-spacer"></div>

<pre>
var monCurseur = db.banque.find( { 'compte': '123456' } );
var monDocument = monCurseur.hasNext() ? monCurseur.next() : null;

if (monDocument) {
    var monCompte = monDocument.compte;
    print(tojson(monCompte));
}
</pre>

<div class="spacer"></div>

<p>
	Dans l'exemple <b>ci-dessus</b>, la variable <b>"monCurseur"</b> va contenir <b>le prochain document</b> du curseur retourné par la méthode find() appelée précédement.
	Si un document est trouvé, alors on <b>l'assigne à la variable</b>, sinon, celle-ci reçoit <b>null</b>.
	Ensuite, on test si le document est <b>non null</b>, si c'est le cas, on affiche <b>le compte du document courant</b>.
    Vous pouvez utiliser <b>la fonction printJson()</b> aussi bien pour aller un peu plus vite :
</p>

<pre>
if (monDocument) {
    var monCompte = monDocument.compte;
    printjson(monCompte);
}
</pre>

<div class="small-spacer"></div>

<p>
    Ou même encore plus rapidement, avec <b>la méthode foreach()</b> :
</p>

<pre>
var monCurseur = db.banque.find( { 'compte': '123456' } );
monCurseur.forEach(printjson);
</pre>

<div class="spacer"></div>

<p>
    Ensuite, pour <b>les tableaux</b>, on peut transformer <b>l'ensemble des résultats</b> en un tableau et accéder aux documents avec <b>l'indice désiré</b> :
</p>

<pre>
var monCurseur = db.banque.find( { 'compte': '123456' } );
var tableauDocuments = monCurseur.toArray();
var monDocument = tableauDocuments[3];
</pre>

<div class="spacer"></div>

<p>
    Certains <b>drivers MongoDB</b>, en fonction du langage de programmation, autorisent l'accès direct à un document avec <b>le curseur et un indice</b> passé à celui-ci,
    comme dans l'exemple ci-dessous :
</p>

<pre>
var monCurseur = db.banque.find( { 'compte': '123456' } );
var monDocument = monCurseur[3];
</pre>

<div class="small-spacer"></div>

<p>Vous avez aussi son <b>équivalent</b> :</p>

<pre>monCurseur.toArray() [3];</pre>

<div class="spacer"></div>

<p>Voilà c'est terminé pour <b>les opérations READ</b> ! 
Je vous avais prévenu qu'il y aurait <b>plus de choses à voir ici</b>, mais au moins maintenant,
vous devriez <b>être plus à l'aise</b> pour naviguer à travers les <b>documents</b> contenus dans vos <b>collections</b>.
Vous pouvez maintenant passer à la suite, <a href="update.php">Les Opérations UPDATE >></a>, où là vous allez apprendre <b>à modifier</b> vos documents.</p>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
