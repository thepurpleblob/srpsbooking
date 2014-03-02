In case I forget.

FIX PERMISSIONS:
(check web-server user, _www on OSX)
$ rm -rf app/cache/*
$ rm -rf app/logs/*

$ sudo chmod +a "www-data allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs
$ sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs

OR

$ sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
$ sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs

OR
at the start of app/console, web/app.php and web/app_dev.php
umask(0002);  or even umask(0000);

app/config/parameters.yml is not in Git. The _dist version is. Copy it and set up for local instance.

Install the PHP extension php5-mcrypt

INSTALL VENDORS:
curl -s http://getcomposer.org/installer | php
php composer.phar install

CREATE DATABASE:
Name is in parameters.yml (default srpsbooking)
create database srpsbooking default character set utf8 collate utf8_unicode_ci;
php app/console doctrine:schema:create

INSTALL ASSETS:
php app/console assets:install

RUN ONLINE CONFIG:
Run app.php/install to create admin account
(default admin account is admin/admin)

Run app.php/admin/install to populate CRS

