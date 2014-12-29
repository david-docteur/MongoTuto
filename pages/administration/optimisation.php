<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Stratégies d'Optimisation pour MongoDB</li>
</ul>

<p class="titre">[ Stratégies d'Optimisation pour MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#eval">I) Evaluer la Performance des Opérations Courantes</a></p>
	<p class="right"><a href="#prof">- a) Utiliser le Profiler de Base de Données pour Evaluer les Opérations</a></p>
	<p class="right"><a href="#oper">- b) Utiliser db.currentOp() pour Evaluer les Opérations mongod</a></p>
	<p class="right"><a href="#perf">- c) Utiliser $explain pour Evaluer la Performance d'une Requête</a></p>
	<p class="elem"><a href="#cc">II) Utiliser des Capped Collections pour des Lectures et Ecritures Rapides</a></p>
	<p class="right"><a href="#ecri">- a) Utiliser des Capped Collections pour des Ecritures Rapides</a></p>
	<p class="right"><a href="#rapi">- b) Utiliser l'Ordre Naturel pour des Lectures Rapides</a></p>
	<p class="elem"><a href="#opti">III) Optimiser la Performance d'une Requête</a></p>
	<p class="right"><a href="#inde">- a) Créer des Indexes pour Aider les Requêtes</a></p>
	<p class="right"><a href="#limi">- b) Limiter le Nombre de Résultats pour Réduire la Demande du Réseau</a></p>
	<p class="right"><a href="#proj">- c) Utiliser les Projections pour Retourner Uniquement les Données Nécessaires</a></p>
	<p class="right"><a href="#hint">- d) Utilisez $hint pour Sélectionner un Indexe Particulier</a></p>
	<p class="right"><a href="#incr">- e) Utilisez l'Opérateur d'Incrémentation pour Effectuer des Opérations Côté Serveur</a></p>
</div>

<p>Il y a plusieurs facteurs qui peuvent affecter les performances d'une base de données, incluant les indexes, la structure des requêtes, la modélisation
des données et la conception de votre application.</p>
<a name="eval"></a>

<div class="spacer"></div>

<p class="titre">I) [ Evaluer la Performance des Opérations Courantes ]</p>

<p></p>
<a name="prof"></a>

<div class="spacer"></div>

<p class="small-titre">a) Utiliser le Profiler de Base de Données pour Evaluer les Opérations</p>

<p>MongoDB fournit un profiler de base de données qui affiche les performances de chaque opération au sein de la base de données. Utilisez le profiler
pour localiser n'importe qu'elle requête ou opération d'écriture qui est lente. Vous pouvez par exemple utiliser cette information pour déterminer
quels indexes vous devrez créer.</p>
<a name="oper"></a>

<div class="spacer"></div>

<p class="small-titre">b) Utiliser db.currentOp() pour Evaluer les Opérations mongod</p>

<p>La méthode db.currentOp() affiche des informations sur les opérations en cours d'une instance mongod.</p>
<a name="perf"></a>

<div class="spacer"></div>

<p class="small-titre">c) Utiliser $explain pour Evaluer la Performance d'une Requête</p>

<p>La méthode explain() retourne des statistiques d'une requête, et indique l'indexe que MongoDB a sélectionné pour effectuer la requête, aussi bien que des informations
sur l'opération interne de la requête. Par exemple, une requête pour trouver les documents qui correspondent à l'expression { a: 1 }, dans la collection records,
utilisez une opération qui resseble à celle-ci, dans un shell mongo :</p>

<pre>db.records.find( { a: 1 } ).explain()</pre>
<a name="cc"></a>

<div class="spacer"></div>

<p class="titre">II) [ Utiliser des Capped Collections pour des Lectures et Ecritures Rapides ]</p>

<p></p>
<a name="ecri"></a>

<div class="spacer"></div>

<p class="small-titre">a) Utiliser des Capped Collections pour des Ecritures Rapides</p>

<p>Les capped collections sont circulaires, fixes en taille et gardent les documents dans un ordre naturel, même sans l'utilisation d'indexe. Cela signifique
que les capped collections peuvent recevoir des écritures très rapides ainsi que des lectures séquentielles.
Ces collections sont particulièrement utiles pour garder des fichier logs mais ne sont pas limitées que pour cette utilisation.</p>
<a name="rapi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Utiliser l'Ordre Naturel pour des Lectures Rapides</p>

<p>Pour retourner les documents dans l'ordre dans lequel ils existent sur le disque, utilisez l'opérateur $natural pour retourner les opérations triées.
Sur une capped collection, cela retourne aussi les documents dans l'ordre dans lequel ils ont été insérés.
L'ordre naturel n'utilise pas d'indexes mais peut être rapide pour les opérations lorsque vous voulez sélectionner les premiers ou derniers éléments sur le disque.</p>
<a name="opti"></a>

<div class="spacer"></div>

<p class="titre">III) [ Optimiser la Performance d'une Requête ]</p>

<p></p>
<a name="inde"></a>

<div class="spacer"></div>

<p class="small-titre">a) Créer des Indexes pour Aider les Requêtes</p>

<p>Pour les requêtes fréquement employées, créez des indexes. Si une requête recherche plusieurs champs, créez un indexe composé. Scanner un indexe
est beaucoup plus rapide que de scanner une collection. Les structures d'indexes sont plus petites que les structures des documents, et stockent les références
dans l'ordre.
Par exemple, si vous avez une collection posts contenant des posts d'un blog, et que vous utilisez régulièrement une requête qui va trier sur le champ author_name,
alors vous pouvez optimiser la requête en créant un indexe sur ce même champ :</p>

<pre>db.posts.ensureIndex( { author_name : 1 } )</pre>

<p>Les indexes améliorent aussi l'efficacité des requêtes qui trient régulièrement sur un champ donné. Par exemple, si vous effectuez régulièrement une requête
qui trie avec le champ timestamp, alors vous pouvez optimiser la requête en créeant un indexe sur ce champ :</p>

<pre>db.posts.ensureIndex( { timestamp : 1 } )</pre>

<p>Maintenant, optimisez la requête :</p>

<pre>db.posts.find().sort( { timestamp : -1 } )</pre>

<p>MongoDB peut lire les indexes d'un sens comme d'un autre, ascendant ou descendant, lorsqu'il n'y a qu'un seul champ.
Les indexes prennent en compte les requêtes, opérations de mise à jour ainsi que quelques phases du pipeline d'aggrégation.
Les indexes étant du type BinData si stockées plus efficacement dans l'indexe si :
- la valeur sous-type binaire se situe entre l'intervalle 0-7 ou 128-135
- la longueur du tableau de byte est : 0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 12, 14, 16, 20, 24 ou 32</p>
<a name="limi"></a>

<div class="spacer"></div>

<p class="small-titre">b) Limiter le Nombre de Résultats pour Réduire la Demande du Réseau</p>

<p>Les curseurs MongoDB retournent des résultats en groupes de plusieurs documents. Si vous connaissez le nombre de résultats que vous désirez, vous pouvez réduire
la demande au sein du réseau en utilisant la méthode limit().
Celle-ci est typiquement utilisée en conjonction avec les opérations de tri sort(). Par exemple, si vous avez besoin de seulement 10 résultats dans votre requête
depuis la collection posts :</p>

<pre>db.posts.find().sort( { timestamp : -1 } ).limit(10)</pre>
<a name="proj"></a>

<div class="spacer"></div>

<p class="small-titre">c) Utiliser les Projections pour Retourner Uniquement les Données Nécessaires</p>

<p>Lorsque vous avez besoin d'un sous-ensemble particulier de champ depuis vos documents, vous pouvez accéder à de meilleures performances en ne retournant uniquement
ces champs spécifiques. Par exemple, si dans votre requête visant la collection posts, vous avez besoin uniquement des champs timestamp, title, author et abstract :</p>

<pre>db.posts.find( {}, { timestamp : 1 , title : 1 , author : 1 , abstract : 1} ).sort( { timestamp : -1............</pre>
<a name="hint"></a>

<div class="spacer"></div>

<p class="small-titre">d) Utilisez $hint pour Sélectionner un Indexe Particulier</p>

<p>Dans la plupart des cas, l'optimiseur de requête sélectionne l'indexe optimal pour une opération spécifique. Toutefois, vous pouvez forcer MongoDB
à utiliser un autre indexe spécifique avec la méthode hint(). Utilisez hint() pour supporter le test de performances, ou pour certaines requêtes
ou vous devez impérativement sélectionner un champ ou un champ inclut dans plusieurs indexes.</p>
<a name="incr"></a>

<div class="spacer"></div>

<p class="small-titre">e) Utilisez l'Opérateur d'Incrémentation pour Effectuer des Opérations Côté Serveur</p>

<p>Utilisez l'opérateur $inc de MongoDB pour incrémenter ou décrémenter les valeurs dans les documents. L'opérateur incrémente la valeur d'un champ côté serveur, plutôt
que de sélectionner le document, effectuer de simples modifications sur le client et ensuite pour écrire le document entier sur le serveur.
L'opérateur $inc peut aussi aider à éviter les conditions de course, qui se produisent lorsque deux applications sélectionnent le même document, incrémentent manuellement
le champ et sauvegardent le même document en même temps.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la partie Pratique, la suite sur la <a href="maintenance.php">"Configuration, la Maintenance et L'Analyse" >></a>.</p>

<?php

	include("footer.php");

?>