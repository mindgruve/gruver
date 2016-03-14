#!/bin/bash
cd /vagrant/application
composer install --no-dev
cd /vagrant
box build -v