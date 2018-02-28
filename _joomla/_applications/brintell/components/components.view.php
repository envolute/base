<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = 1; // Público -> Todos podem visualizar

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINAPP	= $APPNAME;
$MAINTAG	= $APPTAG;
$PROJECT	= $cfg['project'];

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// Admin Actions
require_once('components.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' groupName
		FROM '.
			$db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id AND T2.state = 1
		WHERE '. $db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($view->name)) : // verifica se existe

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $view->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $view->created_by == $user->id);

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $view->id, 0, $cfg['uploadDir']);
		if(!empty($img)) {
			$img = '
				<a href="'.JURI::root(true).'/images/apps/'.$APPPATH.'/'.$img['filename'].'" class="set-modal" rel="component-view">
					<img src="images/apps/'.$APPPATH.'/'.$img['filename'].'" class="w-100 img-fluid b-all p-1 mb-3 bg-white" />
				</a>
			';
		} else {
			$img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"><small>'.JText::_('TEXT_NO_IMAGE').'</small></span></div></div></div>';
		}

		// Variations
		$files[$view->id] = uploader::getFiles($cfg['fileTable'], $view->id);
		$listImages = '';
		for($i = 1; $i < count($files[$view->id]) - 1; $i++) {
			if(!empty($files[$view->id][$i]->filename)) :
				$listImages .= '
					<div class="col-xl-1 col-sm-2 col-3 mb-3">
						<a href="'.JURI::root(true).'/images/apps/'.$APPPATH.'/'.$files[$view->id][$i]->filename.'" class="set-modal" rel="component-view">
							<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$files[$view->id][$i]->filename).'" class="img-fluid img-thumbnail rounded" />
						</a>
					</div>
				';
			endif;
		}
		$listImages = !empty($listImages) ? '<hr class="hr-tag" /><span class="badge badge-primary">'.JText::_('TEXT_VARIATIONS').'</span><div class="row">'.$listImages.'</div>' : '';

		$docs = !empty($view->url) ? '<hr /><a href="'.$view->url.'" target="_blank" class="btn btn-primary"><span class="base-icon-docs btn-icon"></span> '.JText::_('TEXT_DOCUMENTATION').'</a>' : '';

		// description
		$description = !empty($view->description) ? '<div id="'.$MAINTAG.'-desc">'.nl2br($view->description).'</div>' : '';

		echo '
			<h1 class="mt-0">
				<div class="text-sm text-muted">'.baseHelper::nameFormat($view->groupName).'</div>
				'.baseHelper::nameFormat($view->name).'
			</h1>
			'.$description.'
			<div class="row">
				<div class="col-sm-2 col-md-3 col-xl-2 pt-3">
					'.$img.'
				</div>
				<div class="col">
					'.$listImages.$docs.'
				</div>
			</div>
		';
	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
