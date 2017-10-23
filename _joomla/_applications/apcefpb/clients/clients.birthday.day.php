<?php
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

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
$query = 'SELECT *, DAY(birthday) day, DAY(NOW()) today FROM '.$db->quoteName($cfg['mainTable']).' WHERE MONTH(birthday) = MONTH(NOW()) ORDER BY '.$db->quoteName('day').', name';
try {
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();
} catch (RuntimeException $e) {
	 echo $e->getMessage();
	 return;
}

echo '<div id="clients-list-birthday">';

$birth = $month = $modal = '';
if($num_rows) : // verifica se existe

	$birth .= '<ul class="set-list bordered b-bottom mb-2">';
	$month .= '<ul class="set-list bordered list-trim">';
	$count = 0;
	foreach($res as $item) {

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src=\''.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 64, 64).'\' class=\'img-fluid my-1\' />';
		$today = ($item->day == $item->today);
		$info = baseHelper::nameFormat($item->cx_role).'<br />Ag. '.$item->cx_situated;
		if($item->usergroup == 12) $info = JText::_('TEXT_RETIRED');
		else if($item->usergroup == 13) $info = JText::_('TEXT_CONTRIBUTOR');
		if(!empty($img)) $info = $img.'<br />'.$info;
		$cake = $today ? '<span class="base-icon-birthday text-live"></span> ' : '';
		$day = $item->day < 10 ? '0'.$item->day : $item->day;
		$rowState = $today ? 'text-success' : '';
		$list = '
			<li class="'.$rowState.'">
				<div class="float-right">'.$day.'</div>
				<div class="text-truncate pr-2"><span class="cursor-help hasTooltip" title="'.$info.'">'.$cake.baseHelper::nameFormat($item->name).'</span></div>
			</li>
		';
		$month .= $list;
		if($today) :
			$birth .= $list;
			$count++;
		endif;
	}
	$month .= '</ul>';
	$birth .= '</ul>';

	if($count) :
		echo $birth;
	else :
		echo '<p class="base-icon-info-circled alert alert-info text-sm p-1 mb-1"> '.JText::_('MSG_NO_DAY_BIRTHDAY').'</p>';
	endif;

	echo '
		<a id="'.$APPTAG.'-show-month-birthday" href="#'.$APPTAG.'modal-month-birthday" data-toggle="modal">'.JText::_('TEXT_MONTH_BIRTHDAY').'</a>
		<div class="modal fade" id="'.$APPTAG.'modal-month-birthday" tabindex="-1" role="dialog" aria-labelledby="'.$APPTAG.'modal-month-birthdayLabel">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							'.JText::_('TEXT_MONTH_BIRTHDAY').'
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						'.$month.'
					</div>
				</div>
			</div>
		</div>
	';

else :
	echo '<p class="base-icon-info-circled alert alert-info text-sm p-1 m-0"> '.JText::_('MSG_NO_MONTH_BIRTHDAY').'</p>';
endif;

echo '</div>';

?>
