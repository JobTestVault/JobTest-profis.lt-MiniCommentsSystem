# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|

  config.vm.box = "ubuntu/vivid32"

  config.vm.network "forwarded_port", guest: 80, host: 8080
 
  config.vm.synced_folder "www", "/var/www/html"

  config.vm.provision "shell", inline: <<-SHELL	 
     sudo apt-get update
	 sudo DEBIAN_FRONTEND=noninteractive apt-get -y -q --force-yes upgrade
     sudo DEBIAN_FRONTEND=noninteractive apt-get install -y -q --force-yes apache2 mysql-server php5 mc php5-json php5-memcached curl memcached php5-mysql
     sudo curl -sS https://getcomposer.org/installer | php
     sudo mv composer.phar /usr/local/bin/composer
	 sudo a2enmod rewrite
	 sudo rm -rf /etc/apache2/sites-available/000-default.conf
	 sudo cp /vagrant/000-default.conf /etc/apache2/sites-available/000-default.conf
	 sudo service apache2 restart
	 sudo mysql < /vagrant/structure.sql
  SHELL

end
