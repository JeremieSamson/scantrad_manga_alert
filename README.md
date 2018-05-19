# Scantrad Manga Alert


[![Build Status](https://travis-ci.org/JeremieSamson/scantrad_manga_alert.svg?branch=master)](https://travis-ci.org/JeremieSamson/scantrad_manga_alert)

## Installation

Récupérez le projet depuis github :

```shell
git clone git@github.com:JeremieSamson/scantrad_manga_alert.git
```

Installez [composer](https://getcomposer.org) :

```shell
curl -sS https://getcomposer.org/installer | php
```

Mettez à jour les librairies avec composer :

```shell
php composer.phar install
```

Configurez les permissions des répertoires du projet. Si vous êtes sur une machine Mac :

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" var/cache var/logs
sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" var/cache var/logs
```

ou

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/sessions
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/sessions
```

Ajouter le VHost 

```shell
<VirtualHost *:80>
        ServerAdmin webmaster@yourdomain
        ServerName scantrad_manga_alert.local.fr
        DocumentRoot PATH_TO_PROJECT

        <Directory PATH_TO_PROJECT>
                Options Indexes ExecCGI FollowSymLinks MultiViews
                AllowOverride all
                Order allow,deny
                Allow from all
        </Directory>

        ErrorLog /var/log/apache2/scantrad_manga_alert.local.fr.log
        LogLevel error
        CustomLog /var/log/apache2/scantrad_manga_alert.local.fr.log vhost_combined_time_end
</VirtualHost>
```