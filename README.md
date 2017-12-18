# Yutta MF PHP Framework

[![Latest Stable Version](https://poser.pugx.org/tobymaxham/yuttamf/v/stable)](https://packagist.org/packages/tobymaxham/yuttamf)
[![Total Downloads](https://poser.pugx.org/tobymaxham/yuttamf/downloads)](https://packagist.org/packages/tobymaxham/yuttamf)
[![Monthly Downloads](https://poser.pugx.org/tobymaxham/yuttamf/d/monthly)](https://packagist.org/packages/tobymaxham/yuttamf)
[![Latest Unstable Version](https://poser.pugx.org/tobymaxham/yuttamf/v/unstable)](https://packagist.org/packages/tobymaxham/yuttamf)
[![License](https://poser.pugx.org/tobymaxham/yuttamf/license)](https://packagist.org/packages/tobymaxham/yuttamf)

The Yutta MF PHP Framework is a simple framework that halps you to write simple web applications or APIs.


## Installation

The best way to install Yutta MF is to use the [Composer](https://getcomposer.org/) package manager.

```sh
$ composer require tobymaxham/yuttamf
```

With this command Yutta and all required dependencies will be installed.
For a list of all dependencies please scroll down.


## Usage

Now you can create an `index.php` file and put the following example.

```php
<?php

// loads the composer autoloader
require 'vendor/autoload.php';

// registers a new Yutta Application instance,
// where __DIR__ is the root path above the vendor
$app = new \Yutta\Application(__DIR__);

$app->start();
```

For lazy developers you wont need to create a new Application instance.

```php
<?php

require 'vendor/autoload.php';

app()->route()->get('/', function() {
    return 'Hello World!';
});
```

You may quickly test this using the built-in PHP server:
```bash
$ php -S localhost:8000
```

Going to ´http://localhost:8000` will now display "Hello world!".


## Dependencies

The Yutta MF PHP Framework includes
- the Laravel database extensions ([illuminat/database](https://github.com/illuminate/database))
- the environment loader PHP dotenv ([vlucas/phpdotenv](https://github.com/vlucas/phpdotenv))
- the Symfony HttpFoundation Component ([symfony/http-foundation](https://github.com/symfony/http-foundation))
- the Twig Template Engine ([twig/twig](https://github.com/twigphp/Twig))
