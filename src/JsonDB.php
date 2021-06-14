<?php

namespace WAJ\Lib\Data\JsonDB;


/*@

A simple file based JSON DB

Minimal version just opens file in constructor. Multiple:
use more than one object. In-file key via methods.

- no type hinting, be conpatible old versions

*/
class JsonDB  /*@*/
{

  private $ident;
  private $data;

  /*@

  */
  public function __construct( $ident )  /*@*/
  {
    $this->ident = $ident;
    $this->data  = json_decode( file_get_contents("$ident.json"), true );    
  }

  /*@
  
  Read
  magic func just returns data
  
  */
  public function __get( $name )  /*@*/
  {
    if( $name !== 'data' )
      throw new \Exception('currently data only');

    return $this->data;
  }

  /*@
  
  Create
  
  */
  public function add( $rec )  /*@*/
  {
    $this->data[] = $rec;
  }

  /*@
  
  Update

  just use array func
  
  */
  public function upd( $name, $rec )  /*@*/
  {
    $this->data[$name] = $rec;
  }

  /*@
  
  Delete

  just use array func
  
  */
  public function del( $name )  /*@*/
  {
    unset( $this->data[$name] );
  }

  /*@
  
  Special: shift
  
  */
  public function shift()  /*@*/
  {
    array_shift($this->data);
  }

  /*@
  
  Access value directly
  
  might also work for records using '0.field'
  
  */
  public function getVal( $name )  /*@*/
  {
    $a = explode('.', $name);

    $d =& $this->data;
    foreach( $a as $key )
      $d =& $d[ $key ];

    return $d;
  }
    
  /*@

  Set value directly
  
  `name = symbol.field ...`
  
  */
  public function setVal( $name, $val )  /*@*/
  {
    $a = explode('.', $name);

    $d =& $this->data;
    foreach( $a as $key )
      $d =& $d[ $key ];

    $d = $val;
  }

  /*@
  
  Save
  
  */
  public function save()  /*@*/
  {
    file_put_contents("db/$this->ident.json", json_encode($this->data, JSON_PRETTY_PRINT));    
  }

}

?>
