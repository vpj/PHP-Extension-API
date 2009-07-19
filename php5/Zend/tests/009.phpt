--TEST--
get_class() tests
--FILE--
<?php

class foo {
	function bar () {
		var_dump(get_class());
	}
}

class foo2 extends foo {
}

foo::bar();
foo2::bar();

$f1 = new foo;
$f2 = new foo2;

$f1->bar();
$f2->bar();

var_dump(get_class());
var_dump(get_class("qwerty"));

var_dump(get_class($f1));
var_dump(get_class($f2));

echo "Done\n";
?>
--EXPECTF--
Strict Standards: Non-static method foo::bar() should not be called statically in %s on line %d
unicode(3) "foo"

Strict Standards: Non-static method foo::bar() should not be called statically in %s on line %d
unicode(3) "foo"
unicode(3) "foo"
unicode(3) "foo"

Warning: get_class() called without object from outside a class in %s on line %d
bool(false)

Warning: get_class() expects parameter 1 to be object, Unicode string given in %s on line %d
bool(false)
unicode(3) "foo"
unicode(4) "foo2"
Done
