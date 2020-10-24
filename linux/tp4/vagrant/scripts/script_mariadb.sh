# Installation mariadb via repos centos
curl -LsS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | sudo bash
yum install -y MariaDB-server MariaDB-client

# enable and start on boot mariadb
systemctl enable mariadb
systemctl start mariadb

cp /tmp/server.cnf /etc/my.cnf.d/server.cnf
rm /tmp/server.cnf

systemctl restart mariadb.service

firewall-cmd --add-port=3306/tcp --permanent
firewall-cmd --reload

mysql -h "localhost" "--user=root" "--password=" -e \
	"SET old_passwords=0;" -e \
	"CREATE USER 'gitea25'@'192.168.2.25' IDENTIFIED BY 'gitea25';" -e \
	"SET PASSWORD FOR 'gitea25'@'192.168.2.25' = PASSWORD('gitea');" -e \
	"CREATE DATABASE giteadb CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';" -e \
	"grant all privileges on giteadb.* to 'gitea25'@'192.168.2.25' identified by 'gitea25' with grant option;" -e \
	"FLUSH PRIVILEGES;"


# configure nfs
mkdir /mnt/nfsfileshare
mount nfs:/nfsfileshare/mariadb /mnt/nfsfileshare

#Automount NFS Shares
echo -e "
192.168.2.27:/nfsfileshare/mariadb /mnt/nfsfileshare    nfs     nosuid,rw,sync,hard,intr  0  0
" > /etc/fstab

useradd backup -u 1003 -s /sbin/nologin
chown backup:root /etc/my.cnf

cp /tmp/backup_mariadb.sh /opt/backup_mariadb.sh
chmod 755 /opt/backup_mariadb.sh

chown backup /opt/backup_mariadb.sh
chmod 755 /opt/backup_mariadb.sh

chown backup /mnt/nfsfileshare
chmod 755 /mnt/nfsfileshare

echo -e "
[Unit]
Description=backup mariadb

[Service]
User=backup

Type=oneshot
ExecStart=/opt/backup_mariadb.sh /etc/my.cnf

[Install]
WantedBy=multi-user.target
" > /etc/systemd/system/backup.service


echo -e "
[Unit]
Description=Execute backup every hour relative to when the machine was booted up

[Timer]
OnCalendar=*-*-* *:00:00

[Install]
WantedBy=multi-user.target
" > /etc/systemd/system/backup.timer

systemctl is-enabled backup.timer
systemctl start backup.timer
