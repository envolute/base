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

// TIPO DE PÁGINA

	// Parametros para definição da página
	$sitename	= $app->getCfg('sitename');
	$option		= $app->input->getCmd('option');
	$view		= $app->input->getCmd('view') ? ' view-'.$app->input->getCmd('view') : '';
	$layout		= $app->input->getCmd('layout') ? ' layout-'.$app->input->getCmd('layout') : '';
	$task		= $app->input->getCmd('task') ? ' task-'.$app->input->getCmd('task') : '';
	$itemid		= $app->input->getCmd('Itemid') ? ' itemid-'.$app->input->getCmd('Itemid') : '';

	// Atribui, à página, a classe definida no parâmetro 'pageclass_sfx' do item de menu ativo
	$menu = $app->getMenu()->getActive();
	$pageclass = is_object($menu) ? ' '.$menu->params->get('pageclass_sfx') : '';

	// Define o tipo de acesso do site -> accessBase: { 1 => 'Publico', 0 => 'Logado'}
	$accessBase = $this->params->get('accessBase', 1);
	// se o acesso for "logado" e o usuário não estiver, redireciona para a tela de login
	if($accessBase == 0 && $user->guest && strpos($pageclass, 'access-public') === false) :
		$app->redirect(JURI::base().'login', false);
		return;
	endif;

	// 'Mostra/esconde' o preloader
	$preloader = $this->params->get('preloader', 0) ? ' preloader' : '';

// TEMPLATE PARAMS

	// Definições da responsividade
	$responsive	= $this->params->get('responsive', 1);
	$screen		= ($responsive ? ' responsive' : ' not-responsive');
	$menuMobile		= ($responsive) ? ' isMM' : ''; // indica se carrega o menu responsivo
	// NavBar (admin)
  	$navbarContainer	= $this->params->get('navbar_container', 0);
	$navbarGroups = $this->params->get('navbarGroups');
	$navbarAccess = !empty($navbarGroups) ? array_intersect($groups, $navbarGroups) : ''; // se está na lista de grupos permitidos
	$hasAdmin = !empty($navbarAccess) ? ' isAdmin' : ''; // classe para setar a navbar ativa
	$navbarStyle	= $this->params->get('navbarStyle', 'navbar-light bg-light');
	$navbarFixed	= $this->params->get('navbarFixed', 1) == 1 ? 'fixed-top' : '';
	$navbarToggleable	= $this->params->get('navbarToggleable', 'sm');
	$navbarTogglerSide	= $this->params->get('navbarTogglerSide', 'right');
	// css elements
  // -> carrega os arquivos 'css' e 'js' para teste 'local'
	$loadDev	= $this->params->get('loadDev', 1);
	// web services
	$analyticsCode	= $this->params->get('analyticsCode');

// GRID

	// Header
	$params['header_class']	= $this->params->get('header_class');
	$params['header_1_newRowClass']	= $this->params->get('header_1_newRowClass');
	$params['header_1_container'] = $this->params->get('header_1_container', 'container');
	$params['header_1_newContainerClass']	= $this->params->get('header_1_newContainerClass');
	$params['header_1']  = $this->params->get('header_1');
	$params['header_2_newRow']       = $this->params->get('header_2_newRow', 0);
	$params['header_2_newRowClass']	= $this->params->get('header_2_newRowClass');
	$params['header_2_container'] = $this->params->get('header_2_container', 'container');
	$params['header_2_newContainerClass']	= $this->params->get('header_2_newContainerClass');
	$params['header_2']  = $this->params->get('header_2');
	$params['header_3_newRow']       = $this->params->get('header_3_newRow', 0);
	$params['header_3_newRowClass']	= $this->params->get('header_3_newRowClass');
	$params['header_3_container'] = $this->params->get('header_3_container', 'container');
	$params['header_3_newContainerClass']	= $this->params->get('header_3_newContainerClass');
	$params['header_3']  = $this->params->get('header_3');
	$params['header_4_newRow']       = $this->params->get('header_4_newRow', 0);
	$params['header_4_newRowClass']	= $this->params->get('header_4_newRowClass');
	$params['header_4_container'] = $this->params->get('header_4_container', 'container');
	$params['header_4_newContainerClass']	= $this->params->get('header_4_newContainerClass');
	$params['header_4']  = $this->params->get('header_4');
	$params['header_5_newRow']       = $this->params->get('header_5_newRow', 0);
	$params['header_5_newRowClass']	= $this->params->get('header_5_newRowClass');
	$params['header_5_container'] = $this->params->get('header_5_container', 'container');
	$params['header_5_newContainerClass']	= $this->params->get('header_5_newContainerClass');
	$params['header_5']  = $this->params->get('header_5');
	$params['header_6_newRow']       = $this->params->get('header_6_newRow', 0);
	$params['header_6_newRowClass']	= $this->params->get('header_6_newRowClass');
	$params['header_6_container'] = $this->params->get('header_6_container', 'container');
	$params['header_6_newContainerClass']	= $this->params->get('header_6_newContainerClass');
	$params['header_6']  = $this->params->get('header_6');
	$params['header_7_newRow']       = $this->params->get('header_7_newRow', 0);
	$params['header_7_newRowClass']	= $this->params->get('header_7_newRowClass');
	$params['header_7_container'] = $this->params->get('header_7_container', 'container');
	$params['header_7_newContainerClass']	= $this->params->get('header_7_newContainerClass');
	$params['header_7']  = $this->params->get('header_7');
	$params['header_8_newRow']       = $this->params->get('header_8_newRow', 0);
	$params['header_8_newRowClass']	= $this->params->get('header_8_newRowClass');
	$params['header_8_container'] = $this->params->get('header_8_container', 'container');
	$params['header_8_newContainerClass']	= $this->params->get('header_8_newContainerClass');
	$params['header_8']  = $this->params->get('header_8');

	// Sections
	$params['section_top'] = $this->params->get('section_top');
	$params['section_bottom'] = $this->params->get('section_bottom');

	// Full Content -> classe(s) adicional(is)
	$params['full_content_container'] = $this->params->get('full_content_container', 'container');

    // Full content Top -> classe(s) adicional(is)
    $params['full_content_top']	= $this->params->get('full_content_top'); // col

	// Grid das áreas laterais
	$params['left']  = $this->countModules('left') ? $this->params->get('left') : 0;
    $params['content_feft']   = $this->countModules('content-left') ? $this->params->get('content_left') : 0;
	$params['right'] = $this->countModules('right') ? $this->params->get('right') : 0;
    $params['content_right']  = $this->countModules('content-right') ? $this->params->get('content_right') : 0;

	// Full Content Bottom -> classe(s) adicional(is)
	$params['full_content_bottom']	= $this->params->get('full_content_bottom'); // col

  	// Content Top -> classe(s) adicional(is)
    $params['content_top']			= $this->params->get('content_top_class'); // col
    // Content Footer -> classe(s) adicional(is)
  	$params['content_bottom']		= $this->params->get('content_bottom_class'); // col
    // Component Top -> classe(s) adicional(is)
    $params['component_top']		= $this->params->get('component_top_class'); // col
    // Component Footer -> classe(s) adicional(is)
    $params['component_bottom']	= $this->params->get('component_bottom_class'); // col

	// Footer
	$params['footer_class']	= $this->params->get('footer_class');
	$params['footer_1_newRowClass']	= $this->params->get('footer_1_newRowClass');
	$params['footer_1_container'] = $this->params->get('footer_1_container', 'container');
	$params['footer_1_newContainerClass']	= $this->params->get('footer_1_newContainerClass');
	$params['footer_1']	= $this->params->get('footer_1');
	$params['footer_2_newRow']       = $this->params->get('footer_2_newRow', 0);
	$params['footer_2_newRowClass']	= $this->params->get('footer_2_newRowClass');
	$params['footer_2_container'] = $this->params->get('footer_2_container', 'container');
	$params['footer_2_newContainerClass']	= $this->params->get('footer_2_newContainerClass');
	$params['footer_2']	= $this->params->get('footer_2');
	$params['footer_3_newRow']       = $this->params->get('footer_3_newRow', 0);
	$params['footer_3_newRowClass']	= $this->params->get('footer_3_newRowClass');
	$params['footer_3_container'] = $this->params->get('footer_3_container', 'container');
	$params['footer_3_newContainerClass']	= $this->params->get('footer_3_newContainerClass');
	$params['footer_3']	= $this->params->get('footer_3');
	$params['footer_4_newRow']       = $this->params->get('footer_4_newRow', 0);
	$params['footer_4_newRowClass']	= $this->params->get('footer_4_newRowClass');
	$params['footer_4_container'] = $this->params->get('footer_4_container', 'container');
	$params['footer_4_newContainerClass']	= $this->params->get('footer_4_newContainerClass');
	$params['footer_4']	= $this->params->get('footer_4');
	$params['footer_5_newRow']       = $this->params->get('footer_5_newRow', 0);
	$params['footer_5_newRowClass']	= $this->params->get('footer_5_newRowClass');
	$params['footer_5_container'] = $this->params->get('footer_5_container', 'container');
	$params['footer_5_newContainerClass']	= $this->params->get('footer_5_newContainerClass');
	$params['footer_5']	= $this->params->get('footer_5');
	$params['footer_6_newRow']       = $this->params->get('footer_6_newRow', 0);
	$params['footer_6_newRowClass']	= $this->params->get('footer_6_newRowClass');
	$params['footer_6_container'] = $this->params->get('footer_6_container', 'container');
	$params['footer_6_newContainerClass']	= $this->params->get('footer_6_newContainerClass');
	$params['footer_6']	= $this->params->get('footer_6');
	$params['footer_7_newRow']       = $this->params->get('footer_7_newRow', 0);
	$params['footer_7_newRowClass']	= $this->params->get('footer_7_newRowClass');
	$params['footer_7_container'] = $this->params->get('footer_7_container', 'container');
	$params['footer_7_newContainerClass']	= $this->params->get('footer_7_newContainerClass');
	$params['footer_7']	= $this->params->get('footer_7');
	$params['footer_8_newRow']       = $this->params->get('footer_8_newRow', 0);
	$params['footer_8_newRowClass']	= $this->params->get('footer_8_newRowClass');
	$params['footer_8_container'] = $this->params->get('footer_8_container', 'container');
	$params['footer_8_newContainerClass']	= $this->params->get('footer_8_newContainerClass');
	$params['footer_8']	= $this->params->get('footer_8');

	function loadPosition($tmpl, $params, $position, $total, $modStyle = 'base') {

    	$hasContainer = $hasWrapper = $closed = false;
		$html = '';
		$counter = 0; // get active position
    	for($i = 1; $i <= $total; $i++) {

			if($tmpl->countModules($position.'-'.$i) > 0) :
				// Contador indica o número real de itens
				// Caso não seja atribuído nenhum módulo à primeira posição
				$counter++;
				// verifica se o container é definido para a posição
		        // se for, implementa a opção de 'quebra de linha'
		        // senão, carrega apenas a 'row' da grid
		        $hasContainer = isset($params[$position.'_'.$i.'_container']) ? true : false;
				if($hasContainer) :

					$newLine = ($i > 1) ? $params[$position.'_'.$i.'_newRow'] : 0;
					$container = $params[$position.'_'.$i.'_container'];
					$setContainer = ($container == 'none' ? false : true);

					if($newLine || $i == 1) :
						// Fecha linha
						// Se uma nova linha se inicia "$newLine == 1"
						// após a primeira posição "$counter > 1"
						// fecha a linha anterior
						if($counter > 1) :
							if($hasWrapper) :
								$html .= '  </div>'; // fecha 'row'
			        			$html .= '</div>'; // fecha 'container'
								$hasWrapper = false;
			      			endif;
			      			$html .= '</div>'; // fecha 'new-line'
				        endif;
						// Nova linha
						$rowClass = !empty($params[$position.'_'.$i.'_newRowClass']) ? $params[$position.'_'.$i.'_newRowClass'] : '';
						$containerClass = !empty($params[$position.'_'.$i.'_newContainerClass']) ? $params[$position.'_'.$i.'_newContainerClass'] : '';
	  					if($setContainer) :
	  						$html .= '
								<div id="'.$position.'-row-'.$i.'" class="'.$rowClass.' clearfix">
	      						<div class="'.$container.'">
	      						<div class="row '.$containerClass.'">
	  						';
	          				$hasWrapper = true;
						else :
							$html .= '<div id="'.$position.'-row-'.$i.'" class="'.$rowClass.' clearfix">';
	  					endif;
					endif; // $newline

	    		endif; // $hasContainer

	    		// carrega a posição
	  			$class = ' class="'.$params[$position.'_'.$i].' tmplPos"';
	    		$html .= '
	  				<!-- '.$position.'-'.$i.' -->
	  				<div id="'.$position.'-'.$i.'"'.$class.'>
	  					<jdoc:include type="modules" name="'.$position.'-'.$i.'" style="'.$modStyle.'" />
	  				</div>
				';
			endif; // fecha 'countModules'

  		} // fecha 'for'

		if(!empty($html)) :
			// fecha a última posição
			if($hasWrapper) :
				$html .= '  </div>'; // fecha 'row'
				$html .= '</div>'; // fecha 'container'
    		endif;
			$html .= '</div>'; // fecha 'new-line'
			// Mostra a posição
			$class = !empty($params[$position.'_class']) ? ' class="'.$params[$position.'_class'].'"' : '';
			echo '<div id="'.$position.'"'.$class.'>'.$html.'</div>';
		endif;

	} // fecha 'funcion'

	function loadSection($tmpl, $params, $position, $modStyle = 'noContainer') {

		$sgrid = $params['section_'.$position]; // classe da posição
		if($tmpl->countModules('section-'.$position) > 0):
			echo '
				<!-- Section '.$position.' -->
				<div id="section-'.$position.'" class="'.$sgrid.'">
					<jdoc:include type="modules" name="section-'.$position.'" style="base" />
				</div>
				<!--/ Section '.$position.' -->
			';
		endif;

	} // fecha 'funcion'

// BROWSER

	$docProps = 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$this->language.'" lang="'.$this->language.'" dir="'.$this->direction.'"';
	if(!isset($SESSION['docClass'])) :
	    $docClass = '';
	    // Verifica o browser do usuário
		$ua = $_SERVER["HTTP_USER_AGENT"];
	  	// if IE <= 8
	  	$docClass .= (strpos($ua, 'MSIE') && preg_match('/msie [2-8]/i',$ua)) ? ' ie-lte-8' : (strpos($ua, 'Trident/7.0; rv:11.0') ? ' ie ie-11' : ' not-ie');
	  	// Verifica o tipo de Dispositivo do usuário (pc, tablet, celular)
	  	// -> utiliza a classe "Mobile_Detect.php"
	  	if(!class_exists('Mobile_Detect')) : // essa validação evita um erro de 'redeclare class'
			require_once ('libraries/envolute/helpers/template/Mobile_Detect.php');
			$detect = new Mobile_Detect;
			$docClass .= ($detect->isMobile() && $responsive) ? ($detect->isTablet() ? ' tablet' : ' phone') : ' desktop';
		endif;
		$SESSION['docClass'] = $docClass;
	endif;
?>
<!DOCTYPE html>
<html class="media-lg <?php echo $SESSION['docClass'].$hasAdmin.$menuMobile?>" <?php echo $docProps?>>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if($responsive) echo '<meta name="viewport" content="width=device-width, initial-scale=1">'; ?>

	<?php
	// 'BASE' CSS FILES
	require_once('templates/base/_css.tpl.php');
	// INITIALIZE JS FILES
	$jsInit	= $this->params->get('jsInit');
	require_once('templates/base/_js.init.php');
	// REMOVE JS/CSS FILES
	$jsRemove	= $this->params->get('jsRemove'); // Javascript removed files
	$ssRemove	= $this->params->get('ssRemove'); // StyleSheet removed files
	require_once('templates/base/_removed.tpl.php');
	?>

	<jdoc:include type="head" />

	<?php
	// 'BASE' CUSTOM JS FILES
	$jsCustom	= $this->params->get('jsCustom');
	require_once('templates/base/_js.custom.php');
	?>

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="templates/base/libs/browser/html5shiv-printshiv.js"></script>
	<![endif]-->

</head>
