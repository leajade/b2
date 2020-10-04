#!/bin/bash

echo "192.168.2.21  node1.tp2.b2  node1" >> /etc/hosts
echo "192.168.2.22  node2.tp2.b2  node2" >> /etc/hosts

adduser admin -m
echo "root" | passwd admin --stdin
usermod -aG wheel admin

firewall-cmd --add-port=80/tcp --permanent
firewall-cmd --add-port=443/tcp --permanent
firewall-cmd --reload
