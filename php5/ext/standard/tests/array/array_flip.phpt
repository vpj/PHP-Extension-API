--TEST--
basic array_flip test
--FILE--
<?php
$trans = array("a" => 1, 
               "b" => 1, 
               "c" => 2, 
               "z" => 0, 
               "d" => TRUE, 
               "E" => FALSE, 
               "F" => NULL, 
               0 => "G", 
               1 => "h", 
               2 => "i");
$trans = array_flip($trans);
var_dump($trans);
?>
--EXPECTF--
Warning: array_flip(): Can only flip STRING and INTEGER values! in %s on line %d

Warning: array_flip(): Can only flip STRING and INTEGER values! in %s on line %d

Warning: array_flip(): Can only flip STRING and INTEGER values! in %s on line %d
array(6) {
  [1]=>
  unicode(1) "b"
  [2]=>
  unicode(1) "c"
  [0]=>
  unicode(1) "z"
  [u"G"]=>
  int(0)
  [u"h"]=>
  int(1)
  [u"i"]=>
  int(2)
}
