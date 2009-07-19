--TEST--
Test natcasesort() function : object functionality - mixed visibility within objects
--FILE--
<?php
/* Prototype  : bool natcasesort(array &$array_arg)
 * Description: Sort an array using case-insensitive natural sort
 * Source code: ext/standard/array.c
 */

/*
 * Pass natcasesort() an array of objects which have properties of different
 * visibilities to test how it re-orders the array.
 */

echo "*** Testing natcasesort() : object functionality ***\n";

// class declaration for string objects
class for_string_natcasesort
{
	public $public_class_value;
	private $private_class_value;
	protected $protected_class_value;
	// initializing object member value
	function __construct($value1, $value2,$value3){
		$this->public_class_value = $value1;
		$this->private_class_value = $value2;
		$this->protected_class_value = $value3;
	}

	// return string value
	function __tostring() {
		return (string)$this->public_class_value;
	}

}

// array of string objects
$unsorted_str_obj = array (
new for_string_natcasesort("axx","AXX","ass"),
new for_string_natcasesort("t","eee","abb"),
new for_string_natcasesort("w","W", "c"),
new for_string_natcasesort("py","PY", "pt"),
);


echo "\n-- Testing natcasesort() by supplying object arrays --\n";

// testing natcasesort() function by supplying string object array
$temp_array = $unsorted_str_obj;
var_dump(natcasesort($temp_array) );
var_dump($temp_array);

echo "Done";
?>

--EXPECTF--
*** Testing natcasesort() : object functionality ***

-- Testing natcasesort() by supplying object arrays --
bool(true)
array(4) {
  [0]=>
  object(for_string_natcasesort)#%d (3) {
    [u"public_class_value"]=>
    unicode(3) "axx"
    [u"private_class_value":u"for_string_natcasesort":private]=>
    unicode(3) "AXX"
    [u"protected_class_value":protected]=>
    unicode(3) "ass"
  }
  [3]=>
  object(for_string_natcasesort)#%d (3) {
    [u"public_class_value"]=>
    unicode(2) "py"
    [u"private_class_value":u"for_string_natcasesort":private]=>
    unicode(2) "PY"
    [u"protected_class_value":protected]=>
    unicode(2) "pt"
  }
  [1]=>
  object(for_string_natcasesort)#%d (3) {
    [u"public_class_value"]=>
    unicode(1) "t"
    [u"private_class_value":u"for_string_natcasesort":private]=>
    unicode(3) "eee"
    [u"protected_class_value":protected]=>
    unicode(3) "abb"
  }
  [2]=>
  object(for_string_natcasesort)#%d (3) {
    [u"public_class_value"]=>
    unicode(1) "w"
    [u"private_class_value":u"for_string_natcasesort":private]=>
    unicode(1) "W"
    [u"protected_class_value":protected]=>
    unicode(1) "c"
  }
}
Done
