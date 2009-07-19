--TEST--
SPL: FixedArray: adding new elements
--FILE--
<?php

$a = new SplFixedArray(10);

try {
	$a[] = 1;
} catch (Exception $e) {
	var_dump($e->getMessage());
}

?>
===DONE===
--EXPECTF--
unicode(29) "Index invalid or out of range"
===DONE===
