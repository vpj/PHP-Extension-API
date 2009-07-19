--TEST--
#38943, properties in extended class cannot be set
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php
include dirname(__FILE__) . '/bug38943.inc';
?>
--EXPECTF--
array(1) {
  [0]=>
  int(1)
}
object(myZip)#1 (8) {
  [u"test":u"myZip":private]=>
  int(0)
  [u"testp"]=>
  unicode(6) "foobar"
  [u"testarray":u"myZip":private]=>
  array(1) {
    [0]=>
    int(1)
  }
  ["status"]=>
  int(0)
  ["statusSys"]=>
  int(0)
  ["numFiles"]=>
  int(0)
  ["filename"]=>
  string(0) ""
  ["comment"]=>
  string(0) ""
}
