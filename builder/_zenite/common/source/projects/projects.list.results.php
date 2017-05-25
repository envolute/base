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

	$query = '
		SELECT
			DISTINCT('. $db->quoteName('T1.id') .'),
			'. $db->quoteName('T3.name') .' client,
			'. $db->quoteName('T1.name') .' name,
			'. $db->quoteName('T1.date') .',
			'. $db->quoteName('T1.start_date') .',
			'. $db->quoteName('T1.end_date') .',
			'. $db->quoteName('T1.location') .',
			'. $db->quoteName('T1.limit') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_results') .' T2
			ON T2.project_id = T1.id
			JOIN '. $db->quoteName('#__zenite_clients') .' T3
			ON T3.id = T1.client_id
		WHERE
			T1.state = 1 AND T2.state = 1
		ORDER BY T1.date DESC
		LIMIT 10';
	try {

		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// VIEW
$html = '<ul class="list list-no-space">';

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

		$html .= '
			<li class="list-project clearfix">
				<a href="eventos/resultados?p='.urlencode(base64_encode($item->id)).'">
					<img src="'.baseHelper::thumbnail($urlImage,'70','48').'" class="img-thumbnail pull-left right-space-sm" />
				</a>
				<div class="text-xs"><span class="text-live">'.baseHelper::dateFormat($item->date).'</span> | '.baseHelper::nameFormat($item->location).'</div>
				<a href="eventos/resultados?p='.urlencode(base64_encode($item->id)).'">
					'.baseHelper::nameFormat($item->name).'
				</a>
			</li>
		';
	}

else : // num_rows = 0

	$html .= '<li><div class="alert alert-warning alert-icon no-margin">'.JText::_('MSG_LISTNOREG').'</div></li>';

endif;

$html .= '</ul>';
$html .= '<a href="eventos/resultados" class="display-block top-space">Resultados Anteriores <span class="base-icon-right-big text-live"></span></a>';

echo $html;

?>
