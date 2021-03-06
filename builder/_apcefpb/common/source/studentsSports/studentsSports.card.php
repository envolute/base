<?php

// load Joomla's framework
// require_once('../load.joomla.php');
// $app = JFactory::getApplication('site');

defined('_JEXEC') or die;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef']))
$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

//joomla get request data
$input      = $app->input;

// params requests
$id = $input->get('id', 0, 'int');
$fr = $input->get('fr', 0, 'boolean');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

// GET DATA
$query = '
SELECT
  '. $db->quoteName('T1.id') .',
  '. $db->quoteName('T1.student_id') .',
  '. $db->quoteName('T1.sport_id') .',
  '. $db->quoteName('T2.name') .' name,
  '. $db->quoteName('T2.name_card') .' name_card,
  '. $db->quoteName('T3.name') .' sport,
  '. $db->quoteName('T2.birthday') .',
  '. $db->quoteName('T2.has_disease') .',
  '. $db->quoteName('T2.disease_desc') .',
  '. $db->quoteName('T2.has_allergy') .',
  '. $db->quoteName('T2.allergy_desc') .',
  '. $db->quoteName('T2.blood_type') .',
  '. $db->quoteName('T1.registry_date') .'
FROM
  '. $db->quoteName($cfg['mainTable']) .' T1
  JOIN '. $db->quoteName('#__apcefpb_students') .' T2
  ON T2.id = T1.student_id
  JOIN '. $db->quoteName('#__apcefpb_sports') .' T3
  ON T3.id = T1.sport_id
WHERE '. $db->quoteName('T1.id') .' = '.$id;

$db->setQuery($query);
$item = $db->loadObject();

if(!empty($item->name)) : // verifica se existe

  // FOTO
  JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
  $files[$item->student_id] = uploader::getFiles('#__apcefpb_students_files', $item->student_id);
  $imgFile = '';
  for($i = 0; $i < count($files[$item->student_id]); $i++) {
    if(!empty($files[$item->student_id][$i]->filename)) $imgFile .= 'images/uploads/students/'.$files[$item->student_id][$i]->filename;
  }
  $bg[$item->sport_id] = uploader::getFiles('#__apcefpb_sports_files', $item->sport_id);
  $bgFile = '';
  for($i = 0; $i < count($bg[$item->sport_id]); $i++) {
    if(!empty($bg[$item->sport_id][$i]->filename)) $bgFile .= 'images/uploads/sports/'.$bg[$item->sport_id][$i]->filename;
  }
  $blood_type = !empty($item->blood_type) ? '<span class="left-space">Sangue '.$item->blood_type.'</span>' : '';
  $allergy = !empty($item->allergy_desc) ? '<div style="text-align:right;"><strong style="font-size:9px; color:#d00;">ALERGIA</strong>: '.$item->allergy_desc.'</div>' : '';
  $doc = JFactory::getDocument();
  $doc->addStyleDeclaration('body{ overflow: hidden!important; }');
  $html = '
    <div id="'.$APPTAG.'-card" style="padding:10px 5px 0 1px; font-size:11px;">
      <img src="'.JURI::root().$bgFile.'" style="position:absolute; width:324px; z-index:-1; top:0; left:0;" />
      <img src="'.baseHelper::thumbnail($imgFile,'300','400').'" style="float:left; width:68px; height:90px; border:2px solid #f60">
      <div style="padding:75px 0 0; text-align:right;">'.(!empty($item->name_card) ? $item->name_card : $item->name).'</div>
      <div style="text-align:right;">
        <strong>'.$item->sport.'</strong><br />
        <span class="left-space">Nasc. '.baseHelper::dateFormat($item->birthday).'</span>
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
