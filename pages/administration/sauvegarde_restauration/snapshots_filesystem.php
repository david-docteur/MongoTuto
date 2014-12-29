<?php

	set_include_path("../../../");
	include("header.php");

?>

<ul class="breadcrumb">
	<li><a href="../../../index.php">Accueil</a></li>
	<li><a href="../../administration.php">Administration</a></li>
	<li><a href="../sauvegarde_restauration.php">Sauvegarde et Restauration</a></li>
	<li class="active">Sauvegarder et Restaurer avec des Snapshots FileSystem</li>
</ul>

<p class="titre">[ Sauvegarder et Restaurer avec des Snapshots FileSystem ]</p>

<div class="sousMenu">
	<p class="plan">Plan</p>
	<p class="elem"><a href="#ve">I) Vue d'Ensemble de Snapshots</a></p>
	<p class="right"><a href="#jour">- a) Snapshots avec le Journaling</a></p>
	<p class="right"><a href="#raid">- b) Snapshots avec Amazon EBS avec une Configuration RAID 10</a></p>
	<p class="elem"><a href="#lvm">II) Sauvegarder et Restaurer en Utilisant LVM sur un Système Linux</a></p>
	<p class="right"><a href="#cree">- a) Créer un Snapshot</a></p>
	<p class="right"><a href="#arch">- b) Archiver un Snapshot</a></p>
	<p class="right"><a href="#rest">- c) Restaurer un Snapshot</a></p>
	<p class="right"><a href="#snap">- d) Restaurer Directement Depuis un Snapshot</a></p>
	<p class="right"><a href="#stoc">- e) Stockage de Sauvegarde à Distance</a></p>
	<p class="elem"><a href="#inst">III) Créer des Sauvegardes sur des Instances qui n'ont pas le Journaling d'Activé</a></p>
</div>

<p>Le snapshot filesystem est une méthode de sauvegarde utilisant des outils système pour créer des copies du périphérique qui détient les
fichiers de données MongoDB. Ces méthodes se terminent rapidement et sont fiables, mais nécessitent plus de configuration système en dehors
de MongoDB.</p>
<a name="ve"></a>

<div class="spacer"></div>

<p class="titre">I) [ Vue d'Ensemble de Snapshots ]</p>

<p>Les snapshots fonctionnent en crééant des pointeurs entre les données live et un volume spécial de snapshot. Ces pointeurs sont théoriquement
équivalent à des "hard-links".(définition hard-links). Le processus de snapshot utilise une stratégie de copie en écriture. En conséquences, le snapshot
stocke uniquement des données modifiées.
Après avoir effectué le snapshot, vous montez l'image snapshot sur votre système de fichier et copiez les données depuis votre snapshot. La sauvegarde
résultante contient une copie complète des données.

Les snapshots ont les limitations suivantes :

- la base de données doit être intégrale lorsque le snapshot commence. Cela signifique que toutes les écritures acceptées par la base de données doivent
être écrites intégralement sur le disque : soit dans le journal ou dans les fichiers de données.
Si toutes les écritures ne sont pas sur le disque lorsque la sauvegarde s'effectue, la sauvegarde ne va pas refletter ces changements. Si les écritures
sont en cours lorsque la sauvegarde s'effectue, les fichiers de données vont refletter des données incomplètes. Avec le Journaling, tous les fichiers de données
ayant un état d'écriture "en cours" sont récupérable, mais sans le journaling, vous devez vider toutes les écritures en attente du disque avant d'exécuter l'opération
de sauvegarde et être sûr qu'aucune écriture ne s'effectue durant la procédure de sauvegarde entière.
Si vous utilisez le journaling, le journal doit impérativement se situer sur le même volume que celui des données.

- Les snapshots crééent une image d'un disque entier. Sauf si vous avez besoin de de sauvegarder votre système entier, vous pouvez isoler vos fichiers
de données MongoDB, le journal (si possible) et la configuration sur un seul disque logique qui ne contient aucune autre donnée.
D'une autre façon, stockez tous les fichiers de données MongoDB sur un périphérique dédié dans le but de pouvoir effectuer des sauvegardes sans dupliquer
les données.

- Assurez-vous que vous copiez les données des snapshots et sur d'autres systèmes pour vous assurer que les données sont à l'abris de pannes du site.

- Bien que différentes méthodes de snapshots fournissent différentes capacités, la méthode LVM décrite ci-dessous ne fournit aucune capacité à capturer
des sauvegardes incrémentales.</p>
<a name="jour"></a>

<div class="spacer"></div>

<p class="small-titre">a) Snapshots avec le Journaling</p>

<p>Si votre instance mongod a le journaling d'activé, alors vous pouvez utiliser n'importe quel système de fichier ou outil de snapshot pour créer des
sauvegardes. Si vous gérez votre propre infrastructure sur un système basé Linu, configurez votre système avec LVM pour fournir des packages
à votre disque et ajouter la capacité à effectuer des snapshots. Vous pouvez également utiliser des setup basés LVM au sein d'un environnement cloud/virtualisé.</p>

<div class="alert alert-info">
	<u>Note</u> : Exécuter LVM ajoute de la flexibilité et active la possibilité d'utiliser des snapshots pour sauvegarder MongoDB.
</div>
<a name="raid"></a>

<div class="spacer"></div>

<p class="small-titre">b) Snapshots avec Amazon EBS avec une Configuration RAID 10</p>

<p>Si votre déploiement dépend de l'Amazon Elastic Block Storage (EBS) avec RAID configuré au sein de votre instance, il est possible d'avoir un état consistant
à travrs tous les disques en utilisant l'outil de snapshot de la plateforme. Sinon, vous pouvez effectuer l'une des alternatives suivantes :

- Vider toutes les lectures du disque et créer un verrou d'écriture pour vous assurez de l'état consistant pendant le processus de sauvegarde.
- Confgurer LVM pour exécuter et maintenir vos fichiers des données MongoDB avec le RAID au sein de votre système.</p>
<a name="lvm"></a>

<div class="spacer"></div>

<p class="titre">II) [ Sauvegarder et Restaurer en Utilisant LVM sur un Système Linux ]</p>

<p>Ici, nous allons voir comment procéder à une sauvegarde en utilisant LVM sous Linux. Les outils et commandes peuvent légèrement varier
en fonction de votre système.</p>
<a name="cree"></a>

<div class="spacer"></div>

<p class="small-titre">a) Créer un Snapshot</p>

<p>Pour créer un snapshot avec LVM, utilisez la commande suivante :</p>

<pre>lvcreate --size 100M --snapshot --name mdb-snap01 /dev/vg0/mongodb</pre>

<p>Cette commande va créer un snapshot LVM (avec l'option --snapshot) nommé mdb-snap01 du volume "mongodb" dans le groupe de volumes vg0.
Cet exemple créé un snapshot nommé mdb-snap01 situé à /dev/vg0/mdb-snap01. La location et les chemins de vos groupes de volumes systèmes et périphériques pourraient
varier en fonction de a configuration de LVM de votre système de configuration.
Cet exemple a un cap de 100mo comme le définit le paramètre --size 100M. Cette taille ne représente pas le montant total de données sur le disque,
mais plutôt la quantité de différences entre l'état courant /dev/vg0/mongodb et et la création du snapshot /dev/vg0/mdb-snap01.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Assurez-vous de créer des snapshots en ayant assez d'espace pour les grossissements de données, particulièrement pour la période
	de temps qu'il prend à MongoDB de copier les données hors du système ou d'une image temporaire. Si votre snapshot n'a plus assez d'espace, l'image snapshot
	devient inutilisable. Supprimez le volume logique et créez-en un autre.
</div>

<p>Le snapshot va exister lorsque la commande est terminée. Vous pouvez restaurer directement avec le snapshot à n'importe quel moment, ou en créant
un nouveau volume logique et restaurer depuis ce snapshot vers l'image alternée.
Tandis que les snapshots sont biens pour créer des sauvegardes de haute qualité très rapidement, ils ne sont pas idéals en tant que format pour stocker des sauvegardes
pour stocker des données. Les snapshots dépendent et résident sur la même infrastructure de stockage en tant que image de disque originale.
Par conséquent, il est crucial que vous archiviez ces snapshots et que vous les stockiez ailleurs.</p>
<a name="arch"></a>

<div class="spacer"></div>

<p class="small-titre">b) Archiver un Snapshot</p>
 
<p>Après avoir créé un snapshot, montez le snapshot et déplacez les données vers un stockage différent. Votre système devrait essayer de compresser 
les images de sauvegarde. La procédure suivante archive complètement les données du snapshot :</p>

<pre>
umount /dev/vg0/mdb-snap01
dd if=/dev/vg0/mdb-snap01 | gzip > mdb-snap01.gz
</pre> 

<p>La séquence de commande ci-dessus effectue :

- s'assure que /dev/vg0/db-snap01 n'est pas monté
- Effecture une copie block-level de l'image entière du snapshot en utilisant la commande dd et compresse le résultat 
dans un fichier gzippé dans le répertoire de travail courant.</p> 

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Cette commande va créer un fichier gz large dans votre répertoire de travail courant. Soyez sûrs que vous exécutez
	cette commande dans un système de fichier qui a assez d'espace libre.
</div>
<a name="rest"></a>

<div class="spacer"></div>

<p class="small-titre">c) Restaurer un Snapshot</p>

<p>Pour restaurer un snapshot créé avec la méthode ci-dessus, utilisez la séquence de commandes suivante :</p>

<pre>
lvcreate --size 1G --name mdb-new vg0
gzip -d -c mdb-snap01.gz | dd of=/dev/vg0/mdb-new
mount /dev/vg0/mdb-new /srv/mongodb
</pre>

<p>La séquence commande effectue :

- Créé un nouveau volume logique nommé mdb-new, dans le group de volume /dev/vg0/.
Le chemin vers le nouveau périphérique va être /dev/vg0/mdb-new.</p>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : ce volume aura une taille maximum de 1go. Le système de fichier original doit avoir une taille de 1go ou moins,
	sinon, la restauration va échouer. Changez 1G pour votre taille de volume souhaitée.
</div>

<p>- Décompresser et désarchiver le mdb-snap01.gz dans l'image disque mdb-new.
- Monter l'image disque mdb-new sur le répertoire /srv/mongodb. Modifie le point de montage pour correspondre à la location de vos fichiers de données MongoDB,
ou toute autre location souhaitée.</p>

<div class="alert alert-info">
	<u>Note</u> : Le snapshot restauré va avoir un fichier mongod.lock obsolète. Si vous ne supprimez pas ce fichier du snapshot,
	MongoDB assumera que le fichier lock indiquera un arrêt non-propre. Si vous exécutez votre instance avec le journaling d'activé, et que vous n'utilisez
	pas db.fsyncLock(), vous n'aurez alors pas besoin de supprimer le fichier mongod.lock. Si vous utilisez db.fsyncLock(), alors vous aurez besoin de supprimer
	ce fichier.
</div>
<a name="snap"></a>

<div class="spacer"></div>

<p class="small-titre">d) Restaurer Directement Depuis un Snapshot</p>

<p>Pour restaurer une sauvegarde sans écrire dans un fichier gz compressé, utilisez la séquence de commandes suivante :</p>

<pre>
umount /dev/vg0/mdb-snap01
lvcreate --size 1G --name mdb-new vg0
dd if=/dev/vg0/mdb-snap01 of=/dev/vg0/mdb-new
mount /dev/vg0/mdb-new /srv/mongodb
</pre>
<a name="stoc"></a>

<div class="spacer"></div>

<p class="small-titre">e) Stockage de Sauvegarde à Distance</p>

<p>Vous pouvez utiliser des sauvegardes à distance en utilisant le processus ci-dessus en combinant avec SSH. La séquence qui va suivre est identique à celle expliquée
un peut plus haut, mais cette fois cela va archiver et compresser la sauvegarde sur un système à distance en utilisant SSH :</p>

<pre>
umount /dev/vg0/mdb-snap01
dd if=/dev/vg0/mdb-snap01 | ssh username@example.com gzip > /opt/backup/mdb-snap01.gz
lvcreate --size 1G --name mdb-new vg0
ssh username@example.com gzip -d -c /opt/backup/mdb-snap01.gz | dd of=/dev/vg0/mdb-new
mount /dev/vg0/mdb-new /srv/mongodb
</pre>
<a name="inst"></a>

<div class="spacer"></div>

<p class="titre">III) [ Créer des Sauvegardes sur des Instances qui n'ont pas le Journaling d'Activé ]</p>

<p>Si votre instance mongod n'est pas exécutée avec le journaling activé, ou si votre journal de trouve sur un volume séparé, obtenir une sauvegarde fonctionnelle
est plus compliqué. Comme décris dans cette section du tutoriel, vous devez vider toutes les écritures du disque et verrouiller la base de données
afin d'empêcher les écritures pendant le processus de sauvegarde. Si vous avez une configuration de Replica Set, alors utilisez un
membre secondaire qui ne reçoit pas de lectures (par exemple : membre caché) pour votre sauvegarde.

1) Pour vider les écritures du disque et pour verrouiller la base de données (pour éviter les futures écritures), utilisez la commande
db.fsyncLock() dans un shell mongo :</p>

<pre>db.fsyncLock();</pre>

<p>2) Effectuez l'opération de sauvegarde décrite dans la partie Créer un snapshot (lien au dessus).

3) Pour déverrouiller la base de données une fois le snapshot complété, utilisez la commande suivante dans un shell mongo :</p>

<pre>db.fsyncUnlock();</pre>

<div class="alert alert-info">
	<u>Note</u> : Depuis la version 2.0, MongoDB a ajouté les méthodes db.fsynLock() et db.fsyncUnlock() dans le shell mongo.
	Avant cette version, utilisez la commande fsync avec l'option lock comme suivant :
</div>

<div class="spacer"></div>

<pre>
db.runCommand( { fsync: 1, lock: true } );
db.runCommand( { fsync: 1, lock: false } );
</pre>

<div class="alert alert-info">
	<u>Note</u> : La base de données ne peut pas être verrouillée avec db.fsyncLock() pendant que le profiling est activé. Vous devez
	dabord désactiver le profiling avant de verrouiller la base de données avec db.fsyncLock(). Désactiver le profiling en utilisant
	la méthode db.setProfilingLevel() comme suivant :
</div>

<pre>db.setProfilingLevel(0)</pre>

<div class="spacer"></div>

<div class="alert alert-danger">
	<u>Attention</u> : Depuis la version 2.2 de MongoDB, lorsque vous utilisez fsync ou db.fsyncLock(), mongod devrait bloquer
	quelques opérations de lecture, incluant celle de mongodump, lorsque les opérations d'écritures dans la file d'attente attendent derrière le
	verrou fsync.
</div>

<div class="spacer"></div>

<p>La suite va concerner <a href="restaurer_replicaset.php">"Restaurer un Replica Set avec une Sauvegarde MongoDB" >></a>.</p>

<?php

	include("footer.php");

?>
