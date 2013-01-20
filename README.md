# tdt/negotiators

[![Build Status](https://travis-ci.org/tdt/negotiators.png?branch=master)](undefined)

Content and language negotiation written in PHP: GET parameters will overwrite accept header. Support for logging (monolog).

# Installation

Install as a requirement using composer:

1. Add a composer.json in your root

2. Add a requirement:

```json
{ 
  "require" : { 
     "tdt/negotiators" : "1.0.*" 
  }
}
```

3. Install composer: http://getcomposer.com

4. run "composer install"

5. include vendor/autoload.php

# Usage

```php
$cn = new \tdt\negotiators\ContentNegotiator();
$format = $cn->pop();
$default_format = "json";

// $this->formatAllowed is a function you have to define yourself
while (!$this->formatAllowed($format) && $cn->hasNext()) {
    $format = $cn->pop();
}

if(! $this->formatAllowed($format)){
     throw new Exception("Could not find an appropriate formatter.");
}

// use $format further on
```


# Testing

Using phpunit:

```bash
$ phpunit tests
```