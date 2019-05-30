<?php

class RandomPhoneGenerator {
  const PREFIXES = ['0812', '0813'  ,'0814' ,'0815' ,'0816' ,'0817' ,'0818','0819','0909','0908'];

  public function generate($n)
  {
    for($i=0; $i<$n; $i++) {
      $prefix = $this->randomPrefix();
      $sequence = $this->randomSequence();
      echo $prefix . " " . $sequence . "\n";
    }
  }

  // make a sequence of numbers where no digit is repeated three times in a row.
  protected function randomSequence()
  {
    $n = 7;
    $lastDigit = null;
    $rep = 0; // number of times the current digit has appeared in sequence
    $sequence = '';
    for($i=0; $i<$n; $i++) {
      if ( strlen($sequence) ) {
        $d = $this->getDigit();
        $sequence .= $d;
        $lastDigit = $d;
        ++$rep;
      } else {
        $d = $this->getDigit($lastDigit, $rep);
        $sequence .= $d;
        $rep = ($lastDigit == $d) ? $rep+1 : 1;
        $lastDigit = $d;
      }
    }
    return $sequence;
  }

  /**
   * Generates a digit.
   * Avoids the sqeuences of three of the same digits
   */
  protected function getDigit($lastDigit=null, $repeated=null)
  {
    if ( $lastDigit === null || $repeated === null || $repeated < 2 ) {
      return mt_rand(0, 9);
    }
    // Generate  digit
    $d = mt_rand(0, 9);
    // if it is the same as the last digit, 
    // add a random number from 1-9 and take the modulus
    // This has the effect of randomly getting one of the "other" digits
    if ( $d == $lastDigit ) {
      $d += mt_rand(1,9);
      $d = $d % 10;
    }
    return $d;
  }

  // Pick a random prefix from our list
  protected function randomPrefix()
  {
    return self::PREFIXES[mt_rand(0, count(self::PREFIXES)-1)];
  }

}

$r = new RandomPhoneGenerator;
$r->generate(10);

