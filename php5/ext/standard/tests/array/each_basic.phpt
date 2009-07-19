--TEST--
Test each() function : basic functionality 
--FILE--
<?php
/* Prototype  : array each(array $arr)
 * Description: Return the currently pointed key..value pair in the passed array, 
 * and advance the pointer to the next element 
 * Source code: Zend/zend_builtin_functions.c
 */

/*
 * Test basic functionality of each() 
 */

echo "*** Testing each() : basic functionality ***\n";

$arr = array ('one' => 1, 'zero', 'two' => 'deux', 20 => 'twenty');
echo "\n-- Passed array: --\n";
var_dump($arr);

echo "\n-- Initial position: --\n";
var_dump(each($arr));

echo "\n-- End position: --\n";
end($arr);
var_dump(each($arr));

echo "\n-- Passed the end of array: --\n";
var_dump(each($arr));

echo "Done";
?>
--EXPECTF--
*** Testing each() : basic functionality ***

-- Passed array: --
array(4) {
  [u"one"]=>
  int(1)
  [0]=>
  unicode(4) "zero"
  [u"two"]=>
  unicode(4) "deux"
  [20]=>
  unicode(6) "twenty"
}

-- Initial position: --
array(4) {
  [1]=>
  int(1)
  [u"value"]=>
  int(1)
  [0]=>
  unicode(3) "one"
  [u"key"]=>
  unicode(3) "one"
}

-- End position: --
array(4) {
  [1]=>
  unicode(6) "twenty"
  [u"value"]=>
  unicode(6) "twenty"
  [0]=>
  int(20)
  [u"key"]=>
  int(20)
}

-- Passed the end of array: --
bool(false)
Done