#!/bin/bash

# Disable SELinux
setenforce 0
sed -i 's/enforcing/permissive/' /etc/selinux/config

# Update system and repos
yum update -y
yum install -y vim
yum install -y epel-release
yum install nginx -y

# Install and configure firewalld
yum install -y firewalld
systemctl enable --now firewalld
firewall-cmd --add-port=22/tcp --permanent
firewall-cmd --add-port=22/tcp

