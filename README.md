desk-php
========

[![Build Status](https://travis-ci.org/bradfeehan/desk-php.png?branch=master)](https://travis-ci.org/bradfeehan/desk-php)
[![Coverage Status](https://coveralls.io/repos/bradfeehan/desk-php/badge.png)](https://coveralls.io/r/bradfeehan/desk-php)
[![Dependency Status](https://www.versioneye.com/user/projects/51a6bea6fa4f3d0002004335/badge.png)](https://www.versioneye.com/user/projects/51a6bea6fa4f3d0002004335)

PHP client for [Desk.com](http://desk.com) v2 API, based on
[Guzzle](http://guzzlephp.org)

**Note**: This project is currently under initial development and is
**nowhere near stable** yet. Here's a summary of the current progress:

 * Main resource operations (ListCases, etc)
    * ~~[Show](https://github.com/bradfeehan/desk-php/issues/5)~~
    * ~~[List](https://github.com/bradfeehan/desk-php/issues/3)~~
    * [Create](https://github.com/bradfeehan/desk-php/issues/8)
    * [Update](https://github.com/bradfeehan/desk-php/issues/11)
    * ~~[Delete](https://github.com/bradfeehan/desk-php/issues/13)~~
    * ~~[Search](https://github.com/bradfeehan/desk-php/issues/15)~~
 * Sub-item operations (ListCaseNotes, etc)
    * ~~[Show](https://github.com/bradfeehan/desk-php/issues/6)~~
    * ~~[List](https://github.com/bradfeehan/desk-php/issues/4)~~
    * [Create](https://github.com/bradfeehan/desk-php/issues/9)
    * [Update](https://github.com/bradfeehan/desk-php/issues/12)
    * ~~[Delete](https://github.com/bradfeehan/desk-php/issues/14)~~
 * Data type filtering
    * ~~Dates~~
    * Custom fields?
    * Resource property lists (e.g. customer email addresses, etc)
 * Resource relationships (links/embedding)
    * ~~Links to other resources return commands~~
    * ~~Embedded resources return models~~
    * Embedded model data type filtering

When most of these are complete, there'll be a v0.1 release which will
be the first tagged release.


Project Aims
------------

 * Support all API operations [documented by Desk][desk-docs]
 * Consumption of the API's provided [relationship][desk-relationships]
   functionality
    * Resources link to other related resources
    * These can be embedded to reduce the number of requests
 * PHP-friendly data types (dates represented using DateTime objects, etc)
 * 100% *unit* test coverage (using PHPUnit)
 * Additional "use-case" tests for every individual operation, which
   use documented responses as mock responses

[desk-docs]: <http://dev.desk.com/API/using-the-api/#general>
[desk-relationships]: <http://dev.desk.com/API/using-the-api/#relationships>


Installation
------------

To get this library in to an existing project, the best way is to use
[Composer](http://getcomposer.org).

1. Add `bradfeehan/desk-php` as a Composer dependency in your project's
   [`composer.json`][composer-json] file:

    ```json
    {
        "require": {
            "bradfeehan/desk-php": "dev-master"
        }
    }
    ```

2. If you haven't already, download and
   [install Composer][composer-download]:

    ```bash
    $ curl -sS https://getcomposer.org/installer | php
    ```

3. [Install your Composer dependencies][composer-install]:

    ```bash
    $ php composer.phar install
    ```

4. Set up [Composer's autoloader][composer-loader]:

    ```php
    require_once 'vendor/autoload.php';
    ```

You're done! Now the `Desk` namespace should exist and contain
everything you need to consume the Desk.com API.

[composer-json]: <http://getcomposer.org/doc/01-basic-usage.md#the-require-key>
    "More on the composer.json format"
[composer-download]: <http://getcomposer.org/doc/01-basic-usage.md#installation>
    "More detailed installation instructions on the Composer site"
[composer-install]: <http://getcomposer.org/doc/01-basic-usage.md#installing-dependencies>
    "More detailed instructions on the Composer site"
[composer-loader]: <http://getcomposer.org/doc/01-basic-usage.md#autoloading>
    "More information about the autoloader on the Composer site"


Basic Usage
-----------

The main point of entry for your app will usually be the `Desk\Client` class:

```php
$client = \Desk\Client::factory(array(
    'subdomain' => 'foo',
    'username' => 'myuser',
    'password' => 'secret',
));
```

Individual commands can be retrieved from the client and executed:

```php
$command = $client->getCommand('ShowUser');
$command->set('id', 1);
$user = $command->execute();
print $user->get('name');
// => 'John Doe'
```

There are some shortcuts which can be taken. The above is equivalent to:

```php
$command = $client->getCommand('ShowUser', array('id' => 1));
$user = $command->execute();
print $user->get('name');
// => 'John Doe'
```

...which again is the same as:

```php
$user = $client->ShowUser(array('id' => 1));
print $user->get('name');
// => 'John Doe'
```

Complex data types are (generally) converted to/from easier to use
formats. For example, dates are represented as strings over the wire
when communicating with the Desk API, but these will be converted to
PHP `DateTime` objects upon retrieval:

```php
$customer = $client->ShowCustomer(array('id' => 1));
var_dump($customer->get('created_at'));
// => object(DateTime)#209 (3) { ...
```


### Command names

The names of commands follow a strict naming convention. The type of
operation is first; this is usually one of *Show*, *List*, *Create*,
*Update*, *Delete*, or *Search*. This is combined with the resource
name (CamelCase if it's more than one word) -- for example, *Article*,
*Company*, *CustomField* etc. *List* and *Search* operations will have
a pluralised version of the resource name (e.g. *ListCompanies*,
*SearchArticles*, etc). while the other operations will have the
singular form (e.g. *ShowCompany*, *CreateArticle*, etc). The complete
list is in the service description file,
[desk.json][service-description], although it might be a bit hard to
use for this purpose due to its length.

[service-description]: <https://github.com/bradfeehan/desk-php/blob/master/lib/Desk/Client/desk.json>
    "View this file on GitHub"


### Relationships

In version 2 of the Desk API, there exists the concept of relationships
between resources. For example, a Case resource now links to the
Customer resource which created the case. This is fully supported by
this library.

#### Links

A link from one resource to another is represented by a pre-configured
command object which is ready to retrieve the target of the link. To
retrieve a command representing a link, call the `getLink()` method on
the model:

```php
$case = $client->ShowCase(array('id' => 1));
$command = $case->getLink('customer');

print $command->getName();
// => 'ShowCustomer'

print $command->get('id');
// => 1

$customer = $command->execute();

// or, more useful:
$customer = $case->getLink('customer')->execute();
```

#### Embedded Resources

The example above would require two requests -- one for the case, and
another for the customer. If, at the time of the first request, you
know that you will (or might) need to access a related resource, you
can request that the related resource be embedded into the first
response. As an example, to improve on the performance of the previous
example:

```php
$case = $client->ShowCase(array('id' => 1, 'embed' => array('customer')));
$customer = $case->getEmbedded('customer'); // no second request necessary
```

The call to `getEmbedded()` would throw an exception if we hadn't
requested the "customer" relation to be embedded at the time of the
original request to retrieve the case.


Contributing
------------

Contributions are most welcome! At this early stage of development,
I'm working hard on the items at the top of this README. At any time
I'm probably halfway through implementing (or re-implementing)
something on that list, so keep that in mind if you're planning to
start working on something -- I may already be on it.

Here's a few guidelines when coding on this project:

 * I'm trying to use best practices everywhere in this project. Hacky
   solutions are generally rejected in favor of "doing it right" (in
   general).
 * Stick to PSR-2 coding style. This involves many things I wasn't
   aware of when starting out! (e.g. one argument per line in
   multi-line function definitions)
 * Try and stick to 72/80 characters where possible (except in `.json`
   files if necessary).

With that being said, I feel like even if you have some code in a fork
which doesn't adhere to these guidelines, it could certainly still be
useful, so feel free to open a pull request anyway.
