<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Definição de datas em português
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Recife');

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

// ACESSO

	// Pega a url atual
	$curl = JURI::current();

	// Informações sobre o usuário
	$user = JFactory::getUser();
	$groups = $user->groups;
	$access = ($user->guest) ? 'public' : 'logged';

	// Define o tipo de acesso do site -> accessBase: { 1 => 'Publico', 0 => 'Logado'}
	$accessBase = $this->params->get('accessBase', 1);
	// se o acesso for "logado" e o usuário não estiver, redireciona para a tela de login
	if($accessBase == 0 && $user->guest && (strpos($curl, 'login') === false && strpos($curl, 'registration') === false)) :
		$app->redirect(JURI::base().'login', false);
		return;
	endif;

// TIPO DE PÁGINA

	// Parametros para definição da página
	$sitename	= $app->getCfg('sitename');
	$option		= $app->input->getCmd('option');
	$view		= $app->input->getCmd('view') ? ' view-'.$app->input->getCmd('view') : '';
	$layout		= $app->input->getCmd('layout') ? ' layout-'.$app->input->getCmd('layout') : '';
	$task		= $app->input->getCmd('task') ? ' task-'.$app->input->getCmd('task') : '';
	$itemid		= $app->input->getCmd('Itemid') ? ' itemid-'.$app->input->getCmd('Itemid') : '';

	// Full Content -> Páginas sem as áreas laterais no conteúdo
	// -> necessário em páginas para administração de conteúdo no frontend
	$hidePos = false;
	$cond = eval("return ".$this->params->get('items_full').";");
	if($cond) $hidePos = true; // esconde as áreas (left, right e sidebar)

	// Atribui à página a classe definida no parâmetro 'pageclass_sfx' do item de menu ativo
	$menu = $app->getMenu()->getActive();
	$pageclass = is_object($menu) ? ' '.$menu->params->get('pageclass_sfx') : '';

// TEMPLATE PARAMS

	// Definições da responsividade
	$responsive	= $this->params->get('responsive', 1);
	$screen		= ($responsive ? ' responsive' : ' not-responsive');
	$isMM		= ($responsive) ? ' isMM' : '';
	$hiddenXS	= ($responsive) ? 'hidden-xs' : '';
	$showFooter	= $this->params->get('showFooter', 1);
	// define a largura máxima do container
	$fullScreen	= $this->params->get('fullScreen', 0);
	$screen		.= ($this->params->get('fullScreen', 0) ? ' fullScreen' : '');
	$container	= 'container bs-container';
	// NavBar (admin)
	$navbarStyle	= ($this->params->get('navbarStyle') == 2) ? 'navbar-inverse' : 'navbar-default';
	$navbarFixed	= ($this->params->get('navbarFixed')) ? 'navbar-fixed-top' : '';
	// dashboard elements
	$navtopFixed	= ($this->params->get('navtopFixed', 1)) ? 'navtop-fixed' : '';
	// css elements
	$loadBorders	= $this->params->get('loadCssBorders', 0);
	// web services
	$analyticsCode	= $this->params->get('analyticsCode');

// GRID
	//Header
	$header_1	= $this->params->get('header_1');
	$header_2	= $this->params->get('header_2');
	$header_3	= $this->params->get('header_3');

	//Breadcrumb -> posição do breadcrumb (0 => top, 1 => bottom)
	$bread_pos	= $this->params->get('bread_pos', 0);

	//Featured
	$featured_1	= $this->params->get('featured_1');
	$featured_2	= $this->params->get('featured_2');
	$featured_3	= $this->params->get('featured_3');
	$featured_4	= $this->params->get('featured_4');
	$featured_5	= $this->params->get('featured_5');
	$featured_6	= $this->params->get('featured_6');

	//Full Content

		// Largura das áreas laterais
		$leftWidth	= ($this->countModules('left') && !$hidePos) ? $this->params->get('leftWidth') : 0;
		$sidebarWidth	= ($this->countModules('sidebar') && !$hidePos) ? $this->params->get('sidebarWidth') : 0;
		$sidebarFloat	= $this->params->get('sidebarFloat', 'left');
		$rightWidth	= ($this->countModules('right') && !$hidePos) ? $this->params->get('rightWidth') : 0;

		// Funções para definição da largura do conteúdo

			// define a grid default (xs, sm, md...)
			$gDef	= ($responsive) ? $this->params->get('gridDefault', 'sm') : 'xs';

			// define the grid
			// $sz -> element size (1 to 12)
			// $lg -> enable in large desktops (>1200px)
			// $pr -> media print (default = true)
			function grid($sz, $def = 'sm') {

				$grid  = isset($sz) ? 'col-'.$def.'-'.$sz.' clearfix' : '';
				return $grid;
			}
			// calculates the offset
			function setOffset($x = 12, $div = 1){
				if($div <= 0) $div = 1;
				if($div == 5 || ($div > 6 && $div < 12)) :
					$w = '2_4'; /* col-##-2_4 (5 cols) esta definido em base.bootstrap.css */
				else :
					$w = ($x < 12) ? (12 - $x) / $div : $x / $div;
				endif;
				return $w;
			}

	//Syndicate
	$syndicate_1	= $this->params->get('syndicate_1');
	$syndicate_2	= $this->params->get('syndicate_2');
	$syndicate_3	= $this->params->get('syndicate_3');
	$syndicate_4	= $this->params->get('syndicate_4');
	$syndicate_5	= $this->params->get('syndicate_5');
	$syndicate_6	= $this->params->get('syndicate_6');

	//Bottom
	$bottom_1	= $this->params->get('bottom_1');
	$bottom_2	= $this->params->get('bottom_2');
	$bottom_3	= $this->params->get('bottom_3');
	$bottom_4	= $this->params->get('bottom_4');
	$bottom_5	= $this->params->get('bottom_5');
	$bottom_6	= $this->params->get('bottom_6');

	//Footer
	$footer_1	= $this->params->get('footer_1');
	$footer_2	= $this->params->get('footer_2');
	$footer_3	= $this->params->get('footer_3');

	// contagem de módulos por área (header, featured, syndicate, bottom, footer)
	$headerCount = $footerCount = 0;
	for($i = 1; $i <= 3; $i++){
		if($this->countModules('header-'.$i)) $headerCount += 1;
		if($this->countModules('footer-'.$i)) $footerCount += 1;
	}
	$featCount = $syndCount = $bottomCount = 0;
	for($i = 1; $i <= 6; $i++){
		if($this->countModules('featured-'.$i)) $featCount += 1;
		if($this->countModules('syndicate-'.$i)) $syndCount += 1;
		if($this->countModules('bottom-'.$i)) $bottomCount += 1;
	}

// BROWSER

	$docProps = 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$this->language.'" lang="'.$this->language.'" dir="'.$this->direction.'"';
	$docClass = '';

	// Verifica o browser do usuário
	$ua = $_SERVER["HTTP_USER_AGENT"];

	// if IE <= 8
	if(strpos($ua, 'MSIE') && preg_match('/msie [2-8]/i',$ua)){
		$docClass .= ' ie-lte-8';
	} else {
	// if IE11 or NOT IE
		$docClass .= (strpos($ua, 'Trident/7.0; rv:11.0')) ? ' ie ie-11' : ' not-ie';
	}

	// Verifica o tipo de Dispositivo do usuário (pc, tablet, celular)
	// -> utiliza a classe "Mobile_Detect.php"
	if(!class_exists('Mobile_Detect')) { // essa validação evita um erro de 'redeclare class'
		require_once ('templates/base/source/Mobile_Detect.php');
		$detect = new Mobile_Detect;
		$docClass .= ($detect->isMobile() && $responsive) ? ($detect->isTablet() ? ' tablet' : ' phone') : ' desktop';
	}

?>
<!DOCTYPE html>
<html class="<?php echo $docClass.$isMM?>" <?php echo $docProps?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if($responsive) :?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php endif; ?>
	<!-- Set class for especific browser -->
	<script type="text/javascript" src="templates/base/core/js/browser/css_browser_selector.js"></script>

	<?php
	// LOAD JAVASCRIPT CORE
	// para que os arquivos carregados dinâmicamente possam utilizar os recursos da base
	// o script principal deve ser carregado após o jquery e antes dos arquivos
	$doc->addScript('templates/base/js/core.js');
	?>

	<jdoc:include type="head" />

	<?php

	// 'BASE' CSS FILES
	require_once('templates/base/_css.tpl.php');

	// 'BASE' JAVASCRIPT FILES
	// IMPORTANTE: arquivos javascript locais são carregados no final do código (página);
	// Isso é uma recomendação para melhorar o tempo de carregamento da página

	?>

</head>
