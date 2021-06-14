# Json DB

**Minimal JSON db that uses file sys folders and files**

```
composer update
```

**Usage**

```php
$coin = new JsonDB('prices/kucoin_ccxt/BTC-USDT');

// List table use

var_dump( $coin->data[0]['price'] );

// Use when symbol is key in json (coin data, similar)
// read could basically also be done using array syntax above

$val = 'hmpf';

$coin->setVal('0.someval', $val);
print $coin->getVal('0.someval');
```

**Alternatives:** https://sleekdb.github.io, https://github.com/Lazer-Database/Lazer-Database


## LICENSE

Copyright (C) Walter A. Jablonowski 2021, MIT [License](LICENSE)


[Privacy](https://walter-a-jablonowski.github.io/privacy.html) | [Legal](https://walter-a-jablonowski.github.io/imprint.html)