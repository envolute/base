<?php
/* SISTEMA PARA CADASTRO DE PROJETOS
 * AUTOR: IVO JUNIOR
 * EM: 29/01/2018
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINTAG	= $APPTAG;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

//joomla get request data
$input		= $app->input;

// params requests
$pID		= $input->get('pID', 0, 'int');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// database connect
$db		= JFactory::getDbo();

// GET DATA
$query	= '
	SELECT
		T1.*,
		'. $db->quoteName('T2.name') .' client
	FROM '. $db->quoteName($cfg['mainTable']) .' T1
		LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
		ON '.$db->quoteName('T2.id') .' = T1.client_id AND T2.state = 1
	WHERE T1.id = '.$pID
;
try {
	$db->setQuery($query);
	$item = $db->loadObject();
} catch (RuntimeException $e) {
	echo $e->getMessage();
	return;
}

$html = '';
if(!empty($item->name)) :
	// PROJECTS
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE state = 1 ORDER BY name';
	$db->setQuery($query);
	$projects = $db->loadObjectList();

	$html .= '
		<div id="'.$APPTAG.'-details-select">
			<select name="pID" id="'.$APPTAG.'-pID" class="form-control" onchange="location.href=\''.JURI::root().'apps/projects/view?pID=\'+this.value">
				<option value="0">'.JText::_('TEXT_PROJECT').'</option>
	';
				$clientName = '';
				foreach ($projects as $obj) {
					$html .= '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
				}

	$html .= '
			</select>
		</div>
	';

	if($cfg['hasUpload']) :
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 64, 64);
		else $imgPath = $_ROOT.'images/apps/icons/folder_48.png';
		$img = '<img src="'.$imgPath.'" width="48" height="48" class="img-fluid float-left mr-2" />';
	endif;

	$since = (!empty($item->start_date) && $item->start_date != '0000-00-00') ? JText::_('TEXT_SINCE').': '.baseHelper::dateFormat($item->start_date) : '';
	$desc = !empty($item->description) ? '<div>'.$item->description.'</div>' : '';
	$info = (!empty($since) || !empty($desc)) ? '<div class="small py-2 b-top b-top-dashed b-gray-900">'.$since.$desc.'</div>' : '';
	$html .= '
		<div id="'.$APPTAG.'-details-view">
			<div class="p-2">
				<div class="pb-2 clearfix">
					'.$img.'
					<h5 class="text-gray-200 m-0 lh-1">'.baseHelper::nameFormat($item->name).'</h5>
					<div class="small text-muted">'.baseHelper::nameFormat($item->client).'</div>
				</div>
				'.$info.'
			</div>
		</div>
	';

else :

	// MOSTRA A LISTA DE PROJETOS DO USUÁRIO
	$query	= '
		SELECT T1.*
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
		WHERE T1.state = 1
		ORDER BY '. $db->quoteName('T1.name') .' ASC
	';
	try {
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if($num_rows) : // verifica se existe
		$html .= '
			<div id="'.$APPTAG.'-details-select">
				'.JText::_('TEXT_SELECT_PROJECT').'
			</div>
			<div id="'.$APPTAG.'-details-view">
		';
		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 24, 24);
				else $imgPath = $_ROOT.'images/apps/icons/folder_24.png';
				$img = '<img src="'.$imgPath.'" width="24" height="24" class="img-fluid" />';
			endif;

			$html .= '
				<a href="apps/projects/view?pID='.$item->id.'" class="d-flex align-items-center">
					<span class="mr-2">'.$img.'</span>
					<h6 class="m-0">'.baseHelper::nameFormat($item->name).'</h6>
				</a>
			';
		}
		$html .= '</div>';
	else :
		$html = '<p class="base-icon-info-circled alert alert-warning m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;
endif;

echo $html;

?>
