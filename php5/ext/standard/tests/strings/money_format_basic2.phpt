--TEST--
Test money_format() function : basic functionality using international currency symbols
--SKIPIF--
<?php
	if (!function_exists('money_format') || !function_exists('setlocale')) {
		die("SKIP money_format - not supported\n");
	}
	
	if (setlocale(LC_MONETARY, 'en_US') == false) {
		die("SKIP en_US locale not available\n");
	}	
?>
--FILE--
<?php
/* Prototype  : string money_format  ( string $format  , float $number  )
 * Description: Formats a number as a currency string
 * Source code: ext/standard/string.c
*/

echo "*** Testing money_format() : basic functionality using international currency symbols***\n";

$original = setlocale(LC_MONETARY, 'en_US');

$value = 1234.5678;
$negative_value = -1234.5678;

// Format with 14 positions of width, 8 digits of
// left precision, 2 of right precision using national 
// format for en_US
echo "Format with 14 positions, 8 digits to left, 2 to right using national format\n";
var_dump( money_format('%14#8.2i', $value));
var_dump( money_format('%14#8.2i', $negative_value));

// Same again but use '(' for negative values 
echo "Format with ( for negative values\n";
var_dump( money_format('%(14#8.2i', $value));
var_dump( money_format('%(14#8.2i', $negative_value));

// Same again but use a '0' for padding character
echo "Format with 0 for padding character\n";
var_dump( money_format('%=014#8.2i', $value));
var_dump( money_format('%=014#8.2i', $negative_value));

// Same again but use a '*' for padding character
echo "Format with * for padding character\n";
var_dump( money_format('%=*14#8.2i', $value));
var_dump( money_format('%=*14#8.2i', $negative_value));

// Same again but disable grouping character
echo "Format again but disable grouping character\n"; 
var_dump( money_format('%=*^14#8.2i', $value));
var_dump( money_format('%=*^14#8.2i', $negative_value));

// Same again but suppress currency symbol
echo "Format again but suppress currency symbol\n"; 
var_dump( money_format('%=*!14#8.2i', $value));
var_dump( money_format('%=*!14#8.2i', $negative_value));

setlocale(LC_MONETARY, $original);

?>
===DONE===
--EXPECT--
*** Testing money_format() : basic functionality using international currency symbols***
Format with 14 positions, 8 digits to left, 2 to right using national format
string(18) " USD      1,234.57"
string(18) "-USD      1,234.57"
Format with ( for negative values
string(18) " USD      1,234.57"
string(19) "(USD      1,234.57)"
Format with 0 for padding character
string(18) " USD 000001,234.57"
string(18) "-USD 000001,234.57"
Format with * for padding character
string(18) " USD *****1,234.57"
string(18) "-USD *****1,234.57"
Format again but disable grouping character
string(16) " USD ****1234.57"
string(16) "-USD ****1234.57"
Format again but suppress currency symbol
string(14) " *****1,234.57"
string(14) "-*****1,234.57"
===DONE===

