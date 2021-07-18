<?php

namespace WAJ\Lib\Data\JsonDB;


/*@

A simple file based JSON DB

- no type hinting, be compatible old versions
- no internal data is better for threading
- outside just use array func

*/
class JsonDB  /*@*/
{

  private $dbFld;
  private $buffer;


  /*@

  */
  public function __construct( $dbFld )  /*@*/
  {
    $this->dbFld = rtrim($dbFld, "/ ") . '/';
  }


  /*@
  
  Has
  
  */
/*
  public function has( $key )  /*@* /
  {
    return isset( $this->data[$keys] );
  }
*/


  /*@
  
  ensureFolder()
  
  - Alternative see save()
  
  ARGS:
    string key: `main.sub. ...` logical tree path, consists of folder and file, no ext

  */
  public function ensureFolder( $key )  /*@*/
  {
    $dir = explode('.', $key);
    array_pop($dir);

    $dir = $this->base . implode('/', $dir);

    if( ! is_dir($dir) )
      // TASK: maybe use @ if problems with existing
      mkdir($dir, 0777, true);
  }


  /*@

  Read

  - Also use result for interating
  - '0.field' works also

  ˋˋˋphp
  $obj = $db->get('main.sub ...');
  ˋˋˋ

  ARGS:
    string key:    `main.sub. ...` logical tree path, consists of folder, file (no ext) and json key names
    mixed default: 
  
  RETURNS:
    - Folder: empty array if folder has no json files

  */
  public function query( $key, $default = null )  /*@*/
  {
    // TASK: use default ?
    // Key is dir: merge and return all json files

    $dir = $this->base . str_replace('.', '/', $key);

    if( is_dir( $dir ))
    {
      $a = scandir($dir);
      $data = [];
      
      foreach( $a as $fil)

        if( strpos($fil, '.json') === strlen($fil)-6)
        {
          $s = str_replace('.json', '', $fil);
          $data[$s] = json_decode( file_get_contents($this->base . $fil), true );
        }

      $this->buffer = $data;
      return $this;
    }
    else
    {
      $subkeys = [];
      $a = explode('.', $key);

      while( $a )
      {
        $s = implode('/', $a);

        // Key is file

        if( is_file( $this->base . $s))
        {
          $data = json_decode( file_get_contents($this->base . $s), true );
        
          // Return subkey if any in key string

          if( ! $subkeys )
          {
            $this->buffer = $data;
            return $this;
          }
          else
          {
            $d =& $data;
            foreach( $subkeys as $k )
              $d =& $d[ $k ];

            $this->buffer = $data;
            return $this;
          }
        }

        array_unshift( $subkeys, array_pop($a));
      }
    }

    // Use default or error

    if( $default )
    {
      $this->buffer = $data;
      return $this;
    }
    else
      throw new \Exception('unknown key');
  }


  /*@

  Filter

  ˋˋˋphp
  function($v, $k) {
    return ...;
  }
  ˋˋˋ

  */
  public function filter( $function )  /*@*/
  {
    $r = array_filter( $this->buffer, $function, ARRAY_FILTER_USE_BOTH);
    $this->buffer = $r;

    return $this;
  }


  /*@

  Sort

  by value keep keys

  ```php
  function ($a, $b) {

  -1 $a < $b
   0 $a = $b
   1 $a > $b

  function($a, $b) {
    if($a == $b)
      return 0;
      return ($a <= $b) ? -1 : 1;
  }
  ```

  */
  public function sort( $function )  /*@*/
  {
    // TASK: error if buffer empty
  
    uasort( $this->buffer, $function);

    return $this;
  }


  /*@

  Get() returns current result

  */
  public function get()  /*@*/
  {
    return $this->buffer;
  }


  /*@

  Save
  
  - if the folder is missing, a file will be made under db root using first key, all sub keys will be json keys
  - shortcut für ensureFile() `main.sub. .../sub.sub. ...`
  - data will merge with existing files

  ARGS:
    string key: `main.sub ...`
    array val:  data  

  */
  public function save( $key, $val )  /*@*/
  {
    // Ensure explicit given folder using ˋ/ˋ in key exists

    if( strpos($key, '/') !== false )
    {
      $a = explode('/', $key);
      $this->ensureFolder( implode('/', $a[0]) );
      $key = str_replace('/', '.', $key);
    }

    // Find file and keys

    [$keyHas, $file, $subkeys] = $this->identify($key);

    // Save json, add json subkeys if any left
    // merge with existing file

    if( ! $subkeys )
    {
      if( $keyHas == 'existingFile' )
      {
        $val = array_merge_recursive(
          json_decode( file_get_contents( $file, true)), $val
        );
      }
       
      file_put_contents( $file, json_encode($val, JSON_PRETTY_PRINT));
    }
    else
    {
      $a = []; $d =& $a; // pointer most inner key
      foreach( $subkeys as $subkey )
      {
        $d[$subkey] = [];
        $d =& $d[$subkey];
      }

      $d = $val;

      if( $keyHas == 'existingFile' )
      {
        $a = array_merge_recursive(
          json_decode( file_get_contents( $file, true)), $a
        );
      }

      file_put_contents( $file, json_encode($a, JSON_PRETTY_PRINT));
    }
  }


  /*@
  
  Delete
  
  */
  public function del( $key )  /*@*/
  {

  }


  /*@
  
  Special: shift
  
  */
  public function shift()  /*@*/
  {
    // load, shift, save
    // array_shift();
  }


  /*@

  Helper: split a key, identify existing file or folder

  RETURNS: |

    [keyHas='file'|'folder', filePath, (array)subkeys]
    where filePath is path of existing file or file 2 be created

  */
  private function identify( $key )  /*@*/
  {
    $keyHas = '';
    $file = '';
    $subkeys = [];

    $a = explode('.', $key);

    while( $a )
    {
      $s = implode('/', $a);

      if( is_file( $this->base . "$s.json"))
      {
        $keyHas = 'existingFile';
        $file = $this->base . "$s.json";
        break;
      }
      elseif( is_dir( $this->base . $s))
      {
        $keyHas = 'existingFld';
        $file = implode('/', $a + array_shift($subkeys)) . '.json';
        break;
      }

      array_unshift( $subkeys, array_pop($a));
    }
  
    return [$keyHas, $file, $subkeys];
  }

}

?>
