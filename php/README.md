# ENV

```
$ lsb_release -a
No LSB modules are available.
Distributor ID: Ubuntu
Description:    Ubuntu 18.04.3 LTS
Release:        18.04
Codename:       bionic

$ php -v
PHP 7.2.24-0ubuntu0.18.04.1 (cli) (built: Oct 28 2019 12:07:07) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.24-0ubuntu0.18.04.1, Copyright (c) 1999-2018, by Zend Technologies

$ php -r 'echo OPENSSL_VERSION_TEXT."\n";'
OpenSSL 1.1.1  11 Sep 2018

$ php composer.phar install
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 2 installs, 0 updates, 0 removals
  - Installing firebase/php-jwt (v5.0.0): Downloading (100%)
  - Installing lcobucci/jwt (3.3.1): Downloading (100%)
Writing lock file
Generating autoload files
```
