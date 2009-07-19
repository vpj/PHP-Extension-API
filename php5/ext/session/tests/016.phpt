--TEST--
invalid session.save_path should not cause a segfault
--SKIPIF--
<?php
if (!extension_loaded("session") && ini_get("enable_dl")) {
    $dlext = (substr(PHP_OS, 0, 3) == "WIN") ? ".dll" : ".so";
    @dl("session$dlext");
}
if (!extension_loaded("session")) {
    die("skip Session module not loaded");
}
?>
--INI--
session.save_path="123;:/really\\completely:::/invalid;;,23123;213"
session.use_cookies=0
session.cache_limiter=
session.save_handler=files
session.serialize_handler=php
--FILE--
<?php
error_reporting(E_ALL);

@session_start();
$_SESSION["test"] = 1;
@session_write_close();
print "I live\n";
?>
--EXPECT--
I live
