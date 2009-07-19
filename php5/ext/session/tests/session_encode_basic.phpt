--TEST--
Test session_encode() function : basic functionality
--SKIPIF--
<?php include('skipif.inc'); ?>
--FILE--
<?php

ob_start();

/* 
 * Prototype : string session_encode(void)
 * Description : Encodes the current session data as a string
 * Source code : ext/session/session.c 
 */

echo "*** Testing session_encode() : basic functionality ***\n";

// Get an unset variable
$unset_var = 10;
unset($unset_var);

class classA
{
    public function __toString() {
        return "Hello World!";
    }
}

$heredoc = <<<EOT
Hello World!
EOT;

$fp = fopen(__FILE__, "r");

// Unexpected values to be passed as arguments
$inputs = array(

       // Integer data
/*1*/  0,
       1,
       12345,
       -2345,

       // Float data
/*5*/  10.5,
       -10.5,
       12.3456789000e10,
       12.3456789000E-10,
       .5,

       // Null data
/*10*/ NULL,
       null,

       // Boolean data
/*12*/ true,
       false,
       TRUE,
       FALSE,
       
       // Empty strings
/*16*/ "",
       '',

       // Invalid string data
/*18*/ "Nothing",
       'Nothing',
       $heredoc,
       
       // Object data
/*21*/ new classA(),

       // Undefined data
/*22*/ @$undefined_var,

       // Unset data
/*23*/ @$unset_var,

       // Resource variable
/*24*/ $fp
);

var_dump(session_start());
$iterator = 1;
foreach($inputs as $input) {
    echo "\n-- Iteration $iterator --\n";
    $_SESSION["data"] = $input;
    var_dump(session_encode());
    $iterator++;
};

var_dump(session_destroy());
fclose($fp);
echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : basic functionality ***
bool(true)

-- Iteration 1 --
unicode(9) "data|i:0;"

-- Iteration 2 --
unicode(9) "data|i:1;"

-- Iteration 3 --
unicode(13) "data|i:12345;"

-- Iteration 4 --
unicode(13) "data|i:-2345;"

-- Iteration 5 --
unicode(12) "data|d:10.5;"

-- Iteration 6 --
unicode(13) "data|d:-10.5;"

-- Iteration 7 --
unicode(20) "data|d:123456789000;"

-- Iteration 8 --
unicode(86) "data|d:1.2345678899999999145113427164344339914681114578343112953007221221923828125E-9;"

-- Iteration 9 --
unicode(11) "data|d:0.5;"

-- Iteration 10 --
unicode(7) "data|N;"

-- Iteration 11 --
unicode(7) "data|N;"

-- Iteration 12 --
unicode(9) "data|b:1;"

-- Iteration 13 --
unicode(9) "data|b:0;"

-- Iteration 14 --
unicode(9) "data|b:1;"

-- Iteration 15 --
unicode(9) "data|b:0;"

-- Iteration 16 --
unicode(12) "data|U:0:"";"

-- Iteration 17 --
unicode(12) "data|U:0:"";"

-- Iteration 18 --
unicode(19) "data|U:7:"Nothing";"

-- Iteration 19 --
unicode(19) "data|U:7:"Nothing";"

-- Iteration 20 --
unicode(25) "data|U:12:"Hello World!";"

-- Iteration 21 --
unicode(22) "data|O:6:"classA":0:{}"

-- Iteration 22 --
unicode(7) "data|N;"

-- Iteration 23 --
unicode(7) "data|N;"

-- Iteration 24 --
unicode(9) "data|i:0;"
bool(true)
Done

