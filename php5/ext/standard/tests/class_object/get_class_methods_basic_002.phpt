--TEST--
Test get_class_methods() function : basic functionality 
--FILE--
<?php
/* Prototype  : proto array get_class_methods(mixed class)
 * Description: Returns an array of method names for class or class instance. 
 * Source code: Zend/zend_builtin_functions.c
 * Alias to functions: 
 */

/*
 * Test behaviour with various visibility levels.
 */

class C {
	private function privC() {}
	protected function protC() {}
	public function pubC() {}
	
	public static function testFromC() {
		echo "Accessing C from C:\n";
		var_dump(get_class_methods("C"));
		echo "Accessing D from C:\n";
		var_dump(get_class_methods("D"));
		echo "Accessing X from C:\n";
		var_dump(get_class_methods("X"));
	}
}

class D extends C {
	private function privD() {}
	protected function protD() {}
	public function pubD() {}
	
	public static function testFromD() {
		echo "Accessing C from D:\n";
		var_dump(get_class_methods("C"));
		echo "Accessing D from D:\n";
		var_dump(get_class_methods("D"));
		echo "Accessing X from D:\n";
		var_dump(get_class_methods("X"));
	}
}

class X {
	private function privX() {}
	protected function protX() {}
	public function pubX() {}
	
	public static function testFromX() {
		echo "Accessing C from X:\n";
		var_dump(get_class_methods("C"));
		echo "Accessing D from X:\n";
		var_dump(get_class_methods("D"));
		echo "Accessing X from X:\n";
		var_dump(get_class_methods("X"));
	}
}

echo "Accessing D from global scope:\n";
var_dump(get_class_methods("D"));

C::testFromC();
D::testFromD();
X::testFromX();

echo "Done";
?>
--EXPECT--
Accessing D from global scope:
array(4) {
  [0]=>
  unicode(4) "pubD"
  [1]=>
  unicode(9) "testFromD"
  [2]=>
  unicode(4) "pubC"
  [3]=>
  unicode(9) "testFromC"
}
Accessing C from C:
array(4) {
  [0]=>
  unicode(5) "privC"
  [1]=>
  unicode(5) "protC"
  [2]=>
  unicode(4) "pubC"
  [3]=>
  unicode(9) "testFromC"
}
Accessing D from C:
array(7) {
  [0]=>
  unicode(5) "protD"
  [1]=>
  unicode(4) "pubD"
  [2]=>
  unicode(9) "testFromD"
  [3]=>
  unicode(5) "privC"
  [4]=>
  unicode(5) "protC"
  [5]=>
  unicode(4) "pubC"
  [6]=>
  unicode(9) "testFromC"
}
Accessing X from C:
array(2) {
  [0]=>
  unicode(4) "pubX"
  [1]=>
  unicode(9) "testFromX"
}
Accessing C from D:
array(3) {
  [0]=>
  unicode(5) "protC"
  [1]=>
  unicode(4) "pubC"
  [2]=>
  unicode(9) "testFromC"
}
Accessing D from D:
array(7) {
  [0]=>
  unicode(5) "privD"
  [1]=>
  unicode(5) "protD"
  [2]=>
  unicode(4) "pubD"
  [3]=>
  unicode(9) "testFromD"
  [4]=>
  unicode(5) "protC"
  [5]=>
  unicode(4) "pubC"
  [6]=>
  unicode(9) "testFromC"
}
Accessing X from D:
array(2) {
  [0]=>
  unicode(4) "pubX"
  [1]=>
  unicode(9) "testFromX"
}
Accessing C from X:
array(2) {
  [0]=>
  unicode(4) "pubC"
  [1]=>
  unicode(9) "testFromC"
}
Accessing D from X:
array(4) {
  [0]=>
  unicode(4) "pubD"
  [1]=>
  unicode(9) "testFromD"
  [2]=>
  unicode(4) "pubC"
  [3]=>
  unicode(9) "testFromC"
}
Accessing X from X:
array(4) {
  [0]=>
  unicode(5) "privX"
  [1]=>
  unicode(5) "protX"
  [2]=>
  unicode(4) "pubX"
  [3]=>
  unicode(9) "testFromX"
}
Done
