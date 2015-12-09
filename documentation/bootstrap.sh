#!/bin/sh

yum remove -y php.x86_64
yum remove -y php-cli.x86_64
yum remove -y php-common.x86_64

yum install -y libxml2 libxml2-devel

wget http://php.net/get/php-5.5.30.tar.gz/from/this/mirror --output-document php-5.5.30.tar.gz
tar -xvf php-5.5.30.tar.gz
cd php-5.5.30
./configure
make
make install