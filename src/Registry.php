<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

class Registry{
   static $entry = array();
   function set($k, $v){
      self::$entry[$k] = $v;
   }
   
   function get($k){
      if ( isset(self::$entry[$k]) ) {
         return self::$entry[$k]      	 ;
      }
      return null;
   }
}

// Set a value
Registry::set('somevar', 'somevalue');
// Get a value
$somevar = Registry::get('somevar');

// Store objects if you want
$pdo = new PDO($dsn, $dbUsername, $dbPassword);
Registry::set('pdo', $pdo);