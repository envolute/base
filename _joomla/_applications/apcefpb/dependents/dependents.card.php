<?php
$ajaxRequest = false;
require('config.php');

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
$uID = $app->input->get('uID', 0, 'int');

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
$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.name') .',
		'. $db->quoteName('T1.card_name') .',
		'. $db->quoteName('T2.name') .' grp,
		'. $db->quoteName('T2.overtime') .',
		'. $db->quoteName('T4.username') .' code,
		'. $db->quoteName('T1.end_date') .',
		'. $db->quoteName('T1.birthday') .'
	FROM
		'. $db->quoteName($cfg['mainTable']) .' T1
		JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
		ON T2.id = T1.group_id
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T3
		ON T3.id = T1.client_id
		LEFT JOIN '. $db->quoteName('#__users') .' T4
		ON T4.id = T3.user_id
	WHERE '. $db->quoteName('T1.id') .' = '.$uID
;

$db->setQuery($query);
$item = $db->loadObject();

if(!empty($item->name)) : // verifica se existe

	JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
	// Imagem Principal -> Primeira imagem (index = 0)
	$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
	if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 400).'" style="float:left; width:68px; height:90px; border:2px solid #f60" />';

	$venc = '';
	if($item->overtime > 0) :
		$venc = '<span class="ml-3">Venc. '.baseHelper::dateFormat($item->end_date).'</span>';
	endif;
	$doc = JFactory::getDocument();
	$doc->addStyleDeclaration('body{ overflow: hidden!important; }');
	$html = '
		<div id="'.$APPTAG.'-card" style="font-family:arial!important; color:#000!important; padding:10px 0 0 1px; font-size:11px;">
			'.$img.'
			<div style="padding:75px 0 0; text-align:right; font-weight:bold;">'.(!empty($item->card_name) ? $item->card_name : $item->name).'</div>
			<div style="text-align:right; line-height: 1.2;">Cód. <strong>'.$item->code.'</strong>'.$venc.'</div>
			<div style="text-align:right; line-height: 1.2;">DEPENDENTE</div>
		</div>
		<script>jQuery(window).load(function() { print() });</script>
	';
else :
	$html = '<p class="alert alert-info alert-icon m-0">'.JText::_('MSG_EMPTY_CARD').'</p>';
endif;

echo $html;

?>
