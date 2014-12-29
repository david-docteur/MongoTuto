<?php

	set_include_path("../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../index.php">Accueil</a></li>
	<li class="active">Installation</li>
</ul>

<p class="titre">[ Installation ]</p>

<p>Bien, passons à un peu de <b>technique</b> ! Comme promis, MongoDB s'installe sur <b>presque tous</b> les systèmes
d'exploitation les plus connus, vous pouvez sélectionner la version qu'il vous faut, en fonction de votre système, avec le <b>tableau</b> ci-dessous.</p>
<p>Un peu plus bas se trouvent les informations d'installation de la version <b>MongoDB Enterprise</b>.</p>

<div class="spacer"></div>

<p class="titre">[ MongoDB ]</p>
<table>
	<tr>
		<th><img alt="GNU/Linux" src="/img/os/logo-linux.png" height="47" width="55"> GNU/Linux</th>
		<th><img alt="GNU/Linux" src="/img/os/logo-windows.png" height="33" width="41"> Microsoft Windows</th>
		<th><img alt="GNU/Linux" src="/img/os/logo-mac.png" height="40" width="35"> Apple Mac OS</th>
	</tr>
	<tr>
		<td><a href="installations/install_linux_redhat.php">RedHat Enterprise, Fedora, CentOs (.RPM)</a></td>
		<td><a href="installations/install_windows_32-64.php">Windows Vista 32/64bits et plus récentes</a></td>
		<td><a href="installations/install_mac_32-64.php">OS X 10.6 (Snow Leopard) pour Intel x86-64 et plus récentes</a></td>
	</tr>
	<tr>
		<td><a href="installations/install_linux_ubuntu.php">Ubuntu (.DEB)</a></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><a href="installations/install_linux_debian.php">Debian (.DEB)</a></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><a href="installations/install_linux_autres.php">Autres</a></td>
		<td></td>
		<td></td>
	</tr>
</table>

<div class="small-spacer"></div>

<p>Vous ne connaissez pas l'<b>architecture</b> de votre version de Windows ? Tapez la <b>commande</b> ci-dessous dans un <b>invite de commande</b> :</p>

<div class="small-spacer"></div>
 
<pre>wmic os get osarchitecture</pre>

<div class="spacer"></div>

<p class="titre">[ MongoDB Enterprise ]</p>

<p><b>MongoDB Enterprise</b> est une version, comme son nom l'indique, spécialisée pour entreprises et est donc <b>payante</b>.</p>
<p>Elle contient plus de <b>support professionnel</b> concernant la <b>sécurité</b> et le <b>monitoring</b> des informations. Celle-ci existe pour <b>4 plateformes.</b></p>
<p>De plus, depuis la version 2.4.4, MongoDB Enterprise utilise la <b>license Cyrus SASL</b> à la place de la <b>license GNU SASL</b>.</p>
<p>Vous êtes <b>intéressés</b> par la version Enterprise ? Cliquez ici pour <a href="installations/install_mongodb_enterprise.php">"L'installation de MongoDB Enterprise"</a>.</p>

<div class="spacer"></div>

<p>Voilà, une fois que votre <b>installation est terminée</b> et que votre <b>mongod s'exécute</b>, vous pouvez avancer au chapitre sur les <a href="operations_crud.php">"Opérations CRUD" >></a>.</p>

<?php

	include("footer.php");

?>
