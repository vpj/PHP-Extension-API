--TEST--
Overriding $this in catch and checking the object properties later.
--FILE--
<?php

class foo {
	public $test = 0;
	private $test_2 = 1;
	protected $test_3 = 2;
	
	public function bar() {
		try {
			throw new Exception('foo');
		} catch (Exception $this) {
			var_dump($this);
		}

		$this->baz();		
	}
	
	public function baz() {
		foreach ($this as $k => $v) {
			printf("'%s' => '%s'\n", $k, $v);
		}		
		print "ok\n";
	}
}

$test = new foo;
$test->bar();

?>
--EXPECTF--
object(Exception)#2 (%d) {
  [u"message":protected]=>
  unicode(3) "foo"
  [u"string":u"Exception":private]=>
  unicode(0) ""
  [u"code":protected]=>
  int(0)
  [u"file":protected]=>
  unicode(%d) "%s"
  [u"line":protected]=>
  int(%d)
  [u"trace":u"Exception":private]=>
  array(1) {
    [0]=>
    array(6) {
      [u"file"]=>
      unicode(%d) "%s"
      [u"line"]=>
      int(%d)
      [u"function"]=>
      unicode(3) "bar"
      [u"class"]=>
      unicode(3) "foo"
      [u"type"]=>
      unicode(2) "->"
      [u"args"]=>
      array(0) {
      }
    }
  }
  [u"previous":u"Exception":private]=>
  NULL
}
'test' => '0'
'test_2' => '1'
'test_3' => '2'
ok
