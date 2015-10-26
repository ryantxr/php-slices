<?php

/*
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012

This code implements a sort of super late binding using a
factory and a facade.
The factory creates facade objects which are just wrappers
around the real object. When I ask the factory for an object
it gives me the facade. Only when I try to access the object
methods does it create the actual object.

The facade behaves (or is supposed to) the same as the object
it is wrapping.

One shortcoming of this implementation is that it only intercepts
method calls. Attempts to access member variables will fail.
Need to implement __get and __set to handle this.

*/

/*
 * I made a simple log/output class so I don't use echo/print
 * in the actual code.
 */
class Log{
    static function head($s){
        echo "------ $s ------\n";
    }
    static function debug($s){
        echo sprintf("<< %s >>\n", $s);
    }
    static function out($s){
        echo sprintf("       %s\n", $s);
    }
    static function br(){
        echo "--------------\n";
    }
}

/*
 * A wrapper around an actual class.
 * Will pass through all functions and variables
 * as if it is the real underlying object.
 */
class Facade {
    
    private $object = null;
    private $wrappedClass;
    private $constructorArgs;
    private $properties;

    public function __construct($cls, $args = null){
        Log::debug(__METHOD__ . "($cls)");

        $this->wrappedClass = $cls;
        $this->constructorArgs = $args;
    }

    // 
    private function makeDynamicObjectInstance(){
        if ( ! $this->object ){
            if ( ! $this->constructorArgs ){
                Log::debug('Facade constructing new '.$this->wrappedClass);
                $this->object = new $this->wrappedClass();
            }
            else{
                Log::debug('Facade constructing new '.$this->wrappedClass . '(' . implode(', ', $this->constructorArgs) . ')');
                $reflect = new ReflectionClass($this->wrappedClass);
                $this->object = $reflect->newInstanceArgs($this->constructorArgs);
            }
            if ( $this->properties && is_array($this->properties) ){
                foreach($this->properties as $varname => $value){
                    $this->object->$varname = $value;
                }
            }
        }
    }

    // I have to make this funciton public to allow the factory to set it if needed.
    public function facade_setProperties($properties){
        $this->properties = $properties;
    }

    public function __call($name, $args){
        if ( ! $this->object ) $this->makeDynamicObjectInstance();
        Log::debug('invoking ' . $this->wrappedClass . '::' . $name . "() via Facade")  ;

        if ( ! method_exists($this->object, $name) ){
            $trace = debug_backtrace();
            trigger_error('Call to undefined method ' . $this->wrappedClass . '::' . $name . '() in ' . $trace[1]['file'] . ' on line ' . $trace[1]['line'],
                E_USER_ERROR);
        }

        return call_user_func_array (array($this->object, $name), $args);

    }

    public function __get($name){
        Log::debug(__METHOD__."($name)");
        if ( ! $this->object ) $this->makeDynamicObjectInstance();

        return $this->object->$name; // FIXME: will crash if doesn't exist
    }
    public function __set($name, $val){
        Log::debug(__METHOD__."($name)");
        if ( ! $this->object ) $this->makeDynamicObjectInstance();
        $this->object->$name = $val;
    }

}

class Factory{

    /*
    I am telling the factory what objects I want it to make for me.
    I can put any objects in here I want. They do not need to extend
    any base class.
    */
    static $def = array(
        'simple1' => array('class' => 'Simple'),
        'simple2' => array('class' => 'Simple'), // I can make another one with a different reference name
        'simple3' => array('class' => 'Simple',
            // I can add any properties I want
            'properties' => array(
                'myvar1' => 'myvar1-value',
                'temp' => '56',
                'humidity' => 53,
                )
            ),
        'memnon' => array('class' => 'Greek',
            'constructorArgs' => array('Memnon') // I can make it call the constructor with arguments
            ),
        'achilles' => array('class' => 'Greek',
            'constructorArgs' => array('Achilles')
            ),
        'imhotep' => array('class' => 'Egyptian',
            'constructorArgs' => array('Imhotep')
            ),
        'jesse' => array('class' => 'Friend',
            'constructorArgs' => array('Jesse'),
            'properties' => array(
                'car' => array('@ref' => 'jesse-car'),
                'length' => 6.5,
                'status' => 1,
                'sex' => 'm',
                'weight' => 150,
                )
            ),
        'aldo' => array(
            ),
        'car1' => array('class' => 'Car', 
            'constructorArgs' => array('blue')
            ),
        'car2' => array('class' => 'Car', 
            'constructorArgs' => array('blue')
            ),
        'jesse-car' => array('class' => 'Car', 
            'constructorArgs' => array('red')
            ),
        'memnons-car' => array('class' => 'Car', 
            'constructorArgs' => array('purple')
            ),
    );
    static $objects = array();

    // I will call this method when I want an object
    // I don't have to care if it is instantiated yet.
    // FIXME: calling this with a name that isn't in the factory will fail.
    static function getObject($name){
        Log::debug(__METHOD__."($name)");
        if ( isset(self::$objects[$name]) ) return self::$objects[$name];
        $class = self::$def[$name]['class'];

        if ( isset(self::$def[$name]['constructorArgs']) ){
            self::$objects[$name] = new Facade($class, self::$def[$name]['constructorArgs']);
        }       
        else{
            self::$objects[$name] = new Facade($class);
        }
        if ( isset(self::$def[$name]['properties']) && self::$def[$name]['properties'] ){
            $props = array();
            foreach (self::$def[$name]['properties'] as $key => $value) {
                if ( is_array($value) && count($value) > 0 &&  array_key_exists('@ref', $value) ){
                    Log::debug('Reference ' . $value['@ref']);
                    // is it a reference to self?
                    if ( $value['@ref'] == $name ){
                        Log::debug('reference to self');
                        $props[$key] = self::$objects[$name];
                    }
                    else{
                        Log::debug('reference to other object ' . $value['@ref'] );
                        $props[$key] = self::getObject($value['@ref']);
                    }
                }
                else $props[$key] = $value;
            }
            self::$objects[$name]->facade_setProperties($props);
        }
        return self::$objects[$name];
    }
}

class Simple{
    // a simple class to demonstrate classes with no constructor
    public $var = 'something';
}

// Now I will make some classes to play with the factory/facade
abstract class Person{
    protected $name = null;
    public $sex;
    private $car = null;
    public $weight;
    public function __construct($name = null, $carId=null){
        $this->name = ($name) ? $name : null;
        $this->car = ( $carId ) ? Factory::getObject($carId) : null;
    }
    public function car(){
        if ( $this->car )
            Log::out("My car is " . $this->car->getColor() );
        else
            Log::out("I have no car");
    }

    public function outName(){
        Log::debug(__METHOD__." [$this->name]");
        if ( $this->name )
            Log::out( "My name is " . $this->name);
        else
            Log::out("I have no name"); 
    }
    public function getName(){
        Log::debug(__METHOD__." [$this->name]");
        //Log::debug('Returning : '.$this->name);
        return $this->name;
    }
}

class Greek extends Person{

    public function __construct($name){
        Log::debug(__METHOD__."($name)");
        parent::__construct($name);
    }

    function outClassName(){
        Log::out( __METHOD__ . ": My name is ". __CLASS__ );
    }

}

class Egyptian extends Person{
    protected $enemy;
    public function __construct($name){
        Log::debug(__METHOD__);
        parent::__construct($name);
        $this->enemy = Factory::getObject('memnon');
    }
    function attack($s){
        Log::out("Attacking $s") ;
    }
    function enemy(){
        Log::debug(__METHOD__ ." [$this->name]");
        //Log::debug(print_r($this->enemy, true));
        $enemyName = ($this->enemy) ? $this->enemy->getName() : 'no enemy';
        if ( $this->enemy ) Log::out('My enemy is '. $enemyName);
        else Log::out("I have no enemy");
    }
}

class Friend extends Person{
    public $length;
    public $status;
    public function __construct($name){
        Log::debug(__METHOD__);
        parent::__construct($name);
    }

    public function outStats(){
        Log::out(sprintf('sex=%s length=%s status=%s weight=%s', $this->sex, $this->length, $this->status, $this->weight));
    }
}

class Car{

    private $color;
    public function __construct($color){
        Log::debug(__METHOD__);
        $this->color = $color;
    }

    function color(){
        Log::out( __METHOD__ . ": My color is ". $this->color );
        return $this->color;
    }

    function getColor(){
        return $this->color();
    }

}



Log::head('Start');
Log::out('Get memnon object');
$memnon = Factory::getObject('memnon');

Log::out('Get achilles object');
$achilles = Factory::getObject('achilles');
Log::out('Get car1 object');
$car1 = Factory::getObject('car1');
Log::out('Get imhotep object');
$imhotep = Factory::getObject('imhotep');
$simple1 = Factory::getObject('simple1');
$simple3 = Factory::getObject('simple3');

Log::head('Finished getting objects');

Log::out('Call memnon\'s function');
$memnon->outName();
Log::out('Call car1\'s function');
$car1->color();
Log::out('Call imhotep\'s function');
$imhotep->enemy();


Log::out('try to get a property from simple1');
Log::out('var = '.$simple1->var);

Log::out('try to get a property from simple3');
Log::out('myvar1 = '.$simple3->myvar1);
Log::out('temp = '.$simple3->temp);
Log::out('humidity = '.$simple3->humidity);
//the following will fail
//Log::out('myvar1 = '.$simple3->myvar2);

// Let's put it in a non-factory object
class Plain{
    public $friend;

    public function __construct(){

    }
    public function setFriend($name){
        $this->friend = Factory::getObject($name);        
    }
    public function outFriendName(){
        if ( $this->friend ) Log::out('Friend\'s name ' . $this->friend->getName());
    }
}
$plain = new Plain();
$plain->setFriend('jesse');
$plain->outFriendName();

$friend = $plain->friend;
$friend->outStats();