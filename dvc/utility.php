<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
	*/
namespace dvc;

abstract class utility {
	static function CreateThumb( $fpath, $mpath, $Wpx = 120, $Hpx = -1, $quality = 75 ) {

		$Defaults = array(
			'BgColor' => array(0xff, 0xff, 0xff),
			'Px' => 100,
			'Quality' => 90,
			'CropXPos' => 0.5,
			'CropYPos' => 0.5,
			'ImTypes' => array(1=>"gif",2=>"jpeg",3=>"png",15=>"wbmp",16=>"xbm"),
			'ImRx' => array("/\\.(gif|png|jpe|jpe?g|wbmp|xbm)$/", "!^th\\d+---!")
			);

		list($w, $h, $t) = @getimagesize( $fpath );

		if ( $Hpx < 0 )
			$Hpx = $h * ( $Wpx/$w );

		$ox = $w < $Wpx ? round( ( $Wpx - $w )/2 ) : 0;
		$oy = $h < $Hpx ? round( ( $Hpx - $h )/2 ) : 0;
		$cx = 0;	//$h < $w ? round( ( $w-$h ) * $Defaults['CropXPos'] ) : 0;
		$cy = 0;	//$w < $h ? round( ( $h-$w ) * $Defaults['CropYPos'] ) : 0;
		$ow = $w < $Wpx ? $w : $Wpx;
		$oh = $h < $Hpx ? $h : $Hpx;
		$ow = min( $w, $Wpx );
		$oh = min( $h, $Hpx );
		$gd2 = function_exists('imagecreatetruecolor');
		$imcopy = ($gd2)?'imagecopyresampled':'imagecopyresized';
		$imcreate = ($gd2)?'imagecreatetruecolor':'imagecreate';
		$fcreate = 'imagecreatefrom'.$Defaults['ImTypes'][$t];
		$img = $fcreate($fpath);
		if (!@$img) return;

		$nimg = $imcreate( $Wpx, $Hpx );

		list($rr, $gg, $bb) = $Defaults['BgColor'];
		imagefill($nimg, 0, 0, imagecolorallocate($nimg, $rr, $gg, $bb));

		$imcopy( $nimg, $img, $ox, $oy, $cx, $cy, $ow, $oh, $w, $h );

		imagedestroy( $img );
		if(function_exists('imageconvolution'))
			imageconvolution( $nimg, array(array(-1,-1,-1),array(-1,16,-1),array(-1,-1,-1)), 8, 0);
		imagejpeg( $nimg, $mpath, $quality );
		imagedestroy( $nimg );

	}

}