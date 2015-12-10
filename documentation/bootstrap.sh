#!/bin/sh

apt-get update
apt-get install -y php5 php5-dev subversion libsvn-dev php-pear
pecl install svn-beta
echo 'extension=svn.so' >> /etc/php5/apache2/php.ini
echo 'extension=svn.so' >> /etc/php5/cli/php.ini

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Create Test SVN
mkdir -p /vagrant/src/Tests/Temp/
svnadmin create /vagrant/src/Tests/Temp/SVN-Repo
svn co file:///vagrant/src/Tests/Temp/SVN-Repo /vagrant/src/Tests/Temp/SVN-Working-Copy
svn co file:///vagrant/src/Tests/Temp/SVN-Repo /vagrant/src/Tests/Temp/SVN-Working-Copy-2
