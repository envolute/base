<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($APPNAME.'.filter.php');

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T1.title') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.date') .',
			'. $db->quoteName('T1.month') .',
			'. $db->quoteName('T1.year') .',
			'. $db->quoteName('T1.contentType') .',
			'. $db->quoteName('T1.url') .',
			'. $db->quoteName('T1.target') .',
			'. $db->quoteName('T1.content') .',
			'. $db->quoteName('T1.tag') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
		WHERE
			'.$where.$orderList;
	;
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
$adminView = array();
if($hasAdmin) :
	$adminView['head']['info'] = '
		<th width="30" class="hidden-print"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="hidden-print">'.$$SETOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center hidden-print" width="60">'.$$SETOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center hidden-print" width="70">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<div class="row '.$APPNAME.'-items list-'.$APPTAG.' listFull bottom-space-lg">
';

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$itemFile = $pathFile = $itemImage = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					if($i == 0) : // imagem
						$itemImage .= 'images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
					else : // arquivo
						$itemFile .= '
							<a class="'.$_SESSION[$RTAG.'DownloadButtonClass'].'" href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
								<span class="base-icon-download hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.JText::_($_SESSION[$RTAG.'DownloadButtonLabel']).'</span>
							</a>
						';
						$pathFile .= JURI::root().'images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
					endif;
				endif;
			}
		endif;

		$tagInfo = '';
		if($_SESSION[$RTAG.'ShowTagInfo']) :
			$tagIcon = !empty($_SESSION[$RTAG.'TagInfoIcon']) ? '<span class="'.$_SESSION[$RTAG.'TagInfoIcon'].'"></span> ' : '';
			if(!empty($item->tag)) $tagInfo = '<span class="'.$APPNAME.'-item-tag '.$_SESSION[$RTAG.'TagInfoClass'].'">'.$tagIcon.$item->tag.'</span>';
		endif;

		$dateInfo = '';
		if(!$_SESSION[$RTAG.'HideAllDateFields'] && !$_SESSION[$RTAG.'HideDateInfo']) :
			$dateInfo .= '<div class="'.$APPNAME.'-item-date">';
			if(!$_SESSION[$RTAG.'HideDateField'] && !$_SESSION[$RTAG.'HideDate'] && $item->date != '0000-00-00') $dateInfo .= '<span class="'.$APPNAME.'-item-date">'.baseHelper::dateFormat($item->date).'</span>';
			if(!$_SESSION[$RTAG.'HideMonthField'] && !$_SESSION[$RTAG.'HideMonth'] && $item->month != 0) $dateInfo .= '<span class="'.$APPNAME.'-item-month">'.baseHelper::getMonthName($item->month, false).'</span>';
			if(!$_SESSION[$RTAG.'HideYearField'] && !$_SESSION[$RTAG.'HideYear'] && $item->year != '0000') $dateInfo .= '<span class="'.$APPNAME.'-item-year">'.$item->year.'</span>';
			$dateInfo .= '</div>';
		endif;

		$itemActions = '';
		if($hasAdmin) :
			$itemActions .= '
				<span class="check-row"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></span>
				<a href="#" class="btn btn-xs btn-default" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
					<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
				</a>
				<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
				<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
			';
		endif;
		if($_SESSION[$RTAG.'ShowDownloadButton'] && !empty($itemFile)) $itemActions .= $itemFile;

		$img = '';
		if(!empty($itemImage)) :
			if($_SESSION[$RTAG.'ImageWidth'] == 0 && $_SESSION[$RTAG.'ImageHeight'] == 0) :
				$imgPath = JURI::root().$itemImage;
			else :
				$imgPath = baseHelper::thumbnail($itemImage, $_SESSION[$RTAG.'ImageWidth'], $_SESSION[$RTAG.'ImageHeight']);
			endif;
			$img = '<img class="'.$APPNAME.'-item-image '.$_SESSION[$RTAG.'ImageClass'].'" src="'.$imgPath.'" />';
		endif;

		$link = '';
		$target = $item->target == 0 ? '_self' : '_blank';
		$dataTarget = 'target="'.$target.'"';
		if($item->contentType == 0 && !empty($item->url)) $link = $item->url; // quando a url for informada
		if($item->contentType == 1 && $pathFile) $link = $pathFile; // quando o arquivo for enviado
		if($item->contentType == 2 && !empty($item->content)) : // quando o conteúdo for informado
			$link = '#'.$APPTAG.'-content-'.$item->id;
			// abre o conteúdo em uma modal
			$dataTarget = 'data-toggle="modal" data-target="#'.$APPTAG.'-content-'.$item->id.'"';
		endif;
		// read more link
		if($_SESSION[$RTAG.'ShowReadMoreButton'] && !empty($link)) $itemActions .= '<a class="'.$_SESSION[$RTAG.'ReadMoreButtonClass'].'" href="'.$link.'"'.$linkModal.' target="'.$target.'">'.JText::_($_SESSION[$RTAG.'ReadMoreButtonLabel']).'</a>';

		$title = (!$_SESSION[$RTAG.'HideTitle'] && !empty($item->title)) ? '<'.$_SESSION[$RTAG.'TitleTag'].' class="'.$APPNAME.'-item-title '.$_SESSION[$RTAG.'TitleClass'].'">'.$item->title.'</'.$_SESSION[$RTAG.'TitleTag'].'>' : '';
		$desc = (!$_SESSION[$RTAG.'HideDescriptionField'] && !$_SESSION[$RTAG.'HideDescription'] && !empty($item->description)) ? '<p class="'.$APPNAME.'-item-desc">'.$item->description.'</p>' : '';
		$content = '';
		if(!empty($item->content)) :
			$content = '
				<div id="'.$APPTAG.'-content-'.$item->id.'" class="modal '.($_SESSION[$RTAG.'HideModalHeader'] ? 'no-header' : '').' fade" tabindex="-1" role="dialog">
					<div class="modal-dialog '.$_SESSION[$RTAG.'ContentModalSize'].'" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">'.$item->title.'</h4>
							</div>
							<div class="modal-body '.$APPNAME.'-item-content">
								'.$item->content.'
							</div>
						</div>
					</div>
				</div>
			';
		endif;
		$rowState = $item->state == 0 ? 'bg-danger ' : '';
		$html .= '
			<div id="'.$APPTAG.'-item-'.$item->id.'" class="col-sm-'.$_SESSION[$RTAG.'ListGrid'].' '.$APPNAME.'-item-container">
				<div class="'.$APPNAME.'-item '.$rowState.$_SESSION[$RTAG.'ListItemClass'].'">
					'.$title.'
					<a class="'.$APPNAME.'-item-image-container" href="'.$link.'" '.$dataTarget.'>'.$img.'</a>
					'.$tagInfo.$dateInfo.$desc.'
					<div class="'.$APPNAME.'-item-actions">
						'.$itemActions.'
					</div>
					'.$content.'
				</div>
			</div>
		';
	}

else : // num_rows = 0

	$html .= '
		<div class="col-sm-12">
				<div class="alert alert-warning alert-icon no-margin">'.JText::_('MSG_LISTNOREG').'</div>
		</div>
	';

endif;

$html .= '
		</form>
	</div>
';

if($num_rows) :

	// PAGINAÇÃO
		// stats
		$listStart	= $lim0 + 1;
		$listEnd		= $lim0 + $num_rows;
	if($found_rows != $num_rows) :
		$html .= '
			<div class="base-app-pagination pull-left">
				'.$pageNav->getListFooter().'
				<div class="list-stats small text-muted">
					'.JText::sprintf('LIST_STATS', $listStart, $listEnd, $found_rows).'
				</div>
			</div>
		';
	endif;

	$html .= '
		<form id="form-order-'.$APPTAG.'" action="'.$_SERVER['REQUEST_URI'].'" class="pull-right form-inline" method="post">
			<input type="hidden" name="'.$APPTAG.'oF" id="'.$APPTAG.'oF" value="'.$_SESSION[$APPTAG.'oF'].'" />
			<input type="hidden" name="'.$APPTAG.'oT" id="'.$APPTAG.'oT" value="'.$_SESSION[$APPTAG.'oT'].'" />
		</form>
	';

	// ITENS POR PÁGINA
	// seta o parametro 'start = 0' na URL sempre que o limit for refeito
	// isso evita erro quando estiver navegando em páginas subjacentes
	$a = preg_replace("#\?start=.*#", '', $_SERVER['REQUEST_URI']);
	$a = preg_replace("#&start=.*#", '', $a);

	$html .= '
		<form id="form-limit-'.$APPTAG.'" action="'.$a.'" class="pull-right form-inline hidden-print" method="post">
			<label>'.JText::_('LIST_PAGINATION_LIMIT').'</label>
			<select name="list-lim-'.$APPTAG.'" onchange="'.$APPTAG.'_setListLimit()">
				<option value="6" '.($_SESSION[$APPTAG.'plim'] === 6 ? 'selected' : '').'>6</option>
				<option value="8" '.($_SESSION[$APPTAG.'plim'] === 8 ? 'selected' : '').'>8</option>
				<option value="12" '.($_SESSION[$APPTAG.'plim'] === 12 ? 'selected' : '').'>12</option>
				<option value="15" '.($_SESSION[$APPTAG.'plim'] === 15 ? 'selected' : '').'>15</option>
				<option value="20" '.($_SESSION[$APPTAG.'plim'] === 20 ? 'selected' : '').'>20</option>
				<option value="30" '.($_SESSION[$APPTAG.'plim'] === 30 ? 'selected' : '').'>30</option>
				<option value="40" '.($_SESSION[$APPTAG.'plim'] === 40 ? 'selected' : '').'>40</option>
				<option value="60" '.($_SESSION[$APPTAG.'plim'] === 60 ? 'selected' : '').'>60</option>
				<option value="90" '.($_SESSION[$APPTAG.'plim'] === 90 ? 'selected' : '').'>90</option>
				<option value="1" '.($_SESSION[$APPTAG.'plim'] === 1 ? 'selected' : '').'>Todos</option>
			</select>
		</form>
	';

endif;

return $htmlFilter.$html;

?>
