--TEST--
Bug #27278 (*printf() functions treat arguments as if passed by reference)
--FILE--
<?php

function foo ($a)
{
	$a=sprintf("%02d",$a);
	var_dump($a);
}

$x="02";
var_dump($x);
foo($x);
var_dump($x);

?>
--EXPECT--
unicode(2) "02"
unicode(2) "02"
unicode(2) "02"
