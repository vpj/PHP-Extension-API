--TEST--
Test array_merge() function : usage variations - numeric keys
--FILE--
<?php
/* Prototype  : array array_merge(array $arr1, array $arr2 [, array $...])
 * Description: Merges elements from passed arrays into one array 
 * Source code: ext/standard/array.c
 */

/*
 * Pass array_merge() arrays with only numeric keys to test behaviour.
 * $arr2 contains a duplicate element to $arr1.
 */

echo "*** Testing array_merge() : usage variations ***\n";

//numeric keys
$arr1 = array('zero', 'one', 'two', 'three');
$arr2 = array(1 => 'one', 20 => 'twenty', 30 => 'thirty');

var_dump(array_merge($arr1, $arr2));
var_dump(array_merge($arr2, $arr1));

echo "Done";
?>

--EXPECT--
*** Testing array_merge() : usage variations ***
array(7) {
  [0]=>
  unicode(4) "zero"
  [1]=>
  unicode(3) "one"
  [2]=>
  unicode(3) "two"
  [3]=>
  unicode(5) "three"
  [4]=>
  unicode(3) "one"
  [5]=>
  unicode(6) "twenty"
  [6]=>
  unicode(6) "thirty"
}
array(7) {
  [0]=>
  unicode(3) "one"
  [1]=>
  unicode(6) "twenty"
  [2]=>
  unicode(6) "thirty"
  [3]=>
  unicode(4) "zero"
  [4]=>
  unicode(3) "one"
  [5]=>
  unicode(3) "two"
  [6]=>
  unicode(5) "three"
}
Done
