--TEST--
Test posix_access() function : parameter validation 
--DESCRIPTION--
cases: no params, wrong param1, wrong param2, null directory, wrong directory,
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

var_dump( posix_access() );
var_dump( posix_access(array()) );
var_dump( posix_access(b'foo',array()) );
var_dump( posix_access(null) );

var_dump(posix_access('./foobar'));
?>
===DONE===
--EXPECTF--
Warning: posix_access() expects at least 1 parameter, 0 given in %s on line %d
bool(false)

Warning: posix_access() expects parameter 1 to be binary string, array given in %s on line %d
bool(false)

Warning: posix_access() expects parameter 2 to be long, array given in %s on line %d
bool(false)
bool(false)
bool(false)
===DONE===
