# wesender-php

Officiële PHP SDK voor de [WeSender](https://wesender.nl) e-mail API.

## Installatie

```bash
composer require wesender/wesender
```

## Gebruik

```php
<?php
use Wesender\Wesender;

$ws = new Wesender($_ENV['WS_API_KEY']);

// E-mail versturen
$result = $ws->sendEmail([
    'from'    => 'noreply@joudomein.nl',
    'to'      => ['klant@voorbeeld.nl'],
    'subject' => 'Welkom!',
    'html'    => '<h1>Hallo!</h1>',
]);
echo $result['id'];

// Domeinen bekijken
$domains = $ws->listDomains();

// Batch versturen
$results = $ws->sendBatch([
    ['from' => 'noreply@joudomein.nl', 'to' => ['a@voorbeeld.nl'], 'subject' => 'A', 'html' => '<p>A</p>'],
    ['from' => 'noreply@joudomein.nl', 'to' => ['b@voorbeeld.nl'], 'subject' => 'B', 'html' => '<p>B</p>'],
]);
```

## Vereisten

PHP 8.0+, ext-curl, ext-json

## Links

- [Documentatie](https://wesender.nl/docs/sdks/php)
- [Issues](https://github.com/wesender/wesender-php/issues)
