# tdt/negotiators

[![Build Status](https://travis-ci.org/tdt/negotiators.png?branch=master)](undefined)

Content and language negotiation written in PHP: GET parameters will overwrite accept header. Support for logging (monolog).

# Installation

Install as a requirement using composer:

1. Add a composer.json in your root

2. Add a requirement:

```json
{ "require" : { "tdt/negotiators" : "dev-master" } }
```

3. Install composer: http://getcomposer.com

4. run "composer install"

5. include vendor/autoload.php

# Usage

```php
$cn = new \tdt\negotiators\ContentNegotiator();
$format = $cn->pop();
$default_format = "json";
/*
* formatExists checks if we can format something in 
* the format requested.
*/

while (!$this->formatAllowed($format) && $cn->hasNext()) {
    $format = $cn->pop();
    if ($format == "*") {
        $format == $default_format;
     }
}
```


# Testing

Using phpunit:

```bash
$ phpunit tests

```