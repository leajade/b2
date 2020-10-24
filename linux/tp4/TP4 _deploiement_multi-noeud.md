#TP4 : Déploiement multi-noeud

------

Le rendu Markdown doit comporter :

- la liste des hosts
- la liste des interfaces Web joignables
- ce qu'il faut ajouter dans mon `/etc/hosts` si besoin est
- comment et quel fichier je dois modifier pour ajouter l'URL de mon discord pour les alertes
  - une variable dans un script ? :)

### Liste des hosts

| Name         | IP           | Rôle                                                         |
| ------------ | ------------ | ------------------------------------------------------------ |
| node1.tp4.b2 | 192.168.2.24 | Gitea (application libre et opensource qui permet d'héberger des dépôts Git et fournit une interface Web) |
| node2.tp4.b2 | 192.168.2.25 | MariaDB (base de données)                                    |
| node3.tp4.b2 | 192.168.2.26 | NGINX (reverse proxy)                                        |
| node4.tp4.b2 | 192.168.2.27 | serveur NFS (serveur de partage de fichiers sur le réseau)   |

### Une base commune

Toutes les machines déployées doivent :

- figurer dans le fichier `/etc/hosts` des autres
- porter un service
- si le serveur comporte des données, il doit être sauvegardé (voir la section II.5. pour le détail de ce qui doit être backup)
- être monitorées
- posséder un firewall configuré pour ne laisser passer que le strict nécessaire

Je vous laisse réfléchir à ce qui est le mieux : qu'est-ce qui doit être dans la box repackagée, dans le Vagrantfile, ou dans un script qui est lancé au démarrage de la machine. N'hésitez pas à me solliciter pour en discuter.

