<?php

/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

// Get a list of class constants

class MyTestClass{
   const TESTVAR1 = 1001;
   const TESTVAR2 = 1002;
   const TESTSTR1 = 'hello';
}


$rc = new ReflectionClass('MyTestClass');
$v = $rc->getConstants();  

asort($v);// sort by value
//ksort($v);// sort by key

foreach ( $v as $name => $value){
   echo "$name => $value\n";
}