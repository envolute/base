<?php
defined('_JEXEC') or die;

// Add Stylesheets
$base		= JURI::base(true);
$JRoot		= ($base) ? $base.'/' : '';
$tplPath	= $JRoot.'templates/base';
$cssPath	= $tplPath.'/css';

// BRoot
// alteração por causa do parametro 'b' do minify
// nele não deve ter a '\' no início...
$BRoot	= ($base) ? substr($base,1,strlen($base)).'/' : '';
$BPath	= $BRoot.'templates/base/css';

// utilize essa lista para adicionar outros arquivos css
$cssBaseFiles	= array();

// framework tools (user admin)
if($groups[8]) $cssBaseFiles[] = 'cms.navbar.css';

// add css files
$cssBaseFiles[]	= 'style.css';

// loop para gerar lista de arquivos css
$css = "";
foreach ($cssBaseFiles as $i) $css .= $i.',';
$css = isset($css) ? substr($css, 0, -1) : '';
?>

<!-- css template -->
<link rel="stylesheet" href="<?php echo $tplPath ?>/min/index.php?b=<?php echo $BPath?>&f=<?php echo $css?>" type="text/css" />
<link rel="stylesheet" href="<?php echo $tplPath ?>/min/index.php?f=<?php echo $cssPath?>/print.css" type="text/css" media="print" />

<!-- For IE support -->
<!--[if IE]>
	<link href="<?php echo $tplPath?>/css/style.ie.css" rel="stylesheet">
<![endif]-->
