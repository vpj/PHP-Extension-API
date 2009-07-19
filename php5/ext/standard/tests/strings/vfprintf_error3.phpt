--TEST--
Test vfprintf() function : error conditions (wrong argument types)
--INI--
precision=14
--FILE--
<?php
/* Prototype  : int vfprintf(resource stream, string format, array args)
 * Description: Output a formatted string into a stream 
 * Source code: ext/standard/formatted_print.c
 * Alias to functions: 
 */

// Open handle
$file = 'vfprintf_test.txt';
$fp = fopen( $file, "a+" );

// Set unicode encoding
stream_encoding( $fp, 'unicode' );

echo "\n-- Testing vfprintf() function with wrong variable types as argument --\n";
var_dump( vfprintf( $fp, array( 'foo %d', 'bar %s' ), 3.55552 ) );

rewind( $fp );
var_dump( stream_get_contents( $fp ) );
ftruncate( $fp, 0 );
rewind( $fp );

var_dump( vfprintf( $fp, "Foo %y fake", "not available" ) );

rewind( $fp );
var_dump( stream_get_contents( $fp ) );
ftruncate( $fp, 0 );
rewind( $fp );

// Close handle
fclose( $fp );

?>
===DONE===
--CLEAN--
<?php

$file = 'vfprintf_text.txt';
unlink( $file );

?>
--EXPECTF--
-- Testing vfprintf() function with wrong variable types as argument --

Notice: Array to string conversion in %s on line %d
int(5)
unicode(5) "Array"
int(9)
unicode(9) "Foo  fake"
===DONE===
