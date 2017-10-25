<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

//joomla get request data
$input      = $app->input;

// params requests
$itemID			= $input->get('id', '', 'string');
$id					= base64_decode($itemID);

if(!empty($itemID)) :

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id .' AND '. $db->quoteName('state') .' = 1';
	$db->setQuery($query);
	$item = $db->loadObject();

	if($item->id != null) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$imagePath = '';
			$images = Array();
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					if($files[$item->id][$i]->index == 0) : // imagem
						$imagePath = 'images/apps/'.$APPPATH.DS.$files[$item->id][$i]->filename;
					elseif($files[$item->id][$i]->index > 1) : // images
						$images[] = 'images/apps/'.$APPPATH.DS.$files[$item->id][$i]->filename;
					endif;
				endif;
			}
		endif;

		// TÍTULO
		$title = $item->title;
		// IMAGEM
		$imgPath = $image = '';
		$imgWidth = !empty($item->content_imageWidth) ? $item->content_imageWidth : 0;
		$imgHeight = !empty($item->content_imageHeight) ? $item->content_imageHeight : 0;
		if(!empty($imagePath)) :
			if($imgWidth == 0 && $imgHeight == 0) :
				$imgPath = $_ROOT.$imagePath;
			else :
				$imgPath = baseHelper::thumbnail($imagePath, $imgWidth, $imgHeight);
			endif;
			$image = '<img src="'.$imgPath.'" class="'.$_SESSION[$RTAG.'ImageClass'].' '.$item->element_imageClass.'" />';
		endif;
		// TAG/CATEGORIA
		$tagInfo = $item->tag;
		// DATE INFO
		$dateFormat = !empty($item->element_dateFormat) ? $item->element_dateFormat : $_SESSION[$RTAG.'ContentDateFormat'];
		$date = ($item->date != '0000-00-00') ? baseHelper::dateFormat($item->date, $dateFormat) : '';
		$month = baseHelper::getMonthName($item->month, false);
		$year = ($item->year != 0) ? $item->year : '';
		// DESCRIÇÃO
		$description = $item->description;
		// CONTEÚDO
		$itemContent = '';
		if($item->contentType == 2) : // post
			$itemContent = $item->content;
		elseif($item->contentType == 3) : // gallery
			$itemContent = '<div id="pageContentGallery" class="row">';
			$grid = !empty($item->content_galleryGrid) ? $item->content_galleryGrid : $_SESSION[$RTAG.'Content_galleryGrid'];
			$imgWidth = !empty($item->content_galImageWidth) ? $item->content_galImageWidth : $_SESSION[$RTAG.'Content_galImageWidth'];
			$imgHeight = !empty($item->content_galImageHeight) ? $item->content_galImageHeight : $_SESSION[$RTAG.'Content_galImageHeight'];
			$imgClass = !empty($item->content_galImageClass) ? $item->content_galImageClass : $_SESSION[$RTAG.'Content_galImageClass'];
			for($i = 0; $i < count($images); $i++) {
				$img = '<img src="'.baseHelper::thumbnail($images[$i], $imgWidth, $imgHeight).'" class="img-responsive '.$imgClass.'" />';
				$itemContent .= '<div class="'.$grid.' mb-3"><a href="'.$images[$i].'" class="set-modal" data-modal-rel="pageContent" data-modal-title="'.$item->title.'">'.$img.'</a></div>';
			}
			$itemContent .= '</div>';
		endif;
		// LINK PARA LISTAGEM
		$urlList = !empty($item->urlList) ? $item->urlList : '#';

		$itemActions = '';
		if($hasAdmin) :
			$itemActions = '
				<div class="'.$APPNAME.'-item-actions pt-2">
					<a href="#" class="btn btn-xs btn-default" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</div>
			';
		endif;

		$content_layout = !empty($item->content_layout) ? $item->content_layout : $_SESSION[$RTAG.'ContentLayout'];
		$tags = Array();
		$tags[] = '{PAGEITEM TITLE}';
		$tags[] = '{PAGEITEM IMAGEPATH}';
		$tags[] = '{PAGEITEM IMAGE}';
		$tags[] = '{PAGEITEM TAGNAME}';
		$tags[] = '{PAGEITEM DATE}';
		$tags[] = '{PAGEITEM MONTH}';
		$tags[] = '{PAGEITEM YEAR}';
		$tags[] = '{PAGEITEM DESCRIPTION}';
		$tags[] = '{PAGEITEM CONTENT}';
		$tags[] = '{PAGEITEM URLLIST}';
		$reps = Array();
		$reps[] = $title;
		$reps[] = $imgPath;
		$reps[] = $image;
		$reps[] = $tagInfo;
		$reps[] = $date;
		$reps[] = $month;
		$reps[] = $year;
		$reps[] = $description;
		$reps[] = $itemContent;
		$reps[] = $urlList;
		$layout = str_replace($tags, $reps, $content_layout);

		// DISPLAY CONTENT
		$html = '<div class="'.$APPNAME.'-itemContent-container">'.$layout.'</div>'.$itemActions;

		return $html;

	else :
		$app->redirect(JURI::root(true));
		exit();
	endif;

endif;

?>
