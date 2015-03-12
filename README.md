# o80-session

This PHP library provide some security around the stealing of session.

# How-to

## Starting session

Replace `session_start();` by `o80\Session::start();`.

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
