#!/bin/bash

docker rm -f $(docker ps -a -q)
rm /var/lib/gruver/data.db
gruver doctrine:schema:update --force
cd /vagrant/source-code/control-panel
gruver load-config
gruver build
gruver run --tag=1.0.0
gruver promote
cd /vagrant/source-code/hello.world
gruver load-config