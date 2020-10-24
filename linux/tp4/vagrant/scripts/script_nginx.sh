yum install -y nginx

firewall-cmd --add-port=80/tcp --permanent
firewall-cmd --reload

cp /tmp/nginx.conf /etc/nginx/nginx.conf

systemctl enable nginx
systemctl start nginx

mkdir /mnt/nfsfileshare
mount nfs:/nfsfileshare/nginx /mnt/nfsfileshare

#Automount NFS Shares
echo -e "
192.168.2.27:/nfsfileshare/nginx /mnt/nfsfileshare    nfs     nosuid,rw,sync,hard,intr  0  0
" > /etc/fstab

cp /tmp/backup_nginx.sh /opt/backup_nginx.sh

useradd backup -u 1003 -s /sbin/nologin

chown backup:root /etc/nginx

chown backup /opt/backup_nginx.sh
chmod 755 /opt/backup_nginx.sh

chown backup /mnt/nfsfileshare
chmod 755 /mnt/nfsfileshare


echo -e "
[Unit]
Description=backup nginx

[Service]
User=backup

Type=oneshot
ExecStart=/opt/backup_nginx.sh /etc/nginx

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
