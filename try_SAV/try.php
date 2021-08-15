<?php

use WAJ\Lib\Data\JsonDB\JsonDB;

require '../src/JsonDB.php';


$coins = new JsonDB('prices/kucoin_ccxt/BTC-USDT');

// List table use

foreach( $coins as $coin )
  var_dump( $coin );

// Use when symbol is key in json (coin data, similar)
// read could basically also be done using array syntax

$val = 'hmpf';

$coins->set('0.someval', $val);
print $coins->get('0.someval');

?>