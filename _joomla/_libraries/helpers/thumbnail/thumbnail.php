<?php

// caminho para a imagem que será customizada. IMPORTANTE: Apenas imagens locais!
// retira o domínio, caso seja inserido...
$HTTP = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$image = str_replace($HTTP.$_SERVER['HTTP_HOST'],'',$_GET["file"]);

// caminho da imagem
$image = $_SERVER['DOCUMENT_ROOT'].$image;

// medidas para um tamanho em escala (mantem a proporção)
$sWidth = isset($_GET['maxw']) ? (int)$_GET['maxw'] : 0;
$sHeight = isset($_GET['maxh']) ? (int)$_GET['maxh'] : 0;

// medidas para um tamanho exato
// ajusta e, se necessário, corta a imagem para conseguir o tamanho sem distorcer...
$eWidth = isset($_GET['w']) ? (int)$_GET["w"] : 0;
$eHeight = isset($_GET['h']) ? (int)$_GET["h"] : 0;

$sType = '';
if($sWidth && $sHeight) $sType = 'scale';
if($eWidth && $eHeight) $sType = 'exact';

include_once('easyphpthumbnail.class.php');

// thumbnail
$thumb = new easyphpthumbnail;

if(file_exists($image)) :

	if ($thumb) :

		// largura e altura da imagem original
		$sz = getimagesize($image);
		$origW = $sz[0];
		$origH = $sz[1];

		$width = $origW;
		$height = $origH;
		if ($sType == 'scale') :

			$width = $sWidth;
			$height = $sHeight;

			// se 'A' < 'B' seta a largura
			if(($width / $origW) < ($height / $origH)) :
				$thumb -> Thumbwidth = $width;
			else :
				$thumb -> Thumbheight = $height;
			endif;

		elseif($sType == 'exact') :

			$width = $eWidth;
			$height = $eHeight;

			// se 'A' > 1 seta a altura (menor lado)
			if(($width / $origW) > ($height / $origH)) :
				$thumb -> Thumbwidth = $width;

				//Se necessário, corta a imagem
				$ch = floor(($origH * $width) / $origW);
				$nh = floor(($origH * $height) / $ch);
				$nh = floor(($origH - $nh)  / 2);
				$thumb -> Cropimage = array(1,1,0,0,$nh,$nh);

			else :
				$thumb -> Thumbheight = $height;

				// se necessário, corta a imagem
				$cw = floor(($origW * $height) / $origH);
				$nw = floor(($origW * $width) / $cw);
				$nw = floor(($origW - $nw)  / 2);
				$thumb -> Cropimage = array(1,1,$nw,$nw,0,0);
			endif;

		endif;

		// caso o thumbnail seja maior que a imagem original
		if($width > $origW) :
			$thumb -> Inflate = true;
		endif;

		// Create the thumbnail and output to screen
		$thumb -> Createthumb($image);
	else :
		echo 'No Thumb';
	endif;
else :
	// medidas para um tamanho em escala (mantem a proporção)
	$w = ($_GET['maxw']) ? (int)$_GET['maxw'] : NULL;
	$h = ($_GET['maxh']) ? (int)$_GET['maxh'] : NULL;
	if(is_null($w) && is_null($h)) :
		$w = ($_GET['w']) ? (int)$_GET['w'] : NULL;
		$h = ($_GET['h']) ? (int)$_GET['h'] : NULL;
	endif;

	if($thumb && !is_null($w) && !is_null($h)) :
		// gera uma imagem dinamica
		$msg = "Not found";
		$thumb -> Createcanvas($w,$h,IMAGETYPE_PNG,'#EEEEEE',false);
		$thumb -> Addtext = array(1,$msg,'50% 50%','',11,'#AAAAAA');
		$thumb -> Createthumb();
	endif;
endif;

?>
