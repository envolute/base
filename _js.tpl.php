<?php
defined('_JEXEC') or die;

// Add javascript
$base		= JURI::base(true);
$JRoot		= ($base) ? $base.'/' : '';
$tplPath	= $JRoot.'templates/base';
$jsPath		= $tplPath.'/js';
$bootPath	= '../core/bootstrap/js/';
$corePath	= '../core/js/';
$cmsPath	= '../cms/js/';
// BRoot
// alteração por causa do parametro 'b' do minify
// nele não deve ter a '\' no início...
$BRoot	= ($base) ? substr($base,1,strlen($base)).'/' : '';
$BPath	= $BRoot.'templates/base/js';

// utilize essa lista para adicionar outros arquivos javascript
$jsBaseFiles		= array();
// bootstrap
$jsBaseFiles[]		= $bootPath.'bootstrap.min.js';
$jsBaseFiles[]		= $bootPath.'../libs/bootstrap-tabdrop.js';
$jsBaseFiles[]		= $bootPath.'../libs/bootstrap-hover-dropdown.min.js';
// cms
$jsBaseFiles[]		= $cmsPath.'cms.js';
// core
$jsBaseFiles[]		= $corePath.'browser/respond.min.js';
$jsBaseFiles[]		= $corePath.'template/isInViewport.min.js';
$jsBaseFiles[]		= $corePath.'content/fontsize.js';
$jsBaseFiles[]		= $corePath.'ie.js';
// custom
$jsBaseFiles[]		= 'custom.js';

// loop para gerar lista de arquivos css
$js = "";
foreach ($jsBaseFiles as $i) {
	$js .= $i.',';
}
$js = substr($js, 0, -1);

// CHOSEN -> Carrega por default
JHtml::_('formbehavior.chosen', 'select:not(.no-chosen)');

?>

<!-- load javascript -->
<script type="text/javascript" src="<?php echo $tplPath ?>/min/index.php?b=<?php echo $BPath ?>&f=<?php echo $js?>"></script>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
	<script src="<?php echo $tplPath ?>/core/js/browser/html5shiv-printshiv.js"></script>
<![endif]-->
