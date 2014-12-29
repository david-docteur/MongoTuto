<?php

	set_include_path("../../");
	include("header.php");

?>


<ul class="breadcrumb">
	<li><a href="../../index.php">Accueil</a></li>
	<li><a href="../administration.php">Administration</a></li>
	<li class="active">Stratégies de Sauvegarde pour Systèmes MongoDB</li>
</ul>

<p class="titre">[ Stratégies de Sauvegarde pour Systèmes MongoDB ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#cons">I) Considérations Pour la Sauvegarde</a></p>
	<p class="elem"><a href="#appr">II) Approches de Sauvegarde</a></p>
	<p class="elem"><a href="#stra">III) Stratégies de Sauvegarde Pour Déploiements MongoDB</a></p>
</div>

<p>Les sauvegardes représentent une partie très importante et souvent négligée lors d'un plan de restauration en cas de catastrophe.
Un plan de récupération sérieux doit être capable de capturer les données dans un état intégral et exploitable, ce qui devait impliquer une sauevgarde et une
restauration correcte. Vous devez penser aussi à tester tous les composants du système de sauvegarde pour vous assurer que vous pouvez restaurer les données
sauvegardées en cas de besoin. Si vous ne pouvez pas restaurer votre base de données de manière efficace, alors vos sauvegardes sont inutiles.</p>

<div class="spacer"></div>

<div class="alert alert-info">
	<u>Note</u> : Le <a href="https://mms.10gen.com/?pk_campaign=MongoDB-Org&pk_kwd=Backup-Docs" target="_blank">MMS</a> (MongoDB Management Service) permet la sauvegarde et la restauration de vos déploiements MongoDB.
	Vous pouvez consulter la <a href="https://mms.mongodb.com/help/backup/" target="_blank">documentation officielle</a> du MMS si vous souhaitez avoir comment
	vous en servir.
</div>
<a name="cons"></a>

<div class="spacer"></div>

<p class="titre">I) [ Considérations Pour la Sauvegarde ]</p>

<p>Si vous développez une stratégie de sauvegarde pour votre déploiement MongoDB, considérez les facteurs suivants :

- La géographie : Assurez-vous que vous effectuez des sauvegardes à un autre endroit que celui de l'infrastructure de votre base de données primaire.
- Erreurs Système : Soyez sûrs que vos sauvegardes peuvent survivre lors des cas de pannes hardwares ou d'erreurs de disque dûr qui pourraient avoir un impacte
  sur l'intégritée ou la disponibiité de vos sauvegardes.
- Contraintes de production : Les opérations de sauvegarde peuvent elles-même nécessiter pas mal de ressources système. Il est important de considérer
  le temps que représente une sauvegarde, quand le système est le plus utilisé et la période de maintenance prévue.
- Capacité du système : Certains outils de snapshot nécessitent un support spécial sur le système d'exploitation ou au niveau de l'infrastructure.
- Configuration de la base de données : La réplication et le Sharding peuvent affecter le processus de sauvegarde et avoir un impacte sur son implémentation.
- Besoins actuels : vous pourriez gagner du temps, ds efforts et de l'espace en incluant uniquement les données cruciales dans les sauvegardes les plus fréquentes
  et sauvegarder les données les moins importantes moins souvent.</p>

<div class="small-spacer"></div>
  
<div class="alert alert-danger">
	<u>Attention</u> : Afin de pouvoir utiliser des systèmes de fichiers de snapshots pour les sauvegardes, votre instance mongod doit
	impérativement avoir le journaling d'activé, ce qui est activé par défaut sur les versions 64bits de MongoDB depuis sa version 2.0.
	Si le journal est basé sur un filesystem différent que celui de vos fichiers de données, alors vous devrez aussi désactiver les opérations d'écritures
	pendant que le snapshot se termine.
</div>
<a name="appr"></a>

<div class="spacer"></div>

<p class="titre">II) [ Approches de Sauvegarde ]</p>
  
<p>Il y a deux méthodes principales pour pour sauvegarder des instances MongoDB : créer des "dumps" binaires de la base de données en utilisant mongodump
ou créer snapshot filesystem. Les deux méthodes ont chacunes leurs avantages et inconvénients :

- Les dumps binaires de la base de données sont petits car ils n'incluent pas les indexes ou d'espace disque pré-alloué. Par contre, il est impossible
de capturer une copie d'un système en cours d'exécution qui reflette un moment particulier dans le temps.

- Les snapshots filesystem produisent des sauvegardes plus lourdes mais se terminent plus rapidement et peuvent refletter un instant spécifique dans le temps
d'un système en cours d'exécution. En revanche, les systèmes de snapshot nécessitent un système de fichier ainsi que des outils et un système d'exploitation qui les
supporte.

La meilleure option dépend des besoins de votre déploiement ainsi que des besoins de restauration en cas de désastre. Typiquement, les systèmes de fichiers
de snapshots existent pour leur efficacté et simplicité. Par contre, mongodump est une option viable souvent utilisée pour générer des sauvegardes de système MongoDB.
Dans certains cas, effectuer des sauvegardes est difficile voire impossible à cause de gros volumes de données, d'architectures distribuées et de la vitesse
de transmission des données. Dans ces situations, augmentez le nombre de membres dans votre (ou vos) Replica Set(s).</p> 
<a name="stra"></a>

<div class="spacer"></div>
  
<p class="titre">III) [ Stratégies de Sauvegarde Pour Déploiements MongoDB ]</p>

<p>Considérations de sauvegarde pour Sharded Cluster : Pour capturer une sauvegarde d'un certain moment dans le temps d'un sharded cluster, vous devez
stopper toutes les opération d'écritures sur le cluster. Sur un système de production en cours d'exécution, vous pouvez capturer seulement une approximation
du snapshot pointant vers un certain moment dans le temps.

Les sharded clusters compliquent les opérations de sauvegarde vus qu'ils sont des systèmes distribués. Les sauvegardes pointant à un moment précis dans le temps
sont possibles uniquement en stoppant toute activité d'écriture depuis l'application. Pour créer un snapshot précis dans le temps d'un cluster, arrêtez
toutes les opérations d'écriture vers la base de données, capturez une sauvegarde, et autorisez les opérations d'écriture uniquement après que la sauvegarde
de la base de données soit complètement terminée.

En revanche, vous pouvez capturer une sauvegarde d'un cluster qui sera approximativement la reflet d'un moment précis dans le temps, en capturant une sauvegarde
d'un membre secondaire de votre Replica Set qui fournit les shards dans le cluster à un moment similaire. Si vous décidez d'utiliser une sauvegarde approximative,
assurez-vous que votre application peut opérer en utilisant une copie des données qui ne reflette pas un moment particulier dans le temps.</p>

<div class="spacer"></div>

<p>Considérations de sauvegarde de Replica Set : Dans la plupart des cas, sauvegarder les données stockées sur un Replica Set est similaire à sauvegarder les données
stockées sur une une seule instance :

- créer un système de fichier snapshot d'un seule secondaire. Vous pourrez choisir de maintenir un membre caché dédié à la sauvegarde unqiuement.
- d'une autre façon, vous pouvez créer une sauvegarde avec le programme mongodump et l'option --oplog. Pour restaurer cette sauvegarde, utilisez le programme mongodump
avec l'option --oplogReplay.

Si vous avez un sharded cluster ou chaque shard est lui-même un Replica Set, vous pouvez utiliser l'une de ces méthodes pour créer une sauvegarde du cluster entier
sans interrompre les opérations du noeud. Dans ces situations, vous devrez arrêter le balanceur lorsque vous créez des sauvegardes.

Pour tout cluster, utiliser un noeud non primaire pour créer des sauvegardes est particulièrement avantageux vu que l'opération de sauvegarde n'affecte pas 
les performances du membre primaire. La réplication elle-même fournit certaines mesures de redondance. Néanmoins, garder des sauvegardes de votre cluster
pointant dans le temps pour une récupération après un désastre, est une couche additionnelle de protection cruciale.</p>
  
<div class="spacer"></div>

<p>Vous êtes arrivé ici ? Bien joué ! Maintenant, passons à la suite sur le <a href="monitoring.php">"Monitoring avec MongoDB" >></a>.</p>


<?php

	include("footer.php");

?>