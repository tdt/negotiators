negotiators
===========

A repository for content and language negotiation written in PHP

# Usage

```php
$cn = new ContentNegotiator();
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
