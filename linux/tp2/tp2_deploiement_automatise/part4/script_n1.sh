#!/bin/bash

# Léa 
# 29/09/2020
# TP2-Linux (B2)
# Setup vm1 vagrant

# Setup web server

yum install nginx -y
useradd web -M -s /sbin/nologin
useradd backup 
echo root | passwd backup --stdin
usermod -aG web backup

## Generation clefs + certificat
cp /tmp/server.key /etc/pki/tls/private/node1.tp2.b2.key
chmod 400 /etc/pki/tls/private/node1.tp2.b2.key
chown web:web /etc/pki/tls/private/node1.tp2.b2.key
rm /tmp/server.key

cp /tmp/server.crt /etc/pki/tls/certs/node1.tp2.b2.crt
chmod 444 /etc/pki/tls/certs/node1.tp2.b2.crt
chown web:web /etc/pki/tls/certs/node1.tp2.b2.crt

## trust certificat
cp /etc/pki/tls/certs/node1.tp2.b2.crt /usr/share/pki/ca-trust-source/anchors/
update-ca-trust

## Setup des deux sites web
mkdir /srv/site{1,2}
touch /srv/site1/index.html
touch /srv/site2/index.html

echo '<h1>Hello from site 1</h1>' | tee /srv/site1/index.html
echo '<h1>Hello from site 2</h1>' | tee /srv/site2/index.html
chmod --preserve-root 440 /srv/site1/index.html /srv/site2/index.html
chmod --preserve-root 550 /srv/site1 /srv/site2
chown web:web /srv/site1 -R
chown web:web /srv/site2 -R

## Config de NGINX

mv /tmp/nginx.conf /etc/nginx/nginx.conf

# Start nginx
systemctl start nginx
systemctl enable nginx

# mv script backup
cp /tmp/tp2_backup.sh /srv/tp2_backup.sh
rm /tmp/tp2_backup

chown backup /srv/tp2_backup.sh
chmod 775 /srv/tp2_backup.sh

mkdir /srv/backup
chown backup /srv/backup

## Crontab backup
(crontab -u backup -l ; echo "*/1 * * * * /srv/tp2_backup.sh /srv/site1") | crontab -u backup -
(crontab -u backup -l ; echo "*/1 * * * * /srv/tp2_backup.sh /srv/site2") | crontab -u backup -
service crond restart

# Install netdata
yum install zlib-devel libuuid-devel libmnl-devel gcc make git autoconf autogen automake pkgconfig libuv1-dev libuv-devel -y
yum install curl jq nodejs -y
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
