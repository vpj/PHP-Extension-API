--TEST--
Test compact() function : basic functionality 
--FILE--
<?php
/* Prototype  : proto array compact(mixed var_names [, mixed ...])
 * Description: Creates a hash containing variables and their values 
 * Source code: ext/standard/array.c
 * Alias to functions: 
 */

/*
 * Test basic functionality
 */

echo "*** Testing compact() : basic functionality ***\n";

$a=1;
$b=0.2;
$c=true;
$d=array("key"=>"val");
$e=NULL;
$f="string";

// simple array test
var_dump (compact(array("a", "b", "c", "d", "e", "f")));
// simple parameter test
var_dump (compact("a", "b", "c", "d", "e", "f"));
var_dump (compact(array("keyval"=>"a", "b"=>"b", "c"=>1)));

// cases which should not yield any output.
var_dump (compact(array(10, 0.3, true, array(20), NULL)));
var_dump (compact(10, 0.3, true, array(20), NULL));
var_dump (compact(array("g")));

echo "Done";
?>
--EXPECT--
*** Testing compact() : basic functionality ***
array(6) {
  [u"a"]=>
  int(1)
  [u"b"]=>
  float(0.2)
  [u"c"]=>
  bool(true)
  [u"d"]=>
  array(1) {
    [u"key"]=>
    unicode(3) "val"
  }
  [u"e"]=>
  NULL
  [u"f"]=>
  unicode(6) "string"
}
array(6) {
  [u"a"]=>
  int(1)
  [u"b"]=>
  float(0.2)
  [u"c"]=>
  bool(true)
  [u"d"]=>
  array(1) {
    [u"key"]=>
    unicode(3) "val"
  }
  [u"e"]=>
  NULL
  [u"f"]=>
  unicode(6) "string"
}
array(2) {
  [u"a"]=>
  int(1)
  [u"b"]=>
  float(0.2)
}
array(0) {
}
array(0) {
}
array(0) {
}
Done
