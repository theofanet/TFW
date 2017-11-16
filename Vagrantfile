# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

  config.vm.box = "ubuntu/xenial64"

  config.vm.network "private_network", ip: "192.168.33.19"

  config.vm.synced_folder "./", "/var/www/html"

   config.vm.provision "shell", inline: <<-SHELL
     debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
     debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
     debconf-set-selections <<< "postfix postfix/mailname string vagrant.local.dev"
     debconf-set-selections <<< "postfix postfix/main_mailer_type string 'Internet Site'"
     sudo apt-get update
     sudo apt-get install -y apache2 php5 mysql-server libapache2-mod-php5 php5-mysql php5-mcrypt php5-gd php5-curl php-xml emacs24 mailutils
     a2enmod rewrite
     a2enmod headers
     php5enmod mcrypt
     php5enmod curl
     sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=vagrant/g' /etc/apache2/envvars
     sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=vagrant/g' /etc/apache2/envvars
     sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/my.cnf
     mysql -uroot -proot -e "CREATE USER 'root'@'%' IDENTIFIED BY 'root';"
     mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON * . * TO 'root'@'%';"
     mysql -uroot -proot -e "FLUSH PRIVILEGES;"
     rm /var/www/html/index.html
     rm /etc/apache2/sites-available/000-default.conf
     touch /etc/apache2/sites-available/000-default.conf
     echo "<VirtualHost *:80>
           DocumentRoot /var/www/html
           ServerName tframe.local
           <Directory /var/www/html>
           	   allow from all
           	   Options +Indexes
           	   AllowOverride All
           </Directory>
     </VirtualHost>" >> /etc/apache2/sites-available/000-default.conf
     service apache2 restart
     chown -R vagrant:vagrant /var/lib/php5
   SHELL
end
