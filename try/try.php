<?php

use WAJ\Lib\Data\JsonDB\JsonDB;

require '../src/JsonDB.php';


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

// $db->save('0.someval', 'myval');

?>