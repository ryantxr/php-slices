<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

// from this nested array I want to extract only the user_id's
$arr = array(
    array('User' => array('user_id' => 100, 'device_id' => 2100, 'name' => 'a', 'role' => 'admin')),
    array('User' => array('user_id' => 101, 'device_id' => 2101, 'name' => 'b', 'role' => 'admin')),
    array('User' => array('user_id' => 102, 'device_id' => 2102, 'name' => 'c', 'role' => 'admin')),
    array('User' => array('user_id' => 103, 'device_id' => 2103, 'name' => 'd', 'role' => 'admin')),
    array('User' => array('user_id' => 104, 'device_id' => 2104, 'name' => 'e', 'role' => 'admin')),
    array('User' => array('user_id' => 105, 'device_id' => 2105, 'name' => 'f', 'role' => 'admin')),
    );

// this is how to do it using anonymous function
$new = array_map(function($n){return $n['User']['user_id'];}, $arr);

// Use a global function
function map_func($n){
    return $n['User']['user_id'];
}
$new2 = array_map('map_func', $arr);

// Call a static function in a class
class MyMapper{
    static function map_func($n){
        return $n['User']['user_id'];
    }
}

$callable = array('MyMapper', 'map_func');
$new3 = array_map($callable, $arr);

// Using a function from an object.
$myMapperObject = new MyMapper;
$callable = array($myMapperObject, 'map_func');
$new4 = array_map($callable, $arr);

echo "=========================\n";
print_r($new);
print_r($new2);
print_r($new3);
print_r($new4);
echo "=========================\n";