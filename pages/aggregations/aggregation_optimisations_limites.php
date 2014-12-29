<?php

	set_include_path("../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../aggregations.php">Aggrégations</a></li>
	<li class="active">Optimisations et Limites</li>
</ul>

<p class="titre">[ Optimisations et Limites ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#opt">I) Optimisation du Pipeline d'Aggrégation</a></p>
	<p class="right"><a href="#seq">- a) Séquence d'Optimisation du Pipeline</a></p>
	<p class="right"><a href="#pro">- b) Optimisation de la projection</a></p>
	<p class="elem"><a href="#lim">II) Limites du Pipeline d'Aggrégation</a></p>
	<p class="right"><a href="#typ">- a) Restrictions de Types</a></p>
	<p class="right"><a href="#tail">- b) Restrictions de Taille de Résultat</a></p>
	<p class="right"><a href="#mem">- c) Restrictions Mémoire</a></p>
	<p class="elem"><a href="#pip">III) Pipeline d'Aggrégation et Collections Partagées</a></p>
	<p class="elem"><a href="#map">IV) Map-Reduce et Collections Partagées</a></p>
	<p class="right"><a href="#ent">- a) Collection Partagée en entrée</a></p>
	<p class="right"><a href="#sor">- b) Collection Partagée en sortie</a></p>
	<p class="elem"><a href="#par">V) Parallèlisme de Map-Reduce</a></p>
	
</div>

<p>Des optimisations du Pipeline d'Aggréation sont envisagées en ré-arrangeant les différentes phases du Pipeline pour plus de performance.</p>
<a name="opt"></a>

<div class="spacer"></div>

<p class="titre">I) [ Optimisation du Pipeline d'Aggrégation ]</p>

<p></p>
<a name="seq"></a>

<div class="spacer"></div>

<p class="small-titre">a) Séquence d'Optimisation du Pipeline</p>

<p>Quand votre aggrégation rencontre dans l'ordre les phases suivants : $sort + $skip + $limit, MongoDB procède à une optimisation qui consiste à
bouger la phase $limit avant la phase de $skip :</p>

<pre>
{ $sort: { age : -1 } },
{ $skip: 10 },
{ $limit: 5 }
</pre>

<p>devient :</p>

<pre>
{ $sort: { age : -1 } },
{ $limit: 15 }
{ $skip: 10 }
</pre>

<p>Notez que la valeur de $limit devint donc la somme de sa valeur initiale (ici 5) plus celle de la phase $skip (ici 10).</p>

<p>De même que la séquence $limit + $skip + $limit + $skip, quand vous avez une suite de phases de $limit et de $skip, l'aggrégation va tenter d'optimiser
les performances en regroupant les phases de $limit et de $skip en semble, regardons l'exemple suivante :</p>

<pre>
{ $limit: 100 },
{ $skip: 5 },
{ $limit: 10},
{ $skip: 2 }
</pre>

<p>Après l'optimisation, le Document devient :</p>

<pre>
{ $limit: 100 },
{ $limit: 15},
{ $skip: 5 },
{ $skip: 2 }
</pre>

<p>Pendant le stage intermédiaire, l'optimiser inverse la position de $skip suivit par un $limit à un $limit suivit par un $skip.
La valeur du $limit devient la somme de sa valeur plus celle du $skip. Ensuite, pour la valeur finale de $limit, l'optimiseur sélectionne la plus petite valeur des deux.
Et pour finir, l'optimiseur va additioner les valeur des $skips ensemble. Toutes ces opérations vont nous donner le résultat suivant :</p>

<pre>
{ $limit: 15 },
{ $skip: 7 }
</pre>
<a name="pro"></a>

<div class="spacer"></div>

<p class="small-titre">b) Optimisation de la projection</p>

<p>MongoDB optimise aussi les projections pour le pipeline d'aggrégation. Les aggrégations comportant une phase $project qui indique le/les champs à inclure
dans la projection vont automatiquement déplacer cette phase $project en tête du pipeline. En effet, cela réduit le nombre de données passant dans le pipeline
à partir du début. Dans l'exemple suivant, la phase de $project indique que l'on ne souhaite retourner uniquement les champs _id(par défaut) et
amount, l'optimiseur va donc bouger la phase de $project au début du pipeline, comme cela, les Documents traités dans la phase de match ne contiennent
uniquement les champs _id et amount.</p>

<pre>
db.orders.aggregate(
	{ $match: { status: "A" } },
	{ $project: { amount: 1 } }
)
</pre>

<p>Après optimisation :</p>

<pre>
db.orders.aggregate(
	{ $project: { amount: 1 } },
	{ $match: { status: "A" } }
)
</pre>
<a name="lim"></a>

<div class="spacer"></div>

<p class="titre">II) [ Limites du Pipeline d'Aggrégation ]</p>

<p>Même si le pipeline d'aggrégation offre beaucoup d'avantages, il a quand même ses limites que nous allons détailler ci-dessous.</p>
<a name="typ"></a>

<div class="spacer"></div>

<p class="small-titre">a) Restrictions de Types</p>

<p>Le pipeline d'aggrégation ne peut pas traiter les types de données suivants : Symbol, MinKey, MaxKey,
DBRef, Code, CodeWScope.</p>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Depuis la version 2.4, les restrictions sur le type de données Binary disparaît. Dans la version 2.2, MongoDB
	ne pouvait pas opérer avec ce type.
</div>
<a name="tail"></a>

<div class="spacer"></div>

<p class="small-titre">b) Restrictions de Taille de Résultat</p>

<p>Les résultats retournés par le Pipeline d'Aggrégation ne peuvent pas dépasser les 16mo en taille imposés par la limite du Document BSON.
Si le résultat dépasse cette limite, la commande aggregate() produit une erreur.</p>
<a name="mem"></a>

<div class="spacer"></div>

<p class="small-titre">c) Restrictions Mémoire</p>

<p>Si une aggrégation utiise plus de 10% de la RAM du système, l'opération va retourner une erreur.
Les opérateurs cumulatifs tels que $sort et $group ont besoin d'accéder à tous les Documents avant de produire quelconque résultat. Si ceux-ci
utilisent 5% ou plus de la mémoire physique, ils produisent un log d'avertissement, si ils dépassent 10%, une erreur.</p>
<a name="pip"></a>

<div class="spacer"></div>

<p class="titre">III) [ Pipeline d'Aggrégation et Collections Partagées ]</p>

<p>Le Pipeline d'Aggrégation fonctionne avec les Collections Partagées. Celui-ci fonctionne en deux parties. La première partie va
envoyer les opérateurs (jusqu'au premier $group ou $sort rencontré) sur chaque shard. Ensuite, un deuxième Pipeline s'exécute sur les instances de mongos.
Le Pipeline correspond donc au premier $group ou $sort et tous les autres opérateurs du pipeline et exécute les résultats reçus de chaque shard.</p>

<p>L'opérateur $group regroupe les sous-ensembles de chaque shard et les combines. Par exemple, l'expression $avg retourne un total et un count pour chaque
shard, mongos combine les valeurs et les divise.</p>

<div class="alert alert-danger">
	<u>Attention</u> : Depuis la version 2.2, certaines opérations du Pipeline d'aggrégation vont engendrer une augmentation des
	besoins en ressources des instances mongos en cours d'exécution comparé aux anciennes versions. Ce changement devrait changer votre décision d'architecture
	si vous utilisez le Pipeline d'Aggrégation extensivement dans un environnement Partagé.
</div>
<a name="map"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Map-Reduce et Collections Partagées ]</p>

<p></p>
<a name="ent"></a>

<div class="spacer"></div>

<p class="small-titre">a) Collection Partagée en entrée</p>

<p>Quand vous utilisez en entrée une Collection Partagée lors d'une opération Map-Reduce, l'instance mongos va séparer et diffuser le travail sur chaque shard
en parallèle. Il n'y a pas besoin d'options spécifiques, mongos va patienter jusqu'à ce que le travail de chaque shard ai terminé.</p>
<a name="sor"></a>

<div class="spacer"></div>

<p class="small-titre">b) Collection Partagée en sortie</p>

<p>Si le champ "out" d'une fonction Map-Reduce inclus une valeur partagée, MongoDB partage la Collection de sortie qui est générée en utilisant le champ _id
comme clé de partage.</p>

<p>Afin de générer une Collection partagée, si la Collection n'existe pas, MongoDB la créer et utilise le champ _id en tant que clé de partage.
Pour une Collection partagée nouvelle ou vide, MongoDB utilise les résultats de la première étape de la fonction Map-Reduce pour créer les premiers échantillons
distribués à travers les shards.
Mongos sépare et diffuse, en parallèle, une tâche post-processus de Map-Reduce à chaque shard qui détient un échantillon. Durant l'étape de post-traitement,
chaque shard va récupérer les résultats de son propre échantillon depuis les autres shards, exécuter les étapes finales reduce/finalize, et écrire localement
la Collection générée.</p>
<a name="par"></a>

<div class="spacer"></div>

<p class="titre">V) [ Parallèlisme de Map-Reduce ]</p>

<p>Une opération Map-Reduce est composée de plusieurs tâches telles que la lecture depuis la Collection mère, l'exécution de la fonction Map, les exécutions de la fonction
Reduce, l'écriture dans une Collection temporelle pendant le traitement, puis, l'écriture dans la Collection générée en sortie.</p>

<div class="spacer"></div>

<p>Pendant l'exécution, Map-Reduce prend les Locks(verrous) suivant :</p>

<p>La phase de lecture prend un lock de lecture. Il yields tous les 100 Documents.
L'insertion dans la Collection temporaire prend un Lock d'écriture pour chaque écriture simple.
Si la Collection générée en sortie n'existe pas, la création de celle-ci prend un lock d'écriture.
Si elle existe, les actions de sortie comme merge, replace ou reduce, prennent un lock d'écriture.
</p>

<div class="spacer"></div>

<div class="alert alert-success">	
	<u>Astuce</u> : Changé depuis la version 2.4, le moteur V8 de Javascript autorise l'exécution de multiples opérations Javascript en même temps.
	Avant la 2.4, les fonctions Javascript telles que map, reduce ou finalize étaient exécutées dans une seule Thread.
</div>

<div class="spacer"></div>

<div class="alert alert-success">
	<u>Astuce</u> : Le dernier lock d'écriture fait en sorte que les résultats apparaissent atomiquement, c'est pour cela que les méthodes de sortie
	merge et reduce peuvent prendre plusieurs minutes à s'exécuter. Pour les fonctions merge et reduce, le paramètre nonAtomic est disponible.
</div>

<div class="spacer"></div>

<p>Vous avez terminé le chapitre sur les Aggrégations avec MongoDB, cela va vous demander pas mal de pratique afin de vous habituer.
Passons maintenant à l'article sur les <a href="../indexes.php">Indexes  >></a>.</p>

<?php

	include("footer.php");

?>