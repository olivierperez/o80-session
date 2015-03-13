# o80-session

This PHP library provide some security around the stealing of session.

[![Built on travis-ci.org](https://travis-ci.org/olivierperez/o80-session.svg)](https://travis-ci.org/olivierperez/o80-session)

# How-to

## Installation

With [Composer](http://getcomposer.org/), you simply need to require [`o80/session`](https://packagist.org/packages/o80/session):

```json
{
...
    "require": {
        "o80/session": "dev-master"
    }
...
}
```

## Starting session

Replace `session_start();` by `$session = new o80\Session(); $session->start();`.

## Using session

Nothing changed :

```php
// Writing
$_SESSION['x'] = 'foo';

// Reading
$bar = $_SESSION['x'];
```

# Contribution

Just fork the project, make your changes, ask for pull request ;-).
