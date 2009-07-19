--TEST--
get_object_vars(): visibility from non static methods (target object passed as arg)
--FILE--
<?php
/* Prototype  : proto array get_object_vars(object obj)
 * Description: Returns an array of object properties 
 * Source code: Zend/zend_builtin_functions.c
 * Alias to functions: 
 */

Class A {
	private $hiddenPriv = 'A::hiddenPriv';

	public function testA($b) {
		echo __METHOD__ . "\n"; 
		var_dump(get_object_vars($b));
	} 
}

Class B extends A {
	private $hiddenPriv = 'B::hiddenPriv';	
	private $priv = 'B::priv';
	protected $prot = 'B::prot';
	public $pub = 'B::pub';

	public function testB($b) {
		echo __METHOD__ . "\n";		
		var_dump(get_object_vars($b));
	} 
}


$b = new B;
echo "\n---( Declaring class: )---\n";
$b->testB($b);
echo "\n---( Superclass: )---\n";
$b->testA($b);

?>
--EXPECT--

---( Declaring class: )---
B::testB
array(4) {
  [u"hiddenPriv"]=>
  unicode(13) "B::hiddenPriv"
  [u"priv"]=>
  unicode(7) "B::priv"
  [u"prot"]=>
  unicode(7) "B::prot"
  [u"pub"]=>
  unicode(6) "B::pub"
}

---( Superclass: )---
A::testA
array(3) {
  [u"prot"]=>
  unicode(7) "B::prot"
  [u"pub"]=>
  unicode(6) "B::pub"
  [u"hiddenPriv"]=>
  unicode(13) "A::hiddenPriv"
}
