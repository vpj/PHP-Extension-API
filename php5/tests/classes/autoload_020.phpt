--TEST--
Ensure __autoload() is triggered during unserialization.
--FILE--
<?php
  function __autoload($name)
  {
      echo "in autoload: $name\n";
  }
  
  var_dump(unserialize('O:1:"C":0:{}'));
?>
--EXPECTF--
in autoload: C
object(__PHP_Incomplete_Class)#%d (1) {
  [u"__PHP_Incomplete_Class_Name"]=>
  unicode(1) "C"
}
