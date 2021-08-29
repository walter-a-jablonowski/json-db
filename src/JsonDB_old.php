<?php

namespace WAJ\Lib\Data\JsonDB;


/*@

A simple file based JSON DB

Minimal version just opens file in constructor. Multiple:
use more than one object. In-file key via methods.

- no type hinting, be conpatible old versions

*/
class JsonDB_old implements \Iterator  /*@*/
{

  private $ident;
  private $data;
  private $pos = 0;

  private $buffer;

  /*@

  */
  public function __construct( $ident )  /*@*/
  {
    $this->ident = $ident;
    $this->data  = json_decode( file_get_contents("$ident.json"), true );    
  }

  /*@
  
  Has
  
  */
  public function has( $key )  /*@*/
  {
    return isset( $this->data[$keys] );
  }

  /*@
  
  Read

  Returns sub elem of data. Use also for
  interating sub elems. See also iterator below
  for main level.
  
  */
  public function __get( $key )  /*@*/
  {
    if( ! isset( $this->data[$key] ))
      throw new \Exception('unknown key');

    return $this->data[$key];
  }

  /*@
  
  Access value directly
  
  might also work for records using '0.field'

  */
  public function get( $key )  /*@*/
  {
    $a = explode('.', $key);

    $d =& $this->data;
    foreach( $a as $k )
      $d =& $d[ $k ];

    return $d;
  }

  /*@

  Filter

  function($v, $k) {
    return ...;
  }
  */
  public function filter( $function )  /*@*/
  {
    $r = array_filter( $this->data, $function, ARRAY_FILTER_USE_BOTH);
    $this->buffer = $r;

    return $r;
  }

  /*@

  Sort

  by value keep keys

  ˋˋˋ
  function ($a, $b) {

  -1 $a < $b
   0 $a = $b
   1 $a > $b
  ˋˋˋ

  function($a, $b) {
    if ($a == $b)
      return 0;
    return ($a < $b) ? -1 : 1;
  }
  */
  public function sort( $function )  /*@*/
  {
    // TASK: error if buffer empty
  
    uasort( $this->buffer, $function);

    return $this->buffer;
  }

  /*@
  
  Create
  
  */
  public function add( $val, $key = null )  /*@*/
  {
    if( ! $name )
      $this->data[] = $val;
    else
      $this->data[$key] = $val;
  }

  /*@

  Update
  
  key = `symbol.field ...`
  
  */
  public function set( $key, $val )  /*@*/
  {
    $a = explode('.', $key);

    $d =& $this->data;
    foreach( $a as $k )
      $d =& $d[ $k ];

    $d = $val;
  }

  /*@
  
  Delete

  just use array func
  
  */
  public function del( $key )  /*@*/
  {
    unset( $this->data[$key] );
  }

  /*@
  
  Special: shift
  
  */
  public function shift()  /*@*/
  {
    array_shift($this->data);
  }

  /*@
  
  Save
  
  */
  public function save()  /*@*/
  {
    file_put_contents("db/$this->ident.json", json_encode($this->data, JSON_PRETTY_PRINT));    
  }


  // Iterator

  public function rewind() {
    $this->pos = 0;
  }

  public function current() {
    return $this->data[$this->pos];
  }

  public function key() {
    return $this->pos;
  }

  public function next() {
    ++$this->pos;
  }

  public function valid() {
    return isset($this->data[$this->pos]);
  }
}

?>