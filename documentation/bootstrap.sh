#!/bin/sh

apt-get update
apt-get install -y php5 php5-dev subversion php-pear git
#echo 'extension=svn.so' >> /etc/php5/apache2/php.ini
#echo 'extension=svn.so' >> /etc/php5/cli/php.ini

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Create Test SVN
#mkdir -p /vagrant/src/Tests/Temp/
#svnadmin create /vagrant/src/Tests/Temp/SVN-Repo
#svn co file:///vagrant/src/Tests/Temp/SVN-Repo /vagrant/src/Tests/Temp/SVN-Working-Copy
#svn co file:///vagrant/src/Tests/Temp/SVN-Repo /vagrant/src/Tests/Temp/SVN-Working-Copy-2

# Install Docker
apt-get install apt-transport-https ca-certificates
sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
echo 'deb https://apt.dockerproject.org/repo ubuntu-trusty main' >> /etc/apt/sources.list.d/docker.list
apt-get update
apt-cache policy docker-engine
apt-get install -y docker-engine
service docker start
systemctl enable docker

# Install Docker-Compose
curl -L https://github.com/docker/compose/releases/download/1.6.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Install HAProxy
apt-get install -y haproxy
# vi /etc/default/haproxy  (Enabled=1)

# Install Box - http://box-project.github.io/box2/
curl -LSs https://box-project.github.io/box2/installer.php | php
mv box.phar /usr/bin/box
chmod +x /usr/bin/box

# Install SQLite3
apt-get install -y sqlite3
apt-get install php5-sqlite

# Symlink gruver
ln -s /vagrant/gruver /bin/gruver