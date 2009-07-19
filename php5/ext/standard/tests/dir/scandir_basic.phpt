--TEST--
Test scandir() function : basic functionality 
--FILE--
<?php
/* Prototype  : array scandir(string $dir [, int $sorting_order [, resource $context]])
 * Description: List files & directories inside the specified path 
 * Source code: ext/standard/dir.c
 */

/*
 * Test basic functionality of scandir()
 */

echo "*** Testing scandir() : basic functionality ***\n";

// include file.inc for create_files function
include (dirname(__FILE__) . '/../file/file.inc');

// set up directory
$directory = dirname(__FILE__) . '/scandir_basic';
mkdir($directory);
@create_files($directory, 3);

echo "\n-- scandir() with mandatory arguments --\n";
var_dump(scandir($directory));

echo "\n-- scandir() with all arguments --\n";
$sorting_order = 1;
$context = stream_context_create();
var_dump(scandir($directory, $sorting_order, $context));

delete_files($directory, 3);
?>
===DONE===
--CLEAN--
<?php
$directory = dirname(__FILE__) . '/scandir_basic';
rmdir($directory);
?>
--EXPECT--
*** Testing scandir() : basic functionality ***

-- scandir() with mandatory arguments --
array(5) {
  [0]=>
  unicode(1) "."
  [1]=>
  unicode(2) ".."
  [2]=>
  unicode(9) "file1.tmp"
  [3]=>
  unicode(9) "file2.tmp"
  [4]=>
  unicode(9) "file3.tmp"
}

-- scandir() with all arguments --
array(5) {
  [0]=>
  unicode(9) "file3.tmp"
  [1]=>
  unicode(9) "file2.tmp"
  [2]=>
  unicode(9) "file1.tmp"
  [3]=>
  unicode(2) ".."
  [4]=>
  unicode(1) "."
}
===DONE===
