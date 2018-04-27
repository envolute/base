<?php

// load Joomla's framework
// require_once('../load.joomla.php');
// $app = JFactory::getApplication('site');

defined('_JEXEC') or die;
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
$id = $app->input->get('id', 0, 'int'); // VIEW 'ID'
$fr = $app->input->get('fr', 0, 'boolean'); // JOOMLA TEMPLATE

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
$db = JFactory::getDbo();

// GET DATA
$query = '
	SELECT
		T1.*,
		'. $db->quoteName('T2.name') .' name,
		'. $db->quoteName('T2.card_name') .',
		'. $db->quoteName('T3.name') .' sport,
		'. $db->quoteName('T2.birthday') .',
		'. $db->quoteName('T2.has_disease') .',
		'. $db->quoteName('T2.disease_desc') .',
		'. $db->quoteName('T2.has_allergy') .',
		'. $db->quoteName('T2.allergy_desc') .',
		'. $db->quoteName('T2.blood_type') .'
	FROM
		'. $db->quoteName($cfg['mainTable']) .' T1
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_students') .' T2
		ON T2.id = T1.student_id
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_sports') .' T3
		ON T3.id = T1.sport_id
	WHERE '. $db->quoteName('T1.id') .' = '.$id
;

$db->setQuery($query);
$item = $db->loadObject();

if(!empty($item->name)) : // verifica se existe

	// FOTO
	JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');

	// Imagem do aluno
	$imgFile = uploader::getFile('#__'.$cfg['project'].'_students_files', '', $item->student_id, 0, JPATH_BASE.DS.'images/apps/students/');
	if(!empty($imgFile)) $imgFile = '<img src="'.baseHelper::thumbnail('images/apps/students/'.$imgFile['filename'],'300','400').'" style="float:left; width:68px; height:90px; border:2px solid #f60">';
	// Imagem da carteira
	$bgFile = uploader::getFile('#__'.$cfg['project'].'_sports_files', '', $item->sport_id, 0, JPATH_BASE.DS.'images/apps/sports/');
	if(!empty($bgFile)) $bgFile = '<img src="'.JURI::root().'images/apps/sports/'.$bgFile['filename'].'" style="position:absolute; width:324px; z-index:-1; top:0; left:0;" />';

	$blood_type = !empty($item->blood_type) ? '<span class="ml-3">Sangue '.$item->blood_type.'</span>' : '';
	$allergy = !empty($item->allergy_desc) ? '<div style="text-align:right;"><strong style="font-size:9px; color:#d00;">ALERGIA</strong>: '.$item->allergy_desc.'</div>' : '';
	$doc = JFactory::getDocument();
	$doc->addStyleDeclaration('body{ overflow: hidden!important; }');
	$html = '
		<div id="'.$APPTAG.'-card" class="to-print" style="padding:20px 5px 0 1px; font-size:11px;">
			'.$bgFile.$imgFile.'
			<div style="padding:75px 0 0; text-align:right;">'.(!empty($item->card_name) ? $item->card_name : $item->name).'</div>
			<div style="text-align:right;">
				<strong>'.$item->sport.'</strong><br />
				<span class="ml-3">Nasc. '.baseHelper::dateFormat($item->birthday).'</span>
				'.$blood_type.'
			</div>
			'.$allergy.'
		</div>
	';
	$html .= (!$fr) ? '<script>jQuery(window).load(function() { print() });</script>' : '';
	else :
	$html = '<p class="alert alert-info alert-icon no-margin">'.JText::_('MSG_EMPTY_CARD').'</p>';
	endif;

	echo $html;

?>
