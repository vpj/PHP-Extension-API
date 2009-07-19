--TEST--
ReflectionExtension::getDependencies() method on an extension with a required and conflicting dependency
--CREDITS--
Felix De Vliegher <felix.devliegher@gmail.com>
--SKIPIF--
<?php
if (!extension_loaded("dom")) die("skip no dom extension");
?>
--FILE--
<?php
$dom = new ReflectionExtension('dom');
var_dump($dom->getDependencies());
?>
==DONE==
--EXPECTF--
array(2) {
  [%s"libxml"]=>
  %s(8) "Required"
  [%s"domxml"]=>
  %s(9) "Conflicts"
}
==DONE==
