<?php

	set_include_path("../../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../maintenance.php">Configuration, Maintenance et Analyse</a></li>
	<li class="active">Gérer le Journaling</li>
</ul>

<p class="titre">[ Gérer le Journaling ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#acti">I) Activer le Journaling</a></p>
	<p class="elem"><a href="#desa">II) Désactiver le Journaling</a></p>
	<p class="elem"><a href="#rece">III) Recevoir Confirmation d'Opération</a></p>
	<p class="elem"><a href="#evit">IV) Eviter la Latence de Pré-Allocation</a></p>
	<p class="elem"><a href="#moni">V) Monitoring du Statut du Journal</a></p>
	<p class="elem"><a href="#rest">VI) Restaurer les Données après un Arrêt Inattendu</a></p>
</div>

<p>MongoDB écrit dans un fichier journal sur le disque dur afin de garantir qu'une opération d'écriture a bien été effectuée et prévenir des crash.
Avant d'appliquer un changement aux fichiers de données, MongoDB écrit l'opération de changement dans le journal. Si MongoDB doit se terminer ou rencontre
une erreur avant qu'il puisse écrire les changements depuis le journal vers les fichiers de données, MongoDB peut ré-appliquer l'opération d'écriture
et maintenir un état intègre des données.
Sans un journal, si mongod se termine de façon anormale ou immédiate, vous devez assumer le fait que vos données perdent leur intégrité, et vous devrez donc
soit exécuter repair ou resync depuis un membre intègre du Replica Set.

Avec le journaling d'activé, si mongod s'interromp, le programme peut restaurer tout ce qu'il y a dans le journal, et les données restent intègres.
Avec le journaling, si vous souhaitez qu'un ensemble de données reste intégralement dans la mémoire RAM, vous aurez besoin d'assez de RAM pour contenir l'ensemble
des données.</p>
<div class="small-spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Depuis la version 2.0, les versions 64bits de mongod ont le journaling d'activé par défaut.
</div>

<p>Test</p>
<a name="acti"></a>

<div class="spacer"></div>

<p class="titre">I) [ Activer le Journaling ]</p>

<p>Pour activer le journaling, démarrez mongod avec l'option --journal en ligne de commande.
Si aucun journal n'existe lorsque mongod démarre, il doit pré-allouer de nouveaux fichiers journaux. Pendant cette opération, mongod n'écoute pas les connexions
jusqu'à ce que la prallocation se termine correctement, pour certains systèmes, cela risque de prendre quelques minutes. Durant cette période,
vos applications et le shell mongo ne sont pas disponibles.</p>
<a name="desa"></a>

<div class="spacer"></div>

<p class="titre">II) [ Désactiver le Journaling ]</p>

<div class="alert alert-danger">
	<u>Attention</u> : Ne désactivez pas le journaling sur les déploiements de production. Si votre instance mongod ne s'arrête pas proprement pour
	x raison (coupure de courant par exemple) et que vous n'avez pas le journaling d'activé, alors vous devrez restaurer à partir d'un membre du Replica Set
	ou d'une sauvegarde non-affecté par l'interruption.
</div>

<p>Pour désactiver le journaling, démarrez mongod avec l'option --nojournal en ligne de commande.</p>
<a name="rece"></a>

<div class="spacer"></div>

<p class="titre">III) [ Recevoir Confirmation d'Opération ]</p>

<p>Vous pouvez recevoir un message de retour sur la confirmation de l'opération avec la commande getLastError et l'option j avec le Write Concern.</p>
<a name="evit"></a>

<div class="spacer"></div>

<p class="titre">IV) [ Eviter la Latence de Pré-Allocation ]</p>

<p>Pour éviter la latence de pré-allocation, vous pouvez pré-allouer les fichiers dans le répertoire du journal en les copiant depuis une autre instance
mongod.
Les fichiers pré-alloués ne contiennent pas de données. Il est plus prudent de les supprimer plus tard. Mais is vous redémarrez mongod avec le journaling
d'activé, mongod va les créer à nouveau.

Par exemple, la séquence suivante pré-alloue des fichiers journaux pour une instance mongod exécutée sur le port 27017 avec un dbpath /data/db :

1) Créez un répertoire temporaire dans lequel vous allez créer un ensemble de fichiers journaux :</p>

<pre>mkdir ~/tmpDbpath</pre>

<p>2) Créez un ensemble de fichiers journaux en démarrant une instance mongod qui utilise ce répertoire temporaire :</p>

<pre>mongod --port 10000 --dbpath ~/tmpDbpath --journal</pre>

<p>3) Lorsque vous voyez la sortie suivante, indiquant que mongod a les fichiers, pressez Cntrl + C pour arrêter l'instance mongod :</p>

<pre>web admin interface listening on port 11000</pre>

<div class="spacer"></div>

<p>4) Pré-allouez les fichiers journaux pour la nouvelle instance mongod en déplaçant les fichiers journaux depuis le répertoire de données de l'instance
existante vers ce même répertoire de la nouvelle instance.</p>

<pre>mv ~/tmpDbpath/journal /data/db/</pre>

<p>5) Démarrer la nouvelle instance mongod :</p>

<pre>mongod --port 27017 --dbpath /data/db --journal</pre>
<a name="moni"></a>

<div class="spacer"></div>

<p class="titre">V) [ Monitoring du Statut du Journal ]</p>

<p>Utilisez les commandes et méthodes suivantes pour surveiller le statut du journal :

- serverStatus : La commande serverStatus retourne des informations sur le statut de la base de données qui sont utiles pour les statistiques de performances.
- journalLatencyTest : Cette commande va permettre de calculer combien de temps cela prend à votre volume d'écrire sur le disque en ajoutant au fichier. Vous 
pouvez exécuter cette commande sur un système non-occupé (idle) pour avoir un temps de synchronisation de base pour le journaling. Vous pouvez également
exécuter cette même commande sur un système occupé pouvoir voir le temps de synchronisation sur un système occupé, ce qui devrait être plus haut
si le répertoire de journal est sur le même volume que les fichiers de données.</p>
<a name="rest"></a>

<div class="spacer"></div>

<p class="titre">VI) [ Restaurer les Données après un Arrêt Inattendu ]</p>

<p>En redémarrant après un crash, MongoDB re-applique tous les fichiers journaux dans le répertoire de journaux avant que le serveur ne redevienne disponible.
Si MongoDB doit rejouer tous les fichiers journaux, mongod écrit ces évênements dans le fichier log. Il n'y aucune raison d'exécuter repairDatabase
dans ces situations.</p>

<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur <a href="stocker_js.php">"Stocker une Fonction JavaScript sur le Serveur" >></a>.</p>

<?php

	include("footer.php");

?>
