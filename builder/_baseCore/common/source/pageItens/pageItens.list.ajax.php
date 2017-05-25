<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

	defined('_JEXEC') or die;
	$ajaxRequest = true;
	require('config.php');
	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

	//joomla get request data
	$input      = $app->input;

	// params requests
	$APPTAG			= $input->get('aTag', $APPTAG, 'str');
	$RTAG				= $input->get('rTag', $APPTAG, 'str');
	$oCHL				= $input->get('oCHL', 0, 'bool');
	$oCHL				= $_SESSION[$RTAG.'OnlyChildList'] ? $_SESSION[$RTAG.'OnlyChildList'] : $oCHL;
	$rNID       = $input->get('rNID', '', 'str');
	$rNID				= !empty($_SESSION[$RTAG.'RelListNameId']) ? $_SESSION[$RTAG.'RelListNameId'] : $rNID;
	$rID      	= $input->get('rID', 0, 'int');
	$rID				= !empty($_SESSION[$RTAG.'RelListId']) ? $_SESSION[$RTAG.'RelListId'] : $rID;

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef']))
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

	// get current user's data
	$user = JFactory::getUser();
	$groups = $user->groups;

	// verifica o acesso
	$hasGroup = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$noReg = true;
	$query = 'SELECT *';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T2
				ON '.$db->quoteName('T2.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T2.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1 WHERE '. $db->quoteName($rNID) .' = '. $rID;
		endif;
	else :
		$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		else :
			$query .= ' WHERE T1.state = 1 AND T1.tag = '.$db->quote($APPTAG);
		endif;
	endif;
	$query .= ' ORDER BY '.$_SESSION[$RTAG.'ListOrder'];
	$query .= ' LIMIT '.$_SESSION[$RTAG.'ListAjaxLimit'];
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

		if($_SESSION[$RTAG.'Enable_slide']) $html .= '<ul class="'.$RTAG.'BxSlider '.$APPNAME.'-slider listSlider">';
		else $html .= '<div class="row '.$APPNAME.'-items list-'.$APPTAG.' listAjax">';

		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
				$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
				$imagePath = $downloadLink = $downloadPath = $pathFile = '';
				for($i = 0; $i < count($files[$item->id]); $i++) {
					if(!empty($files[$item->id][$i]->filename)) :
						if($files[$item->id][$i]->index == 0) : // imagem
							$imagePath = 'images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
						elseif($files[$item->id][$i]->index == 1) : // arquivo
							$element_downloadLabel = !empty($item->element_downloadLabel) ? $item->element_downloadLabel : $_SESSION[$RTAG.'DownloadButtonLabel'];
							$downloadLink = '
								<a class="'.$_SESSION[$RTAG.'DownloadButtonClass'].' '.$item->element_downloadClass.'" href="'.$_root.'get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
									<span class="base-icon-download hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.JText::_($element_downloadLabel).'</span>
								</a>
							';
							$downloadPath = $_root.'get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME);
							$pathFile = $_root.'images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
						endif;
					endif;
				}
			endif;

			// TÍTULO
			$title = $item->title;
			// LINK
			$link = $dataTarget = '';
			$target = $item->target ? '_blank' : '';
			$urlContent = !empty($item->urlContent) ? $item->urlContent : $_SESSION[$RTAG.'UrlContent'];
			if($item->contentType == 0) :
				$link = !empty($item->url) ? $item->url : '#'; // quando a url for informada
			endif;
			if($item->contentType == 1 && $pathFile) $link = $pathFile; // quando o arquivo for enviado
			if($item->contentType == 2 && !empty($item->content)) : // quando o conteúdo for informado
				if($item->element_contentModal == 1 || ($item->element_contentModal != 0 && $_SESSION[$RTAG.'ContentModal'] == 1)) :
					$link = '#'.$APPTAG.'-content-'.$item->id.'" data-toggle="modal" data-target="#'.$APPTAG.'-content-'.$item->id; // não fechar com '"'
				else :
					$link = $_root.$urlContent.'?id='.urlencode(base64_encode($item->id)); // quando for visualizado na 'pageItens.content'
				endif;
			endif;
			if($item->contentType == 3) $link = $_root.$urlContent.'?id='.urlencode(base64_encode($item->id)); // quando for visualizado na 'pageItens.content'
			// IMAGEM
			$imgPath = $image = $imageLink = '';
			$imgWidth = !empty($item->element_imageWidth) ? $item->element_imageWidth : $_SESSION[$RTAG.'ImageWidth'];
			$imgHeight = !empty($item->element_imageHeight) ? $item->element_imageHeight : $_SESSION[$RTAG.'ImageHeight'];
			if(!empty($imagePath)) :
				if(($imgWidth == 0 && $imgHeight == 0) || ($_SESSION[$RTAG.'Enable_slide'] && $_SESSION[$RTAG.'Slider_fullScreen'])) :
					$imgPath = $_root.$imagePath;
				else :
					$imgPath = baseHelper::thumbnail($imagePath, $imgWidth, $imgHeight);
				endif;
				$caption = $_SESSION[$RTAG.'Enable_slide'] ? ' title="'.$title.'"' : '';
				if(!$_SESSION[$RTAG.'Enable_slide'] || !$_SESSION[$RTAG.'Slider_fullScreen']) :
					$image = '<img src="'.$imgPath.'" class="'.$_SESSION[$RTAG.'ImageClass'].' '.$item->element_imageClass.'"'.$caption.' />';
					$imageLink = '<a href="'.$link.'" target="'.$target.'">'.$image.'</a>';
				endif;
			endif;
			// VIDEO EMBED
			$videoEmbed = $item->videoEmbed;
			// TAG/CATEGORIA
			$tagInfo = $item->tag;
			// DATE INFO
			$dateFormat = !empty($item->element_dateFormat) ? $item->element_dateFormat : $_SESSION[$RTAG.'ListItemDateFormat'];
			$date = ($item->date != '0000-00-00') ? baseHelper::dateFormat($item->date, $dateFormat) : '';
			$month = baseHelper::getMonthName($item->month, false);
			$year = ($item->year != 0) ? $item->year : '';
			// DESCRIÇÃO
			$description = $item->description;

			$itemActions = '';
			if($hasAdmin) :
				$pos = $_SESSION[$RTAG.'Enable_slide'] ? ' style="position:absolute;bottom:5px;right:5px;z-index:1;' : '';
				$itemActions = '
					<div class="'.$APPNAME.'-item-actions top-expand-sm"'.$pos.'">
						<a href="#" class="btn btn-xs btn-default" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
							<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
						</a>
						<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
						<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
					</div>
				';
			endif;

			$modalContent = '';
			if(!empty($item->content)) :
				$modalContent = '
					<div id="'.$APPTAG.'-content-'.$item->id.'" class="modal '.(($item->element_modalHeader == 1 || (empty($item->element_modalHeader) && !$_SESSION[$RTAG.'HideModalHeader'])) ? '' : 'no-header').' fade" tabindex="-1" role="dialog">
						<div class="modal-dialog '.(!empty($item->element_modalSize) ? $item->element_modalSize : $_SESSION[$RTAG.'ContentModalSize']).'" role="document">
						  <div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">'.$item->title.'</h4>
								</div>
						    <div class="modal-body '.$APPNAME.'-item-content">
						      '.$item->element_modalHeader.'-'.$item->element_modalHeader.'-'.$_SESSION[$RTAG.'HideModalHeader'].$item->content.'
						    </div>
						  </div>
						</div>
					</div>
				';
			endif;
			$rowState = $item->state == 0 ? 'bg-danger ' : '';

			$element_layout = !empty($item->element_layout) ? $item->element_layout : $_SESSION[$RTAG.'ListItemLayout'];
			$cover = $item->coverType == 0 ? $imageLink : $videoEmbed;
			$tags = Array();
			$tags[] = '{PAGEITEM TITLE}';
			$tags[] = '{PAGEITEM LINK}';
			$tags[] = '{PAGEITEM LINKTARGET}';
			$tags[] = '{PAGEITEM FILEPATH}';
			$tags[] = '{PAGEITEM DOWNLOADPATH}';
			$tags[] = '{PAGEITEM DOWNLOADLINK}';
			$tags[] = '{PAGEITEM COVER}';
			$tags[] = '{PAGEITEM IMAGEPATH}';
			$tags[] = '{PAGEITEM IMAGE}';
			$tags[] = '{PAGEITEM IMAGELINK}';
			$tags[] = '{PAGEITEM VIDEO}';
			$tags[] = '{PAGEITEM TAGNAME}';
			$tags[] = '{PAGEITEM DATE}';
			$tags[] = '{PAGEITEM MONTH}';
			$tags[] = '{PAGEITEM YEAR}';
			$tags[] = '{PAGEITEM DESCRIPTION}';
			$reps = Array();
			$reps[] = $title;
			$reps[] = $link;
			$reps[] = $target;
			$reps[] = $pathFile;
			$reps[] = $downloadPath;
			$reps[] = $downloadLink;
			$reps[] = $cover;
			$reps[] = $imgPath;
			$reps[] = $image;
			$reps[] = $imageLink;
			$reps[] = $videoEmbed;
			$reps[] = $tagInfo;
			$reps[] = $date;
			$reps[] = $month;
			$reps[] = $year;
			$reps[] = $description;
			$layout = str_replace($tags, $reps, $element_layout);
			// ELEMENT CLASS
			$full = $_SESSION[$RTAG.'Slider_fullScreen'] ? ' style="background-image: url(\''.$imgPath.'\');"' : '';
			$html .= $_SESSION[$RTAG.'Enable_slide'] ? '<li'.$full.'>' : '<div id="'.$APPTAG.'-item-'.$item->id.'" class="'.$_SESSION[$RTAG.'ListGrid'].' '.$APPNAME.'-item-container">';
			$html .= '
				<div class="'.$APPNAME.'-item '.$rowState.' '.$_SESSION[$RTAG.'ListItemClass'].' '.$item->element_class.'">
					'.$layout.$itemActions.$modalContent.'
				</div>
			';
			$html .= ($_SESSION[$RTAG.'Enable_slide']) ? '</li>' : '</div>';

		}

		if($_SESSION[$RTAG.'Enable_slide']) $html .= '</ul>';

		if(!empty($_SESSION[$RTAG.'UrlListView']) && $_SESSION[$RTAG.'ShowListViewButton']) :
			$html .= '
				<div class="col-sm-12 '.$APPNAME.'-item-listView">
					<a class="'.$_SESSION[$RTAG.'ListViewButtonClass'].'" href="'.$_SESSION[$RTAG.'UrlListView'].'">'.JText::_($_SESSION[$RTAG.'ListViewButtonLabel']).'</a>
				</div>
			';
		endif;
		$html .= !$_SESSION[$RTAG.'Enable_slide'] ? '</div>' : '';

	else :
		if($noReg) $html = '<p class="base-icon-info-circled alert alert-info no-margin"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
