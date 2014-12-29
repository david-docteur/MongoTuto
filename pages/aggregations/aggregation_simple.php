<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li><a href="../aggregations.php">Aggrégations</a></li>
	<li class="active">Les Commandes d'Aggrégation Simples</li>
</ul>

<p class="titre">[ Les Commandes d'Aggrégation Simples ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#cou">I) Count</a></p>
	<p class="elem"><a href="#dis">II) Distinct</a></p>
	<p class="elem"><a href="#gro">III) Group</a></p>
</div>

<p></p>
<a name="cou"></a>

<div class="spacer"></div>

<p class="titre">I) [ Count ]</p>

<p>La commande count() va, comme en SQL, compter le nombre de Document qui correspondent à votre requête. Elle existe même pour les Curseurs
comme ceci : monCurseur.count() et va compter le nombre d'élément sur lesquels on peut itérer.</p>

<p>Si l'on considère la Collection records ne contenant que les Document suivants :</p>

<pre>
{ a: 1, b: 0 }
{ a: 1, b: 1 }
{ a: 1, b: 4 }
{ a: 2, b: 2 }
</pre>

<p>
Exécuter la fonction db.records.count() va retourner le nombre 4.
La fonction db.records.count( { a: 1 } ) va sélectionner uniquement les Documents ayant la clé a définie à 1,
le résultat retourné sera donc de 3.
</p>
<a name="dis"></a>

<div class="spacer"></div>

<p class="titre">II) [ Distinct ]</p>

<p>Les commandes d'aggrégation Distinct sont représentées par la fonction distinct() sur une Collection, et va permettre de retourner un Document par unique champ donné
en paramètre dans une requête, comme en SQL normalisé.</p>

<div class="spacer"></div>

<p>Prenons l'ensemble des Documents suivants :</p>

<pre>
{ a: 1, b: 0 }
{ a: 1, b: 1 }
{ a: 1, b: 1 }
{ a: 1, b: 4 }
{ a: 2, b: 2 }
{ a: 2, b: 2 }
</pre>

<div class="spacer"></div>

<p>Effectuons la commande Distinct suivante :</p>

<pre>db.records.distinct( "b" )</pre>

<div class="spacer"></div>

<p>Les résultat va donc être :</p>

<pre>[ 0, 1, 4, 2 ]</pre>

<p>Cette commande va retourner toutes les instances uniques de b.</p>
<a name="gro"></a>

<div class="spacer"></div>

<p class="titre">III) [ Group ]</p>

<p>La commande Group va regrouper des groupes de Documents en fonctions des paramètres passés à la requête.Puis, celle-ci va retouner un tableau
de Documents avec les différents résultats par groupe de Documents.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Les résultats d'une commande group ne peuvent pas excéder la limite de 16mo.
</div>

<div class="spacer"></div>

<p>Voyons un exemple avec la Collection records :</p>

<pre>
{ a: 1, count: 4 }
{ a: 1, count: 2 }
{ a: 1, count: 4 }
{ a: 2, count: 3 }
{ a: 2, count: 1 }
{ a: 1, count: 5 }
{ a: 4, count: 4 }
</pre>

<div class="spacer"></div>

<p>Avec la requête suivante, on souhaite regrouper les Documents par le champ "a" ou "a" est inférieur à 3 puis effectue la somme des champs count :</p>

<pre>
db.records.group(
	{
		key: { a: 1 },
		cond: { a: { $lt: 3 } },
		reduce: function(cur, result) { result.count += cur.count },
		initial: { count: 0 }
	}
)
</pre>

<div class="spacer"></div>

<p>Cette opération va retourner le résultat suivant :</p>

<pre>
[
	{ a: 1, count: 15 },
	{ a: 2, count: 4 }
]
</pre>

<div class="spacer"></div>

<p>Voilà, c'est terminé pour les opérations simples d'aggrégation, la prochaine section porte sur <a href="aggregation_optimisations_limites.php">Les Optimisations et Limites des Aggrégations >></a>.</p>

<?php

	include("footer.php");

?>