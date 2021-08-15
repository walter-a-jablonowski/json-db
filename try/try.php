<?php

use WAJ\Lib\Data\JsonDB\JsonDB;

require '../src/JsonDB.php';


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

// $db->save('0.someval', 'myval');

?>