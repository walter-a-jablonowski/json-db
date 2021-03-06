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

Information is accessed by a ˋhierarchical.keyˋ. The library will take care of what needs to be loaded.

So we don't need to deal with how information is organised, we can just use it. If you save new information the library will use a standard behaviour to determine where it should be saved (see below). If you want to have control over the structure in the file system yourself, the only thing you need to do is make some folders and emtpy json files. The library will use existing files. Usage is quite intuitive: `$data = $db->query('things.some_thing')`.

**Read**

When key is | ->query() will load
----------- | --------------------------
json key    | part of file
file        | full file
dir         | all json files in that folder merged


**Write**

save() will split the given key in parts like ˋfolder.part.file-part.json.partˋ.
It looks for existing folders and file that might already have data. Behaviour is

When key has             | and json is | ->save() will
-------------------------|-------------|---------------------------
existing json key (1)    |             | merge data under key
existing file name (1)   |             | merge data in file
existing dir only        | object (2)  | forbidden cause file name missing
existing dir only        | array       | split data in single files (numeric name)
existing dir & more keys |             | use first key behind dir as filename
non-existing key (3) (4) |             | make a new folder and file from key

Comments

- (1) If it has more keys, these will be used as json keys
- (2) Similar javascript: object = an array with string keys, array = array with numeric keys
- (3) If you want use some of the keys as json keys call ensureFolder() frist
      TASK or ensureFile()?
- (4) If you want split an array in multiple files use a loop and call ˋ->save()ˋ multiple times

DEV

- Do we have a del files func? or just folder?


## Usage

```php
$db = new JsonDB('some/db');

$data = $db->query('things.some.thing')

  ->filter( fn($v, $k) => floatval($v['price']) < 50.0 )
  ->sort( fn($a, $b) => // uses uasort()
      $a['price'] == $b['price'] ? 0 :
      $a['price'] <  $b['price'] ? -1 : 1
    )
  ->get();

foreach( $data as $rec )
  var_dump( $rec );

// Save

$db->save('0.someval', 'myval');

// Join stuff: a join is just a loop joining 2 arrays
```

**Alternatives:** https://sleekdb.github.io, https://github.com/Lazer-Database/Lazer-Database


## Advanced

maybe ...

- [ ] When a file contains array and we save obj, we might need solve this in some way
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
