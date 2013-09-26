<?php
/*
*
* Simple captcha script used for JSONP example.
*
*/
include 'JSONP.class.php';

function hex2rgb($hex) {
   $hex = str_replace('#', NULL, $hex);
   if (strlen($hex) == 3) {
      $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
      $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
      $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
   } else {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
   }
   return array($r, $g, $b);
}

function validateHexColor($hex) {
	$hex = (substr($hex, 0, 1) == '#') ? $hex : '#' . $hex;
	if (preg_match('/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $hex)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

session_start();

if (isset($_GET['expected'])) {
	$JSONP = new JSONP;
	if ($_GET['expected'] == $_SESSION['expected']) {
		$JSONP->encode(TRUE);
	} else {
		$JSONP->encode(FALSE);
	}
} else {
	// Background color
	if (isset($_GET['bg']) && validateHexColor($_GET['bg'])) {
		$bgc = hex2rgb($_GET['bg']);
	} else {
		$bgc = hex2rgb('#fff');
	}

	// Foreground color
	if (isset($_GET['fg']) && validateHexColor($_GET['fg'])) {
		$fgc = hex2rgb($_GET['fg']);
	} else {
		$fgc = hex2rgb('#000');
	}

	// Font
	if (isset($_GET['font']) && (int) $_GET['font'] > 0 && (int) $_GET['font'] <= 5) {
		$font = (int) $_GET['font'];
	} else {
		$font = 5;
	}
	
	// Is code?
	if (isset($_GET['code']) && $_GET['code']) {
		$string = rand(1000, 9999);
		$_SESSION['expected'] = $string;
	} else {
		$n1 = rand(1, 9);
		$n2 = rand(1, 9);
		$string = $n1 . ' + ' . $n2;
		$_SESSION['expected'] = $n1 + $n2;
	}
	
	// Width
	$w = imagefontwidth($font) * strlen($string) + 10;
	
	// Height
	$h = imagefontheight($font) + 10;
	
	$im = imagecreatetruecolor($w, $h);
	$bg = imagecolorallocate($im, $bgc[0], $bgc[1], $bgc[2]); //background color blue
	$fg = imagecolorallocate($im, $fgc[0], $fgc[1], $fgc[2]); //text color white

	imagefill($im, 0, 0, $bg);
	imagestring($im, $font, 5, 5,  $string, $fg);
	header("Cache-Control: no-cache, must-revalidate");
	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);
}