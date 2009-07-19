--TEST--
Bug #43927 (koi8r is missing from html_entity_decode())
--FILE--
<?php
var_dump(html_entity_decode("&amp;lt;", ENT_COMPAT, 'koi8-r'));
var_dump(html_entity_decode("&amp;#38;", ENT_COMPAT, 'koi8-r'));
var_dump(html_entity_decode("&amp;#38;lt;", ENT_COMPAT, 'koi8-r'));
?>
--EXPECT--
unicode(4) "&lt;"
unicode(5) "&#38;"
unicode(8) "&#38;lt;"
