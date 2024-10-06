# KaliPHP
KaliPHP is a fast, lightweight PHP framework. In an age where frameworks are a dime a dozen, We believe that KaliPHP will stand out in the crowd. It will do this by combining all the things you love about the great frameworks out there, while getting rid of the bad.

## Requires
PHP 7.1 or Higher  

## Installation

```
composer require owner888/kaliphp
# If a template is needed
composer require smarty/smarty
```

## Test

```
composer test
```

## Basic Usage

### DB 
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
// The _init() method will execute when the class is loaded.
autoloader::register();
use kaliphp\db;

// query
db::query($sql)->execute($is_master = false);

// select
db::select(['id', 'name'])->from('user')->execute();

// insert
// INSERT INTO `user`(`name`,`email`,`password`)
// VALUES ("John Random", "john@example.com", "s0_s3cr3t")
list($insert_id, $rows_affected) = db::insert('user')->set(array(
    'name'      => 'John Random',
    'email'     => 'john@example.com',
    'password'  => 's0_s3cr3t',
))->execute();

// update
// UPDATE `user` SET `name` = "John Random" WHERE `id` = "2";
$rows_affected = db::update('user')
    ->value("name", "John Random")
    ->where('id', '=', '2')
    ->execute();

// delete
// DELETE FROM `user` WHERE `email` LIKE "%@example.com"
$rows_affected = db::delete('users')->where('email', 'like', '%@example.com')->execute(); // (int) 7
```

## Documentation

[http://doc.kaliphp.com](http://doc.kaliphp.com)

## LICENSE

KaliPHP is released under the [MIT license](https://github.com/owner888/kaliphp/blob/master/LICENSE).
