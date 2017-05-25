<?php
defined('_JEXEC') or die;
?>

<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">
			<?php
				echo JText::_('FORM_TITLE');
				if($cfg['showFormDesc']) :
					echo '<div class="small font-condensed">'.JText::_('FORM_DESCRIPTION').'</div>';
				endif;
			?>
		</h4>
	</div>
	<div class="modal-body">
		<fieldset>
			<div class="row">
				<div id="<?php echo $APPTAG?>-formPaginator" class="col-sm-4 hide">
					<div class="form-group field-required">
						<span class="input-group">
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-prev" class="base-icon-left-open btn btn-sm btn-default" disabled></button>
							</span>
							<input type="text" name="id" id="<?php echo $APPTAG?>-id" class="form-control input-sm" readonly="readonly" />
							<input type="hidden" name="relationId" id="<?php echo $APPTAG?>-relationId" value="<?php echo $_SESSION[$RTAG.'RelId']?>" />
							<input type="hidden" name="<?php echo $APPTAG?>-prev" id="<?php echo $APPTAG?>-prev" />
							<input type="hidden" name="<?php echo $APPTAG?>-next" id="<?php echo $APPTAG?>-next" />
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-next" class="base-icon-right-open btn btn-sm btn-default" disabled></button>
							</span>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-fieldState" class="col-sm-4">
					<div class="form-group">
						<span id="<?php echo $APPTAG?>-state-group" class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-default btn-active-success strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-1" value="1" />
								<?php echo JText::_('TEXT_ACTIVE'); ?>
							</label>
							<label class="btn btn-sm btn-default btn-active-danger strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-0" value="0" />
								<?php echo JText::_('TEXT_INACTIVE'); ?>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-restart" class="col-sm-4 hide">
					<div class="form-group">
						<button type="button" id="btn-<?php echo $APPTAG?>-restart" class="base-icon-cw btn btn-sm btn-default btn-block">
							 <?php echo JText::_('TEXT_RESTART'); ?>
						</button>
					</div>
				</div>
			</div>

			<hr class="hr-xs" />

			<!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#tab<?php echo $APPTAG?>Main" aria-controls="tab<?php echo $APPTAG?>Main" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_MAIN'); ?></a></li>
				<li role="presentation"><a href="#tab<?php echo $APPTAG?>Content" aria-controls="tab<?php echo $APPTAG?>Content" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_CONTENT'); ?></a></li>
				<?php if($_SESSION[$RTAG.'ShowTabOptions']) :?>
					<li role="presentation"><a href="#tab<?php echo $APPTAG?>Config" aria-controls="tab<?php echo $APPTAG?>Config" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_CONFIGURATION')?></a></li>
				<?php endif; ?>
			</ul>
			<!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="tab<?php echo $APPTAG?>Main">
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_TITLE'); ?></label>
								<input type="text" name="title" id="<?php echo $APPTAG?>-title" class="form-control" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_TAG_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_TAG'); ?></label>
								<input type="text" name="tag" id="<?php echo $APPTAG?>-tag" class="form-control" <?php if(!empty($APPTAG)) echo 'readonly'?> />
								<input type="hidden" name="ctag" id="<?php echo $APPTAG?>-ctag" />
							</div>
						</div>
					</div>
					<hr class="hr-label" />
					<span class="label label-warning"><?php echo JText::_('FIELD_LABEL_COVER'); ?></span>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_COVER_TYPE_0_DESC'); ?>">
										<input type="radio" name="coverType" id="<?php echo $APPTAG?>-coverType-0" value="0" class="auto-tab" data-target="<?php echo $APPTAG?>-cover-0" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-cover-1" />
										<?php echo JText::_('FIELD_LABEL_COVER_TYPE_0'); ?>
									</label>
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_COVER_TYPE_1_DESC'); ?>">
										<input type="radio" name="coverType" id="<?php echo $APPTAG?>-coverType-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-cover-1" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-cover-0" />
										<?php echo JText::_('FIELD_LABEL_COVER_TYPE_1'); ?>
									</label>
								</span>
							</div>
						</div>
						<div class="col-sm-8">
							<div id="<?php echo $APPTAG?>-cover-0" class="form-group">
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action"> <?php echo JText::_('TEXT_IMAGE_SELECT'); ?></button>
								</span>
								<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control element-invisible" />
							</div>
							<div id="<?php echo $APPTAG?>-cover-1" class="form-group hide">
								<input type="text" name="videoEmbed" id="<?php echo $APPTAG?>-videoEmbed" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_ONLINE_VIDEO_EMBED'); ?>" />
							</div>
						</div>
					</div>
					<hr class="hr-sm" />
					<div class="row">
						<div class="col-sm-2 <?php if($_SESSION[$RTAG.'HideOrderField']) echo 'hide'; ?>">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ORDER_DESC')?>"><?php echo JText::_('FIELD_LABEL_ORDER')?></label>
								<input type="number" name="ordering" id="<?php echo $APPTAG?>-ordering" class="field-number" size="4" />
								<input type="hidden" name="cordering" id="<?php echo $APPTAG?>-cordering" />
							</div>
						</div>
						<?php if(!$_SESSION[$RTAG.'HideAllDateFields']) : ?>
							<?php if(!$_SESSION[$RTAG.'HideDateField']) : ?>
								<div class="col-sm-3">
									<div class="form-group">
										<label><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
										<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
									</div>
								</div>
							<?php endif;?>
							<?php if(!$_SESSION[$RTAG.'HideMonthField']) : ?>
								<div class="col-sm-3">
									<div class="form-group">
										<label><?php echo JText::_('FIELD_LABEL_MONTH'); ?></label>
										<select name="month" id="<?php echo $APPTAG?>-month" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-year">
											<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
											<option value="1">Janeiro</option>
											<option value="2">Fevereiro</option>
											<option value="3">Março</option>
											<option value="4">Abril</option>
											<option value="5">Maio</option>
											<option value="6">Junho</option>
											<option value="7">Julho</option>
											<option value="8">Agosto</option>
											<option value="9">Setembro</option>
											<option value="10">Outubro</option>
											<option value="11">Novembro</option>
											<option value="12">Dezembro</option>
										</select>
									</div>
								</div>
							<?php endif;?>
							<?php if(!$_SESSION[$RTAG.'HideYearField']) : ?>
								<div class="col-sm-2">
									<div class="form-group">
										<label><?php echo JText::_('FIELD_LABEL_YEAR'); ?></label>
										<input type="text" name="year" id="<?php echo $APPTAG?>-year" class="form-control field-number upper" />
									</div>
								</div>
							<?php endif;?>
						<?php endif;?>
						<?php if(!$_SESSION[$RTAG.'HideDescriptionField']) : ?>
							<div class="col-sm-<?php echo $_SESSION[$RTAG.'HideAllDateFields'] ? '10' : '12'?>">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
									<textarea rows="4" name="description" id="<?php echo $APPTAG?>-description" class="<?php echo $_SESSION[$RTAG.'DescriptionHtml'] ? 'field-html' : 'form-control'?>" data-editor-full="<?php echo $_SESSION[$RTAG.'DescriptionHtmlFull'] ? 'true' : 'false'?>"></textarea>
								</div>
							</div>
						<?php endif;?>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="tab<?php echo $APPTAG?>Content">
					<div class="row">
						<div class="col-sm-12<?php if($_SESSION[$RTAG.'OnlyContentDef']) echo ' hide'?>">
							<div class="form-group">
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 0 || $_SESSION[$RTAG.'FormShowLinkContent'] == 1) :?>
										<label class="btn btn-default btn-active-success">
											<input type="radio" name="contentType" id="<?php echo $APPTAG?>-contentType-0" value="0" class="auto-tab" data-target="<?php echo $APPTAG?>-group-0" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-group-1,#<?php echo $APPTAG?>-group-2,#<?php echo $APPTAG?>-group-3" />
											<?php echo JText::_('FIELD_LABEL_CONTENT_TYPE_0'); ?>
										</label>
									<?php endif;?>
									<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 1 || $_SESSION[$RTAG.'FormShowFileContent'] == 1) :?>
										<label class="btn btn-default btn-active-success">
											<input type="radio" name="contentType" id="<?php echo $APPTAG?>-contentType-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-group-1" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-group-0,#<?php echo $APPTAG?>-group-2,#<?php echo $APPTAG?>-group-3" />
											<?php echo JText::_('FIELD_LABEL_CONTENT_TYPE_1'); ?>
										</label>
									<?php endif;?>
									<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 2 || $_SESSION[$RTAG.'FormShowTextContent'] == 1) :?>
										<label class="btn btn-default btn-active-success">
											<input type="radio" name="contentType" id="<?php echo $APPTAG?>-contentType-2" value="2" class="auto-tab" data-target="<?php echo $APPTAG?>-group-2" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-group-0,#<?php echo $APPTAG?>-group-1,#<?php echo $APPTAG?>-group-3" />
											<?php echo JText::_('FIELD_LABEL_CONTENT_TYPE_2'); ?>
										</label>
									<?php endif;?>
									<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 3 || $_SESSION[$RTAG.'FormShowGalleryContent'] == 1) :?>
										<label class="btn btn-default btn-active-success">
											<input type="radio" name="contentType" id="<?php echo $APPTAG?>-contentType-3" value="3" class="auto-tab" data-target="<?php echo $APPTAG?>-group-3" data-target-display="true" data-target-group="#<?php echo $APPTAG?>-group-0,#<?php echo $APPTAG?>-group-1,#<?php echo $APPTAG?>-group-2" />
											<?php echo JText::_('FIELD_LABEL_CONTENT_TYPE_3'); ?>
										</label>
									<?php endif;?>
								</span>
							</div>
						</div>
						<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 0 || $_SESSION[$RTAG.'FormShowLinkContent'] == 1) :?>
							<div id="<?php echo $APPTAG?>-group-0" class="hide">
								<div class="col-sm-8">
									<div class="form-group">
										<label><?php echo JText::_('FIELD_LABEL_URL'); ?></label>
										<input type="text" name="url" id="<?php echo $APPTAG?>-url" class="form-control" />
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="display-block">&#160;</label>
										<div class="btn-group width-full" data-toggle="buttons">
											<label class="btn btn-warning btn-block btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_TARGET_DESC'); ?>">
												<span class="base-icon-cancel btn-icon"></span>
												<input type="checkbox" name="target" id="<?php echo $APPTAG?>-target" value="1" /> <?php echo JText::_('FIELD_LABEL_TARGET'); ?>
											</label>
										</div>
									</div>
								</div>
							</div>
						<?php endif;?>
						<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 1 || $_SESSION[$RTAG.'FormShowFileContent'] == 1) :?>
							<div id="<?php echo $APPTAG?>-group-1" class="col-sm-12 hide">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_FILE'); ?></label><br />
									<span class="btn-group">
										<button type="button" class="base-icon-search btn btn-default set-file-action"> <?php echo JText::_('TEXT_FILE_SELECT')?></button>
									</span>
									<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control element-invisible" />
								</div>
							</div>
						<?php endif;?>
						<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 2 || $_SESSION[$RTAG.'FormShowTextContent'] == 1) :?>
							<div id="<?php echo $APPTAG?>-group-2" class="col-sm-12 hide">
								<!-- Nav tabs -->
							  <ul class="nav nav-tabs" role="tabContentMain">
							    <li role="presentation" class="active"><a href="#tab<?php echo $APPTAG?>ContentMain" aria-controls="tab<?php echo $APPTAG?>ContentMain" role="tab" data-toggle="tab"><?php echo JText::_('FIELD_LABEL_CONTENT'); ?></a></li>
									<li role="presentation"><a href="#tab<?php echo $APPTAG?>ContentConfig" aria-controls="tab<?php echo $APPTAG?>ContentConfig" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_CONFIGURATION')?></a></li>
							  </ul>
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="tab<?php echo $APPTAG?>ContentMain">
										<div class="form-group">
											<textarea rows="8" name="content" id="<?php echo $APPTAG?>-content" class="field-html" data-editor-full="true"></textarea>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="tab<?php echo $APPTAG?>ContentConfig">
										<div class="row">
											<div class="col-sm-5">
												<div class="form-group">
													<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_CONTENT_MODAL_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_CONTENT_MODAL'); ?></label>
													<div class="btn-group btn-group-justified" data-toggle="buttons">
														<label class="btn btn-default btn-active-danger">
															<input type="radio" name="element_contentModal" id="<?php echo $APPTAG?>-element_contentModal-0" value="0" />
															<?php echo JText::_('TEXT_NO'); ?>
														</label>
														<label class="btn btn-default btn-active-success">
															<input type="radio" name="element_contentModal" id="<?php echo $APPTAG?>-element_contentModal-1" value="1" />
															<?php echo JText::_('TEXT_YES'); ?>
														</label>
														<label class="btn btn-default btn-active-success">
															<input type="radio" name="element_contentModal" id="<?php echo $APPTAG?>-element_contentModal-3" value="" />
															<?php echo JText::_('TEXT_DEFAULT'); ?>
														</label>
													</div>
												</div>
											</div>
											<div class="col-sm-5">
												<div class="form-group">
													<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_MODAL_HEADER_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_MODAL_HEADER'); ?></label>
													<div class="btn-group btn-group-justified" data-toggle="buttons">
														<label class="btn btn-default btn-active-danger">
															<input type="radio" name="element_modalHeader" id="<?php echo $APPTAG?>-element_modalHeader-0" value="0" />
															<?php echo JText::_('TEXT_NO'); ?>
														</label>
														<label class="btn btn-default btn-active-success">
															<input type="radio" name="element_modalHeader" id="<?php echo $APPTAG?>-element_modalHeader-1" value="1" />
															<?php echo JText::_('TEXT_YES'); ?>
														</label>
														<label class="btn btn-default btn-active-success">
															<input type="radio" name="element_modalHeader" id="<?php echo $APPTAG?>-element_modalHeader-3" value="" />
															<?php echo JText::_('TEXT_DEFAULT'); ?>
														</label>
													</div>
												</div>
											</div>
											<div class="col-sm-5">
												<div class="form-group">
													<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_MODAL_SIZE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_MODAL_SIZE'); ?></label>
													<select name="element_modalSize" id="<?php echo $APPTAG?>-element_modalSize" class="form-control" />
														<option value=""><?php echo JText::_('TEXT_SELECT'); ?></option>
														<option value="modal-lg"><?php echo JText::_('FIELD_LABEL_LARGE'); ?></option>
														<option value="modal-md"><?php echo JText::_('FIELD_LABEL_MEDIUM'); ?></option>
														<option value="modal-sm"><?php echo JText::_('FIELD_LABEL_SMALL'); ?></option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif;?>
						<?php if($_SESSION[$RTAG.'ContentTypeDef'] == 3 || $_SESSION[$RTAG.'FormShowGalleryContent'] == 1) :?>
							<div id="<?php echo $APPTAG?>-group-3" class="col-sm-12 hide">
								<hr class="hr-sm" />
								<div class="row">
									<div class="col-xs-6 col-sm-3">
										<div class="form-group">
											<span class="btn-group">
												<button type="button" class="base-icon-search btn btn-default set-file-action"></button>
											</span>
											<input type="file" name="file[2]" id="<?php echo $APPTAG?>-file2" class="form-control field-image element-invisible" />
										</div>
									</div>
									<div id="<?php echo $APPTAG?>-files-group"></div>
								</div>
								<hr class="hr-sm" />
								<div class="row">
									<div class="col-sm-4">
										<button type="button" class="base-icon-plus btn btn-block btn-success" onclick="<?php echo $APPTAG?>_setNewFile()">
											<?php echo JText::_('TEXT_ADD'); ?>
										</button>
									</div>
								</div>
							</div>
						<?php endif;?>
					</div>
				</div>
				<?php if($_SESSION[$RTAG.'ShowTabOptions']) :?>
					<div role="tabpanel" class="tab-pane" id="tab<?php echo $APPTAG?>Config">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#tab<?php echo $APPTAG?>ConfigElement" aria-controls="tab<?php echo $APPTAG?>ConfigElement" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_MAIN'); ?></a></li>
							<li role="presentation"><a href="#tab<?php echo $APPTAG?>ConfigContent" aria-controls="tab<?php echo $APPTAG?>ConfigContent" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_PAGE_CONTENT')?></a></li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="tab<?php echo $APPTAG?>ConfigElement">
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_CLASS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_CLASS'); ?></label>
											<input type="text" name="element_class" id="<?php echo $APPTAG?>-element_class" class="form-control" />
										</div>
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_DATE_FORMAT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_DATE_FORMAT'); ?></label>
											<input type="text" name="element_dateFormat" id="<?php echo $APPTAG?>-element_dateFormat" class="form-control" />
										</div>
									</div>
									<div class="col-sm-8">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_LAYOUT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_LAYOUT'); ?></label>
											<textarea rows="5" name="element_layout" id="<?php echo $APPTAG?>-element_layout"></textarea>
										</div>
									</div>
								</div>
								<hr class="hr-label" />
								<span class="label label-warning"><?php echo JText::_('TEXT_IMAGE_COVER'); ?></span>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_WIDTH_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_WIDTH'); ?></label>
											<span class="input-group">
												<input type="text" name="element_imageWidth" id="<?php echo $APPTAG?>-element_imageWidth" class="form-control" />
												<span class="input-group-addon">px</span>
											</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_HEIGHT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_HEIGHT'); ?></label>
											<span class="input-group">
												<input type="text" name="element_imageHeight" id="<?php echo $APPTAG?>-element_imageHeight" class="form-control" />
												<span class="input-group-addon">px</span>
											</span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_CLASS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_IMAGE_CLASS'); ?></label>
											<input type="text" name="element_imageClass" id="<?php echo $APPTAG?>-element_imageClass" class="form-control" />
										</div>
									</div>
								</div>
								<hr class="hr-label" />
								<span class="label label-warning"><?php echo JText::_('TEXT_DOWNLOAD_LINK'); ?></span>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_DOWNLOAD_LABEL_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_DOWNLOAD_LABEL'); ?></label>
											<input type="text" name="element_downloadLabel" id="<?php echo $APPTAG?>-element_downloadLabel" class="form-control" />
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_ELEMENT_DOWNLOAD_CLASS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ELEMENT_DOWNLOAD_CLASS'); ?></label>
											<input type="text" name="element_downloadClass" id="<?php echo $APPTAG?>-element_downloadClass" class="form-control" />
										</div>
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="tab<?php echo $APPTAG?>ConfigContent">
								<div class="row">
									<div class="col-sm-8">
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_CONTENT_LAYOUT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_CONTENT_LAYOUT'); ?></label>
											<textarea rows="7" name="content_layout" id="<?php echo $APPTAG?>-content_layout" style="height:152px;"></textarea>
										</div>
										<hr class="hr-label" />
										<span class="label label-warning"><?php echo JText::_('TEXT_IMAGE_COVER'); ?></span>
										<div class="text-xs text-muted font-featured"><?php echo JText::_('FIELD_LABEL_CONTENT_IMAGE_COVER_DESC'); ?></div>
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label><?php echo JText::_('FIELD_LABEL_CONTENT_IMAGE_WIDTH'); ?></label>
													<span class="input-group">
														<input type="text" name="content_imageWidth" id="<?php echo $APPTAG?>-content_imageWidth" class="form-control" />
														<span class="input-group-addon">px</span>
													</span>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label><?php echo JText::_('FIELD_LABEL_CONTENT_IMAGE_HEIGHT'); ?></label>
													<span class="input-group">
														<input type="text" name="content_imageHeight" id="<?php echo $APPTAG?>-content_imageHeight" class="form-control" />
														<span class="input-group-addon">px</span>
													</span>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label><?php echo JText::_('FIELD_LABEL_CONTENT_IMAGE_CLASS'); ?></label>
													<input type="text" name="content_imageClass" id="<?php echo $APPTAG?>-content_imageClass" class="form-control" />
												</div>
											</div>
										</div>
										<hr class="hr-label" />
										<span class="label label-warning"><?php echo JText::_('FIELD_LABEL_URL_LIST'); ?></span>
										<div class="text-xs text-muted font-featured bottom-space-xs"><?php echo JText::_('FIELD_LABEL_URL_LIST_DESC'); ?></div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<input type="text" name="urlList" id="<?php echo $APPTAG?>-urlList" class="form-control" />
												</div>
											</div>
										</div>
										<hr class="hr-label" />
										<span class="label label-warning"><?php echo JText::_('FIELD_LABEL_URL_CONTENT'); ?></span>
										<div class="text-xs text-muted font-featured bottom-space-xs"><?php echo JText::_('FIELD_LABEL_URL_CONTENT_DESC'); ?></div>
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group">
													<input type="text" name="urlContent" id="<?php echo $APPTAG?>-urlContent" class="form-control" />
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-4">
										<hr class="hr-label" />
										<span class="label label-warning"><?php echo JText::_('TEXT_GALLERY'); ?></span>
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_GRID_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_GRID'); ?></label>
											<input type="text" name="content_galleryGrid" id="<?php echo $APPTAG?>-content_galleryGrid" class="form-control" />
										</div>
										<div class="form-group">
											<label><?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_IMAGE_WIDTH'); ?></label>
											<span class="input-group">
												<input type="text" name="content_galImageWidth" id="<?php echo $APPTAG?>-content_galImageWidth" class="form-control" />
												<span class="input-group-addon">px</span>
											</span>
										</div>
										<div class="form-group">
											<label><?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_IMAGE_HEIGHT'); ?></label>
											<span class="input-group">
												<input type="text" name="content_galImageHeight" id="<?php echo $APPTAG?>-content_galImageHeight" class="form-control" />
												<span class="input-group-addon">px</span>
											</span>
										</div>
										<div class="form-group">
											<label><?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_IMAGE_CLASS'); ?></label>
											<input type="text" name="content_galImageClass" id="<?php echo $APPTAG?>-content_galImageClass" class="form-control" />
										</div>
										<div class="form-group">
											<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_CAPTION_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_CONTENT_GALLERY_CAPTION'); ?></label>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-default btn-active-danger">
													<input type="radio" name="content_galleryCaption" id="<?php echo $APPTAG?>-content_galleryCaption-0" value="0" />
													<?php echo JText::_('TEXT_NO'); ?>
												</label>
												<label class="btn btn-default btn-active-success">
													<input type="radio" name="content_galleryCaption" id="<?php echo $APPTAG?>-content_galleryCaption-1" value="1" />
													<?php echo JText::_('TEXT_YES'); ?>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif;?>
			</div>
		</fieldset>
	</div>
	<div class="modal-footer">
		<div class="pull-left bottom-space-xs text-left text-overflow">
			<span class="base-icon-ok-circled2 set-success text-success hide"></span>
			<span class="base-icon-cancel-circled set-error text-danger hide"></span>
			<span class="ajax-loader hide"></span>
		</div>
		<div class="pull-right">
			<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save('reset');"> <?php echo JText::_('TEXT_SAVENEW'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-delete" id="btn-<?php echo $APPTAG?>-delete" class="base-icon-trash btn btn-danger btn-sm hide" onclick="<?php echo $APPTAG?>_del(0, true)"> <?php echo JText::_('TEXT_DELETE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-default btn-sm" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
		</div>
	</div>

</form>
