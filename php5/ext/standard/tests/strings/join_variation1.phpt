--TEST--
Test join() function : usage variations - unexpected values for 'glue' argument
--FILE--
<?php
/* Prototype  : string join( string $glue, array $pieces )
 * Description: Join array elements with a string
 * Source code: ext/standard/string.c
 * Alias of function: implode()
*/

/*
 * testing join() by passing different unexpected value for glue argument
*/

echo "*** Testing join() : usage variations ***\n";
// initialize all required variables
$pieces = array("element1", "element2");

// get an unset variable
$unset_var = 'string_val';
unset($unset_var);

// get a resource variable
$fp = fopen(__FILE__, "r");

// define a class
class test
{
   var $t = 10;
   function __toString() {
     return  "testObject";
   }
}

// array with different values
$values =  array (

  // integer values
  0,
  1,
  12345,
  -2345,

  // float values
  10.5,
  -10.5,
  10.1234567e10,
  10.7654321E-10,
  .5,

  // array values
  array(),
  array(0),
  array(1),
  array(1, 2),
  array('color' => 'red', 'item' => 'pen'),

  // boolean values
  true,
  false,
  TRUE,
  FALSE,

  // objects
  new test(),

  // empty string
  "",
  '',

  // null vlaues
  NULL,
  null,
  
  // resource variable
  $fp,

  // undefined variable
  @$undefined_var,

  // unset variable
  @$unset_var
);


// loop through each element of the array and check the working of join()
// when $glue arugment is supplied with different values
echo "\n--- Testing join() by supplying different values for 'glue' argument ---\n";
$counter = 1;
for($index = 0; $index < count($values); $index ++) {
  echo "-- Iteration $counter --\n";
  $glue = $values [$index];

  var_dump( join($glue, $pieces) );

  $counter ++;
}

echo "Done\n";
?>
--EXPECTF--
*** Testing join() : usage variations ***

--- Testing join() by supplying different values for 'glue' argument ---
-- Iteration 1 --
unicode(17) "element10element2"
-- Iteration 2 --
unicode(17) "element11element2"
-- Iteration 3 --
unicode(21) "element112345element2"
-- Iteration 4 --
unicode(21) "element1-2345element2"
-- Iteration 5 --
unicode(20) "element110.5element2"
-- Iteration 6 --
unicode(21) "element1-10.5element2"
-- Iteration 7 --
unicode(28) "element1101234567000element2"
-- Iteration 8 --
unicode(29) "element11.07654321E-9element2"
-- Iteration 9 --
unicode(19) "element10.5element2"
-- Iteration 10 --

Notice: Array to string conversion in %s on line %d
unicode(0) ""
-- Iteration 11 --

Notice: Array to string conversion in %s on line %d
unicode(1) "0"
-- Iteration 12 --

Notice: Array to string conversion in %s on line %d
unicode(1) "1"
-- Iteration 13 --

Notice: Array to string conversion in %s on line %d
unicode(7) "1Array2"
-- Iteration 14 --

Notice: Array to string conversion in %s on line %d
unicode(11) "redArraypen"
-- Iteration 15 --
unicode(17) "element11element2"
-- Iteration 16 --
unicode(16) "element1element2"
-- Iteration 17 --
unicode(17) "element11element2"
-- Iteration 18 --
unicode(16) "element1element2"
-- Iteration 19 --
unicode(26) "element1testObjectelement2"
-- Iteration 20 --
unicode(16) "element1element2"
-- Iteration 21 --
unicode(16) "element1element2"
-- Iteration 22 --
unicode(16) "element1element2"
-- Iteration 23 --
unicode(16) "element1element2"
-- Iteration 24 --
unicode(30) "element1Resource id #%delement2"
-- Iteration 25 --
unicode(16) "element1element2"
-- Iteration 26 --
unicode(16) "element1element2"
Done
