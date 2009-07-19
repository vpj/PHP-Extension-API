--TEST--
Test array_merge() function : usage variations - Mixed keys
--FILE--
<?php
/* Prototype  : array array_merge(array $arr1, array $arr2 [, array $...])
 * Description: Merges elements from passed arrays into one array 
 * Source code: ext/standard/array.c
 */

/*
 * Pass array_merge() arrays with mixed keys to test how it attaches them to
 * existing arrays
 */

echo "*** Testing array_merge() : usage variations ***\n";

//mixed keys
$arr1 = array('zero', 20 => 'twenty', 'thirty' => 30, true => 'bool');
$arr2 = array(0, 1, 2, null => 'null', 1.234E-10 => 'float');

var_dump(array_merge($arr1, $arr2));
var_dump(array_merge($arr2, $arr1));

echo "Done";
?>

--EXPECT--
*** Testing array_merge() : usage variations ***
array(8) {
  [0]=>
  unicode(4) "zero"
  [1]=>
  unicode(6) "twenty"
  [u"thirty"]=>
  int(30)
  [2]=>
  unicode(4) "bool"
  [3]=>
  unicode(5) "float"
  [4]=>
  int(1)
  [5]=>
  int(2)
  [u""]=>
  unicode(4) "null"
}
array(8) {
  [0]=>
  unicode(5) "float"
  [1]=>
  int(1)
  [2]=>
  int(2)
  [u""]=>
  unicode(4) "null"
  [3]=>
  unicode(4) "zero"
  [4]=>
  unicode(6) "twenty"
  [u"thirty"]=>
  int(30)
  [5]=>
  unicode(4) "bool"
}
Done
