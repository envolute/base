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

//joomla get request data
$input      = $app->input;

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	// params requests
	$id = $input->get('id', 0, 'int');

	$query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .' client,
			'. $db->quoteName('T1.name') .' name,
			'. $db->quoteName('T1.date') .',
			'. $db->quoteName('T1.start_date') .',
			'. $db->quoteName('T1.end_date') .',
			IF(T1.start_date < NOW() && T1.end_date > CURDATE(), 1, 0) status,
			'. $db->quoteName('T1.location') .',
			'. $db->quoteName('T1.limit') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.url_info') .',
			'. $db->quoteName('T1.url_registration') .',
			'. $db->quoteName('T1.rules_content') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__zenite_clients') .' T2
			ON T2.id = T1.client_id
		WHERE
			T1.id = '.$id;
	try {

		$db->setQuery($query);
		$item = $db->loadObject();

		// get categories
		$query = '
	    SELECT
	      '. $db->quoteName('T1.id') .',
	      '. $db->quoteName('T2.name') .' category,
	      '. $db->quoteName('T3.name') .' project,
	      '. $db->quoteName('T3.date') .' projectDate,
	      '. $db->quoteName('T4.name') .' disability,
	      '. $db->quoteName('T1.distance') .',
	      '. $db->quoteName('T1.distance_unit') .',
	      '. $db->quoteName('T1.description') .',
	      '. $db->quoteName('T1.min_age') .',
	      '. $db->quoteName('T1.max_age') .',
	      '. $db->quoteName('T1.price') .',
	      '. $db->quoteName('T1.limit') .',
	      '. $db->quoteName('T1.link_map') .',
	      '. $db->quoteName('T1.state') .'
	    FROM
	      '. $db->quoteName('#__zenite_projects_types') .' T1
	      JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
	      ON T2.id = T1.category_id
	      JOIN '. $db->quoteName('#__zenite_projects') .' T3
	      ON T3.id = T1.project_id
	      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T4
	      ON T4.id = T1.disability_id
	    WHERE
	    T1.project_id = '.$id.' AND T1.state = 1 ORDER BY T2.name, T1.distance_unit, T1.distance;
	  ';
	  $db->setQuery($query);
	  $db->execute();
	  $num_rows = $db->getNumRows();
	  $types = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// VIEW
$html = '';

if(!empty($item->name)) : // verifica se existe

	if($num_rows > 0) :
		$categ = '
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Categoria</th>
						<th>Distância</th>
						<th>Faixa Etária</th>
						<th>Grupo</th>
						<th>Valor</th>
					</tr>
				</thead>
				<tbody>
		';
		foreach ($types as $cat) {

			$distance_unit = ($cat->distance_unit == 0 ? ' m' : ' Km');
			if($cat->max_age == 0) :
				$faixa = 'À partir dos '.$cat->min_age.' anos';
			else :
				$faixa = 'Dos '.$cat->min_age.' aos '.$cat->max_age.' anos';
			endif;

			$categ .= '
				<tr>
					<td>
						'.baseHelper::nameFormat($cat->category).'
						'.($cat->limit == 0 ? '' : '<br /><small class="text-muted font-featured cursor-help hasTooltip" title="Limite de inscrições da categoria">Limite: '.$item->limit.'</small>').'
					</td>
					<td>'.$cat->distance.$distance_unit.'</td>
					<td>'.$faixa.'</td>
					<td>'.(!empty($cat->disability) ? '<div>'.baseHelper::nameFormat($cat->disability).'</div>' : '').'</td>
					<td class="strong">R$ '.baseHelper::priceFormat($cat->price).'</td>
				</tr>
			';
		}
		$categ .= '
				</tbody>
			</table>
		';
	else :
		$categ = '<div class="alert alert-warning">Não existem categorias cadastradas para esse evento!<div>';
	endif;

	if($cfg['hasUpload']) :
		JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
		$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
		$itemFile = array();
		for($i = 0; $i < count($files[$item->id]); $i++) {
			if(!empty($files[$item->id][$i]->filename)) :
				if($i < 3) :
					$itemFile[$i] = '/images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
				else :
					$itemFile[$i] = '
						<a class="btn btn-warning pull-left" href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-doc-text"></span> Regulamento
						</a>
					';
				endif;
			endif;
		}
	endif;

	$info = !empty($item->url_info) ? '<a href="'.$item->url_info.'" target="_blank" class="new-window strong pull-right">Mais Informações</a>' : '';
	$subscribe = !empty($item->url_registration) ? $item->url_registration : 'eventos/inscricoes?pid='.$item->id;
	$actions = '';
	if($item->status == 1) :
		$actions = '
		<p class="project-actions">
			Inscrições: <span class="text-live">'.baseHelper::dateFormat($item->start_date, 'd/m/Y').'</span> à <span class="text-live">'.baseHelper::dateFormat($item->end_date, 'd/m/Y').'</span>
			'.$itemFile[3].'
			<a href="'.$subscribe.'" class="btn-registration btn btn-success pull-right" target="'.(!empty($item->url_registration) ? '_blank' : '_self').'"><span class="base-icon-ok"></span> Inscreva-se</a>
		</p>
		';
	else :
		$actions = '<hr />';
	endif;
	$html .= '
		<div class="item-project detail clearfix">
			<h2 class="item-project-title no-margin-top">'.baseHelper::nameFormat($item->name).'</h2>
			<div class="item-project-images clearfix">
				<img src="'.baseHelper::thumbnail($itemFile[0],'340','250').'" class="project-img-cover img-thumbnail bottom-space" />
				<a href="'.JURI::root().$itemFile[1].'" class="set-modal text-center" title="Modelo de Camisa" style rel="img-info">
					<img src="'.baseHelper::thumbnail($itemFile[1],'170','112').'" class="img-thumbnail" /><br /><small class="text-live font-featured">Modelo de Camisa</small>
				</a>
				<a href="'.JURI::root().$itemFile[2].'" class="set-modal text-center" title="Modelo de Medalha" rel="img-info">
					<img src="'.baseHelper::thumbnail($itemFile[2],'170','112').'" class="img-thumbnail" /><br /><small class="text-live font-featured">Modelo de Medalha</small>
				</a>
			</div>
			<hr class="hr-sm clear visible-xs" />
			<p class="project-info">
				<strong>Data</strong>: '.baseHelper::dateFormat($item->date).'<br />
				<strong>Local</strong>: '.baseHelper::nameFormat($item->location).'
				'.$info.'
			</p>
			'.$actions.'
			<hr class="hr-sm clear visible-xs" />
			<p class="font-featured">'.$item->description.'</p>
		</div>
		<div class="item-project-categories">
			'.$categ.'
		</div>
	';

else : // num_rows = 0

	$html .= '
	<h1 class="text-primary">Evento não disponível!</h1>
	<p>Desculpe-nos, o evento que você tentou visualizar ou não existe ou não está mais disponível.</p>
	<a href="'.JURL::root(true).'">&laquo; Clique aqui para acessar a página inicial</a>
	';

endif;

echo $html;

?>
