<?php

namespace WAJ\Lib\Data\JsonDB;


/*@

A simple file based JSON DB

USAGE:
  - outside just use array func

DEV-COMMENTS:
  - no type hinting, be compatible old versions
  - no internal data is better for threading

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

  Read

  ˋˋˋphp
  $obj = $db->get('main.sub ...');
  ˋˋˋ

  USAGE:
    - also use result for interating
    - '0.field' works also

  ARGS:
    string key:     `main.sub. ...` logical tree path, consists of folder, file (no ext) and json key names
    mixed  default: 
  
  RETURNS:
    - Folder: empty array if folder has no json files

  DEV-COMMENTS:
    - was first function, makes no use of $this->identify()
    - we cont nesseccarily need it here cause has no json keys if folder (simpler)
    - could be used in file portion

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
      $jsonKeys = [];
      $a = explode('.', $key);

      while( $a )
      {
        $s = implode('/', $a);

        // Key is file

        if( is_file( $this->base . $s))
        {
          $data = json_decode( file_get_contents($this->base . $s), true );
        
          // Return subkey if any in key string

          if( ! $jsonKeys )
          {
            $this->buffer = $data;
            return $this;
          }
          else
          {
            $d =& $data;
            foreach( $jsonKeys as $k )
              $d =& $d[ $k ];

            $this->buffer = $data;
            return $this;
          }
        }

        array_unshift( $jsonKeys, array_pop($a));
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
    return ...;
  }
  ˋˋˋ

  */
  public function filter( $function )  /*@*/
  {
    $r = array_filter( $this->buffer, $function, ARRAY_FILTER_USE_BOTH);
    $this->buffer = $r;

    return $this;
  }


  /*@

  Sort: by value keep keys

  */
  public function sort( $function )  /*@*/
  {
    // TASK: error if buffer empty
  
    uasort( $this->buffer, $function);

    return $this;
  }


  /*@

  Get: returns current result

  */
  public function get()  /*@*/
  {
    return $this->buffer;
  }


  /*@

  Save
  
  - if the folder is missing, a file will be made under db root using first key, all sub keys will be json keys
  - shortcut für ensureFolder() `main.folder/json.key`
  - data will merge with existing files

  ARGS:
    string key: `main.sub ...`
    array  val: data

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

    [$keyHas, $file, $jsonKeys] = $this->identify($key);

    // Save json

    if( ! $jsonKeys )
    {
      // Merge with eisting file

      if( $keyHas === 'existingFile' )
      {
        $val = array_merge_recursive(
          json_decode( file_get_contents( $file, true)), $val
        );
      }
       
      file_put_contents( $file, json_encode($val, JSON_PRETTY_PRINT));
    }
    else
    {
      // Add left json keys result

      $a = []; $d =& $a; // pointer most inner key
      foreach( $jsonKeys as $jsonKey )
      {
        $d[$subkey] = [];
        $d =& $d[$jsonKey];
      }

      $d = $val;

      // Merge with eisting file

      if( $keyHas === 'existingFile' )
      {
        $a = array_merge_recursive(
          json_decode( file_get_contents( $file, true)), $a
        );
      }

      file_put_contents( $file, json_encode($a, JSON_PRETTY_PRINT));
    }
  }


  /*@
  
  Delete (currently optional)
  
  */
/*
  public function del( $key )  /*@* /
  {
    [$keyHas, $file, $jsonKeys] = $this->identify($key);

    if( $keyHas !== 'existingFile' && $keyHas !== 'existingFld' )
      throw new \Exception('no existing file');

    if( $keyHas === 'existingFile' )
    {
      if( ! $jsonKeys )
        unlink( $file );
      else
      {
      }
    }
    elseif( $keyHas === 'existingFld' )
    {
      // basename($file)
    }
  }
*/


  /*@
  
  Special: shift() a value and save (coins)

  TASKS:
    - maybe move
  
  */
  public function shift( $kry, $default = null )  /*@*/
  {
    $a = $this->load($key, $default);
    array_shift($a);
    $this->save($key, $a);
  }


  /*@
  
  Ensure folder: use for folders in db
 
  - alternative see save()
  
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

  Helper: identify() split a key, identify existing file or folder

  RETURNS: |

    [keyHas='file'|'folder', filePath, (array)jsonKeys]
    where filePath is path of existing file or file 2 be created

  */
  private function identify( $key )  /*@*/
  {
    $keyHas = '';
    $file = '';
    $jsonKeys = [];

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
        $file = implode('/', $a + array_shift($jsonKeys)) . '.json';
        break;
      }

      array_unshift( $jsonKeys, array_pop($a));
    }
  
    return [$keyHas, $file, $jsonKeys];
  }

}

?>
