# TP3 : systemd

Le but ici c'est d'explorer un peu systemd.

systemd est un outil qui a été très largement adopté au sein des distributions GNU/Linux les plus répandues (Debian, RedHat, Arch, etc.). systemd occupe plusieurs fonctions :

- système d'init
- gestion de services
- embarque plusieurs applications très proche du noyau et nécessaires au bon fonctionnement du système
  - comme par exemple la gestion de la date et de l'heure, ou encore la gestion des périphériques
- PID 1

Ce TP3 a donc pour objectif d'explorer un peu ces différentes facettes. La finalité derrière tout ça est de vous faire un peu mieux appréhender comment marche un OS GNU/Linux ; mais aussi de façon plus générale vous faire mieux appréhender en quoi consiste l'application qu'on appelle "système d'exploitation" (car ui, c'est juste une application).

Au menu :

- manipulation des *unités systemd*, et en particulier les *services*
- analyse (succincte) du boot d'une machine GNU/Linux
- appréhension de certains des éléments embarqués avec systemd
  - tâche planifiées (alternative à cron)
  - gestion de l'heure
  - gestion des noms
- bonus frappe : on va réviser un peu la manipulation de la ligne de commande n_n
  - les lignes précédées d'un **|CLI|** font appel à vos talents sur la ligne de commande
  - en réponse à ces lignes, une seule ligne de commande est attendue

- [0. Prérequis](#0-prérequis)
- I. Services systemd
  - [1. Intro](#1-intro)
  - [2. Analyse d'un service](#2-analyse-dun-service)
  - \3. Création d'un service
    - [A. Serveur web](#a-serveur-web)
    - [B. Sauvegarde](#b-sauvegarde)
- II. Autres features
  - [1. Gestion d'interfaces](#1-gestion-dinterfaces)
  - [2. Gestion de boot](#2-gestion-de-boot)
  - [3. Gestion de l'heure](#3-gestion-de-lheure)
  - [4. Gestion des noms et de la résolution de noms](#4-gestion-des-noms-et-de-la-résolution-de-noms)
- [Structure du dépôt attendu](#structure-du-dépôt-attendu)

# 0. Prérequis

> De toute évidence, vous utiliserez désormais Vagrant systématiquement pour créer votre environnement de travail.

Une VM suffira pour le TP. Je vous conseille d'utiliser une box `centos/7` comme base, et de la repackager avec :

- une mise à jour complète du système (pas obligé si la connexion dont vous bénéficiez a deux de tension)
- NGINX installé
- d'autres trucs si vous le souhaitez (comme `vim` :D)

**HA** et on va se reservir du script de backup du [TP1]().

# I. Services systemd

## 1. Intro

Section d'intro aux services systemd. Ui c'est ces trucs qu'on lance avec des commandes `systemctl start` par exemple.

Pour voir une liste de tous les services actuellement disponibles sur la machine, on peut interroger systemd :

```
# Liste les services actifs
$ sudo systemctl -t service

# Liste les services et leur état au boot
$ sudo systemctl list-unit-files -t service

# Liste tous les services
$ sudo systemctl list-unit-files -t service -a
```

🌞 Utilisez la ligne de commande pour sortir les infos suivantes :

- **|CLI|** afficher le nombre de *services systemd* dispos sur la machine

  ```bash
  [vagrant@b2-tp3-systemd ~]$ sudo systemctl list-unit-files -t service -a | wc -l
  159
  ```

  

- **|CLI|** afficher le nombre de *services systemd* actifs et en cours d'exécution *("running")* sur la machine

  ```bash
  [vagrant@b2-tp3-systemd ~]$ sudo systemctl -t service | grep 'running' | wc -l
  18
  ```

  

- **|CLI|** afficher le nombre de *services systemd* qui ont échoué *("failed")* ou qui sont inactifs *("exited")* sur la machine

  ```bash
  [vagrant@b2-tp3-systemd ~]$ sudo systemctl list-units -t service -a | grep -E 'failed|exited' | wc -l
  18
  ```

  

- **|CLI|** afficher la liste des *services systemd* qui démarrent automatiquement au boot *("enabled")*

  ```bash
  [vagrant@b2-tp3-systemd ~]$ sudo systemctl list-unit-files -t service | grep 'enabled'
  auditd.service                                enabled 
  autovt@.service                               enabled 
  chronyd.service                               enabled 
  crond.service                                 enabled 
  dbus-org.fedoraproject.FirewallD1.service     enabled 
  dbus-org.freedesktop.nm-dispatcher.service    enabled 
  firewalld.service                             enabled 
  getty@.service                                enabled 
  irqbalance.service                            enabled 
  NetworkManager-dispatcher.service             enabled 
  NetworkManager-wait-online.service            enabled 
  NetworkManager.service                        enabled 
  postfix.service                               enabled 
  qemu-guest-agent.service                      enabled 
  rhel-autorelabel-mark.service                 enabled 
  rhel-autorelabel.service                      enabled 
  rhel-configure.service                        enabled 
  rhel-dmesg.service                            enabled 
  rhel-domainname.service                       enabled 
  rhel-import-state.service                     enabled 
  rhel-loadmodules.service                      enabled 
  rhel-readonly.service                         enabled 
  rpcbind.service                               enabled 
  rsyslog.service                               enabled 
  sshd.service                                  enabled 
  systemd-readahead-collect.service             enabled 
  systemd-readahead-drop.service                enabled 
  systemd-readahead-replay.service              enabled 
  tuned.service                                 enabled 
  vboxadd-service.service                       enabled 
  vboxadd.service                               enabled 
  vgauthd.service                               enabled 
  vmtoolsd-init.service                         enabled 
  vmtoolsd.service                              enabled 
  ```

  

------

**Okay mais un service c'est quoi ?**

Un service c'est juste un truc pratique pour lancer des processus ou des tâches simplement. Par "simplement", ça veut dire qu'une fois qu'on utilise une gestion de service, commes les *services systemd*, on a plus besoin de (entre autres) :

- connaître par coeur la commande pour lancer un truc
- connaître par coeur quelles applications doivent se lancer dans quel ordre pour que tout fonctionne
- gérer à la main l'environnement pour lancer une application
  - l'utilisateur qui lance l'app
  - les droits qu'a l'application
  - etc.
- écrire des scripts shell inmaintenables pour maintenir tout ça n_n

**Donc concrètement, un service ça permet de lancer un processus ET gérer son environnement.**

## 2. Analyse d'un service

Pour voir le contenu d'un service existant :

```
# Affiche le path du fichier qui définit un service donné
$ systemctl status <SERVICE>

# Affiche le contenu de l'unité directement
$ systemctl cat <SERVICE>
```

**La ligne la plus importante est celle qui commence par `ExecStart=` :** c'est elle qui indique le binaire à exécuter quand le service est démarré (c'est à dire la commande à lancer pour que le service soit considéré comme "actif").

🌞 Etudiez le service `nginx.service`

- déterminer le path de l'unité `nginx.service`

  ```bash
  /usr/lib/systemd/system/nginx.service
  ```

  (Première ligne commenté de la commande ```sudo systemctl cat nginx```)

  

- afficher son contenu et expliquer les lignes qui comportent :

  - `ExecStart`

    ```bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'ExecStart'
    ExecStartPre=/usr/bin/rm -f /run/nginx.pid
    ExecStartPre=/usr/sbin/nginx -t
    ExecStart=/usr/sbin/nginx
    
    Commands with their arguments that are executed when this service is started. For each of the specified commands, the first argument must be an absolute path to an executable.
    ```

    

  - `ExecStartPre`

    ````bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'ExecStartPre'
    ExecStartPre=/usr/bin/rm -f /run/nginx.pid
    ExecStartPre=/usr/sbin/nginx -t
    
    Additional commands that are executed before or after the command in ExecStart=, respectively.
    ````

    

  - `PIDFile`

    ```bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'PIDFile'
    PIDFile=/run/nginx.pid
    
    Takes an absolute path referring to the PID file of the service. Usage of this option is recommended for services where Type= is set to forking.
    ```

    

  - `Type`

    ```bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'Type'
    Type=forking
    
    Configures the process start-up type for this service unit. One of simple, forking, oneshot, dbus, notify or idle.
    ```

    

  - `ExecReload`

    ````bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'ExecReload'
    ExecReload=/bin/kill -s HUP $MAINPID
    
    Commands to execute to trigger a configuration reload in the service.
    ````

    

  - `Description`

    ````bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'Description'
    Description=The nginx HTTP and reverse proxy server
    
    A free-form string describing the unit. This is intended for use in UIs to show descriptive information along with the unit name. The description should contain a name that means something to the end user.
    ````

    

  - `After`

    ```bash
    [vagrant@b2-tp3-systemd ~]$ sudo systemctl cat nginx | grep 'After'
    After=network.target remote-fs.target nss-lookup.target
    
    Configures ordering dependencies between units. If a unit foo.service contains a setting Before=bar.service and both units are being started, bar.service's start-up is delayed until foo.service is started up. Note that this setting is independent of and orthogonal to the requirement dependencies as configured by Requires=.
    After= is the inverse of Before=, i.e. while After= ensures that the configured unit is started after the listed unit finished starting up, Before= ensures the opposite, i.e. that the configured unit is fully started up before the listed unit is started.
    ```

    

> Les mans de systemd sont très complets : `man systemd.unit` et `man systemd.service` par exemple. Une recherche ggl ça marche aussi, la meilleure doc étant [la doc officielle](https://www.freedesktop.org/software/systemd/man/systemd.service.html) (PS : c'est la même chose que dans le `man` n_n)

🌞 **|CLI|** Listez tous les services qui contiennent la ligne `WantedBy=multi-user.target`

```

```



## 3. Création d'un service

Pour créer un service, il suffit de créer un fichier au bon endroit, avec une syntaxe particulière.

L'endroit qui est dédié à la création de services par l'administrateur est `/etc/systemd/system/`. Les services système (installés par des paquets par exemple) se place dans d'autres dossiers.

Une fois qu'un service a été ajouté, il est nécessaire de demander à systemd de relire tous les fichiers afin qu'il découvre le vôtre :

```
$ sudo systemctl daemon-reload
```

### A. Serveur web

🌞 Créez une unité de service qui lance un serveur web

- la commande pour lancer le serveur web est `python3 -m http.server <PORT>`

- quand le service se lance, le port doit s'ouvrir juste avant dans le firewall

- quand le service se termine, le port doit se fermer juste après dans le firewall

- un utilisateur dédié doit lancer le service

- le service doit comporter une description

- le port utilisé doit être défini dans une variable d'environnement (avec la clause `Environment=`)

  ```bash
  [vagrant@b2-tp3-systemd system]$ cat serverweb.service
  [Unit]
  Description=Unite de service qui lance serveur web
  
  [Service]
  User=web
  Environment="PORT=80"
  
  ExecStartPre=/usr/bin/sudo /usr/bin/firewall-cmd --add-port=${PORT}/tcp --permanent
  ExecStartPre=/usr/bin/sudo /usr/bin/firewall-cmd --reload
  ExecStart=/usr/bin/sudo /usr/bin/python3 -m http.server ${PORT}
  ExecStartPost=/usr/bin/sudo /usr/bin/firewall-cmd --remove-port=${PORT}/tcp --permanent
  ExecStartPost=/usr/bin/sudo /usr/bin/firewall-cmd --reload
  
  ExecReload=/usr/bin/sudo /bin/kill -HUP $MAINPID
  
  
  [Install]
  WantedBy=multi-user.target
  ```

  

🌞 Lancer le service

- prouver qu'il est en cours de fonctionnement pour systemd

  ```bash
  [web@b2-tp3-systemd system]$ systemctl status serverweb.service 
  ● serverweb.service - Unite de service qui lance serveur web
     Loaded: loaded (/etc/systemd/system/serverweb.service; disabled; vendor preset: disabled)
     Active: active (running) since Wed 2020-10-07 08:41:51 UTC; 20s ago
    Process: 4054 ExecStartPost=/usr/bin/sudo /usr/bin/firewall-cmd --reload (code=exited, status=0/SUCCESS)
    Process: 4044 ExecStartPost=/usr/bin/sudo /usr/bin/firewall-cmd --remove-port=${PORT}/tcp --permanent (code=exited, status=0/SUCCESS)
    Process: 4010 ExecStartPre=/usr/bin/sudo /usr/bin/firewall-cmd --reload (code=exited, status=0/SUCCESS)
    Process: 4004 ExecStartPre=/usr/bin/sudo /usr/bin/firewall-cmd --add-port=${PORT}/tcp --permanent (code=exited, status=0/SUCCESS)
   Main PID: 4043 (sudo)
     CGroup: /system.slice/serverweb.service
             ‣ 4043 /usr/bin/sudo /usr/bin/python3 -m http.server 80
  
  Oct 07 08:41:49 b2-tp3-systemd systemd[1]: Starting Unite de service qui lance serveur web...
  Oct 07 08:41:49 b2-tp3-systemd sudo[4004]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --add-port=80/tcp --permanent
  Oct 07 08:41:50 b2-tp3-systemd sudo[4010]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --reload
  Oct 07 08:41:50 b2-tp3-systemd sudo[4043]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/python3 -m http.server 80
  Oct 07 08:41:50 b2-tp3-systemd sudo[4044]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --remove-port=80/tcp --permanent
  Oct 07 08:41:50 b2-tp3-systemd sudo[4054]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --reload
  Oct 07 08:41:51 b2-tp3-systemd systemd[1]: Started Unite de service qui lance serveur web.
  Hint: Some lines were ellipsized, use -l to show in full.
  ```

- faites en sorte que le service s'allume au démarrage de la machine

  ```bash
  [vagrant@b2-tp3-systemd system]$ sudo systemctl enable serverweb.service 
  [vagrant@b2-tp3-systemd system]$ sudo systemctl status serverweb.service 
  ● serverweb.service - Unite de service qui lance serveur web
     Loaded: loaded (/etc/systemd/system/serverweb.service; enabled; vendor preset: disabled)
     Active: active (running) since Wed 2020-10-07 10:07:55 UTC; 14s ago
   Main PID: 4596 (sudo)
     CGroup: /system.slice/serverweb.service
             ‣ 4596 /usr/bin/sudo /usr/bin/python3 -m http.server 80
  
  Oct 07 10:07:54 b2-tp3-systemd systemd[1]: Starting Unite de service qui lance serveur web...
  Oct 07 10:07:54 b2-tp3-systemd sudo[4558]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --add-port=80/tcp --permanent
  Oct 07 10:07:54 b2-tp3-systemd sudo[4564]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --reload
  Oct 07 10:07:55 b2-tp3-systemd sudo[4596]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/python3 -m http.server 80
  Oct 07 10:07:55 b2-tp3-systemd sudo[4597]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --remove-port=80/tcp --permanent
  Oct 07 10:07:55 b2-tp3-systemd sudo[4608]:      web : TTY=unknown ; PWD=/ ; USER=root ; COMMAND=/usr/bin/firewall-cmd --reload
  Oct 07 10:07:55 b2-tp3-systemd systemd[1]: Started Unite de service qui lance serveur web.
  Hint: Some lines were ellipsized, use -l to show in full.
  ```

  

- prouver que le serveur web est bien fonctionnel

  ```bash
  [vagrant@b2-tp3-systemd system]$ curl localhost:80
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
  <html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Directory listing for /</title>
  </head>
  <body>
  <h1>Directory listing for /</h1>
  <hr>
  <ul>
  <li><a href="bin/">bin@</a></li>
  <li><a href="boot/">boot/</a></li>
  <li><a href="dev/">dev/</a></li>
  <li><a href="etc/">etc/</a></li>
  <li><a href="home/">home/</a></li>
  <li><a href="lib/">lib@</a></li>
  <li><a href="lib64/">lib64@</a></li>
  <li><a href="media/">media/</a></li>
  <li><a href="mnt/">mnt/</a></li>
  <li><a href="opt/">opt/</a></li>
  <li><a href="proc/">proc/</a></li>
  <li><a href="root/">root/</a></li>
  <li><a href="run/">run/</a></li>
  <li><a href="sbin/">sbin@</a></li>
  <li><a href="srv/">srv/</a></li>
  <li><a href="swapfile">swapfile</a></li>
  <li><a href="sys/">sys/</a></li>
  <li><a href="tmp/">tmp/</a></li>
  <li><a href="usr/">usr/</a></li>
  <li><a href="var/">var/</a></li>
  </ul>
  <hr>
  </body>
  </html>
  ```

  

> N'oubliez pas de tester votre service : le lancer avec `systemctl start <SERVICE>` et vérifier que votre serveur web fonctionne avec un navigateur ou un `curl` par exemple.

### B. Sauvegarde

Ici on va réutiliser votre script de sauvegarde du [TP1]() que vous avez *bien évidemment* gardé.

🌞 Créez une unité de service qui déclenche une sauvegarde avec votre script

- le script doit se lancer sous l'identité d'un utilisateur dédié

- le service doit utiliser un PID file

- le service doit posséder une description

- vous éclaterez votre script en trois scripts :

  - un script qui se lance AVANT la sauvegarde, qui effectue les tests

  - script de sauvegarde

  - un script qui s'exécute APRES la sauvegarde, et qui effectue la rotation (ne garder que les 7 sauvegardes les plus récentes)

  - une fois fait, utilisez les clauses `ExecStartPre`, `ExecStart` et `ExecStartPost` pour les lancer au bon moment

    ```bash
    [vagrant@b2-tp3-systemd system]$ cat backupweb.service 
    [Unit]
    Description=Unite de service qui lance une backup de serveur web
    
    [Service]
    User=backup
    Type=oneshot
    PIDFile=/opt/pid/backup_pid.pid
    Environment="ARG=/srv/site1"
    
    ExecStartPre=/opt/pre_backup.sh ${ARG}
    ExecStart=/opt/backup.sh ${ARG}
    ExecStopPost=/opt/post_backup.sh ${ARG}
    
    [Install]
    WantedBy=multi-user.target
    
    
    
    
    Unit backupweb.service has begun starting up.
    Oct 07 15:06:01 b2-tp3-systemd backup.sh[2947]: [Oct 07 15:06:01] [INFO] Starting backup.
    Oct 07 15:06:01 b2-tp3-systemd backup.sh[2947]: [Oct 07 15:06:01] [INFO] Success. Backup site1_201007_150601.tar.gz has been saved to /opt/backup/.
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: lala
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: [Oct 07 15:06:01] [INFO] This script only keep the 8 most recent backups for a given directory.
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: [Oct 07 15:06:01] [INFO] Success. Backup site1_201007_145552.tar.gz has been removed from /opt/ba
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: lolo
    Oct 07 15:06:01 b2-tp3-systemd sudo[2935]: pam_unix(sudo:session): session closed for user root
    Oct 07 15:06:01 b2-tp3-systemd systemd[1]: Started Unite de service qui lance une backup de serveur web.
    -- Subject: Unit backupweb.service has finished start-up
    -- Defined-By: systemd
    -- Support: http://lists.freedesktop.org/mailman/listinfo/systemd-devel
    -- 
    -- Unit backupweb.service has finished starting up.
    -- 
    -- The start-up result is done.
    Oct 07 15:06:01 b2-tp3-systemd polkitd[377]: Unregistered Authentication Agent for unix-process:2937:344978 (system bus name :1.191, object path /org/
    Oct 07 15:06:05 b2-tp3-systemd sudo[2969]:   backup : TTY=pts/0 ; PWD=/opt ; USER=root ; COMMAND=/bin/systemctl status backupweb
    Oct 07 15:06:05 b2-tp3-systemd sudo[2969]: pam_unix(sudo:session): session opened for user root by vagrant(uid=0)
    Oct 07 15:06:05 b2-tp3-systemd sudo[2969]: pam_unix(sudo:session): session closed for user root
    [backup@b2-tp3-systemd opt]$ sudo systemctl status backupweb
    ● backupweb.service - Unite de service qui lance une backup de serveur web
       Loaded: loaded (/etc/systemd/system/backupweb.service; disabled; vendor preset: disabled)
       Active: inactive (dead)
    
    Oct 07 15:05:05 b2-tp3-systemd systemd[1]: Starting Unite de service qui lance une backup de serveur web...
    Oct 07 15:05:05 b2-tp3-systemd backup.sh[2887]: [Oct 07 15:05:05] [INFO] Starting backup.
    Oct 07 15:05:05 b2-tp3-systemd post_backup.sh[2896]: lala
    Oct 07 15:05:05 b2-tp3-systemd post_backup.sh[2896]: [Oct 07 15:05:05] [INFO] This script only keep the 8 most recent backups for a given directory.
    Oct 07 15:05:05 b2-tp3-systemd systemd[1]: Started Unite de service qui lance une backup de serveur web.
    Oct 07 15:06:01 b2-tp3-systemd systemd[1]: Starting Unite de service qui lance une backup de serveur web...
    Oct 07 15:06:01 b2-tp3-systemd backup.sh[2947]: [Oct 07 15:06:01] [INFO] Starting backup.
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: lala
    Oct 07 15:06:01 b2-tp3-systemd post_backup.sh[2956]: [Oct 07 15:06:01] [INFO] This script only keep the 8 most recent backups for a given directory.
    Oct 07 15:06:01 b2-tp3-systemd systemd[1]: Started Unite de service qui lance une backup de serveur web.
    ```

    🌞 Ecrire un fichier `.timer` systemd

    ```bash
    [backup@b2-tp3-systemd system]$ cat backupweb.timer 
    [Unit]
    Description=Execute backup every hour relative to when the machine was booted up
    
    [Timer]
    OnCalendar=*-*-* *:00:00
    
    [Install]
    WantedBy=multi-user.target
    
    
    [backup@b2-tp3-systemd system]$ sudo systemctl is-enabled backupweb.timer
    enabled
    [backup@b2-tp3-systemd system]$ sudo systemctl start backupweb.timer
    [backup@b2-tp3-systemd system]$ systemctl status backupweb.timer
    ● backupweb.timer - Execute backup every hour relative to when the machine was booted up
       Loaded: loaded (/etc/systemd/system/backupweb.timer; enabled; vendor preset: disabled)
       Active: active (waiting) since Wed 2020-10-07 15:27:19 UTC; 8s ago
    
    Oct 07 15:27:19 b2-tp3-systemd systemd[1]: Started Execute backup every hour relat...p.
    Hint: Some lines were ellipsized, use -l to show in full.
    ```

    

🐙 Améliorer la sécurité du service de sauvegarde

```
# Commande permettant de mettre en évidence des faiblesses de sécurité au sein d'un service donné
$ systemd-analyze security <SERVICE>
```

- **NB** : la version de systemd livré avec CentOS 7 est trop vieille pour cette feature, il vous CentOS 8 (ou un autre OS)
- mettre en place des mesures de sécurité pour avoir un score inférieur à 7

# II. Autres features

**Pour cette section, il sera nécessaire d'utiliser une version plus récente de systemd**. Vous devrez donc changer de box Vagrant, et utiliser une box possédant une version plus récente (par exemple une box CentOS8 ou une box Fedora récente).

## 1. Gestion de boot

🌞 Utilisez `systemd-analyze plot` pour récupérer une diagramme du boot, au format SVG

- il est possible de rediriger l'output de cette commande pour créer un fichier .svg

  - un `.svg` ça peut se lire avec un navigateur

- déterminer les 3 **services** les plus lents à démarrer

  ```bash
  [vagrant@centos8 ~]$ systemd-analyze plot > systemd_analyse.svg
  [vagrant@centos8 ~]$ ls
  systemd_analyse.svg
  ```

  

## 2. Gestion de l'heure

🌞 Utilisez la commande `timedatectl`

```bash
[vagrant@centos8 ~]$ timedatectl
               Local time: Thu 2020-10-08 20:58:21 UTC
           Universal time: Thu 2020-10-08 20:58:21 UTC
                 RTC time: Thu 2020-10-08 20:57:38
                Time zone: UTC (UTC, +0000)
System clock synchronized: no
              NTP service: active
          RTC in local TZ: yes

Warning: The system is configured to read the RTC time in the local time zone.
         This mode cannot be fully supported. It will create various problems
         with time zone changes and daylight saving time adjustments. The RTC
         time is never updated, it relies on external facilities to maintain it.
         If at all possible, use RTC in UTC by calling
         'timedatectl set-local-rtc 0'.
```



- déterminer votre fuseau horaire

  ```bash
  [vagrant@centos8 ~]$ timedatectl | grep Time
                  Time zone: UTC (UTC, +0000)
  ```

  

- déterminer si vous êtes synchronisés avec un serveur NTP

  ```bash
  [vagrant@centos8 ~]$ timedatectl | grep NTP
                NTP service: active
  ```

  

- changer le fuseau horaire

  ```bash
  [vagrant@centos8 ~]$ timedatectl list-timezones | grep Paris
  Europe/Paris
  [vagrant@centos8 ~]$ timedatectl set-timezone Europe/Paris
  ==== AUTHENTICATING FOR org.freedesktop.timedate1.set-timezone ====
  Authentication is required to set the system timezone.
  Authenticating as: root
  Password: 
  polkit-agent-helper-1: pam_authenticate failed: Authentication failure
  ==== AUTHENTICATION FAILED ====
  Failed to set time zone: Not authorized
  [vagrant@centos8 ~]$ sudo !!
  sudo timedatectl set-timezone Europe/Paris
  [vagrant@centos8 ~]$ timedatectl | grep Time
                  Time zone: Europe/Paris (CEST, +0200)
  ```

  

## 3. Gestion des noms et de la résolution de noms

🌞 Utilisez `hostnamectl`

```bash
[vagrant@centos8 ~]$ hostnamectl
   Static hostname: centos8.localdomain
         Icon name: computer-vm
           Chassis: vm
        Machine ID: 5532b3992b684b8ea30a5846cda86f36
           Boot ID: ee3846651985404ea5faeec8256fc25d
    Virtualization: oracle
  Operating System: CentOS Linux 8 (Core)
       CPE OS Name: cpe:/o:centos:centos:8
            Kernel: Linux 4.18.0-193.19.1.el8_2.x86_64
      Architecture: x86-64
```



- déterminer votre hostname actuel

  ```bash
  [vagrant@centos8 ~]$ hostnamectl | grep hostname
     Static hostname: centos8.localdomain
  ```

  

- changer votre hostname

  ```bash
  [vagrant@centos8 ~]$ sudo hostnamectl set-hostname leadu
  [vagrant@centos8 ~]$ hostnamectl | grep hostname
     Static hostname: leadu
  ```

  

# Structure du dépôt attendu

```
[it4@nowhere]$ tree tp3/
tp3/
├── README.md
├── scripts/
├── systemd/
│   ├── conf/
│   └── units/
└── Vagrantfile
```

- `scripts/` contient (si besoin) les scripts lancés par le Vagrantfile au boot des VMs
- `conf/` contient (si besoin) les fichiers de configuration relatifs à systemd
- `units/` contient les fichiers d'unités systemd