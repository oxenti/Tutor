# OxenTI Tutor API plugin for CakePHP 3

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require oxenti/Tutor
```


## Configuration

In your app's `config/bootstrap.php` add:

```php
// In config/bootstrap.php
Plugin::load('Tutor');
```

or using cake's console:

```sh
./bin/cake plugin load Tutor
```

In your app's 'config/app.php' add this to your Datasources array:

```php
	'oxenti_tutor' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Mysql',
        'persistent' => false,
        'host' => 'ỳour_db_host',
        'username' => 'username',
        'password' => 'password',
        'database' => 'databse_name',
        'encoding' => 'utf8',
        'timezone' => 'UTC',
        'cacheMetadata' => true,
        'log' => false,
        'quoteIdentifiers' => false,
    ],
    'test_oxenti_tutor' => [
        'className' => 'Cake\Database\Connection',
        'driver' => 'Cake\Database\Driver\Mysql',
        'persistent' => false,
        'host' => 'ỳour_db_host',
        'username' => 'username',
        'password' => 'password',
        'database' => 'databse_name',
        'encoding' => 'utf8',
        'timezone' => 'UTC',
        'cacheMetadata' => true,
        'log' => false,
        'quoteIdentifiers' => false,
    ],
```
In your app's initial folder execute plugin's migrations:

```sh
./bin/cake migrations migrate -p Tutor
```

### Configuration files
Move the 'tutor.php' config file from the plugin's config folder to your app's config folder.

On your app's 'bootstrap.php' add the tutor configuration file:
```php
    ...
    try {
        Configure::config('default', new PhpConfig());
        Configure::load('app', 'default', false);
    } catch (\Exception $e) {
        die($e->getMessage() . "\n");
    }

    Configure::load('tutor', 'default');
    ...
```
