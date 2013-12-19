php-datalanche
==============

Official PHP client for [Datalanche](https://www.datalanche.com).

## Install

[Create an account](https://www.datalanche.com/account/signup) and obtain an API key and secret. Then install the client.

### Composer

Add [datalance/client](https://packagist.org/packages/datalanche/client) to your project's ```composer.json```. If
it does not exist, create it in your project's root directory.
```
{
    "require": {
        "datalanche/client": "dev-master"
    }
}
```

Install [Composer](http://getcomposer.org/) and dependencies.
```
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```
In PHP code:
```
require('vendor/autoload.php');
```

### Manual

1. Clone the Git repo: ```git clone https://github.com/datalanche/php-datalanche.git```
2. In PHP code: ```require('/path/to/repo/Datalanche.php');```

## Documentation

All documentation is on our [website](https://www.datalanche.com/docs).
