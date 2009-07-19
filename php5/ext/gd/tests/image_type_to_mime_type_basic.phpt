--TEST--
image_type_to_mime_type()
--SKIPIF--
<?php 
	if (!function_exists('image_type_to_mime_type')) die('skip image_type_to_mime_type() not available'); 
	require_once('skipif_imagetype.inc');
?>
--FILE--
<?php

/* Prototype  : string image_type_to_mime_type  ( int $imagetype  )
 * Description: Get Mime-Type for image-type returned by getimagesize, exif_read_data, exif_thumbnail, exif_imagetype.
 * Source code: ext/standard/image.c
 * Alias to functions: 
 */

echo "Starting image_type_to_mime_type() test\n\n";

$image_types = array (
	IMAGETYPE_GIF,     
	IMAGETYPE_JPEG,     
	IMAGETYPE_PNG,      
	IMAGETYPE_SWF,      
	IMAGETYPE_PSD,      
	IMAGETYPE_BMP,  
	IMAGETYPE_TIFF_II,  
	IMAGETYPE_TIFF_MM,  
	IMAGETYPE_JPC,      
	IMAGETYPE_JP2,      
	IMAGETYPE_JPX,      
	IMAGETYPE_JB2,      
	IMAGETYPE_IFF,     
	IMAGETYPE_WBMP, 
	IMAGETYPE_JPEG2000, 
	IMAGETYPE_XBM      
);

	foreach($image_types as $image_type) {
		var_dump(image_type_to_mime_type($image_type));
	}

echo "\nDone image_type_to_mime_type() test\n";
?>
--EXPECT--
Starting image_type_to_mime_type() test

unicode(9) "image/gif"
unicode(10) "image/jpeg"
unicode(9) "image/png"
unicode(29) "application/x-shockwave-flash"
unicode(9) "image/psd"
unicode(14) "image/x-ms-bmp"
unicode(10) "image/tiff"
unicode(10) "image/tiff"
unicode(24) "application/octet-stream"
unicode(9) "image/jp2"
unicode(24) "application/octet-stream"
unicode(24) "application/octet-stream"
unicode(9) "image/iff"
unicode(18) "image/vnd.wap.wbmp"
unicode(24) "application/octet-stream"
unicode(9) "image/xbm"

Done image_type_to_mime_type() test
