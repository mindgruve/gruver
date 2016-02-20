#!/bin/bash

docker rm -f $(docker ps -a -q)
rm /var/lib/gruver/data.db
gruver doctrine:schema:update --force