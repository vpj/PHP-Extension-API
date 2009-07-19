--TEST--
Test array_merge() function : usage variations - string keys
--FILE--
<?php
/* Prototype  : array array_merge(array $arr1, array $arr2 [, array $...])
 * Description: Merges elements from passed arrays into one array 
 * Source code: ext/standard/array.c
 */

/*
 * Pass array_merge arrays with string keys to test behaviour.
 * $arr2 has a duplicate key to $arr1
 */

echo "*** Testing array_merge() : usage variations ***\n";

//string keys
$arr1 = array('zero' => 'zero', 'one' => 'un', 'two' => 'deux');
$arr2 = array('zero' => 'zero', 'un' => 'eins', 'deux' => 'zwei');

var_dump(array_merge($arr1, $arr2));
var_dump(array_merge($arr2, $arr1));

echo "Done";
?>

--EXPECT--
*** Testing array_merge() : usage variations ***
array(5) {
  [u"zero"]=>
  unicode(4) "zero"
  [u"one"]=>
  unicode(2) "un"
  [u"two"]=>
  unicode(4) "deux"
  [u"un"]=>
  unicode(4) "eins"
  [u"deux"]=>
  unicode(4) "zwei"
}
array(5) {
  [u"zero"]=>
  unicode(4) "zero"
  [u"un"]=>
  unicode(4) "eins"
  [u"deux"]=>
  unicode(4) "zwei"
  [u"one"]=>
  unicode(2) "un"
  [u"two"]=>
  unicode(4) "deux"
}
Done
