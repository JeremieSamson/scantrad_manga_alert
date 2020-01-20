# Scantrad Manga Alert

[![CircleCI](https://circleci.com/gh/JeremieSamson/scantrad_manga_alert/tree/master.svg?style=svg)](https://circleci.com/gh/JeremieSamson/scantrad_manga_alert/tree/master)
[![SymfonyInsight](https://insight.symfony.com/projects/2f3d0fa6-9762-41b1-8cd9-48d5c709ed0b/mini.svg)](https://insight.symfony.com/projects/2f3d0fa6-9762-41b1-8cd9-48d5c709ed0b)

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

Créer la base de données

```shell
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

Synchroniser les premiers mangas et chapitre

```shell
php bin/console sync:chapter
```
