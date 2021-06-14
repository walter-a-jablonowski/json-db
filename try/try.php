<?php

use WAJ\Lib\Data\JsonDB\JsonDB;

require '../src/JsonDB.php';


$coin = new JsonDB('prices/kucoin_ccxt/BTC-USDT');

// List table use

var_dump( $coin->data[0]['price'] );

// Use when symbol is key in json (coin data, similar)
// read could basically also be done using array syntax above

$val = 'hmpf';

$coin->setVal('0.someval', $val);
print $coin->getVal('0.someval');

?>