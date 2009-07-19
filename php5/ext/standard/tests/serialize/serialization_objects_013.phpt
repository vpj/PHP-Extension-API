--TEST--
Object serialization / unserialization: references amongst properties 
--INI--
error_reporting = E_ALL & ~E_STRICT
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

function check(&$obj) {
	var_dump($obj);
	$ser = serialize($obj);
	var_dump($ser);
	
	$uobj = unserialize($ser);
	var_dump($uobj);
	$uobj->a = "obj->a.changed";
	var_dump($uobj);
	$uobj->b = "obj->b.changed";
	var_dump($uobj);
	$uobj->c = "obj->c.changed";
	var_dump($uobj);	
}

echo "\n\n--- a refs b:\n";
$obj = new stdClass;
$obj->a = &$obj->b;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- a refs c:\n";
$obj = new stdClass;
$obj->a = &$obj->c;
$obj->b = 1;
$obj->c = 1;
check($obj);

echo "\n\n--- b refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->a;
$obj->c = 1;
check($obj);

echo "\n\n--- b refs c:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->c;
$obj->c = 1;
check($obj);

echo "\n\n--- c refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = 1;
$obj->c = &$obj->a;
check($obj);

echo "\n\n--- c refs b:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = 1;
$obj->c = &$obj->b;
check($obj);

echo "\n\n--- a,b refs c:\n";
$obj = new stdClass;
$obj->a = &$obj->c;
$obj->b = &$obj->c;
$obj->c = 1;
check($obj);

echo "\n\n--- a,c refs b:\n";
$obj = new stdClass;
$obj->a = &$obj->b;
$obj->b = 1;
$obj->c = &$obj->b;
check($obj);

echo "\n\n--- b,c refs a:\n";
$obj = new stdClass;
$obj->a = 1;
$obj->b = &$obj->a;
$obj->c = &$obj->a;
check($obj);

echo "Done";
?>
--EXPECTF--

--- a refs b:
object(stdClass)#%d (3) {
  [u"b"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"c"]=>
  int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"b";i:1;U:1:"a";R:2;U:1:"c";i:1;}"
object(stdClass)#%d (3) {
  [u"b"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->a.changed"
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  unicode(14) "obj->c.changed"
}


--- a refs c:
object(stdClass)#%d (3) {
  [u"c"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"b"]=>
  int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"c";i:1;U:1:"a";R:2;U:1:"b";i:1;}"
object(stdClass)#%d (3) {
  [u"c"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"b"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->a.changed"
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->a.changed"
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->c.changed"
  [u"a"]=>
  &unicode(14) "obj->c.changed"
  [u"b"]=>
  unicode(14) "obj->b.changed"
}


--- b refs a:
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"a";i:1;U:1:"b";R:2;U:1:"c";i:1;}"
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  &unicode(14) "obj->a.changed"
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  unicode(14) "obj->c.changed"
}


--- b refs c:
object(stdClass)#%d (3) {
  [u"a"]=>
  int(1)
  [u"c"]=>
  &int(1)
  [u"b"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"a";i:1;U:1:"c";i:1;U:1:"b";R:3;}"
object(stdClass)#%d (3) {
  [u"a"]=>
  int(1)
  [u"c"]=>
  &int(1)
  [u"b"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"c"]=>
  &int(1)
  [u"b"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"c"]=>
  &unicode(14) "obj->b.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"c"]=>
  &unicode(14) "obj->c.changed"
  [u"b"]=>
  &unicode(14) "obj->c.changed"
}


--- c refs a:
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  int(1)
  [u"c"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"a";i:1;U:1:"b";i:1;U:1:"c";R:2;}"
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  int(1)
  [u"c"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  int(1)
  [u"c"]=>
  &unicode(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  unicode(14) "obj->b.changed"
  [u"c"]=>
  &unicode(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->c.changed"
  [u"b"]=>
  unicode(14) "obj->b.changed"
  [u"c"]=>
  &unicode(14) "obj->c.changed"
}


--- c refs b:
object(stdClass)#%d (3) {
  [u"a"]=>
  int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"a";i:1;U:1:"b";i:1;U:1:"c";R:3;}"
object(stdClass)#%d (3) {
  [u"a"]=>
  int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"b"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  &unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  unicode(14) "obj->a.changed"
  [u"b"]=>
  &unicode(14) "obj->c.changed"
  [u"c"]=>
  &unicode(14) "obj->c.changed"
}


--- a,b refs c:
object(stdClass)#%d (3) {
  [u"c"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"c";i:1;U:1:"a";R:2;U:1:"b";R:2;}"
object(stdClass)#%d (3) {
  [u"c"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->a.changed"
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  &unicode(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->b.changed"
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"c"]=>
  &unicode(14) "obj->c.changed"
  [u"a"]=>
  &unicode(14) "obj->c.changed"
  [u"b"]=>
  &unicode(14) "obj->c.changed"
}


--- a,c refs b:
object(stdClass)#%d (3) {
  [u"b"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"b";i:1;U:1:"a";R:2;U:1:"c";R:2;}"
object(stdClass)#%d (3) {
  [u"b"]=>
  &int(1)
  [u"a"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->a.changed"
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"c"]=>
  &unicode(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  &unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"b"]=>
  &unicode(14) "obj->c.changed"
  [u"a"]=>
  &unicode(14) "obj->c.changed"
  [u"c"]=>
  &unicode(14) "obj->c.changed"
}


--- b,c refs a:
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
unicode(55) "O:8:"stdClass":3:{U:1:"a";i:1;U:1:"b";R:2;U:1:"c";R:2;}"
object(stdClass)#%d (3) {
  [u"a"]=>
  &int(1)
  [u"b"]=>
  &int(1)
  [u"c"]=>
  &int(1)
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->a.changed"
  [u"b"]=>
  &unicode(14) "obj->a.changed"
  [u"c"]=>
  &unicode(14) "obj->a.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->b.changed"
  [u"b"]=>
  &unicode(14) "obj->b.changed"
  [u"c"]=>
  &unicode(14) "obj->b.changed"
}
object(stdClass)#%d (3) {
  [u"a"]=>
  &unicode(14) "obj->c.changed"
  [u"b"]=>
  &unicode(14) "obj->c.changed"
  [u"c"]=>
  &unicode(14) "obj->c.changed"
}
Done
