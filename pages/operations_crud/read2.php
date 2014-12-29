<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Les Opérations READ - Sous-Documents et Tableaux</li>
</ul>
<p class="titre">[ Les Opérations READ - Sous-Documents et Tableaux ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#sousdoc">I) Les Sous-Documents</a></p>
	<p class="right"><a href="#unsd">- a) Sélectionner un Sous-Document</a></p>
	<p class="right"><a href="#dessd">- b) Sélectionner un Champ d'un Sous-Document</a></p>
	<p class="elem"><a href="#tab">II) Les Tableaux</a></p>
	<p class="right"><a href="#untab">- a) Sélectionner un Tableau</a></p>
	<p class="right"><a href="#destabs">- b) Sélectionner un Tableau avec un Elément</a></p>
	<p class="right"><a href="#ind">- c) Sélectionner avec un Indice</a></p>
	<p class="right"><a href="#tabsd">- d) Sélectionner un Tableau de Sous-Documents</a></p>
	<p class="elem"><a href="read3.php">Limites de Projection et Curseurs</a></p>
</div>

<p>Vous voilà sur la <b>deuxième partie</b> du tutoriel sur <b>les opérations READ</b> de MongoDB. Ici, nous allons voir comment sélectionner, <b>plus spécifiquement</b>,
des <b>sous-documents</b> ainsi que <b>des tableaux</b> contenus dans <b>différents contextes</b>. Vous allez apprendre à <b>accéder facilement</b> à vos données,
par exemple, si un tableau contient <b>plusieurs documents BSON</b>, ou alors si un document contient <b>plusieurs tableaux</b>. C'est partit !</p>
<a name="sousdoc"></a>

<div class="spacer"></div>

<p class="titre">I) [ Les Sous-Documents ]</p>

<p>
    Voilà, alors nous savons déjà <b>gérer de simples champs</b>, mais alors comment gérer <b>des sous-documents</b> ?
    Premièrement, qu'appelle-t'on un <b>"Sous-Document"</b> ? Un <b>sous-document</b> est un simple <b>document imbriqué dans un autre</b>.
    Pour accéder à ses champs, vous allez devoir spécifier le document <b>en entier</b>, ou alors, spécifier un champ de ce sous-document avec <b>un point '.'</b>.
</p>
<a name="unsd"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sélectionner un Sous-Document</p>

<p>Si l'on veut rechercher un <b>sous-document exact</b>, nous allons devoir <b>passer en paramètre</b> la structure entière de celui que nous recherchons.
Par exemple, le document ci-dessous va recherche le sous-document <b>{ entreprise : 'ABC123', adresse : '123 Street' }</b> avec le champ <b>'producteur'</b>.</p>

<div class="small-spacer"></div>

<pre>
db.maCollection.find(
            {
                producteur: {
                            entreprise: 'ABC123',
                            adresse: '123 Street'
                }
            }
)
</pre>

<div class="small-spacer"></div>

<p>Cette requête va <b>sélectionner tous les documents</b> contenus dans la collection <b>maCollection</b> ayant le champ <b>"producteur"</b> contenant le sous-document
<b>{ entreprise: 'ABC123', adresse: '123 Street' }</b></p>
<a name="dessd"></a>

<div class="spacer"></div>

<p class="small-titre">b) Sélectionner un Champ d'un Sous-Document</p>

<p>Et comment fait-on si l'on souhaite <b>accéder au champ "entreprise"</b> de ce sous-document ? Il suffit d'utiliser <b>la notation '.'</b> comme ceci :</p>

<pre>
db.inventory.find( 
         {
            'producteur.entreprise': 'ABC123'
         }
)
</pre>
<a name="tab"></a>

<div class="spacer"></div>

<p class="titre">II) [ Les Tableaux ]</p>

<p>Pour les tableaux alors ? <b>C'est pareil !</b> La notation avec le point est requise afin <b>d'accéder à des éléments du tableau</b> en question.</p>
<a name="untab"></a>

<div class="spacer"></div>

<p class="small-titre">a) Sélectionner un Tableau</p>

<p>Premier exemple avec les tableaux, pour sélectionner un tableau exact, vous pouvez saisir la commande suivante :</p>

<div class="small-spacer"></div>

<pre>
db.maCollection.find( 
    { 
    fruits : [ 
            'pomme',
            'orange',
            'banane'
        ]
    }
)
</pre>

<div class="small-spacer"></div>

<p>La requête va sélectionner <b>tous les documents</b> contenant <b>exactement</b> le tableau <b>"fruits"</b> ayant pour éléments <b>"pomme", "orange" et "banane"</b>.</p>
<a name="destabs"></a>

<div class="spacer"></div>

<p class="small-titre">b) Sélectionner un Tableau avec un Elément</p>

<p>Si vous souhaitez que MongoDB vous retourne les documents ayant un tableau contenant <b>au moins le champ</b> que vous spécifiez, utilisez
la requête suivante, comme si le champ <b>"fruits" n'était pas un tableau</b> :</p>

<div class="small-spacer"></div>

<pre>
db.inventory.find( 
    { 
        'fruits' : 'orange' 
    }
)
</pre>

<div class="small-spacer"></div>

<p>Cette requête va donc retourner <b>tous les documents</b> ayant le tableau <b>"fruits"</b> contenant <b>au moins l'élément "orange"</b>.</p>
<a name="ind"></a>

<div class="spacer"></div>

<p class="small-titre">c) Sélectionner avec un Indice</p>

<p>Bien sûr, comme tout tableau, il peut y avoir <b>plusieurs éléments</b> étant accessibles <b>à partir de l'indice 0</b>.
Sur un tableau de 100 éléments, on utiliserait <b>l'indice 99</b> pour accéder <b>au dernier</b>, rien ne change de ce point de vue.</p>

<div class="small-spacer"></div>

<pre>
db.inventory.find(
    {
        'fruits.0' : 'orange'
    }
)
</pre>

<div class="small-spacer"></div>

<p>Ici rien à dire de plus, en général, <b>une boucle</b> dans votre code pour parcourir le tableau et on en parle plus.</p>
<a name="tabsd"></a>

<div class="spacer"></div>

<p class="small-titre">d) Sélectionner un Tableau de Sous-Documents</p>

<p>Et comment fait-on si l'on a un tableau contenant <b>plusieurs sous-documents</b> ?
Si l'on veut accéder à un sous-document particulier dans un tableau en spécifiant l'indice :</p>

<div class="small-spacer"></div>

<pre>
db.inventaire.find(
    {
        'produits.0.type': 'alimentation'
    }
)
</pre>

<div class="small-spacer"></div>

<p>Ici, la requête va sélectionner le sous-document ayant <b>un tableau de produits</b> qui a pour <b>indice 0</b> un produit <b>de type 'alimentation'</b>.</p>

<div class="spacer"></div>

<p>Et si l'<b>on ne sait pas</b> à quel indice se trouve <b>le document</b> ? Si l'on souhaite rechercher
<b>parmis tous ceux qui existent</b> ? On utilise la notation point <b>sans spécifier d'indice</b> tout simplement :</p>

<div class="small-spacer"></div>

<pre>
db.inventaire.find(
    {
        'produits.type': 'alimentation'
    }
)
</pre>

<div class="small-spacer"></div>

<p>Voilà, avec cette requête, on va retourner <b>tous les documents</b> ayant un tableau de produits <b>contenant un type 'alimentation'</b>.</p>

<div class="spacer"></div>

<p>Enfin, pour rechercher <b>avec plusieurs champs</b>, on peut utiliser la notation <b>point</b> :</p>

<pre>
db.inventory.find(
    {
        'livres.auteur': 'Emile Zola',
        'livres.datePubli': '1877'
    }
)
</pre>

<div class="spacer"></div>

<p>Ou alors avec <b>l'opérateur $elemMatch</b> qui va être plus <b>optimisé</b> pour ce genre de requête :</p>

<pre>
db.inventory.find(
    {
        livres: {
            $elemMatch: {
                auteur : 'Emile Zola',
                datePubli: '1877'
            }
        }
    }
)
</pre>

<div class="small-spacer"></div>

<p>Les deux requêtes ont la <b>même fonction</b> : retourner tous les <b>livres</b> ayant le champ <b>'livres' qui est un tableau</b>, et qui contient <b>au moins</b> un sous-document
ayant le champ <b>'auteur' avec la valeur 'Emile Zola'</b> et le champ <b>'datePubli' avec la valeur '1877'</b>.</p>

<div class="spacer"></div>

<p>Okay, c'était facile à digérer ? Si quelque chose ne vous paraît <b>pas clair</b>, vous savez où me <a href="../contact.php">"contacter"</a> !
Passons à la suite sur les <b>Limites de Projection et Curseurs</b>, cela va être <b>essentiel</b> pour la structure de vos données.
<a href="read3.php">Opérations READ - Limites de projection et Curseurs >></a>

<div class="spacer"></div>

<?php

	include("footer.php");

?>
