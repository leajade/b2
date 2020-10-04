#!/bin/bash

cp /tmp/server.crt /usr/share/pki/ca-trust-source/anchors/
update-ca-trust
