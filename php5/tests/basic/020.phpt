--TEST--
POST Method test and arrays - 8 
--POST--
a[a[]]=1&a[b[]]=3
--FILE--
<?php
var_dump($_POST['a']); 
?>
--EXPECT--
array(2) {
  [u"a["]=>
  unicode(1) "1"
  [u"b["]=>
  unicode(1) "3"
}
