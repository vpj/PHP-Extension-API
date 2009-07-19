--TEST--
image_type_to_mime_type() (passinf equivalent integer values)
--CREDITS--
Sanjay Mantoor <sanjay.mantoor@gmail.com>
--FILE--
<?php
/* Prototype  : string image_type_to_mime_type(int imagetype)
 * Description: Get Mime-Type for image-type returned by getimagesize, exif_read_data, exif_thumbnail, exif_imagetype 
 * Source code: ext/standard/image.c
 */

echo "*** Testing image_type_to_mime_type() : usage variations ***\n";

for($imagetype = 0; $imagetype <= IMAGETYPE_COUNT; ++$imagetype) {
  echo "\n-- Iteration $imagetype --\n";
  var_dump(image_type_to_mime_type($imagetype));
}
?>
===DONE===
--EXPECTREGEX--
\*\*\* Testing image_type_to_mime_type\(\) : usage variations \*\*\*

-- Iteration 0 --
unicode\(24\) "application\/octet-stream"

-- Iteration 1 --
unicode\(9\) "image\/gif"

-- Iteration 2 --
unicode\(10\) "image\/jpeg"

-- Iteration 3 --
unicode\(9\) "image\/png"

-- Iteration 4 --
unicode\(29\) "application\/x-shockwave-flash"

-- Iteration 5 --
unicode\(9\) "image\/psd"

-- Iteration 6 --
unicode\(14\) "image\/x-ms-bmp"

-- Iteration 7 --
unicode\(10\) "image\/tiff"

-- Iteration 8 --
unicode\(10\) "image\/tiff"

-- Iteration 9 --
unicode\(24\) "application\/octet-stream"

-- Iteration 10 --
unicode\(9\) "image\/jp2"

-- Iteration 11 --
unicode\(24\) "application\/octet-stream"

-- Iteration 12 --
unicode\(24\) "application\/octet-stream"

-- Iteration 13 --
unicode\(2[49]\) "application\/(x-shockwave-flash|octet-stream)"

-- Iteration 14 --
unicode\(9\) "image\/iff"

-- Iteration 15 --
unicode\(18\) "image\/vnd.wap.wbmp"

-- Iteration 16 --
unicode\(9\) "image\/xbm"

-- Iteration 17 --
unicode\(24\) "image\/vnd.microsoft.icon"

-- Iteration 18 --
unicode\(24\) "application\/octet-stream"
===DONE===
