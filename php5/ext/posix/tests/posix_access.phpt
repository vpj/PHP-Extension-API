--TEST--
Test posix_access() function test
--DESCRIPTION--
checks for existence, read-access, write-access, execute-access
--CREDITS--
Moritz Neuhaeuser, info@xcompile.net
PHP Testfest Berlin 2009-05-10
--SKIPIF--
<?php
if (!extension_loaded('posix')) {
    die('SKIP The posix extension is not loaded.');
}
if (posix_geteuid() == 0) {
    die('SKIP Cannot run test as root.');
}
?>
--FILE--
<?php
$filename = dirname(__FILE__) . '/foo.test';
$fp = fopen($filename,"w");
fwrite($fp, b"foo");
fclose($fp);

chmod ($filename, 0000);
var_dump(posix_access($filename, POSIX_F_OK));

chmod ($filename, 0400);
var_dump(posix_access($filename, POSIX_R_OK));

chmod ($filename, 0600);
var_dump(posix_access($filename, POSIX_W_OK));

chmod ($filename, 0700);
var_dump(posix_access($filename, POSIX_X_OK));
?>
===DONE===
--CLEAN--
<?php
$filename = dirname(__FILE__) . '/foo.test';
chmod ($filename, 0700);
unlink($filename);
?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
===DONE===
