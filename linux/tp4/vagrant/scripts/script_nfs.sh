firewall-cmd --permanent --add-service=nfs
firewall-cmd --permanent --add-port=2049/tcp
firewall-cmd --reload

systemctl start nfs-server rpcbind
systemctl enable nfs-server rpcbind

mkdir /nfsfileshare
mkdir /nfsfileshare/gitea
mkdir /nfsfileshare/mariadb
mkdir /nfsfileshare/nginx

chmod 777 /nfsfileshare/
chmod 777 /nfsfileshare/gitea
chmod 777 /nfsfileshare/mariadb
chmod 777 /nfsfileshare/nginx

echo -e "
/nfsfileshare/gitea 192.168.2.24(rw,sync,no_root_squash)
/nfsfileshare/mariadb 192.168.2.25(rw,sync,no_root_squash)
/nfsfileshare/nginx 192.168.2.26(rw,sync,no_root_squash)
" > /etc/exports

exportfs -r

firewall-cmd --permanent --add-service mountd
firewall-cmd --permanent --add-service rpc-bind
firewall-cmd --reload
