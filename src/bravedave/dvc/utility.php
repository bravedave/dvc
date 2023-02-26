<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

abstract class utility {

	static function CreateThumb($fpath, $mpath, $Wpx = 120, $Hpx = -1, $quality = 75) {

		$Defaults = [
			'BgColor' => [0xff, 0xff, 0xff],
			'Px' => 100,
			'Quality' => 90,
			'CropXPos' => 0.5,
			'CropYPos' => 0.5,
			'ImTypes' => [1 => "gif", 2 => "jpeg", 3 => "png", 15 => "wbmp", 16 => "xbm"],
			'ImRx' => ["/\\.(gif|png|jpe|jpe?g|wbmp|xbm)$/", "!^th\\d+---!"]
		];

		list($w, $h, $t) = @getimagesize($fpath);

		if ($Hpx < 0) $Hpx = $h * ($Wpx / $w);

		$ox = $w < $Wpx ? round(($Wpx - $w) / 2) : 0;
		$oy = $h < $Hpx ? round(($Hpx - $h) / 2) : 0;
		$cx = 0;	//$h < $w ? round( ( $w-$h ) * $Defaults['CropXPos'] ) : 0;
		$cy = 0;	//$w < $h ? round( ( $h-$w ) * $Defaults['CropYPos'] ) : 0;
		$ow = $w < $Wpx ? $w : $Wpx;
		$oh = $h < $Hpx ? $h : $Hpx;
		$ow = min($w, $Wpx);
		$oh = min($h, $Hpx);
		$gd2 = function_exists('imagecreatetruecolor');
		$imcopy = ($gd2) ? 'imagecopyresampled' : 'imagecopyresized';
		$imcreate = ($gd2) ? 'imagecreatetruecolor' : 'imagecreate';
		$fcreate = 'imagecreatefrom' . $Defaults['ImTypes'][$t];
		$img = $fcreate($fpath);
		if (!@$img) return;

		$nimg = $imcreate((int)$Wpx, (int)$Hpx);

		list($rr, $gg, $bb) = $Defaults['BgColor'];
		imagefill($nimg, 0, 0, imagecolorallocate($nimg, $rr, $gg, $bb));

		$imcopy($nimg, $img, (int)$ox, (int)$oy, (int)$cx, (int)$cy, (int)$ow, (int)$oh, (int)$w, (int)$h);

		imagedestroy($img);
		if (function_exists('imageconvolution'))
			imageconvolution($nimg, [[-1, -1, -1], [-1, 16, -1], [-1, -1, -1]], 8, 0);

		imagejpeg($nimg, $mpath, $quality);
		imagedestroy($nimg);
	}
}
