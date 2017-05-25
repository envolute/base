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

	// Items Full -> Páginas para edição de conteúdo [apenas a área de conteúdo]
	// -> necessário em páginas para administração de conteúdo no frontend
	$hidePos = false;
	$cond = eval("return ".$this->params->get('items_full').";");
	if($cond) $hidePos = true;

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

// TEMPLATE PARAMS

	// Definições da responsividade
	$responsive	= $this->params->get('responsive', 1);
	$screen		= ($responsive ? ' responsive' : ' not-responsive');
	$menuMobile		= ($responsive) ? ' isMM' : ''; // indica se carrega o menu responsivo
	// NavBar (admin)
  $navbarContainer	= $this->params->get('navbar_container', 'container');
	$navbarGroups = $this->params->get('navbarGroups');
		$navbarAccess = array_intersect($groups, $navbarGroups); // se está na lista de grupos permitidos
	$navbarStyle	= ($this->params->get('navbarStyle') == 2) ? 'navbar-inverse' : 'navbar-default';
	$navbarFixed	= ($this->params->get('navbarFixed')) ? 'navbar-fixed-top' : '';
	// css elements
  // -> carrega os arquivos 'css' e 'js' para teste 'local'
	$loadDev	= $this->params->get('loadDev', 1);
	// web services
	$analyticsCode	= $this->params->get('analyticsCode');

// GRID
	// Header
  $params['header_1_container'] = $this->params->get('header_1_container', 'container');
  $params['header_1']  = $this->params->get('header_1');
  $params['header_1_newRow']       = $this->params->get('header_1_newRow', 0);
	$params['header_2_container'] = $this->params->get('header_2_container', 'container');
  $params['header_2']  = $this->params->get('header_2');
  $params['header_2_newRow']       = $this->params->get('header_2_newRow', 0);
	$params['header_3_container'] = $this->params->get('header_3_container', 'container');
  $params['header_3']  = $this->params->get('header_3');
  $params['header_3_newRow']       = $this->params->get('header_3_newRow', 0);
	$params['header_4_container'] = $this->params->get('header_4_container', 'container');
  $params['header_4']  = $this->params->get('header_4');
  $params['header_4_newRow']       = $this->params->get('header_4_newRow', 0);
	$params['header_5_container'] = $this->params->get('header_5_container', 'container');
  $params['header_5']  = $this->params->get('header_5');
  $params['header_5_newRow']       = $this->params->get('header_5_newRow', 0);
	$params['header_6_container'] = $this->params->get('header_6_container', 'container');
  $params['header_6']  = $this->params->get('header_6');
  $params['header_6_newRow']       = $this->params->get('header_6_newRow', 0);
	$params['header_7_container'] = $this->params->get('header_7_container', 'container');
  $params['header_7']  = $this->params->get('header_7');
  $params['header_7_newRow']       = $this->params->get('header_7_newRow', 0);
	$params['header_8_container'] = $this->params->get('header_8_container', 'container');
  $params['header_8']  = $this->params->get('header_8');

	// Section
  $params['section_1_container'] = $this->params->get('section_1_container', 'container');
  $params['section_1']	= $this->params->get('section_1');
	$params['section_1_newRow']       = $this->params->get('section_1_newRow', 0);
	$params['section_2_container'] = $this->params->get('section_2_container', 'container');
  $params['section_2']	= $this->params->get('section_2');
	$params['section_2_newRow']       = $this->params->get('section_2_newRow', 0);
	$params['section_3_container'] = $this->params->get('section_3_container', 'container');
  $params['section_3']	= $this->params->get('section_3');
	$params['section_3_newRow']       = $this->params->get('section_3_newRow', 0);
	$params['section_4_container'] = $this->params->get('section_4_container', 'container');
  $params['section_4']	= $this->params->get('section_4');
	$params['section_4_newRow']       = $this->params->get('section_4_newRow', 0);
	$params['section_5_container'] = $this->params->get('section_5_container', 'container');
  $params['section_5']	= $this->params->get('section_5');
	$params['section_5_newRow']       = $this->params->get('section_5_newRow', 0);
	$params['section_6_container'] = $this->params->get('section_6_container', 'container');
  $params['section_6']	= $this->params->get('section_6');
	$params['section_6_newRow']       = $this->params->get('section_6_newRow', 0);
	$params['section_7_container'] = $this->params->get('section_7_container', 'container');
  $params['section_7']	= $this->params->get('section_7');
	$params['section_7_newRow']       = $this->params->get('section_7_newRow', 0);
	$params['section_8_container'] = $this->params->get('section_8_container', 'container');
  $params['section_8']	= $this->params->get('section_8');
	$params['section_8_newRow']       = $this->params->get('section_8_newRow', 0);
	$params['section_9_container'] = $this->params->get('section_9_container', 'container');
  $params['section_9']	= $this->params->get('section_9');
	$params['section_9_newRow']       = $this->params->get('section_9_newRow', 0);
	$params['section_10_container'] = $this->params->get('section_10_container', 'container');
  $params['section_10']	= $this->params->get('section_10');
	$params['section_10_newRow']       = $this->params->get('section_10_newRow', 0);
	$params['section_11_container'] = $this->params->get('section_11_container', 'container');
  $params['section_11']	= $this->params->get('section_11');
	$params['section_11_newRow']       = $this->params->get('section_11_newRow', 0);
	$params['section_12_container'] = $this->params->get('section_12_container', 'container');
  $params['section_12']	= $this->params->get('section_12');

	// Full Content
  $params['full_content_container'] = $this->params->get('full_content_container', 'container');

    // Full content header
    $params['full_content_header']	= $this->params->get('full_content_header'); // col

		// Largura das áreas laterais
		$params['leftWidth']  = ($this->countModules('left') && !$hidePos) ? $this->params->get('leftWidth') : 0;
    $params['contentLeftWidth']   = ($this->countModules('content-left') && !$hidePos) ? $this->params->get('content_left') : 0;
		$params['rightWidth'] = ($this->countModules('right') && !$hidePos) ? $this->params->get('rightWidth') : 0;
    $params['contentRightWidth']  = ($this->countModules('content-right') && !$hidePos) ? $this->params->get('content_right') : 0;

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
			function setOffset($x = 12, $div = 1) {
				if($div <= 0) $div = 1;
				if($div == 5 || ($div > 6 && $div < 12)) :
					$w = '2_5'; /* col-##-2_5 (5 cols) esta definido em base.bootstrap.css */
				else :
					$w = ($x < 12) ? (12 - $x) / $div : $x / $div;
				endif;
				return $w;
			}

      // Full Content footer
      $params['full_content_footer']	= $this->params->get('full_content_footer'); // row

  	//Content Header
    $params['content-header_1']	= $this->params->get('content_header_1'); // col
  	$params['content-header_2']	= $this->params->get('content_header_2'); // col
  	$params['content-header_3']	= $this->params->get('content_header_3'); // col
  	$params['content-header_4']	= $this->params->get('content_header_4'); // col

    //Content Footer
  	$params['content-footer_1']	= $this->params->get('content_footer_1'); // col
  	$params['content-footer_2']	= $this->params->get('content_footer_2'); // col
  	$params['content-footer_3']	= $this->params->get('content_footer_3'); // col
  	$params['content-footer_4']	= $this->params->get('content_footer_4'); // col

    //Component Header
    $params['component-header_1']	= $this->params->get('component_header_1'); // col
  	$params['component-header_2']	= $this->params->get('component_header_2'); // col
  	$params['component-header_3']	= $this->params->get('component_header_3'); // col

    //Component Footer
    $params['component-footer_1']	= $this->params->get('component_footer_1'); // col
  	$params['component-footer_2']	= $this->params->get('component_footer_2'); // col
  	$params['component-footer_3']	= $this->params->get('component_footer_3'); // col

	//Bottom
  $params['bottom_1_container'] = $this->params->get('bottom_1_container', 'container');
  $params['bottom_1']	= $this->params->get('bottom_1');
	$params['bottom_1_newRow']       = $this->params->get('bottom_1_newRow', 0);
	$params['bottom_2_container'] = $this->params->get('bottom_2_container', 'container');
  $params['bottom_2']	= $this->params->get('bottom_2');
	$params['bottom_2_newRow']       = $this->params->get('bottom_2_newRow', 0);
	$params['bottom_3_container'] = $this->params->get('bottom_3_container', 'container');
  $params['bottom_3']	= $this->params->get('bottom_3');
	$params['bottom_3_newRow']       = $this->params->get('bottom_3_newRow', 0);
	$params['bottom_4_container'] = $this->params->get('bottom_4_container', 'container');
  $params['bottom_4']	= $this->params->get('bottom_4');
	$params['bottom_4_newRow']       = $this->params->get('bottom_4_newRow', 0);
	$params['bottom_5_container'] = $this->params->get('bottom_5_container', 'container');
  $params['bottom_5']	= $this->params->get('bottom_5');
	$params['bottom_5_newRow']       = $this->params->get('bottom_5_newRow', 0);
	$params['bottom_6_container'] = $this->params->get('bottom_6_container', 'container');
  $params['bottom_6']	= $this->params->get('bottom_6');
	$params['bottom_6_newRow']       = $this->params->get('bottom_6_newRow', 0);
	$params['bottom_7_container'] = $this->params->get('bottom_7_container', 'container');
  $params['bottom_7']	= $this->params->get('bottom_7');
	$params['bottom_7_newRow']       = $this->params->get('bottom_7_newRow', 0);
	$params['bottom_8_container'] = $this->params->get('bottom_8_container', 'container');
  $params['bottom_8']	= $this->params->get('bottom_8');
	$params['bottom_8_newRow']       = $this->params->get('bottom_8_newRow', 0);
	$params['bottom_9_container'] = $this->params->get('bottom_9_container', 'container');
  $params['bottom_9']	= $this->params->get('bottom_9');
	$params['bottom_9_newRow']       = $this->params->get('bottom_9_newRow', 0);
	$params['bottom_10_container'] = $this->params->get('bottom_10_container', 'container');
  $params['bottom_10']	= $this->params->get('bottom_10');
	$params['bottom_10_newRow']       = $this->params->get('bottom_10_newRow', 0);
	$params['bottom_11_container'] = $this->params->get('bottom_11_container', 'container');
  $params['bottom_11']	= $this->params->get('bottom_11');
	$params['bottom_11_newRow']       = $this->params->get('bottom_11_newRow', 0);
	$params['bottom_12_container'] = $this->params->get('bottom_12_container', 'container');
  $params['bottom_12']	= $this->params->get('bottom_12');

  //Footer
  $params['footer_1_container'] = $this->params->get('footer_1_container', 'container');
  $params['footer_1']	= $this->params->get('footer_1');
	$params['footer_1_newRow']       = $this->params->get('footer_1_newRow', 0);
	$params['footer_2_container'] = $this->params->get('footer_2_container', 'container');
  $params['footer_2']	= $this->params->get('footer_2');
	$params['footer_2_newRow']       = $this->params->get('footer_2_newRow', 0);
	$params['footer_3_container'] = $this->params->get('footer_3_container', 'container');
  $params['footer_3']	= $this->params->get('footer_3');
	$params['footer_3_newRow']       = $this->params->get('footer_3_newRow', 0);
	$params['footer_4_container'] = $this->params->get('footer_4_container', 'container');
  $params['footer_4']	= $this->params->get('footer_4');
	$params['footer_4_newRow']       = $this->params->get('footer_4_newRow', 0);
	$params['footer_5_container'] = $this->params->get('footer_5_container', 'container');
  $params['footer_5']	= $this->params->get('footer_5');
	$params['footer_5_newRow']       = $this->params->get('footer_5_newRow', 0);
	$params['footer_6_container'] = $this->params->get('footer_6_container', 'container');
  $params['footer_6']	= $this->params->get('footer_6');
	$params['footer_6_newRow']       = $this->params->get('footer_6_newRow', 0);
	$params['footer_7_container'] = $this->params->get('footer_7_container', 'container');
  $params['footer_7']	= $this->params->get('footer_7');
	$params['footer_7_newRow']       = $this->params->get('footer_7_newRow', 0);
	$params['footer_8_container'] = $this->params->get('footer_8_container', 'container');
  $params['footer_8']	= $this->params->get('footer_8');

  function loadPosition($tmpl, $params, $position, $total, $modStyle = 'mod') {

    $isNewline = false;
    $html = '';
    for($i = 1; $i <= $total; $i++) {
      if($tmpl->countModules($position.'-'.$i)) :

        // verifica se o container é definido para a posição
        // se for, implementa a opção de 'quebra de linha'
        // senão, carrega apenas a 'row' da grid
        $hasContainer = isset($params[$position.'_'.$i.'_container']) ? true : false;
        if($hasContainer) :

          $newLine = ($i < $total) ? $params[$position.'_'.$i.'_newRow'] : 0;
      		$wrapper = isset($params[$position.'_'.$i.'_container']) ? $params[$position.'_'.$i.'_container'] : 'none';
      		$setWrapper = ($wrapper == 'none' ? false : true);

          // start new line
    			if($i == 1 || $isNewline) :
      			// open new line
      			$html .= '<div class="'.$position.'-row-'.$i.' clearfix">';
            $hasWrapper = false;
      			if($setWrapper) :
      				$html .= '
      					<div class="'.$wrapper.'">
      						<div class="row">
      							<div class="row-wrapper">
      								<div class="row">
      				';
              $hasWrapper = true;
      			endif;
      		endif;
        endif;

    		// carrega a posição
  			$class = ' class="'.$params[$position.'_'.$i].' tmplPos"';
        $html .= '
  				<!-- '.$position.'-'.$i.' -->
  				<div id="'.$position.'-'.$i.'"'.$class.'>
  					<jdoc:include type="modules" name="'.$position.'-'.$i.'" style="'.$modStyle.'" />
  				</div>
  			';

        if(($newLine == 1 || $i == $total) && $hasContainer) : // close line
          if($hasWrapper) :
            $html .= '      </div>'; // fecha 'row'
            $html .= '    </div>'; // fecha 'row-wrapper'
            $html .= '  </div>'; // fecha 'row'
            $html .= '</div>'; // fecha 'container'
          endif;
          $html .= '</div>'; // fecha 'new-line'
        endif;
        $isNewline = ($newLine == 1 ? true : false);
        $current = $i;

      endif; // fecha 'countModules'

  	}

    if($hasContainer) :
      // fecha a posição caso o loop não vá até o último item
      if($current < $total && $hasWrapper) :
        if($hasWrapper) :
          $html .= '      </div>'; // fecha 'row'
          $html .= '    </div>'; // fecha 'row-wrapper'
          $html .= '  </div>'; // fecha 'row'
          $html .= '</div>'; // fecha 'container'
        endif;
        $html .= '</div>'; // fecha 'new-line'
      endif;
      echo !empty($html) ? '<div id="'.$position.'">'.$html.'</div>' : '';
    else :
      echo '
        <div id="'.$position.'">
          <div class="row">
            <div class="row-wrapper">
              <div class="row">
                '.$html.'
              </div>
            </div>
          </div>
        </div>
      ';
    endif;
  }

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
  		require_once ('templates/base/source/template/Mobile_Detect.php');
  		$detect = new Mobile_Detect;
  		$docClass .= ($detect->isMobile() && $responsive) ? ($detect->isTablet() ? ' tablet' : ' phone') : ' desktop';
  	endif;
    $SESSION['docClass'] = $docClass;
  endif;
?>
<!DOCTYPE html>
<html class="media-md <?php echo $SESSION['docClass'].$menuMobile?>" <?php echo $docProps?>>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if($responsive) echo '<meta name="viewport" content="width=device-width, initial-scale=1">'; ?>
	<!-- Set class for especific browser -->
	<script type="text/javascript" src="templates/base/core/libs/js/browser/css_browser_selector.js"></script>

	<jdoc:include type="head" />

	<?php
		// 'BASE' CSS FILES
	require_once('templates/base/_css.tpl.php');

	// 'BASE' JAVASCRIPT FILES
	// IMPORTANTE: arquivos javascript locais são carregados através do plugin 'eorisis';
	// Isso faz com que sejam carregados após o Jquery e antes dos demais...
	if($loadDev) $doc->addScript('templates/base/_dev/custom.js');
	?>
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="templates/base/core/libs/js/browser/html5shiv-printshiv.js"></script>
	<![endif]-->

</head>
