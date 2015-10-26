<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

// Get the name of the function who called me
// I use this when debugging and I want to know which function called
// the code I am looking at. This is especially useful when debugging a 
// complex system.
class Base{
   function logMethodStart(){
      $trace = debug_backtrace();
      //var_dump($trace);
      $method = '';
      if ( count($trace) > 1 ){
         $data = $trace[1];
         if ( ! empty($data['class']) ){
            $method .= $data['class'].'::';
         }
         $method .= $data['function'];
      }
      echo "Method = \"". $method. "\"";
      echo "\n";
   }
}

class Outer extends Base {
   
   function mytest(){
   
      $this->logMethodStart();
   
   }
   
}


function functest(){
   Base::logMethodStart();
}

$o = new Outer();
$o->mytest();

functest();

Base::logMethodStart();

?>