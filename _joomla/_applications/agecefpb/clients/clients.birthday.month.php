<?php
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = false; // Público -> acesso aberto a todos

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// database connect
$db = JFactory::getDbo();

// GET DATA
$query = 'SELECT id, name, DAY(birthday) day, DAY(NOW()) today FROM '.$db->quoteName($cfg['mainTable']).' WHERE MONTH(birthday) = MONTH(NOW())';
try {
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();
} catch (RuntimeException $e) {
	 echo $e->getMessage();
	 return;
}

$html = '';
if($num_rows) : // verifica se existe
	$html .= '<ul class="list-unstyled bordered list-striped list-hover m-0">';
	foreach($res as $item) {

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 20, 20).'" class="d-none d-md-inline img-fluid rounded-circle float-left mr-2" />';
		$today = ($item->day == $item->today);
		$rowState = $today ? 'text-success' : '';
		$cake = $today ? '<span class="base-icon-birthday text-live"></span> ' : '';
		$html .= '
			<li class="'.$rowState.'">
				<div class="float-right">'.$cake.$item->day.'</div>
				<div class="text-truncate pr-2">'.$img.baseHelper::nameFormat($item->name).'</div>
				<div class="small text-muted">'.$item->type.'</div>
			</li>
		';
	}
	$html .= '</ul>';
else :
	$html = '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_NO_MONTH_BIRTHDAY').'</p>';
endif;

echo $html;

?>
