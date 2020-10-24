#!/bin/bash

# Disable SELinux
setenforce 0
sed -i 's/enforcing/permissive/' /etc/selinux/config

# Update system and repos
yum update -y
yum install -y vim
yum install -y epel-release
yum install -y wget

# Install and configure firewalld
yum install -y firewalld
systemctl enable --now firewalld
firewall-cmd --add-port=22/tcp --permanent
firewall-cmd --add-port=22/tcp

# Configure fichier host
echo "192.168.2.24  gitea" >> /etc/hosts
echo "192.168.2.25  mariadb" >> /etc/hosts
echo "192.168.2.26  nginx" >> /etc/hosts
echo "192.168.2.27  nfs" >> /etc/hosts

#configure netdata
bash <(curl -Ss https://my-netdata.io/kickstart.sh) --dont-wait

sed -i "/DISCORD_WEBHOOK_URL=/c\DISCORD_WEBHOOK_URL=''https://discord.com/api/webhooks/760218826091659344/_yp_UFJDM3CgvLv6ca5ICpgrYjlRWOBiaheEqxaHmpCiE1kND02mkM8colcu1NtHLuRO" /usr/lib/netdata/conf.d/health_alarm_notify.conf

