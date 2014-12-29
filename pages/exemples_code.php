<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Exemples de Code</li>
</ul>

<p class="titre">[ Exemples de Code ]</p>

<p>Pour ceux qui souhaitent <b>appliquer la théorie à la pratique</b>, ceux qui <b>recherchent un exemple de code particulier</b> ou même les curieux qui désirent
<b>juste voir à quoi ressemble</b> l'utilisation de MongoDB dans leur langage préféré, c'est par ici !
J'ai vraiment envie que cette section du site <b>devienne très complète</b>, contenant des exemples de <b>bases de données fictives</b> et même d'arriver à
contenir des exemples pour <b>chaque langage implémentant un driver MongoDB</b>. Si vous avez des requêtes en tête, <b>envoyez-les moi</b>, même si vous avez
des exemples déjà faits, <b>ça m'aidera beaucoup</b>, merci :)</p>

<div id="tableauLangages">

	<div class="langage">
		Java
		<p><a href="exemples_code/java.php">Installation du driver MongoDB avec Java</a></p>
		<p><a href="exemples_code/java.php">Connexion</a></p>
		<p><a href="exemples_code/java.php">Opération CREATE - insert(), save(), update()</a></p>
		<p><a href="exemples_code/java.php">Opération READ - find()</a></p>
		<p><a href="exemples_code/java.php">Opération DELETE - remove()</a></p>
	</div>

	<div class="langage">
		Php
		<p><a href="exemples_code/php.php">Installation du driver MongoDB avec Php</a></p>
		<p><a href="exemples_code/php.php">Connexion</a></p>
		<p><a href="exemples_code/php.php">Opération CREATE - insert(), save(), update()</a></p>
		<p><a href="exemples_code/php.php">Opération READ - find()</a></p>
		<p><a href="exemples_code/php.php">Opération DELETE - remove()</a></p>
	</div>

	<div class="langage">
		Python
		<p><a href="exemples_code/python.php">Installation du driver MongoDB avec Python</a></p>
		<p><a href="exemples_code/python.php">Connexion</a></p>
		<p><a href="exemples_code/python.php">Opération CREATE - insert(), save(), update()</a></p>
		<p><a href="exemples_code/python.php">Opération READ - find()</a></p>
		<p><a href="exemples_code/python.php">Opération DELETE - remove()</a></p>
	</div>

</div>

<div class="spacer"></div>

<p><b>Vous ne trouvez pas un exemple de code particulier ?</b> Ou votre langage n'est pas listé ? <a href="contact.php">Contactez-moi !</a> et j'ajouterai
<b>votre code</b> à cette section.</p>

<?php

	include("footer.php");

?>
