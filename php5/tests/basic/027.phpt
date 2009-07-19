--TEST--
Handling of max_input_nesting_level being reached
--INI--
magic_quotes_gpc=0
always_populate_raw_post_data=0
display_errors=0
max_input_nesting_level=10
track_errors=1
log_errors=0
--POST--
a=1&b=ZYX&c[][][][][][][][][][][][][][][][][][][][][][]=123&d=123&e[][]][]=3
--FILE--
<?php
var_dump($_POST, $php_errormsg);
?>
--EXPECT--
array(4) {
  [u"a"]=>
  unicode(1) "1"
  [u"b"]=>
  unicode(3) "ZYX"
  [u"d"]=>
  unicode(3) "123"
  [u"e"]=>
  array(1) {
    [0]=>
    array(1) {
      [0]=>
      unicode(1) "3"
    }
  }
}
unicode(106) "Input variable nesting level exceeded 10. To increase the limit change max_input_nesting_level in php.ini."
