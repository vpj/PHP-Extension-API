--TEST--
Bug #43073 (TrueType bounding box is wrong for angle<>0)
--SKIPIF--
<?php
	if(!extension_loaded('gd')){ die('skip gd extension not available'); }
	if(!function_exists('imagettftext')) die('skip imagettftext() not available');
?>
--FILE--
<?php
$cwd = dirname(__FILE__);
$font = "$cwd/test8859.ttf";
$delta_t = 360.0 / 16; # Make 16 steps around
$g = imagecreate(800, 800);
$bgnd  = imagecolorallocate($g, 255, 255, 255);
$black = imagecolorallocate($g, 0, 0, 0);
$x = 100;
$y = 0;
$cos_t = cos(deg2rad($delta_t));
$sin_t = sin(deg2rad($delta_t));
for ($angle = 0.0; $angle < 360.0; $angle += $delta_t) {
  $bbox = imagettftext($g, 24, $angle, 400+$x, 400+$y, $black, $font, 'ABCDEF');
  $s = vsprintf("(%d, %d), (%d, %d), (%d, %d), (%d, %d)\n", $bbox);
  echo $s;
  $temp = $cos_t * $x + $sin_t * $y;
  $y    = $cos_t * $y - $sin_t * $x;
  $x    = $temp;
}
?>
--EXPECTF--
(500, 400), (579, 400), (579, 370), (500, 370)
(492, 361), (575, 327), (564, 301), (480, 335)
(470, 329), (540, 260), (521, 242), (451, 312)
(438, 307), (478, 210), (461, 204), (420, 301)
(400, 300), (400, 193), (383, 193), (383, 300)
(361, 307), (319, 207), (297, 216), (338, 317)
(329, 329), (254, 254), (233, 275), (307, 351)
(307, 361), (215, 323), (203, 354), (294, 392)
(300, 400), (203, 400), (203, 431), (300, 431)
(307, 438), (219, 474), (229, 501), (318, 465)
(329, 470), (263, 535), (281, 553), (347, 489)
(361, 492), (326, 575), (343, 582), (378, 499)
(400, 500), (400, 584), (416, 584), (416, 500)
(438, 492), (468, 567), (490, 559), (460, 483)
(470, 470), (523, 525), (545, 505), (491, 449)
(492, 438), (560, 467), (572, 436), (504, 408)
