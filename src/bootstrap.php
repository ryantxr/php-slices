<?php
/**********
 * Author: Ryan Teixeira
 * Company: Blazecore Incorporated
 * December 2012
 */

// put this in bootstrap.php and place in the main app dir
date_default_timezone_set('America/New_York');
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('APPDIR', dirname(__FILE__));
define('INCDIR', APPDIR . DS . 'include');

set_include_path(get_include_path() . PS . INCDIR);
function x_autoload($cls){
   @include_once($cls . '.php');
   
   // does the class requested actually exist now?
   if (class_exists($cls, false)) {
      // yes, we're done
      return;
   }
}
spl_autoload_register('x_autoload');