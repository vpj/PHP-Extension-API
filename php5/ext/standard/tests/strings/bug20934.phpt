--TEST--
Bug #20934 (htmlspecialchars returns latin1 from UTF-8)
--SKIPIF--
<?php
if (!function_exists("utf8_encode") || !function_exists("utf8_decode")) {
	die("SKIP Neither utf8_encode() nor utf8_decode() are available");
}
?> 
--FILE--
<?php
$str = utf8_encode("\xe0\xe1");
var_dump(bin2hex((binary)utf8_decode($str)));
var_dump(bin2hex((binary)utf8_decode(htmlspecialchars($str, ENT_COMPAT, "UTF-8"))));
?>
--EXPECT--
unicode(4) "e0e1"
unicode(4) "e0e1"
