--TEST--
Test serialize() & unserialize() functions: objects - ensure that COW references of objects are not serialized separately (unlike other types). 
--FILE--
<?php
/* Prototype  : proto string serialize(mixed variable)
 * Description: Returns a string representation of variable (which can later be unserialized) 
 * Source code: ext/standard/var.c
 * Alias to functions: 
 */
/* Prototype  : proto mixed unserialize(string variable_representation)
 * Description: Takes a string representation of variable and recreates it 
 * Source code: ext/standard/var.c
 * Alias to functions: 
 */

$x = new stdClass;
$ref = &$x;
var_dump(serialize(array($x, $x)));

$x = 1;
$ref = &$x;
var_dump(serialize(array($x, $x)));

$x = "a";
$ref = &$x;
var_dump(serialize(array($x, $x)));

$x = true;
$ref = &$x;
var_dump(serialize(array($x, $x)));

$x = null;
$ref = &$x;
var_dump(serialize(array($x, $x)));

$x = array();
$ref = &$x;
var_dump(serialize(array($x, $x)));

echo "Done";
?>
--EXPECT--
unicode(37) "a:2:{i:0;O:8:"stdClass":0:{}i:1;r:2;}"
unicode(22) "a:2:{i:0;i:1;i:1;i:1;}"
unicode(30) "a:2:{i:0;U:1:"a";i:1;U:1:"a";}"
unicode(22) "a:2:{i:0;b:1;i:1;b:1;}"
unicode(18) "a:2:{i:0;N;i:1;N;}"
unicode(26) "a:2:{i:0;a:0:{}i:1;a:0:{}}"
Done
