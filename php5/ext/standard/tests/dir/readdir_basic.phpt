--TEST--
Test readdir() function : basic functionality 
--FILE--
<?php
/* Prototype  : string readdir([resource $dir_handle])
 * Description: Read directory entry from dir_handle 
 * Source code: ext/standard/dir.C
 */

/*
 * Test basic functionality of readdir()
 */

echo "*** Testing readdir() : basic functionality ***\n";

// include the file.inc for Function: function create_files()
include(dirname(__FILE__) . "/../file/file.inc");

$path = dirname(__FILE__) . '/readdir_basic';
mkdir($path);
@create_files($path, 3, 'numeric', 0755, 1, 'w', 'readdir_basic');

echo "\n-- Call readdir() with \$path argument --\n";
var_dump($dh = opendir($path));
$a = array();
while( FALSE !== ($file = readdir($dh)) ) {
	$a[] = $file;
}
sort($a);
foreach($a as $file) {
	var_dump($file);
}

echo "\n-- Call readdir() without \$path argument --\n";
var_dump($dh = opendir($path));
$a = array();
while( FALSE !== ( $file = readdir() ) ) {
	$a[] = $file;
}
sort($a);
foreach($a as $file) {
	var_dump($file);
}

delete_files($path, 3, 'readdir_basic');
closedir($dh);
?>
===DONE===
--CLEAN--
<?php
$path = dirname(__FILE__) . '/readdir_basic';
rmdir($path);
?>
--EXPECTF--
*** Testing readdir() : basic functionality ***

-- Call readdir() with $path argument --
resource(%d) of type (stream)
unicode(1) "."
unicode(2) ".."
unicode(18) "readdir_basic1.tmp"
unicode(18) "readdir_basic2.tmp"
unicode(18) "readdir_basic3.tmp"

-- Call readdir() without $path argument --
resource(%d) of type (stream)
unicode(1) "."
unicode(2) ".."
unicode(18) "readdir_basic1.tmp"
unicode(18) "readdir_basic2.tmp"
unicode(18) "readdir_basic3.tmp"
===DONE===
