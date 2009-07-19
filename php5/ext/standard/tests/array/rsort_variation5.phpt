--TEST--
Test rsort() function : usage variations - String values
--FILE--
<?php
/* Prototype  : bool rsort(array &$array_arg [, int $sort_flags])
 * Description: Sort an array in reverse order 
 * Source code: ext/standard/array.c
 */

/*
 * Pass arrays containing different string data to rsort() to test behaviour
 */

echo "*** Testing rsort() : variation ***\n";

$various_arrays = array (
// group of escape sequences
array(null, NULL, "\a", "\cx", "\e", "\f", "\n", "\t", "\xhh", "\ddd", "\v"),

// array contains combination of capital/small letters
array("lemoN", "Orange", "banana", "apple", "Test", "TTTT", "ttt", "ww", "x", "X", "oraNGe", "BANANA")
);

$flags = array("SORT_REGULAR" => SORT_REGULAR, "SORT_STRING" => SORT_STRING);

$count = 1;
// loop through to test rsort() with different arrays
foreach ($various_arrays as $array) {
	echo "\n-- Iteration $count --\n";

	echo "- With Default sort flag -\n";
	$temp_array = $array;
	var_dump(rsort($temp_array) );
	var_dump($temp_array);

	// loop through $flags array and setting all possible flag values
	foreach($flags as $key => $flag){
		echo "- Sort flag = $key -\n";
		
		$temp_array = $array;
		var_dump(rsort($temp_array, $flag) );
		var_dump($temp_array);
	}
	$count++;
}

echo "Done";
?>

--EXPECTF--
*** Testing rsort() : variation ***

-- Iteration 1 --
- With Default sort flag -
bool(true)
array(11) {
  [0]=>
  unicode(4) "\xhh"
  [1]=>
  unicode(2) "\e"
  [2]=>
  unicode(4) "\ddd"
  [3]=>
  unicode(3) "\cx"
  [4]=>
  unicode(2) "\a"
  [5]=>
  unicode(1) ""
  [6]=>
  unicode(1) ""
  [7]=>
  unicode(1) "
"
  [8]=>
  unicode(1) "	"
  [9]=>
  NULL
  [10]=>
  NULL
}
- Sort flag = SORT_REGULAR -
bool(true)
array(11) {
  [0]=>
  unicode(4) "\xhh"
  [1]=>
  unicode(2) "\e"
  [2]=>
  unicode(4) "\ddd"
  [3]=>
  unicode(3) "\cx"
  [4]=>
  unicode(2) "\a"
  [5]=>
  unicode(1) ""
  [6]=>
  unicode(1) ""
  [7]=>
  unicode(1) "
"
  [8]=>
  unicode(1) "	"
  [9]=>
  NULL
  [10]=>
  NULL
}
- Sort flag = SORT_STRING -
bool(true)
array(11) {
  [0]=>
  unicode(4) "\xhh"
  [1]=>
  unicode(2) "\e"
  [2]=>
  unicode(4) "\ddd"
  [3]=>
  unicode(3) "\cx"
  [4]=>
  unicode(2) "\a"
  [5]=>
  unicode(1) ""
  [6]=>
  unicode(1) ""
  [7]=>
  unicode(1) "
"
  [8]=>
  unicode(1) "	"
  [9]=>
  NULL
  [10]=>
  NULL
}

-- Iteration 2 --
- With Default sort flag -
bool(true)
array(12) {
  [0]=>
  unicode(1) "x"
  [1]=>
  unicode(2) "ww"
  [2]=>
  unicode(3) "ttt"
  [3]=>
  unicode(6) "oraNGe"
  [4]=>
  unicode(5) "lemoN"
  [5]=>
  unicode(6) "banana"
  [6]=>
  unicode(5) "apple"
  [7]=>
  unicode(1) "X"
  [8]=>
  unicode(4) "Test"
  [9]=>
  unicode(4) "TTTT"
  [10]=>
  unicode(6) "Orange"
  [11]=>
  unicode(6) "BANANA"
}
- Sort flag = SORT_REGULAR -
bool(true)
array(12) {
  [0]=>
  unicode(1) "x"
  [1]=>
  unicode(2) "ww"
  [2]=>
  unicode(3) "ttt"
  [3]=>
  unicode(6) "oraNGe"
  [4]=>
  unicode(5) "lemoN"
  [5]=>
  unicode(6) "banana"
  [6]=>
  unicode(5) "apple"
  [7]=>
  unicode(1) "X"
  [8]=>
  unicode(4) "Test"
  [9]=>
  unicode(4) "TTTT"
  [10]=>
  unicode(6) "Orange"
  [11]=>
  unicode(6) "BANANA"
}
- Sort flag = SORT_STRING -
bool(true)
array(12) {
  [0]=>
  unicode(1) "x"
  [1]=>
  unicode(2) "ww"
  [2]=>
  unicode(3) "ttt"
  [3]=>
  unicode(6) "oraNGe"
  [4]=>
  unicode(5) "lemoN"
  [5]=>
  unicode(6) "banana"
  [6]=>
  unicode(5) "apple"
  [7]=>
  unicode(1) "X"
  [8]=>
  unicode(4) "Test"
  [9]=>
  unicode(4) "TTTT"
  [10]=>
  unicode(6) "Orange"
  [11]=>
  unicode(6) "BANANA"
}
Done