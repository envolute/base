<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Content
* Version:	1.0.0
* Date:		24/02/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;
?>

<div class="baseContent">
	<?php

	// page header
	if ($this->params->get('show_page_heading', 1) && $this->escape($this->params->get('page_heading'))) :
		echo '<h5 class="page-header">'.$this->escape($this->params->get('page_heading')).'</h5>';
	endif;

	if(!empty($this->app) && file_exists(JPATH_BASE.'/base-apps/'.$this->app.'/'.$this->app.'.php')) :

		// PARAMS
		// Config View
		// TAGS - evita conflito entre instâncias da mesma APP
		${$this->app.'AppTag'} = $this->appTag;
		${$this->appTag.'RelTag'} = $this->relTag;
		${$this->appTag.'IsPublic'} = $this->isPublic;
		// LIST

		${$this->appTag.'ItemView'} = $this->itemView;
		${$this->appTag.'ShowApp'} = $this->showApp;
		${$this->appTag.'ShowList'} = $this->showList;
		${$this->appTag.'ListModal'} = $this->listModal;
		${$this->appTag.'ListFull'} = $this->listFull;
		${$this->appTag.'ListAjax'} = $this->listAjax;
		${$this->appTag.'AjaxReload'} = $this->ajaxReload;
		${$this->appTag.'AjaxFilter'} = $this->ajaxFilter;
		${$this->appTag.'OpenFilter'} = $this->openFilter;
		${$this->appTag.'StaticToolbar'} = $this->staticToolbar;
		${$this->appTag.'ShowAddBtn'} = $this->showAddBtn;
		${$this->appTag.'AddText'} = $this->addText;
		${$this->appTag.'AddClass'} = $this->addClass;
		${$this->appTag.'hasUpload'} = $this->hasUpload;
		${$this->appTag.'RelTag'} = $this->relTag;
		${$this->appTag.'RelTable'} = $this->relTable;
		${$this->appTag.'RelNameId'} = $this->relNameId;
		${$this->appTag.'RelId'} = $this->relId;
		${$this->appTag.'AppNameId'} = $this->appNameId;
		${$this->appTag.'RelListId'} = $this->relListId;
		${$this->appTag.'RelListNameId'} = $this->relListNameId;
		${$this->appTag.'OnlyChildList'} = $this->onlyChildList;
		${$this->appTag.'FieldUpdated'} = $this->fieldUpdated;
		${$this->appTag.'TableField'} = $this->tableField;
		${$this->appTag.'HideParentField'} = $this->hideParentField;

		$code = $this->params->get('code');
		if(!empty($code)) :
			$code=ltrim($code,'<?php');
			$code=rtrim($code,'?>');
			echo eval($code);
		endif;

		// APP
		require(JPATH_BASE.'/base-apps/'.$this->app.'/'.$this->app.'.php');

	endif;

	?>
</div>
