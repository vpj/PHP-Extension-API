--TEST--
Object serialization / unserialization: real references and COW references
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

echo "\n\nArray containing same object twice:\n";
$obj = new stdclass;
$a[0] = $obj;
$a[1] = $a[0];
var_dump($a);

$ser = serialize($a);
var_dump($ser); 

$ua = unserialize($ser);
var_dump($ua);
$ua[0]->a = "newProp";
var_dump($ua);
$ua[0] = "a0.changed";
var_dump($ua);


echo "\n\nArray containing object and reference to that object:\n";
$obj = new stdclass;
$a[0] = $obj;
$a[1] = &$a[0];
var_dump($a);

$ser = serialize($a);
var_dump($ser); 

$ua = unserialize($ser);
var_dump($ua);
$ua[0]->a = "newProp";
var_dump($ua);
$ua[0] = "a0.changed";
var_dump($ua);

echo "\n\nObject containing same object twice:";
$obj = new stdclass;
$contaner = new stdclass;
$contaner->a = $obj;
$contaner->b = $contaner->a; 
var_dump($contaner);

$ser = serialize($contaner);
var_dump($ser); 

$ucontainer = unserialize($ser);
var_dump($ucontainer);
$ucontainer->a->a = "newProp";
var_dump($ucontainer);
$ucontainer->a = "container->a.changed";
var_dump($ucontainer);


echo "\n\nObject containing object and reference to that object:\n";
$obj = new stdclass;
$contaner = new stdclass;
$contaner->a = $obj;
$contaner->b = &$contaner->a; 
var_dump($contaner);

$ser = serialize($contaner);
var_dump($ser); 

$ucontainer = unserialize($ser);
var_dump($ucontainer);
$ucontainer->a->a = "newProp";
var_dump($ucontainer);
$ucontainer->b = "container->a.changed";
var_dump($ucontainer);

echo "Done";
?>
--EXPECTF--

Array containing same object twice:
array(2) {
  [0]=>
  object(stdClass)#%d (0) {
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
}
unicode(37) "a:2:{i:0;O:8:"stdClass":0:{}i:1;r:2;}"
array(2) {
  [0]=>
  object(stdClass)#%d (0) {
  }
  [1]=>
  object(stdClass)#%d (0) {
  }
}
array(2) {
  [0]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
  [1]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}
array(2) {
  [0]=>
  unicode(10) "a0.changed"
  [1]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}


Array containing object and reference to that object:
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
unicode(37) "a:2:{i:0;O:8:"stdClass":0:{}i:1;R:2;}"
array(2) {
  [0]=>
  &object(stdClass)#%d (0) {
  }
  [1]=>
  &object(stdClass)#%d (0) {
  }
}
array(2) {
  [0]=>
  &object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
  [1]=>
  &object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}
array(2) {
  [0]=>
  &unicode(10) "a0.changed"
  [1]=>
  &unicode(10) "a0.changed"
}


Object containing same object twice:object(stdClass)#%d (2) {
  [u"a"]=>
  object(stdClass)#%d (0) {
  }
  [u"b"]=>
  object(stdClass)#%d (0) {
  }
}
unicode(58) "O:8:"stdClass":2:{U:1:"a";O:8:"stdClass":0:{}U:1:"b";r:2;}"
object(stdClass)#%d (2) {
  [u"a"]=>
  object(stdClass)#%d (0) {
  }
  [u"b"]=>
  object(stdClass)#%d (0) {
  }
}
object(stdClass)#%d (2) {
  [u"a"]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
  [u"b"]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}
object(stdClass)#%d (2) {
  [u"a"]=>
  unicode(20) "container->a.changed"
  [u"b"]=>
  object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}


Object containing object and reference to that object:
object(stdClass)#%d (2) {
  [u"a"]=>
  &object(stdClass)#%d (0) {
  }
  [u"b"]=>
  &object(stdClass)#%d (0) {
  }
}
unicode(58) "O:8:"stdClass":2:{U:1:"a";O:8:"stdClass":0:{}U:1:"b";R:2;}"
object(stdClass)#%d (2) {
  [u"a"]=>
  &object(stdClass)#%d (0) {
  }
  [u"b"]=>
  &object(stdClass)#%d (0) {
  }
}
object(stdClass)#%d (2) {
  [u"a"]=>
  &object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
  [u"b"]=>
  &object(stdClass)#%d (1) {
    [u"a"]=>
    unicode(7) "newProp"
  }
}
object(stdClass)#%d (2) {
  [u"a"]=>
  &unicode(20) "container->a.changed"
  [u"b"]=>
  &unicode(20) "container->a.changed"
}
Done
