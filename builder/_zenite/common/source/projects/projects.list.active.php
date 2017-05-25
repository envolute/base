<?php
defined('_JEXEC') or die;

$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// init general css/js files
require(JPATH_BASE.'/templates/base/source/_init.app.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .' client,
			'. $db->quoteName('T1.name') .' name,
			'. $db->quoteName('T1.date') .',
			'. $db->quoteName('T1.start_date') .',
			IF('.$db->quoteName('T1.start_date').' < NOW(), 1, 0) started,
			'. $db->quoteName('T1.end_date') .',
			IF('.$db->quoteName('T1.end_date').' < NOW(), 1, 0) closed,
			'. $db->quoteName('T1.location') .',
			'. $db->quoteName('T1.limit') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.url_info') .',
			'. $db->quoteName('T1.url_registration') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__zenite_clients') .' T2
			ON T2.id = T1.client_id
		WHERE
			T1.date >= NOW() AND T1.state = 1
		ORDER BY T1.date ASC';
	try {

		$db->setQuery($query, $lim0, $lim);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// VIEW
$html = '';

if($num_rows) : // verifica se existe

	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$urlImage = '';
			// CUSTOM => Pega apenas o primeiro item 'Imagem de Capa'
			$exist = count($files[$item->id]) > 0 ? 1 : 0;
			if($exist) $urlImage = '/images/uploads/'.$APPNAME.'/'.$files[$item->id][0]->filename;
		endif;

		$reg = !empty($item->url_registration) ? $item->url_registration : 'eventos/inscricoes?p='.urlencode(base64_encode($item->id));

		$subs = '<span class="text-danger strong">Inscrições Encerradas</span>';
		$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$item->id.' AND state = 1';
		$db->setQuery($query);
		$subsTotal = $db->loadResult();
		if($item->closed == 0) :
			if($subsTotal > $item->limit) :
				$subs = '<span class="text-danger strong">Inscrições Esgotadas!</span>';
			else :
				$subs = 'Inscrições: <span class="text-live">'.baseHelper::dateFormat($item->start_date, 'd/m/Y').'</span> à <span class="text-live">'.baseHelper::dateFormat($item->end_date, 'd/m/Y').'</span>';
				if($item->started == 1) :
					$subs .= '<a href="'.$reg.'" class="btn-register btn btn-success btn-block pull-right" target="'.(!empty($item->url_registration) ? '_blank' : '_self').'"><span class="base-icon-ok"></span> Inscreva-se</a>';
				endif;
			endif;
		endif;
		$html .= '
			<div class="item-project text-center-xs clearfix">
				<a href="evento?p='.urlencode(base64_encode($item->id)).'">
					<img src="'.baseHelper::thumbnail($urlImage,'180','110').'" class="pull-left right-space-sm" />
				</a>
				<h4 class="text-overflow">
					<a href="evento?p='.urlencode(base64_encode($item->id)).'">'.baseHelper::nameFormat($item->name).'</a>
				</h4>
				<div class="project-info">
					Data: '.baseHelper::dateFormat($item->date).'<br />
					Local: '.baseHelper::nameFormat($item->location).'
					<a href="evento?p='.urlencode(base64_encode($item->id)).'" class="pull-right hidden-xs">Saiba mais...</a>
				</div>
				<div class="project-actions text-center-xs">
					'.$subs.'
				</div>
			</div>
		';
	}

else : // num_rows = 0

	$html .= '<div class="alert alert-warning alert-icon no-margin">'.JText::_('MSG_LISTNOREG').'</div>';

endif;

echo $html;

?>
