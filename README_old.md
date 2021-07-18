# Json DB

**Minimal JSON db that uses file sys folders and files**

```
composer update
```

**Usage**

```php
$coin = new JsonDB('prices/kucoin_ccxt/BTC-USDT');

// List table use

foreach( $coins as $coin )
  var_dump( $coin );

// Use when symbol is key in json (coin data, similar)
// read could basically also be done using array syntax

$val = 'hmpf';

$coins->set('0.someval', $val);
print $coins->get('0.someval');
```

**Alternatives:** https://sleekdb.github.io, https://github.com/Lazer-Database/Lazer-Database


## LICENSE

Copyright (C) Walter A. Jablonowski 2021, MIT [License](LICENSE)

Licenses of third party software used in samples see [credits](credits.md).

[Privacy](https://walter-a-jablonowski.github.io/privacy.html) | [Legal](https://walter-a-jablonowski.github.io/imprint.html)