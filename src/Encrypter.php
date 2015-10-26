<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

/*
 * This class encapsulates Blowfish encryption and decryption.
 */ 
class Encrypter{
   protected $key;
   protected $iv;
   
   function __construct($iv=null, $key=null){
      $this->iv = $iv;
      $this->key = $key;
   }
   
   /**
    * Encrypts data
    * Returns binary data.    
    */       
   function encrypt($data){
      $len = strlen($data);
      $enc = mcrypt_encrypt( MCRYPT_BLOWFISH, $this->key, $data, MCRYPT_MODE_CBC, $this->iv );
      return $enc;
   }
   
   /**
    * Encrypts data and convert to hex. 
    * Ideal for storing as text.   
    */       
   function encryptToHex($data){
      return $this->bin2hex($this->encrypt($data));
   }
   
   /**
    * Takes hex input, convert from hex to binary then decrypt.
    */       
   function decryptFromHex($data){
      //echo "hex \"$data\"\n";
      return $this->decrypt($this->hex2bin($data));
   }

   /**
    *
    */       
   function decrypt($data){
      $decode = mcrypt_decrypt( MCRYPT_BLOWFISH, $this->key, $data, MCRYPT_MODE_CBC, $this->iv );
      //echo "decode \"$decode\"\n";
      $realdata = rtrim($decode, "\0");
      
      //echo sprintf("[%s]\n", $realdata);
      return $realdata;
   }
   
   /**
    * Convert from binary to hex
    */       
   function bin2hex($s){
      return bin2hex($s);
   }
   
   /**
    * Convert from hex to binary
    */       
   function hex2bin($s){
      return pack("H*", $s);   
   }

   /**
    * loads key and iv from a file.
    * Key is on line 1 in hex.
    * IV is on line 2 in hex.        
    */       
   function loadKeysFromFile($filename){
      return $this->loadKeysFromString(file_get_contents($filename));
   }
   
   /**
    * String is the key and the iv separates by a newline.
    * Use this when storing the key in a file.
    * Line 1 would be the key, line 2 is the iv.        
    */       
   function loadKeysFromString($string){
      $keys = explode("\n", $string);
      $key = $keys[0];
      $iv = pack("H*", $keys[1]);
      $this->key = $key;
      $this->iv = $iv;
      return array($key, $iv);
   }
   
   function showKeys(){
      return sprintf("%s %s", $this->key, $this->bin2hex($this->iv));
   }
}

--- put the following 2 lines in a file names 'keys' to get the demo to work ---
4987bf894deh898efae46384efffeabcc387da8d7e8af893783745d1
4fecabab1234bcbc
----- end keys ------

<?php
require_once 'Encrypter.php';
class MyApp {
	

	public function run() {
		$e = new Encrypter;
		$e->loadKeysFromFile('keys');

		echo $e->showKeys() . "\n";
		$hex = $e->encryptToHex("4646123412341234");
		print $hex . "\n";
		print $e->decryptFromHex($hex) . "\n";
	}
}
$app = new MyApp;
$app->run();