# TP1 : Déploiement classique

Le but de ce TP est d'effectuer le déploiement de services réseau assez classiques, ainsi que réaliser une configuration système élémentaire (réseau, stockage, utilisateurs, etc.).

Au menu :

- partitionnement de disque
- gestion d'utilisateurs et de permissions
- gestion de firewall
- installation et configuration de services
  - serveur web
  - backup
  - monitoring + alerting

- [0. Prérequis](#0-prérequis)
- [I. Setup serveur Web](#i-setup-serveur-web)
- [II. Script de sauvegarde](#ii-script-de-sauvegarde)
- [III. Monitoring, alerting](#iii-monitoring-alerting)

# 0. Prérequis

🌞 **Setup de deux machines CentOS7 configurée de façon basique.**

- partitionnement

  - ajouter un deuxième disque de 5Go à la machine

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ lsblk
    NAME            MAJ:MIN RM  SIZE RO TYPE MOUNTPOINT
    sda               8:0    0    8G  0 disk 
    |-sda1            8:1    0    1G  0 part /boot
    `-sda2            8:2    0    7G  0 part 
      |-centos-root 253:0    0  6.2G  0 lvm  /
      `-centos-swap 253:1    0  820M  0 lvm  [SWAP]
    sr0              11:0    1 1024M  0 rom  
    [lea@localhost ~]$ packet_write_wait: Connection to 192.168.1.11 port 22: Broken pipe
    ➜  ~ ssh lea@192.168.1.11
    lea@192.168.1.11's password: 
    Last login: Wed Sep 23 16:55:56 2020
    -bash: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
    [lea@localhost ~]$ lsblk
    NAME            MAJ:MIN RM  SIZE RO TYPE MOUNTPOINT
    sda               8:0    0    8G  0 disk 
    |-sda1            8:1    0    1G  0 part /boot
    `-sda2            8:2    0    7G  0 part 
      |-centos-root 253:0    0  6.2G  0 lvm  /
      `-centos-swap 253:1    0  820M  0 lvm  [SWAP]
    sdb               8:16   0  
    
    
    
    MACHINE 2 :
    [lea@node2 ~]$ lsblk
    NAME            MAJ:MIN RM  SIZE RO TYPE MOUNTPOINT
    sda               8:0    0    8G  0 disk 
    |-sda1            8:1    0    1G  0 part /boot
    `-sda2            8:2    0    7G  0 part 
      |-centos-root 253:0    0  6.2G  0 lvm  /
      `-centos-swap 253:1    0  820M  0 lvm  [SWAP]
    sdb               8:16   0    5G  0 disk 
    |-data-site1    253:2    0    2G  0 lvm  /srv/site1
    `-data-site2    253:3    0    3G  0 lvm  /srv/site2
    sr0              11:0    1 1024M  0 rom  
    ```

    ```bash
    [lea@localhost ~]$ sudo pvcreate /dev/sdb
    [sudo] password for lea: 
      Physical volume "/dev/sdb" successfully created.
    ```

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ sudo pvs
      PV         VG     Fmt  Attr PSize  PFree
      /dev/sda2  centos lvm2 a--  <7.00g    0 
      /dev/sdb          lvm2 ---   5.00g 5.00g
    [lea@localhost ~]$ sudo pvdisplay
      --- Physical volume ---
      PV Name               /dev/sda2
      VG Name               centos
      PV Size               <7.00 GiB / not usable 3.00 MiB
      Allocatable           yes (but full)
      PE Size               4.00 MiB
      Total PE              1791
      Free PE               0
      Allocated PE          1791
      PV UUID               7vZhWh-uHnx-UbjS-cGfj-7jHk-9vO6-QuRJPL
       
      "/dev/sdb" is a new physical volume of "5.00 GiB"
      --- NEW Physical volume ---
      PV Name               /dev/sdb
      VG Name               
      PV Size               5.00 GiB
      Allocatable           NO
      PE Size               0   
      Total PE              0
      Free PE               0
      Allocated PE          0
      PV UUID               8fYX02-ir1u-m3UL-1sBn-Edgw-pQlC-Laoc6y
      
      
      
    MACHINE 2 : 
    [lea@node2 ~]$ sudo pvs
    [sudo] password for lea: 
      PV         VG     Fmt  Attr PSize  PFree
      /dev/sda2  centos lvm2 a--  <7.00g    0 
      /dev/sdb   data   lvm2 a--  <5.00g    0 
    [lea@node2 ~]$ sudo pvdisplay
      --- Physical volume ---
      PV Name               /dev/sda2
      VG Name               centos
      PV Size               <7.00 GiB / not usable 3.00 MiB
      Allocatable           yes (but full)
      PE Size               4.00 MiB
      Total PE              1791
      Free PE               0
      Allocated PE          1791
      PV UUID               7vZhWh-uHnx-UbjS-cGfj-7jHk-9vO6-QuRJPL
       
      --- Physical volume ---
      PV Name               /dev/sdb
      VG Name               data
      PV Size               5.00 GiB / not usable 4.00 MiB
      Allocatable           yes (but full)
      PE Size               4.00 MiB
      Total PE              1279
      Free PE               0
      Allocated PE          1279
      PV UUID               nuuBqH-21cW-m4ea-VIkH-f5YL-0Sd6-IN02av
    ```

    

  - partitionner le nouveau disque avec LVM

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ sudo vgcreate data /dev/sdb
      Volume group "data" successfully created
    [lea@localhost ~]$ sudo vgs
      VG     #PV #LV #SN Attr   VSize  VFree 
      centos   1   2   0 wz--n- <7.00g     0 
      data     1   0   0 wz--n- <5.00g <5.00g
    [lea@localhost ~]$ sudo vgdisplay
      --- Volume group ---
      VG Name               centos
      System ID             
      Format                lvm2
      Metadata Areas        1
      Metadata Sequence No  3
      VG Access             read/write
      VG Status             resizable
      MAX LV                0
      Cur LV                2
      Open LV               2
      Max PV                0
      Cur PV                1
      Act PV                1
      VG Size               <7.00 GiB
      PE Size               4.00 MiB
      Total PE              1791
      Alloc PE / Size       1791 / <7.00 GiB
      Free  PE / Size       0 / 0   
      VG UUID               oXNWAe-4n3q-Erhp-KKog-G7xw-epGW-YIpbb0
       
      --- Volume group ---
      VG Name               data
      System ID             
      Format                lvm2
      Metadata Areas        1
      Metadata Sequence No  1
      VG Access             read/write
      VG Status             resizable
      MAX LV                0
      Cur LV                0
      Open LV               0
      Max PV                0
      Cur PV                1
      Act PV                1
      VG Size               <5.00 GiB
      PE Size               4.00 MiB
      Total PE              1279
      Alloc PE / Size       0 / 0   
      Free  PE / Size       1279 / <5.00 GiB
      VG UUID               NmbwjU-yr54-yAaJ-qj3f-ukY6-0RFi-ZISKFw
    
    
    
    MACHINE 2 :
    [lea@node2 ~]$ sudo vgs
      VG     #PV #LV #SN Attr   VSize  VFree
      centos   1   2   0 wz--n- <7.00g    0 
      data     1   2   0 wz--n- <5.00g    0 
    [lea@node2 ~]$ sudo vgdisplay
      --- Volume group ---
      VG Name               centos
      System ID             
      Format                lvm2
      Metadata Areas        1
      Metadata Sequence No  3
      VG Access             read/write
      VG Status             resizable
      MAX LV                0
      Cur LV                2
      Open LV               2
      Max PV                0
      Cur PV                1
      Act PV                1
      VG Size               <7.00 GiB
      PE Size               4.00 MiB
      Total PE              1791
      Alloc PE / Size       1791 / <7.00 GiB
      Free  PE / Size       0 / 0   
      VG UUID               oXNWAe-4n3q-Erhp-KKog-G7xw-epGW-YIpbb0
       
      --- Volume group ---
      VG Name               data
      System ID             
      Format                lvm2
      Metadata Areas        1
      Metadata Sequence No  3
      VG Access             read/write
      VG Status             resizable
      MAX LV                0
      Cur LV                2
      Open LV               2
      Max PV                0
      Cur PV                1
      Act PV                1
      VG Size               <5.00 GiB
      PE Size               4.00 MiB
      Total PE              1279
      Alloc PE / Size       1279 / <5.00 GiB
      Free  PE / Size       0 / 0   
      VG UUID               ws1yIm-aeKr-IICg-G2zb-ZR56-X60N-2LeuZz
    ```

    - deux partitions, une de 2Go, une de 3Go

    - la partition de 2Go sera montée sur `/srv/site1`

    - la partition de 3Go sera montée sur `/srv/site2`

      ```bash
      MACHINE 1 :
      [lea@localhost ~]$ sudo lvcreate -L 2G data -n site1
        Logical volume "site1" created.
      [lea@localhost ~]$ sudo lvcreate -l 100%FREE data -n site2
        Logical volume "site2" created.
      [lea@localhost ~]$ sudo lvs
        LV    VG     Attr       LSize   Pool Origin Data%  Meta%  Move Log Cpy%Sync Convert
        root  centos -wi-ao----  <6.20g                                                    
        swap  centos -wi-ao---- 820.00m                                                    
        site1 data   -wi-a-----   2.00g                                                    
        site2 data   -wi-a-----  <3.00g                                                    
      [lea@localhost ~]$ sudo lvdisplay
        --- Logical volume ---
        LV Path                /dev/centos/swap
        LV Name                swap
        VG Name                centos
        LV UUID                ZDW3ew-cIQ4-tGaN-K1oX-YUKV-9soW-not1Mr
        LV Write Access        read/write
        LV Creation host, time localhost, 2020-09-23 16:34:48 +0200
        LV Status              available
        # open                 2
        LV Size                820.00 MiB
        Current LE             205
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:1
         
        --- Logical volume ---
        LV Path                /dev/centos/root
        LV Name                root
        VG Name                centos
        LV UUID                ZvruX1-qvTP-dFYt-TTIK-60ss-Ol5j-XoAUVu
        LV Write Access        read/write
        LV Creation host, time localhost, 2020-09-23 16:34:49 +0200
        LV Status              available
        # open                 1
        LV Size                <6.20 GiB
        Current LE             1586
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:0
         
        --- Logical volume ---
        LV Path                /dev/data/site1
        LV Name                site1
        VG Name                data
        LV UUID                saPyqb-p960-pDNW-8oQI-vz8r-bR9n-rkgRPh
        LV Write Access        read/write
        LV Creation host, time localhost.localdomain, 2020-09-23 17:25:03 +0200
        LV Status              available
        # open                 0
        LV Size                2.00 GiB
        Current LE             512
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:2
         
        --- Logical volume ---
        LV Path                /dev/data/site2
        LV Name                site2
        VG Name                data
        LV UUID                dJFV9o-ecC5-tkEa-oE7C-Fzw0-V6Au-Q0uxuh
        LV Write Access        read/write
        LV Creation host, time localhost.localdomain, 2020-09-23 17:25:18 +0200
        LV Status              available
        # open                 0
        LV Size                <3.00 GiB
        Current LE             767
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:3
         
      
      
      MACHINE 2 :
      [lea@node2 ~]$ sudo lvs
        LV    VG     Attr       LSize   Pool Origin Data%  Meta%  Move Log Cpy%Sync Convert
        root  centos -wi-ao----  <6.20g                                                    
        swap  centos -wi-ao---- 820.00m                                                    
        site1 data   -wi-ao----   2.00g                                                    
        site2 data   -wi-ao----  <3.00g                                                    
      [lea@node2 ~]$ sudo lvdisplay
        --- Logical volume ---
        LV Path                /dev/centos/swap
        LV Name                swap
        VG Name                centos
        LV UUID                ZDW3ew-cIQ4-tGaN-K1oX-YUKV-9soW-not1Mr
        LV Write Access        read/write
        LV Creation host, time localhost, 2020-09-23 16:34:48 +0200
        LV Status              available
        # open                 2
        LV Size                820.00 MiB
        Current LE             205
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:1
         
        --- Logical volume ---
        LV Path                /dev/centos/root
        LV Name                root
        VG Name                centos
        LV UUID                ZvruX1-qvTP-dFYt-TTIK-60ss-Ol5j-XoAUVu
        LV Write Access        read/write
        LV Creation host, time localhost, 2020-09-23 16:34:49 +0200
        LV Status              available
        # open                 1
        LV Size                <6.20 GiB
        Current LE             1586
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:0
         
        --- Logical volume ---
        LV Path                /dev/data/site1
        LV Name                site1
        VG Name                data
        LV UUID                saPyqb-p960-pDNW-8oQI-vz8r-bR9n-rkgRPh
        LV Write Access        read/write
        LV Creation host, time localhost.localdomain, 2020-09-23 17:25:03 +0200
        LV Status              available
        # open                 1
        LV Size                2.00 GiB
        Current LE             512
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:2
         
        --- Logical volume ---
        LV Path                /dev/data/site2
        LV Name                site2
        VG Name                data
        LV UUID                dJFV9o-ecC5-tkEa-oE7C-Fzw0-V6Au-Q0uxuh
        LV Write Access        read/write
        LV Creation host, time localhost.localdomain, 2020-09-23 17:25:18 +0200
        LV Status              available
        # open                 1
        LV Size                <3.00 GiB
        Current LE             767
        Segments               1
        Allocation             inherit
        Read ahead sectors     auto
        - currently set to     8192
        Block device           253:3
      ```

      

  - les partitions doivent être montées automatiquement au démarrage

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ mkfs -t ext4 /dev/data/site1
    mke2fs 1.42.9 (28-Dec-2013)
    mkfs.ext4: Permission denied while trying to determine filesystem size
    [lea@localhost ~]$ sudo!!
    sudomkfs -t ext4 /dev/data/site1
    -bash: sudomkfs: command not found
    [lea@localhost ~]$ sudo sudomkfs -t ext4 /dev/data/site1
    [sudo] password for lea: 
    sudo: sudomkfs: command not found
    [lea@localhost ~]$ sudo mkfs -t ext4 /dev/data/site1
    mke2fs 1.42.9 (28-Dec-2013)
    Filesystem label=
    OS type: Linux
    Block size=4096 (log=2)
    Fragment size=4096 (log=2)
    Stride=0 blocks, Stripe width=0 blocks
    131072 inodes, 524288 blocks
    26214 blocks (5.00%) reserved for the super user
    First data block=0
    Maximum filesystem blocks=536870912
    16 block groups
    32768 blocks per group, 32768 fragments per group
    8192 inodes per group
    Superblock backups stored on blocks: 
    	32768, 98304, 163840, 229376, 294912
    
    Allocating group tables: done                            
    Writing inode tables: done                            
    Creating journal (16384 blocks): done
    Writing superblocks and filesystem accounting information: done 
    
    [lea@localhost ~]$ sudo mkfs -t ext4 /dev/data/site2
    mke2fs 1.42.9 (28-Dec-2013)
    Filesystem label=
    OS type: Linux
    Block size=4096 (log=2)
    Fragment size=4096 (log=2)
    Stride=0 blocks, Stripe width=0 blocks
    196608 inodes, 785408 blocks
    39270 blocks (5.00%) reserved for the super user
    First data block=0
    Maximum filesystem blocks=805306368
    24 block groups
    32768 blocks per group, 32768 fragments per group
    8192 inodes per group
    Superblock backups stored on blocks: 
    	32768, 98304, 163840, 229376, 294912
    
    Allocating group tables: done                            
    Writing inode tables: done                            
    Creating journal (16384 blocks): done
    Writing superblocks and filesystem accounting information: done 
    
    [lea@localhost ~]$ mkdir /srv/site1
    mkdir: cannot create directory '/srv/site1': Permission denied
    [lea@localhost ~]$ sudo !!
    sudo mkdir /srv/site1
    [lea@localhost ~]$ sudo mkdir /srv/site2
    
    [lea@localhost ~]$ sudo mount /dev/data/site1 /srv/site1
    [lea@localhost ~]$ sudo mount /dev/data/site2 /srv/site2
    
    [lea@localhost ~]$ mount
    sysfs on /sys type sysfs (rw,nosuid,nodev,noexec,relatime,seclabel)
    proc on /proc type proc (rw,nosuid,nodev,noexec,relatime)
    devtmpfs on /dev type devtmpfs (rw,nosuid,seclabel,size=495404k,nr_inodes=123851,mode=755)
    securityfs on /sys/kernel/security type securityfs (rw,nosuid,nodev,noexec,relatime)
    tmpfs on /dev/shm type tmpfs (rw,nosuid,nodev,seclabel)
    devpts on /dev/pts type devpts (rw,nosuid,noexec,relatime,seclabel,gid=5,mode=620,ptmxmode=000)
    tmpfs on /run type tmpfs (rw,nosuid,nodev,seclabel,mode=755)
    tmpfs on /sys/fs/cgroup type tmpfs (ro,nosuid,nodev,noexec,seclabel,mode=755)
    cgroup on /sys/fs/cgroup/systemd type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,xattr,release_agent=/usr/lib/systemd/systemd-cgroups-agent,name=systemd)
    pstore on /sys/fs/pstore type pstore (rw,nosuid,nodev,noexec,relatime)
    cgroup on /sys/fs/cgroup/net_cls,net_prio type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,net_prio,net_cls)
    cgroup on /sys/fs/cgroup/cpu,cpuacct type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,cpuacct,cpu)
    cgroup on /sys/fs/cgroup/perf_event type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,perf_event)
    cgroup on /sys/fs/cgroup/freezer type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,freezer)
    cgroup on /sys/fs/cgroup/blkio type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,blkio)
    cgroup on /sys/fs/cgroup/memory type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,memory)
    cgroup on /sys/fs/cgroup/devices type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,devices)
    cgroup on /sys/fs/cgroup/pids type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,pids)
    cgroup on /sys/fs/cgroup/cpuset type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,cpuset)
    cgroup on /sys/fs/cgroup/hugetlb type cgroup (rw,nosuid,nodev,noexec,relatime,seclabel,hugetlb)
    configfs on /sys/kernel/config type configfs (rw,relatime)
    /dev/mapper/centos-root on / type xfs (rw,relatime,seclabel,attr2,inode64,noquota)
    selinuxfs on /sys/fs/selinux type selinuxfs (rw,relatime)
    systemd-1 on /proc/sys/fs/binfmt_misc type autofs (rw,relatime,fd=35,pgrp=1,timeout=0,minproto=5,maxproto=5,direct,pipe_ino=12331)
    mqueue on /dev/mqueue type mqueue (rw,relatime,seclabel)
    hugetlbfs on /dev/hugepages type hugetlbfs (rw,relatime,seclabel)
    debugfs on /sys/kernel/debug type debugfs (rw,relatime)
    /dev/sda1 on /boot type xfs (rw,relatime,seclabel,attr2,inode64,noquota)
    tmpfs on /run/user/1000 type tmpfs (rw,nosuid,nodev,relatime,seclabel,size=101484k,mode=700,uid=1000,gid=1000)
    tmpfs on /run/user/0 type tmpfs (rw,nosuid,nodev,relatime,seclabel,size=101484k,mode=700)
    /dev/mapper/data-site1 on /srv/site1 type ext4 (rw,relatime,seclabel,data=ordered)
    /dev/mapper/data-site2 on /srv/site2 type ext4 (rw,relatime,seclabel,data=ordered)
    
    [lea@localhost ~]$ df -h
    Filesystem               Size  Used Avail Use% Mounted on
    devtmpfs                 484M     0  484M   0% /dev
    tmpfs                    496M     0  496M   0% /dev/shm
    tmpfs                    496M  6.9M  489M   2% /run
    tmpfs                    496M     0  496M   0% /sys/fs/cgroup
    /dev/mapper/centos-root  6.2G  1.3G  5.0G  20% /
    /dev/sda1               1014M  137M  878M  14% /boot
    tmpfs                    100M     0  100M   0% /run/user/1000
    tmpfs                    100M     0  100M   0% /run/user/0
    /dev/mapper/data-site1   2.0G  6.0M  1.8G   1% /srv/site1
    /dev/mapper/data-site2   2.9G  9.0M  2.8G   1% /srv/site2
    
    [lea@localhost ~]$ sudo vi /etc/fstab
    [lea@localhost ~]$ cat /etc/fstab
    
    #
    # /etc/fstab
    # Created by anaconda on Wed Sep 23 16:34:49 2020
    #
    # Accessible filesystems, by reference, are maintained under '/dev/disk'
    # See man pages fstab(5), findfs(8), mount(8) and/or blkid(8) for more info
    #
    /dev/mapper/centos-root /                       xfs     defaults        0 0
    UUID=b70db18a-cbf6-4f7e-b6b5-1ebb4c46341a /boot                   xfs     defaults        0 0
    /dev/mapper/centos-swap swap                    swap    defaults        0 0
    /dev/data/site1 /srv/site1 ext4 defaults 0 0
    /dev/data/site2 /srv/site2 ext4 defaults 0 0
    [lea@node1 ~]$ 
    [lea@localhost ~]$ sudo umount /srv/site1
    [lea@localhost ~]$ sudo umount /srv/site2
    
    [lea@localhost ~]$ sudo mount -av
    /                        : ignored
    /boot                    : already mounted
    swap                     : ignored
    mount: /srv/site1 does not contain SELinux labels.
           You just mounted an file system that supports labels which does not
           contain labels, onto an SELinux box. It is likely that confined
           applications will generate AVC messages and not be allowed access to
           this file system.  For more details see restorecon(8) and mount(8).
    /srv/site1               : successfully mounted
    mount: /srv/site2 does not contain SELinux labels.
           You just mounted an file system that supports labels which does not
           contain labels, onto an SELinux box. It is likely that confined
           applications will generate AVC messages and not be allowed access to
           this file system.  For more details see restorecon(8) and mount(8).
    /srv/site2               : successfully mounted
    
    
    
    
    MACHINE 2 : 
    [lea@node2 ~]$ sudo umount /srv/site1
    [lea@node2 ~]$ sudo umount /srv/site2
    [lea@node2 ~]$ sudo mount -av
    /                        : ignored
    /boot                    : already mounted
    swap                     : ignored
    mount: /srv/site1 does not contain SELinux labels.
           You just mounted an file system that supports labels which does not
           contain labels, onto an SELinux box. It is likely that confined
           applications will generate AVC messages and not be allowed access to
           this file system.  For more details see restorecon(8) and mount(8).
    /srv/site1               : successfully mounted
    mount: /srv/site2 does not contain SELinux labels.
           You just mounted an file system that supports labels which does not
           contain labels, onto an SELinux box. It is likely that confined
           applications will generate AVC messages and not be allowed access to
           this file system.  For more details see restorecon(8) and mount(8).
    /srv/site2               : successfully mounted
    ```

    

- un accès internet

  ```bash
  MACHINE 1 :
  [lea@localhost ~]$ dig google.com@8.8.8.8
  
  ; <<>> DiG 9.11.4-P2-RedHat-9.11.4-16.P2.el7_8.6 <<>> google.com@8.8.8.8
  ;; global options: +cmd
  ;; Got answer:
  ;; ->>HEADER<<- opcode: QUERY, status: NXDOMAIN, id: 48728
  ;; flags: qr rd ra; QUERY: 1, ANSWER: 0, AUTHORITY: 0, ADDITIONAL: 1
  
  ;; OPT PSEUDOSECTION:
  ; EDNS: version: 0, flags:; udp: 4096
  ;; QUESTION SECTION:
  ;google.com\@8.8.8.8.		IN	A
  
  ;; Query time: 17 msec
  ;; SERVER: 10.33.10.148#53(10.33.10.148)
  ;; WHEN: Wed Sep 23 17:55:13 CEST 2020
  ;; MSG SIZE  rcvd: 47
  
  
  
  MACHINE 2 :
  [lea@node2 ~]$ ping 8.8.8.8
  PING 8.8.8.8 (8.8.8.8) 56(84) bytes of data.
  64 bytes from 8.8.8.8: icmp_seq=1 ttl=63 time=47.2 ms
  64 bytes from 8.8.8.8: icmp_seq=2 ttl=63 time=51.6 ms
  64 bytes from 8.8.8.8: icmp_seq=3 ttl=63 time=55.9 ms
  ^C
  --- 8.8.8.8 ping statistics ---
  3 packets transmitted, 3 received, 0% packet loss, time 2005ms
  rtt min/avg/max/mdev = 47.214/51.582/55.922/3.555 ms
  ```

  

  - carte réseau dédiée

    ```bash
    MACHINE 1 :
    2: enp0s3: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
        link/ether 08:00:27:14:63:a0 brd ff:ff:ff:ff:ff:ff
        inet 10.0.2.15/24 brd 10.0.2.255 scope global noprefixroute dynamic enp0s3
           valid_lft 83277sec preferred_lft 83277sec
        inet6 fe80::e311:d99f:66ee:ae9f/64 scope link noprefixroute 
           valid_lft forever preferred_lft forever
           
           
           
           
    MACHINE 2 :
    2: enp0s3: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
        link/ether 08:00:27:8b:a8:78 brd ff:ff:ff:ff:ff:ff
        inet 10.0.2.15/24 brd 10.0.2.255 scope global noprefixroute dynamic enp0s3
           valid_lft 85870sec preferred_lft 85870sec
        inet6 fe80::e311:d99f:66ee:ae9f/64 scope link noprefixroute 
           valid_lft forever preferred_lft forever
    ```

    

  - route par défaut

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ ip route show
    default via 10.0.2.2 dev enp0s3 proto dhcp metric 100 
    10.0.2.0/24 dev enp0s3 proto kernel scope link src 10.0.2.15 metric 100 
    192.168.1.0/24 dev enp0s8 proto kernel scope link src 192.168.1.11 metric 101 
    
    
    
    MACHINE 2 :
    [lea@node2 ~]$ ip r s
    default via 10.0.2.2 dev enp0s3 proto dhcp metric 100 
    10.0.2.0/24 dev enp0s3 proto kernel scope link src 10.0.2.15 metric 100 
    192.168.1.0/24 dev enp0s8 proto kernel scope link src 192.168.1.12 metric 101 
    ```

    

- un accès à un réseau local (les deux machines peuvent se PING

  ```bash
  [lea@node1 ~]$ ping 192.168.1.12
  PING 192.168.1.12 (192.168.1.12) 56(84) bytes of data.
  64 bytes from 192.168.1.12: icmp_seq=1 ttl=64 time=1.05 ms
  64 bytes from 192.168.1.12: icmp_seq=2 ttl=64 time=0.615 ms
  64 bytes from 192.168.1.12: icmp_seq=3 ttl=64 time=0.386 ms
  ^C
  --- 192.168.1.12 ping statistics ---
  3 packets transmitted, 3 received, 0% packet loss, time 2002ms
  rtt min/avg/max/mdev = 0.386/0.684/1.053/0.278 ms
  
  
  
  [lea@node2 ~]$ ping 192.168.1.11
  PING 192.168.1.11 (192.168.1.11) 56(84) bytes of data.
  64 bytes from 192.168.1.11: icmp_seq=1 ttl=64 time=0.711 ms
  64 bytes from 192.168.1.11: icmp_seq=2 ttl=64 time=0.737 ms
  ^C
  --- 192.168.1.11 ping statistics ---
  2 packets transmitted, 2 received, 0% packet loss, time 1006ms
  rtt min/avg/max/mdev = 0.711/0.724/0.737/0.013 ms
  ```

  - carte réseau dédiée

    ```bash
    MACHINE 1 :
    3: enp0s8: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
        link/ether 08:00:27:aa:83:a1 brd ff:ff:ff:ff:ff:ff
        inet 192.168.1.11/24 brd 192.168.1.255 scope global noprefixroute enp0s8
           valid_lft forever preferred_lft forever
        inet6 fe80::a00:27ff:feaa:83a1/64 scope link 
           valid_lft forever preferred_lft forever
           
           
           
       
    MACHINE 2 :
    3: enp0s8: <BROADCAST,MULTICAST,UP,LOWER_UP> mtu 1500 qdisc pfifo_fast state UP group default qlen 1000
        link/ether 08:00:27:1b:9c:9e brd ff:ff:ff:ff:ff:ff
        inet 192.168.1.12/24 brd 192.168.1.255 scope global noprefixroute enp0s8
           valid_lft forever preferred_lft forever
        inet6 fe80::a00:27ff:fe1b:9c9e/64 scope link 
           valid_lft forever preferred_lft forever
    ```

    

  - route locale

    ```bash
    MACHINE 1 :
    [lea@localhost ~]$ ip r s
    default via 10.0.2.2 dev enp0s3 proto dhcp metric 100 
    10.0.2.0/24 dev enp0s3 proto kernel scope link src 10.0.2.15 metric 100 
    192.168.1.0/24 dev enp0s8 proto kernel scope link src 192.168.1.11 metric 101 
    
    
    
    MACHINE 2 :
    [lea@node2 ~]$ ip r s
    default via 10.0.2.2 dev enp0s3 proto dhcp metric 100 
    10.0.2.0/24 dev enp0s3 proto kernel scope link src 10.0.2.15 metric 100 
    192.168.1.0/24 dev enp0s8 proto kernel scope link src 192.168.1.12 metric 101 
    ```

    

- les machines doivent avoir un nom

  - `/etc/hostname`

  - commande `hostname`)

    ```bash
    Machine 1 :
    
    [lea@localhost ~]$ hostname
    node1.tp1.b2
    [lea@localhost ~]$ cat /etc/hostname 
    node1.tp1.b2
    
    
    Machine 2 :
    [lea@node2 ~]$ hostname
    node2.tp1.b2
    [lea@node2 ~]$ cat /etc/hostname
    node2.tp1.b2
    ```

    

- les machines doivent pouvoir se joindre par leurs noms respectifs

  - fichier `/etc/hosts`

    ```bash
    Machine 1 :
    [lea@node1 ~]$ cat /etc/hosts
    127.0.0.1   localhost localhost.localdomain localhost4 localhost4.localdomain4
    ::1         localhost localhost.localdomain localhost6 localhost6.localdomain6
    192.168.1.12 node2 node2.tp1.b2
    
    
    
    MACHINE 2 :
    [lea@node2 ~]$ cat /etc/hosts
    127.0.0.1   localhost localhost.localdomain localhost4 localhost4.localdomain4
    ::1         localhost localhost.localdomain localhost6 localhost6.localdomain6
    192.168.1.11 node1 node1.tp1.b2
    
    
    -----------------------------------------------------------------------------------------------------
    
    
    [lea@node1 ~]$ ping node2
    PING node2 (192.168.1.12) 56(84) bytes of data.
    64 bytes from node2 (192.168.1.12): icmp_seq=1 ttl=64 time=0.554 ms
    64 bytes from node2 (192.168.1.12): icmp_seq=2 ttl=64 time=0.979 ms
    64 bytes from node2 (192.168.1.12): icmp_seq=3 ttl=64 time=1.04 ms
    ^C
    --- node2 ping statistics ---
    3 packets transmitted, 3 received, 0% packet loss, time 2002ms
    rtt min/avg/max/mdev = 0.554/0.859/1.046/0.220 ms
    
    
    
    [lea@node2 ~]$ ping node1
    PING node1 (192.168.1.11) 56(84) bytes of data.
    64 bytes from node1 (192.168.1.11): icmp_seq=1 ttl=64 time=0.613 ms
    64 bytes from node1 (192.168.1.11): icmp_seq=2 ttl=64 time=0.851 ms
    64 bytes from node1 (192.168.1.11): icmp_seq=3 ttl=64 time=0.799 ms
    ^C
    --- node1 ping statistics ---
    3 packets transmitted, 3 received, 0% packet loss, time 2018ms
    rtt min/avg/max/mdev = 0.613/0.754/0.851/0.104 ms
    ```

- un utilisateur administrateur est créé sur les deux machines (il peut exécuter des commandes sudo en tant que root)

  - création d'un user

    ```bash
    [lea@node1 ~]$ sudo useradd admin
    [sudo] password for lea: 
    [lea@node1 ~]$ passwd admin
    passwd: Only root can specify a user name.
    [lea@node1 ~]$ sudo !!
    sudo passwd admin
    Changing password for user admin.
    New password: 
    BAD PASSWORD: The password is shorter than 8 characters
    Retype new password: 
    passwd: all authentication tokens updated successfully.
    ```

  - modification de la conf sudo

    ```bash
    [lea@node1 ~]$ sudo usermod -aG wheel admin
    [lea@node1 ~]$ groups admin
    admin : admin wheel
    ```

- vous n'utilisez QUE 

  ```
  ssh
  ```

   pour administrer les machines

  - création d'une paire de clés (sur VOTRE PC)

  - déposer la clé publique sur l'utilisateur de destination

    ```bash
    MACHINE 1 :
    ➜  ~ ssh-keygen
    Generating public/private rsa key pair.
    Enter file in which to save the key (/Users/leaduvigneau/.ssh/id_rsa): 
    /Users/leaduvigneau/.ssh/id_rsa already exists.
    Overwrite (y/n)? 
    ➜  ~ ssh-copy-id lea@192.168.1.11
    /usr/bin/ssh-copy-id: INFO: Source of key(s) to be installed: "/Users/leaduvigneau/.ssh/id_rsa.pub"
    /usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
    /usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
    lea@192.168.1.11's password: 
    /etc/profile.d/lang.sh: line 19: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
    
    Number of key(s) added:        1
    
    Now try logging into the machine, with:   "ssh 'lea@192.168.1.11'"
    and check to make sure that only the key(s) you wanted were added.
    
    ➜  ~ ssh lea@192.168.1.11 
    Last login: Thu Sep 24 08:59:02 2020 from 192.168.1.2
    -bash: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
    [lea@node1 ~]$ 
    
    
    
    
    ---------------------------------------------------------------------------------------------------------
    MACHINE 2 :
    ➜  ~ ssh-keygen                  
    Generating public/private rsa key pair.
    Enter file in which to save the key (/Users/leaduvigneau/.ssh/id_rsa): 
    /Users/leaduvigneau/.ssh/id_rsa already exists.
    Overwrite (y/n)? 
    ➜  ~ ssh-copy-id lea@192.168.1.12
    /usr/bin/ssh-copy-id: INFO: Source of key(s) to be installed: "/Users/leaduvigneau/.ssh/id_rsa.pub"
    /usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
    /usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
    lea@192.168.1.12's password: 
    Permission denied, please try again.
    lea@192.168.1.12's password: 
    /etc/profile.d/lang.sh: line 19: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
    
    Number of key(s) added:        1
    
    Now try logging into the machine, with:   "ssh 'lea@192.168.1.12'"
    and check to make sure that only the key(s) you wanted were added.
    
    ➜  ~ ssh lea@192.168.1.12        
    Last login: Thu Sep 24 08:59:31 2020 from 192.168.1.2
    -bash: warning: setlocale: LC_CTYPE: cannot change locale (UTF-8): No such file or directory
    [lea@node2 ~]$ 
    ```

- le pare-feu est configuré pour bloquer toutes les connexions exceptées celles qui sont nécessaires

  - commande `firewall-cmd` ou `iptables`
  
    ```bash
    MACHINE 1 :
    [lea@node1 ~]$ sudo firewall-cmd --list-all
    [sudo] password for lea: 
    public (active)
      target: default
      icmp-block-inversion: no
      interfaces: enp0s3 enp0s8
      sources: 
      services: dhcpv6-client ssh
      ports: 80/tcp 443/tcp
      protocols: 
      masquerade: no
      forward-ports: 
      source-ports: 
      icmp-blocks: 
      rich rules:
      
      
      
      MACHINE 2 :
    [lea@node2 ~]$ sudo firewall-cmd --list-all
    [sudo] password for lea: 
    public (active)
      target: default
      icmp-block-inversion: no
      interfaces: enp0s3 enp0s8
      sources: 
      services: dhcpv6-client ssh
      ports: 
      protocols: 
      masquerade: no
      forward-ports: 
      source-ports: 
      icmp-blocks: 
      rich rules: 
    ```
  
    

Pour le réseau des différentes machines :

| Name           | IP             |
| -------------- | -------------- |
| `node1.tp1.b2` | `192.168.1.11` |
| `node2.tp1.b2` | `192.168.1.12` |

# I. Setup serveur Web

🌞 Installer le serveur web NGINX sur `node1.tp1.b2` (avec une commande `yum install`).

```bash
MACHINE 1 / node1 :
[lea@node1 ~]$ sudo yum install epel-release
[sudo] password for lea: 
Failed to set locale, defaulting to C
Loaded plugins: fastestmirror
Loading mirror speeds from cached hostfile
 * base: mirror.in2p3.fr
 * extras: mirror.in2p3.fr
 * updates: mirror.in2p3.fr
Resolving Dependencies
--> Running transaction check
[...]
Installed:
  epel-release.noarch 0:7-11                                                                          

Complete!

[lea@node1 ~]$ sudo yum install nginx -y
Failed to set locale, defaulting to C
Loaded plugins: fastestmirror
Loading mirror speeds from cached hostfile
epel/x86_64/metalink                                                           |  30 kB  00:00:00     
 * base: mirror.in2p3.fr
 * epel: mirror.in2p3.fr
[...]
  perl-parent.noarch 1:0.225-244.el7                   perl-podlators.noarch 0:2.5.1-3.el7            
  perl-threads.x86_64 0:1.87-4.el7                     perl-threads-shared.x86_64 0:1.43-6.el7        

Complete!
```

🌞 Faites en sorte que :

- NGINX servent deux sites web, chacun possède un fichier unique `index.html`

- les sites web doivent se trouver dans 

  ```
  /srv/site1
  ```

   et 

  ```
  /srv/site2
  ```

  - les permissions sur ces dossiers doivent être le plus restrictif possible

    ```bash
    [admin@node1 site1]$ ls -la
    total 24
    drwxr-xr-x. 3 web  web   4096 Sep 24 10:21 .
    drwxr-xr-x. 4 root root    32 Sep 23 17:34 ..
    -r--------. 1 web  web     24 Sep 24 10:21 index.html
    drwx------. 2 web  web  16384 Sep 23 17:31 lost+found
    
    
    [admin@node1 site2]$ ls -la
    total 24
    drwxr-xr-x. 3 web  web   4096 Sep 24 10:40 .
    drwxr-xr-x. 4 root root    32 Sep 23 17:34 ..
    -r--------. 1 web  web     29 Sep 24 10:40 index.html
    drwx------. 2 web  web  16384 Sep 23 17:31 lost+found
    ```

    

  - ces dossiers doivent appartenir à un utilisateur et un groupe spécifique

    ```bash
    [admin@node1 site1]$ sudo useradd web
    [sudo] password for admin: 
    [admin@node1 site1]$ sudo passwd web
    Changing password for user web.
    New password: 
    BAD PASSWORD: The password is shorter than 8 characters
    Retype new password: 
    passwd: all authentication tokens updated successfully.
    [admin@node1 site1]$ groups web
    web : web
    
    
    --------------------------------------------------------------------------------------------------------
    [admin@node1 site1]$ sudo chown -R web:web /srv/site1/ 
    [admin@node1 site1]$ ls -la
    total 24
    drwxr-xr-x. 3 web  web   4096 Sep 24 10:21 .
    drwxr-xr-x. 4 root root    32 Sep 23 17:34 ..
    -rw-r--r--. 1 web  web     24 Sep 24 10:21 index.html
    drwx------. 2 web  web  16384 Sep 23 17:31 lost+found
    
    
    [admin@node1 site2]$ sudo chown -R web:web /srv/site2/ 
    [admin@node1 site2]$ ls -la
    total 24
    drwxr-xr-x. 3 web  web   4096 Sep 24 10:40 .
    drwxr-xr-x. 4 root root    32 Sep 23 17:34 ..
    -rw-r--r--. 1 web  web     29 Sep 24 10:40 index.html
    drwx------. 2 web  web  16384 Sep 23 17:31 lost+found
    ```

    

- NGINX doit utiliser un utilisateur dédié que vous avez créé à cet effet

- les sites doivent être servis en HTTPS sur le port 443 et en HTTP sur le port 80

  - n'oubliez pas d'ouvrir les ports firewall
  
    ```bash
    [lea@node1 ~]$ sudo firewall-cmd --add-port=80/tcp --permanent
    [sudo] password for lea: 
    Warning: ALREADY_ENABLED: 80:tcp
    success
    [lea@node1 ~]$ sudo firewall-cmd --add-port=443/tcp --permanent
    Warning: ALREADY_ENABLED: 443:tcp
    success
    [lea@node1 ~]$ firewall-cmd --reload
    Authorization failed.
        Make sure polkit agent is running or run the application as superuser.
    [lea@node1 ~]$ sudo !!
    sudo firewall-cmd --reload
    success
    [lea@node1 ~]$ sudo firewall-cmd --list-all
    public (active)
      target: default
      icmp-block-inversion: no
      interfaces: enp0s3 enp0s8
      sources: 
      services: dhcpv6-client ssh
      ports: 80/tcp 443/tcp
      protocols: 
      masquerade: no
      forward-ports: 
      source-ports: 
      icmp-blocks: 
      rich rules: 
    ```
  
    🌞 Faire en sorte que les sites soient disponibles en HTTPS
    
    - les sites doivent être servis en HTTPS sur le port 443, en plus du port 80
    
      - n'oubliez pas d'ouvrir les ports firewall sur le serveur
    
        ```bash
        [lea@node1 ~]$ sudo firewall-cmd --list-all
        [sudo] password for lea: 
        public (active)
          target: default
          icmp-block-inversion: no
          interfaces: enp0s3 enp0s8
          sources: 
          services: dhcpv6-client ssh
          ports: 80/tcp 443/tcp 
          protocols: 
          masquerade: no
          forward-ports: 
          source-ports: 
          icmp-blocks: 
          rich rules: 
        	
        [lea@node1 ~]$ 
        ```
    
        
    
    - vous pouvez générer une clé et son certificat associé dans le répertoire courant en une seule commande avec :
    
      - `openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout server.key -out server.crt`
    
      - plusieurs infos vous seront alors demandées. La seule qui est importante est le `Common Name` qui doit correspondre exactement au nom du site (chez nous c'est `node1.tp1.b2`)
    
      - le cert s'appelle alors `server.crt` et la clé `server.key` (vous pouvez parfaitement les renommer)
    
        ```bash
        [lea@node1 certs]$ sudo openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 -keyout /etc/pki/tls/private/node1.tp1.b2.key -out /etc/pki/tls/certs/node1.tp1.b2.crt
        Generating a 2048 bit RSA private key
        .+++
        .......+++
        writing new private key to '/etc/pki/tls/private/node1.tp1.b2.key'
        -----
        You are about to be asked to enter information that will be incorporated
        into your certificate request.
        What you are about to enter is what is called a Distinguished Name or a DN.
        There are quite a few fields but you can leave some blank
        For some fields there will be a default value,
        If you enter '.', the field will be left blank.
        -----
        Country Name (2 letter code) [XX]:
        State or Province Name (full name) []:
        Locality Name (eg, city) [Default City]:
        Organization Name (eg, company) [Default Company Ltd]:
        Organizational Unit Name (eg, section) []:
        Common Name (eg, your name or your server's hostname) []:node1.tp1.b2
        Email Address []:
        ```
    
        
    
    - bonnes pratiques
    
      - le certificat et la clé portent le nom du site. Par exemple, ici : `node1.tp1.b2.key` et `node1.tp1.b2.crt`
    
      - sous CentOS7, on place souvent les certificats dans `/etc/pki/tls/certs`
    
      - sous CentOS7, on place souvent les clés dans 
    
        ```
        /etc/pki/tls/private
        ```
    
        - les clés doivent posséder des permissions extrêmement restrictives (`400` c'est très bien)
    
          ```bash
          [lea@node1 certs]$ sudo chmod 644 node1.tp1.b2.crt 
          [lea@node1 certs]$ ls -la
          total 16
          drwxr-xr-x. 2 root root  141 Sep 28 16:39 .
          drwxr-xr-x. 5 root root   81 Sep 23 16:35 ..
          -rw-r--r--. 1 root root 2516 Aug  9  2019 Makefile
          lrwxrwxrwx. 1 root root   49 Sep 23 16:35 ca-bundle.crt -> /etc/pki/ca-trust/extracted/pem/tls-ca-bundle.pem
          lrwxrwxrwx. 1 root root   55 Sep 23 16:35 ca-bundle.trust.crt -> /etc/pki/ca-trust/extracted/openssl/ca-bundle.trust.crt
          -rwxr-xr-x. 1 root root  610 Aug  9  2019 make-dummy-cert
          -rw-r--r--. 1 root root 1281 Sep 28 16:39 node1.tp1.b2.crt
          -rwxr-xr-x. 1 root root  829 Aug  9  2019 renew-dummy-cert
          
          
          
          [lea@node1 private]$ sudo chmod 400 node1.tp1.b2.key 
          [lea@node1 private]$ ls -la
          total 4
          drwxr-xr-x. 2 root root   30 Sep 28 16:38 .
          drwxr-xr-x. 5 root root   81 Sep 23 16:35 ..
          -r--------. 1 root root 1704 Sep 28 16:39 node1.tp1.b2.key
          ```
    
          
    
      ```bash
      [lea@node1 certs]$ curl -kL https://localhost
      <h1> Hello Site 1 </h1>
      ```
    
      

Voici un exemple d'une unique fichier de configuration `nginx.conf` qui ne sert qu'un seul site, sur le port 8080, se trouvant dans `/tmp/test`:

```
[lea@node1 ~]$ cat /etc/nginx/nginx.conf
# For more information on configuration, see:
#   * Official English Documentation: http://nginx.org/en/docs/
#   * Official Russian Documentation: http://nginx.org/ru/docs/

user web;
worker_processes auto;
error_log /var/log/nginx/error.log;
pid /run/nginx/nginx.pid;

# Load dynamic modules. See /usr/share/doc/nginx/README.dynamic.
include /usr/share/nginx/modules/*.conf;

events {
    worker_connections 1024;
}

http {
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;

    sendfile            on;
    tcp_nopush          on;
    tcp_nodelay         on;
    keepalive_timeout   65;
    types_hash_max_size 2048;

    include             /etc/nginx/mime.types;
    default_type        application/octet-stream;

    # Load modular configuration files from the /etc/nginx/conf.d directory.
    # See http://nginx.org/en/docs/ngx_core_module.html#include
    # for more information.
    include /etc/nginx/conf.d/*.conf;

    server {
        listen       80;
        server_name  node1.tp1.b2;

        return 301 https://$host$request_uri;

    }
    
    server {
        listen 443 ssl;
        server_name node1.tp1.b2;

        ssl_certificate /etc/pki/tls/certs/node1.tp1.b2.crt;
        ssl_certificate_key /etc/pki/tls/private/node1.tp1.b2.key;
	
	location / {
            return 301 /site1;
        }
        location /site1 {
            alias /srv/site1;
        }
        location /site2 {
            alias /srv/site2;
        }

    }
}
```

🌞 Prouver que la machine `node2` peut joindre les deux sites web.

```bash
[lea@node2 ~]$ curl -L http://node1.tp1.b2
<h1> Hello Site 1 </h1>
[lea@node2 ~]$ curl -L http://node1.tp1.b2/site2
<h1>Site 2 on fire !!!</h1> 
[lea@node2 ~]$ curl -L http://node1.tp1.b2/site1
<h1> Hello Site 1 </h1>
[lea@node2 ~]$ 
```



# II. Script de sauvegarde

**Yup. Again.**

🌞 Ecrire un script qui :

- s'appelle `tp1_backup.sh`
- sauvegarde les deux sites web
  - c'est à dire qu'il crée une archive compressée pour chacun des sites
  - je vous conseille d'utiliser le format `tar` pour l'archivage et `gzip` pour la compression
- les noms des archives doivent contenir le nom du site sauvegardé ainsi que la date et heure de la sauvegarde
  - par exemple `site1_20200923_2358` (pour le 23 Septembre 2020 à 23h58)
- vous ne devez garder que 7 exemplaires sauvegardes
  - à la huitième sauvegarde réalisée, la plus ancienne est supprimée
- le script ne sauvegarde qu'un dossier à la fois, le chemin vers ce dossier est passé en argument du script
  - on peut donc appeler le script en faisant `tp1_backup.sh /srv/site1` afin de déclencher une sauvegarde de `/srv/site1`

🌞 Utiliser la `crontab` pour que le script s'exécute automatiquement toutes les heures.

```bash
[backup@node1 script]$ crontab -e

00 * * * * sh /home/backup/script/tp1_backup.sh /srv/site1
00 * * * * sh /home/backup/script/tp1_backup.sh /srv/site2
```



🌞 Prouver que vous êtes capables de restaurer un des sites dans une version antérieure, et fournir une marche à suivre pour restaurer une sauvegarde donnée.

```
Pour restaurer un des sites dans une version antérieure :

-Supprimer le site et ses fichiers :
rm -rf /srv/site1 (pour site1)
rm -rf /srv/site2 (pour site2)

-Recréer le dossier :
mkdir /srv/site1 (pour site 1)
mkdir /srv/site1 (pour site 2)

-Lui remettre l'appartennance au bon utilisateur et les bonnes permissions :
chown web:web /srv/site1 (pour site1)
chown web:web /srv/site2 (pour site2)

chmod 740 /srv/site1 (pour site1)
chmod 740 /srv/site2 (pour site2)

-Décompresser et désarchiver le fichier d'archive en question avec la commande :
mv tar -xzf nom_du_fichier_de_backup.tar.gz /srv/site1 (ou /srv/site2 si c'est le site 2)

-Delete all posteriory files in /home/backup/backup :
rm /home/backup/backup/backup_file_opsolete.tar.gz
```





```bash
[backup@node1 script]$ cat tp1_backup.sh 
#!/bin/bash
# Léa
# 28/09/2020
# Script de backup de fichiers web


# date et heure du jour
date=$(date +%Y%m%d_%H%M%S)

target_path="${1}"
target_dir="$(basename ${1})"

backup_path="/home/backup/backup/"
backup_file="${backup_path}${target_dir}_${date}"


function backup {
	tar -czf ${backup_file}.tar.gz -P ${target_path}
}

nb_backup=$(ls $backup_path | wc -l)
echo "${nb_backup}"
if [[ "${nb_backup}" -lt 7 ]] 
then
        echo "sauvegarde en cours de $(echo ${target_dir})"
				backup
else
        echo "suppression du plus vieux fichier de sauvegarde"
        older_file=$(ls ${backup_path} -t | tail -1)
        rm ${backup_path}${older_file}
        echo "sauvegarde de $(echo ${target_dir})"
				backup
fi
```



**NB** : votre script

- doit s'exécuter sous l'identité d'un utilisateur dédié appelé `backup`

- ne doit comporter **AUCUNE** commande `sudo`

- doit posséder des permissions minimales à son bon fonctionnement

  ```bash
  [backup@node1 script]$ ll
  total 4
  -rwxrwxr-x. 1 backup backup 790 Sep 28 18:19 tp1_backup.sh
  -rw-rw-r--. 1 backup backup   0 Sep 24 18:42 tp1_backup.sh~
  ```

  ```bash
  [backup@node1 srv]$ ll
  total 8
  dr-xr-x---. 3 web web 4096 Sep 28 16:22 site1
  dr-xr-x---. 3 web web 4096 Sep 24 10:40 site2
  [backup@node1 srv]$ cd site1
  [backup@node1 site1]$ ll
  total 20
  -r--r-----. 1 web web    24 Sep 24 10:21 index.html
  dr--r-----. 2 web web 16384 Sep 23 17:31 lost+found
  [backup@node1 site1]$ cd ..
  [backup@node1 srv]$ cd site2
  [backup@node1 site2]$ ll
  total 20
  -r--r-----. 1 web web    29 Sep 24 10:40 index.html
  dr--r-----. 2 web web 16384 Sep 23 17:31 lost+found
  ```

  

- doit utiliser des variables et des fonctions, **avec des noms explicites**

🐙 Créer une unité systemd qui permet de déclencher le script de backup

- c'est à dire, faire en sorte que votre script de backup soit déclenché lorsque l'on exécute `sudo systemctl start backup`

# III. Monitoring, alerting

🌞 Mettre en place l'outil Netdata en suivant [les instructions officielles](https://learn.netdata.cloud/docs/agent/packaging/installer) et s'assurer de son bon fonctionnement.

```bash
[lea@node1 script]$ bash <(curl -Ss https://my-netdata.io/kickstart.sh)
System            : Linux
Operating System  : GNU/Linux
Machine           : x86_64
BASH major version: 
 --- Fetching script to detect required packages... --- 
[/tmp/netdata-kickstart-cgRpeid73q]$ curl -q -sSL --connect-timeout 10 --retry 3 --output /tmp/netdata-kickstart-cgRpeid73q/install-required-packages.sh https://raw.githubusercontent.com/netdata/netdata/master/packaging/installer/install-required-packages.sh  OK  
[...]
netdata by default listens on all IPs on port 19999,
so you can access it with:

  http://this.machine.ip:19999/

To stop netdata run:

  systemctl stop netdata

To start netdata run:

  systemctl start netdata

Uninstall script copied to: /usr/libexec/netdata/netdata-uninstaller.sh

 --- Installing (but not enabling) the netdata updater tool --- 
Update script is located at /usr/libexec/netdata/netdata-updater.sh

 --- Check if we must enable/disable the netdata updater tool --- 
Auto-updating has been enabled through cron, updater script linked to /etc/cron.daily/netdata-updater

If the update process fails and you have email notifications set up correctly for cron on this system, you should receive an email notification of the failure.
Successful updates will not send an email.

 --- Wrap up environment set up --- 
Preparing .environment file
[/tmp/netdata-kickstart-cgRpeid73q/netdata-v1.25.0-75-gb53c13a8]# chmod 0644 /etc/netdata/.environment 
 OK   

Setting netdata.tarball.checksum to 'new_installation'

 --- We are done! --- 

  ^
  |.-.   .-.   .-.   .-.   .-.   .  netdata                          .-.   .-
  |   '-'   '-'   '-'   '-'   '-'   is installed and running now!  -'   '-'  
  +----+-----+-----+-----+-----+-----+-----+-----+-----+-----+-----+-----+--->

  enjoy real-time performance and health monitoring...

 OK  

```



🌞 Configurer Netdata pour qu'ils vous envoient des alertes dans un salon Discord dédié

- c'est à dire que Netdata vous informera quand la RAM est pleine, ou le disque, ou autre, *via* Discord

  ```
  [lea@node1 ~]$ sudo /etc/netdata/edit-config health_alarm_notify.conf
  
  […]
  # discord (discordapp.com) global notification options
  
  # multiple recipients can be given like this:
  #                  "CHANNEL1 CHANNEL2 ..."
  
  # enable/disable sending discord notifications
  SEND_DISCORD="YES"
  
  # Create a webhook by following the official documentation -
  # https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks
  DISCORD_WEBHOOK_URL="https://discordapp.com/api/webhooks/760218826091659344/_yp_UFJDM3CgvLv6ca5ICpgrYjlRWOBiaheEqxaHmpCiE1kND02mkM8colcu1NtHLuRO"
  
  # if a role's recipients are not configured, a notification will be send to
  # this discord channel (empty = do not send a notification for unconfigured
  # roles):
  DEFAULT_RECIPIENT_DISCORD="alarm"
  […]
  [lea@node1 ~]$ export NETDATA_ALARM_NOTIFY_DEBUG=1
  [lea@node1 ~]$ /usr/libexec/netdata/plugins.d/alarm-notify.sh test
  # SENDING TEST WARNING ALARM TO ROLE: sysadmin
  2020-09-28 20:30:57: alarm-notify.sh: DEBUG: Loading config file '/usr/lib/netdata/conf.d/health_alarm_notify.conf'...
  2020-09-28 20:30:57: alarm-notify.sh: DEBUG: Loading config file '/etc/netdata/health_alarm_notify.conf'...
  2020-09-28 20:30:57: alarm-notify.sh: DEBUG: Cannot find aws command in the system path.  Disabling Amazon SNS notifications.
  […]
  ```

  ![](/Users/leaduvigneau/Documents/ynov/cours/b2/linux/tp1/images/discord_notification.png)