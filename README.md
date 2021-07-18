# Json DB

**Minimal JSON db that uses file sys folders and files**

***currently in dev***

recently ... done just for fun in coffee breaks while working on more important weekend projects;-)

```
composer update
```


## Concepts

We ignore the fact that information in file system is organized in files, folders and in this case json keys. Instead we handle
all as one big logical tree of information. File and folder names are hierarchical keys that just continue within json files as
json keys.

Information is accessed by a ˋhierarchical.keyˋ. The library will take care of what needs 2 be loaded.


**Read**

When key is | ->query() will load
----------- | --------------------------
dir         | all json files in that folder merged
file        | full file
json key    | part of file


## Usage

```php
$db = new JsonDB('some/db');

$prices = $db->query('prices.kucoin_ccxt.BTC-USDT')

  ->filter( fn($v, $k) => floatval($v['price']) < 50.0 )
  ->sort( fn($a, $b) => // uses uasort()
      $a['price'] == $b['price'] ? 0 :
      $a['price'] <  $b['price'] ? -1 : 1
    )
  ->get();

foreach( $prices as $price )
  var_dump( $price );

// Save

$db->save('0.someval', 'myval');

// Join stuff: a join is just a loop joining 2 arrays
```

**Alternatives:** https://sleekdb.github.io, https://github.com/Lazer-Database/Lazer-Database


## Advanced

maybe ...

- [ ] get('first') get(idx) saves some methods: no first()
- [ ] save('first') save(idx)
- [ ] delete('last') delete(idx)
- [ ] We could add a method that returns a new "sub-db" for queried information, that can be filtered again e.g. in loops
- [ ] Finish delete()
- [ ] Add more file types, rename the project - fwd from this
  - plain text is also data available under a key
  - yml files
  - md files
  - images
  - ...
- [ ] maybe implement has()
- [ ] maybe also load subfolders when my/folder/*

this currently is an undefined behaviour

- [ ] folder with json subkeys alone could mean del in all files (no subkey for file just folder)


## LICENSE

Copyright (C) Walter A. Jablonowski 2021, MIT [License](LICENSE)

Licenses of third party software used in samples see [credits](credits.md).

[Privacy](https://walter-a-jablonowski.github.io/privacy.html) | [Legal](https://walter-a-jablonowski.github.io/imprint.html)
