--TEST--
SPL: RegexIterator::SPLIT, USE_KEY
--FILE--
<?php

class MyRegexIterator extends RegexIterator
{
	function show()
	{
		foreach($this as $k => $v)
		{
			var_dump($k);
			var_dump($v);
		}
	}
}

$ar = new ArrayIterator(array('1'=>0,'1,2'=>1,'1,2,3'=>2,0=>3,'FooBar'=>4,','=>5,',,'=>6));
$it = new MyRegexIterator($ar, '/(\d),(\d)/', RegexIterator::SPLIT, RegexIterator::USE_KEY);

$it->show();

var_dump($ar);

?>
===DONE===
<?php exit(0); ?>
--EXPECTF--
unicode(3) "1,2"
array(2) {
  [0]=>
  unicode(0) ""
  [1]=>
  unicode(0) ""
}
unicode(5) "1,2,3"
array(2) {
  [0]=>
  unicode(0) ""
  [1]=>
  unicode(2) ",3"
}
object(ArrayIterator)#%d (1) {
  [u"storage":u"ArrayIterator":private]=>
  array(7) {
    [1]=>
    int(0)
    [u"1,2"]=>
    int(1)
    [u"1,2,3"]=>
    int(2)
    [0]=>
    int(3)
    [u"FooBar"]=>
    int(4)
    [u","]=>
    int(5)
    [u",,"]=>
    int(6)
  }
}
===DONE===
