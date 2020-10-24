wget -O gitea https://dl.gitea.io/gitea/1.12.5/gitea-1.12.5-linux-amd64
chmod +x gitea

yum install -y git
git config --global user.name "lea"
git config --global user.email "lea.duvigneau@ynov.com"

useradd git -m
echo 'root' | passwd git --stdin

# create required directory structure
mkdir -p /var/lib/gitea/{custom,data,log}
chown -R git:git /var/lib/gitea/
chmod -R 750 /var/lib/gitea/
mkdir /etc/gitea
chown root:git /etc/gitea
chmod 770 /etc/gitea
export GITEA_WORK_DIR=/var/lib/gitea/
cp gitea /usr/local/bin/gitea
cp /tmp/gitea.service /etc/systemd/system/gitea.service

# open default port for gitea
firewall-cmd --add-port=80/tcp --permanent
firewall-cmd --add-port=3000/tcp --permanent
firewall-cmd --reload

# enable and start on boot gitea
systemctl daemon-reload
systemctl enable gitea
systemctl start gitea

#configure nfs
mkdir /mnt/nfsfileshare
mount nfs:/nfsfileshare/gitea /mnt/nfsfileshare

#Automount NFS Shares
echo -e "
192.168.2.27:/nfsfileshare/gitea /mnt/nfsfileshare    nfs     nosuid,rw,sync,hard,intr  0  0
" > /etc/fstab


useradd backup -u 1003 -s /sbin/nologin

cp /tmp/backup_gitea.sh /opt/backup_gitea.sh

chown backup:root /etc/gitea

chmod 755 /opt/backup_gitea.sh
chown backup:backup /opt/backup_gitea.sh

chown backup:root /mnt/nfsfileshare
chmod 755 /mnt/nfsfileshare

echo -e "
[Unit]
Description=backup gitea

[Service]
User=backup

Type=oneshot
ExecStart=/opt/backup_gitea.sh /etc/gitea

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
