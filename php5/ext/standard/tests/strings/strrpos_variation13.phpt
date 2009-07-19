--TEST--
Test strrpos() function : usage variations - checking binary safe with 'needle' argument
--FILE--
<?php
/* Prototype  : int strrpos ( string $haystack, string $needle [, int $offset] );
 * Description: Find position of last occurrence of 'needle' in 'haystack'.
 * Source code: ext/standard/string.c
*/

/* Test strrpos() function with null terminated strings for 'needle' argument 
 *  in order to check binary safe 
*/

echo "*** Test strrpos() function: binary safe ***\n";
$haystack = "\0Hello\0World\0";

$needles = array(
  "Hello".chr(0)."World",
  chr(0)."Hello\0",
  chr(0),
  "Hello\0world",
  "\0Hello\0world\0",
  "\0Hello",
  "Hello\0"
);

for($index = 0; $index < count($needles); $index++ ) {
  var_dump( strrpos($haystack, $needles[$index]) );
  var_dump( strrpos($haystack, $needles[$index], $index) );
}
echo "*** Done ***";
?>
--EXPECTF--
*** Test strrpos() function: binary safe ***
int(1)
int(1)
int(0)
bool(false)
int(12)
int(12)
bool(false)

Warning: strrpos(): Offset is greater than the length of haystack string in %s on line %d
bool(false)
bool(false)

Warning: strrpos(): Offset is greater than the length of haystack string in %s on line %d
bool(false)
int(0)
bool(false)
int(1)
bool(false)
*** Done ***
