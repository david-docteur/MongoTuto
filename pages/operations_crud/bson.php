<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../operations_crud.php">Opérations CRUD</a></li>
	<li class="active">Documents BSON</li>
</ul>
<p class="titre">[ Documents BSON ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#quoi">I) BSON, C'est Quoi ?</a></p>
	<p class="elem"><a href="#comp">II) Comparaison BSON et JSON</a></p>
	<p class="elem"><a href="#donc">III) JSON ou BSON donc ?</a></p>
	<p class="elem"><a href="#sd">IV) Les Sous-Documents</a></p>
	<p class="elem"><a href="#test">V) Tester Votre Document BSON</a></p>
</div>

<p>Tout d'abord, avant de commencer à explorer les différentes <b>opérations CRUD</b>, vous devez comprendre la notion de <b>Document</b>.
Les <b>Documents MongoDB</b>, que contient une <b>collection</b> (rappelez-vous, <b>équivalent à une table</b> en SQL normalisé), sont au <b>format BSON</b>.
Cela vous rappelle peut-être le <b>format JSON</b> ? Hé bien <b>oui</b>, vous êtes sur la bonne voie ! Allez, commençons par quelques <b>explications</b>.<p>
<a name="quoi"></a>

<div class="spacer"></div>

<p class="titre">I) [ BSON, C'est Quoi ? ]</p>

<p>En bref, le format BSON <b>(Binary JSON)</b> est une sérialisation binaire du format <b>JSON</b>. <b>JSON</b> est initialement l'accronyme pour
<b>JavaScript Object Notation</b>. Celui-ci est un format <b>ouvert</b> et a été déployé afin de <b>transmettre des données objets</b> en JavaScript
de manière a être lu par nous les humains ! Ce format se résume à des <b>paires clés/valeurs</b> contenues entre des <b>accolades</b>. <b>BSON</b>, lui, celui-ci a été conçu pour :<p>

<div class="small-spacer"></div>

<p class="un-list caract">- être léger en terme de stockage ainsi que de communication réseau (plus léger que JSON)</p>
<p class="un-list caract">- être traversable, c'est-à-dire que l'on peut accéder aux informations facilement et rapidement</p>
<p class="un-list caract">- être encodé et décodé de manière efficace</p>
<p class="un-list caract">- offrir plus de types de données que JSON</p>

<div class="small-spacer"></div>

<p>Voici le lien vers le <b>site officiel de BSON</b> si vous souhaitez plus de détails :
<a href="http://bsonspec.org/" target="_blank">"BsonSpec"</a></p>
<a name="comp"></a>

<div class="spacer"></div>

<p class="titre">II) [ Comparaison BSON et JSON ]</p>

<p>BSON est <b>très similaire</b> à JSON mais implémente des types de données <b>supplémentaires</b> venant du <b>langage C</b> tels que le type <b>Date</b> ou <b>BinaryData</b>.
Mais <b>à quoi ressemble</b> un Document BSON ? Très similaire à JSON :</p>

<div class="spacer"></div>

<pre>
{
	"hello": "world"
}
</pre>

<p>Ou "hello" est le <b>champ</b> et "world" la <b>valeur</b>.
Ou encore :</p>

<div class="spacer"></div>

<pre>
{
	"BSON": [
		"super", 
		3.08, 
		2001
	]
}
</pre>

<p>Ou <b>"BSON"</b> est le champ et le tableau <b>["super", 3.08, 2001]</b> est la valeur.</p>
<a name="donc"></a>

<div class="spacer"></div>

<p class="titre">III) [ JSON ou BSON donc ? ]</p>

<p>Ayant posé la question aux <b>développeurs de MongoDB</b>, la réponse est : <b>les deux !</b> En effet, de base le BSON est <b>purement du JSON</b> et vous utiliserez, pour la
plupart du temps, des Documents JSON qui <b>n'incluront pas forcément</b> des types qu'offre BSON. Voilà ... au choix !</p>
<a name="sd"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Les Sous-Documents ]</p>

<p>Les <b>valeurs</b> de votre document BSON peuvent contenir un <b>tableau</b> mais elles peuvent également contenir un <b>Document</b>, ou dans ce cas serait appelé <b>Sous-Document</b>.
Voyons rapidement un exemple :</p>

<div class="spacer"></div>

<pre>
{
	"_id" : "123",
	"monSousDoc" : {
		"_idSousDoc" : "456"
	}
}
</pre>

<p>Ici, dans notre document, nous avons le <b>sous-document "monSousDoc"</b>.</p>

<div class="small-spacer"></div>

<p>Vous pouvez même trouver des <b>sous-documents</b> contenus dans un <b>tableau</b> comme celui-ci :</p>

<div class="spacer"></div>

<pre>
{
    "_id": "0123456789",
    "mon_tableau": [
        {
            "idSousDoc": "000000",
            "nomSousDoc": "nom001"
        },
        {
            "idSousDoc": "000002",
            "nomSousDoc": "nom002"
        }
    ],
    "monChampX": "nomX",
    "monChampY": "nomY"
}
</pre>

<p>Le tableau <b>"mon_tableau"</b> va contenir <b>deux sous-documents</b> ayant pour id <b>"000000"</b> et <b>"000002"</b>.</p>
<a name="test"></a>

<div class="spacer"></div>

<p class="titre">V) [ Tester Votre Document BSON ]</p>

<p>Pour vérifier que <b>la structure</b> de votre document BSON est <b>correcte</b>, vous allez pouvoir <b>tester</b> sur le site de <a href="http://www.jsonlint.com/" target="_blank">"JsonLint"</a> qui
, en un simple click, vous dira si celui-ci est <b>valide ou non</b>. Il y a même un <b>validateur de syntaxe intégré</b> dans le logiciel <a href="http://www.robomongo.org" target="_blank">"RoboMongo"</a> <b>disponible au téléchargement</b> dans la rubrique
<a href="../outils.php">"Outils"</a> du site, et qui sera d'ailleurs le <b>client MongoDB préféré</b> utilisé sur <b>MongoTuto</b>.</p>

<div class="spacer"></div>

<p>Vous savez maintenant ce qu'est un <b>document BSON</b> mais vous ne savez pas encore l'exploiter. Passons maintenant à l'étape d'<b>initialisation de votre base de données MongoDB</b> : <a href="init_bdd.php">"Initialisation" >></a></p>

<?php

	include("footer.php");

?>
