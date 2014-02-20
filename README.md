[![Stories in Ready](https://badge.waffle.io/drakojn/io.png?label=ready)](https://waffle.io/drakojn/io)
Drakojn
===

> Drakojn is a library suite thought to be simple and effective for web development.
> This suite has small libraries to be connected on your current or future project easily.

Io
==

> Io is a small tool aimed to abstract persistency simple way. The idea is keep your model/data classes as is,
> and provide a simple way to connect them either a relational (or not) database, text files, web protocols and
> services and so on

Usage
---

Imagine you have the following class:

```php
namespace Dummy\Data;

class User
{
    protected $id;
    protected $alias;
    protected $name;
    protected $email;

    /* all setters and getters here */
}
```

And you have a table like this on your rdbms:

```
Table user
___________________________________________________________________
|id_user (PK)|login        |name         |email       |password   |
___________________________________________________________________
|1           |duodraco     |Anderson...  |o@duodr...  |*****      |
___________________________________________________________________
|2           |alganet      |Alexandre..  |alexandre...|*****      |
 __________________________________________________________________
|3           |hagiro       |Augusto...   |augusto.h...|*****      |
___________________________________________________________________
```

You just have to setup a Drakojn\Io\Mapper\Map object this way:

```php
$map = new Drakojn\Io\Mapper\Map(
    'Dummy\\Data\\User', //local class
    'user', // remote entity, the table
    'id', //local attribute used to identify this object through remote part
    [
        'id' => 'id_user',
        'alias' => 'login',
        'name' => 'name',
        'email' => 'email'
    ] //map between local class and remote presistency part
);
```

Set up the communication driver:

```php
$driver = new Drakojn\Io\Driver\Pdo(
  new \PDO('mysql:host=localhost;dbname=dummy','your-user','your-password')
);
```

Create the mapper:

```php
$userMapper = new Drakojn\Io\Mapper($driver, $map);
```

And start to play:

```php
$allUsers = $userMapper->findAll();
$myself = $userMapper->findByIdentifier(1);
$gaigalas = $userMapper->find(['login'=>'alganet']);
$someoneIDontLike = $userMapper->find(['name'=>'something bad']);
$klaus = new User;
$klaus->setAlias('klaus');
$klaus->setName('Klaus Silveira');
$userMapper->save($klaus);
$userMapper->delete($someoneIDontLike);
```

Now imagine that you cannot use a RDBMS and the only way to persist is the filesystem. No Problem.
You can use another Driver:

```php
$fileDriver = new Drakojn\Io\Driver\File('/path/to/store/your/objects/');
$userMapper = new Drakojn\Io\Mapper($fileDriver, $map);
```

And develop as you were working with RDBMS.
You (or Drakojn Developer) (or YOU as Drakojn Developer) could do an exchange strategy between many sources.


RoadMap:
---
* IMAP Driver
* Specialized Pdo Drivers
* Nosql DB Driver
* RESTful Driver

Changelog:
---
* 0.0.2 - Added FileDriver
* 0.0.1 - First Release with basic mapping through Pdo

Attention:
---

This is a early in-development library and it could have a lot of problems. There are some already mapped on github
project page issues - if you find something wrong please create an issue there.

Even better: help this project coding or documenting. The developer and opensource community will be really thankful.
