--TEST--
SPL: RegexIterator::SPLIT
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

$ar = new ArrayIterator(array('1','1,2','1,2,3','',NULL,array(),'FooBar',',',',,'));
$it = new MyRegexIterator($ar, '/,/', RegexIterator::SPLIT);

$it->show();

var_dump($ar);

?>
===DONE===
<?php exit(0); ?>
--EXPECTF--
int(1)
array(2) {
  [0]=>
  unicode(1) "1"
  [1]=>
  unicode(1) "2"
}
int(2)
array(3) {
  [0]=>
  unicode(1) "1"
  [1]=>
  unicode(1) "2"
  [2]=>
  unicode(1) "3"
}
int(7)
array(2) {
  [0]=>
  unicode(0) ""
  [1]=>
  unicode(0) ""
}
int(8)
array(3) {
  [0]=>
  unicode(0) ""
  [1]=>
  unicode(0) ""
  [2]=>
  unicode(0) ""
}
object(ArrayIterator)#%d (1) {
  [u"storage":u"ArrayIterator":private]=>
  array(9) {
    [0]=>
    unicode(1) "1"
    [1]=>
    unicode(3) "1,2"
    [2]=>
    unicode(5) "1,2,3"
    [3]=>
    unicode(0) ""
    [4]=>
    NULL
    [5]=>
    array(0) {
    }
    [6]=>
    unicode(6) "FooBar"
    [7]=>
    unicode(1) ","
    [8]=>
    unicode(2) ",,"
  }
}
===DONE===
