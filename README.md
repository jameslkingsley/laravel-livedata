# Pull database rows from a live server

This Laravel >=5.5 package provides a quick and simple way to pull database rows from a live server. Perfect for when you want to test your development code on live data without having to leave the terminal.

## Installation

You can install this package via composer using this command:

```bash
composer require jameslkingsley/laravel-livedata
```

**If you're using Laravel 5.5 or greater this package will be auto-discovered, however if you're using anything lower than 5.5 you will need to register it the old way:**

Next, you must install the service provider in `config/app.php`:

```php
'providers' => [
    ...
    Kingsley\LiveData\LiveDataServiceProvider::class,
];
```

Now publish the config:

```bash
php artisan vendor:publish --provider="Kingsley\LiveData\LiveDataServiceProvider"
```

This is the contents of the published config file:

```php
return [
    /**
     * The live database connection to pull from.
     * Connection defined in config\database.php.
     */
    'live' => 'mysql_live',

    /**
     * The local database connection to pull to.
     * Connection defined in config\database.php.
     * If null will use the app's default configuration.
     */
    'local' => null
];
```

You'll need to setup your live database connection in `config\database.php`. Then just update `config\livedata.php` with its connection name. **Make sure you don't mix up the connections, you really don't want to truncate rows on the live database!**

And now just run the command!

```bash
php artisan livedata:pull
```

## Note
1. It will not pull any structural changes from the live, only the data. This means that if there are tables on the live that are not on the local, those tables will be skipped.

2. Currently this package only supports MySQL databases due to the toggling of foreign key checks. I'm not sure of an elegant way to handle this necessity for multiple databases.
