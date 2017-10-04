<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-description" data-toggle="tab" href="#<?php echo $APPTAG?>TabDesc" role="tab" aria-controls="description"><?php echo JText::_('FIELD_LABEL_ABOUT'); ?></a>
	</li>
	<li class="nav-item dropdown">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-service" data-toggle="tab" href="#<?php echo $APPTAG?>TabService" role="tab" aria-controls="service"><?php echo JText::_('FIELD_LABEL_SERVICE'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-sm-9">
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
							<div class="input-group">
								<select name="group_id" id="<?php echo $APPTAG?>-group_id" class="form-control field-id">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($groups as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<span class="input-group-btn">
									<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
									<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Groups_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_AGREEMENT_DESC')?>"><?php echo JText::_('FIELD_LABEL_AGREEMENT'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-danger">
									<input type="radio" name="agreement" id="<?php echo $APPTAG?>-agreement-0" value="0" />
									<?php echo JText::_('TEXT_NO'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="agreement" id="<?php echo $APPTAG?>-agreement-1" value="1" />
									<?php echo JText::_('TEXT_YES'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
							<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label>E-mail</label>
							<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
							<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
						</div>
					</div>
				</div>
				<hr class="mt-2" />
				<div class="row">
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label>CNPJ</label>
							<input type="text" name="cnpj" id="<?php echo $APPTAG?>-cnpj" class="form-control field-cnpj" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label>Insc. Municipal</label>
							<input type="text" name="insc_municipal" id="<?php echo $APPTAG?>-insc_municipal" class="form-control" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label>Insc. Estadual</label>
							<input type="text" name="insc_estadual" id="<?php echo $APPTAG?>-insc_estadual" class="form-control" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DUE_DATE_DESC')?>"><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
							<select name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control">
								<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<?php
								for($i = 1; $i <= 31; $i++) {
									$d = $i < 10 ? '0'.$i : $i;
									echo '<option value="'.$i.'">'.$d.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_WEBSITE'); ?></label>
							<input type="text" name="website" id="<?php echo $APPTAG?>-website" class="form-control field-url" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_LOGO'); ?></label>
					<div class="image-file">
						<a href="#" class="image-action">
							<div class="image-file-label">
								<span class="image-file-off base-icon-file-image"><small>200 x 200</small></span>
								<span class="image-file-on text-sm base-icon-ok" hidden></span>
								<span class="image-file-edit base-icon-pencil" hidden></span>
							</div>
						</a>
						<span class="btn-group mt-2"></span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabDesc" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-description">
		<div class="form-group">
			<label class="text-sm"><?php echo JText::_('FIELD_LABEL_ABOUT_DESC')?></label>
			<textarea name="description" id="<?php echo $APPTAG?>-description" rows="4" class="form-control field-html"></textarea>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabService" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-service">
		<div class="form-group">
			<label class="text-sm"><?php echo JText::_('FIELD_LABEL_SERVICE_DESC')?></label>
			<textarea name="service_desc" id="<?php echo $APPTAG?>-service_desc" rows="4" class="form-control field-html"></textarea>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DOC_DESC')?>"><?php echo JText::_('FIELD_LABEL_DOC'); ?></label>
					<div class="btn-file">
						<span class="btn-group w-100">
							<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
						</span>
						<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control" hidden />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if(!$cfg['itemView']) :?>
	<div id="<?php echo $APPTAG?>-msg-relations" class="alert alert-info base-icon-info-circled">
		<span class="text-live font-featured"><?php echo JText::_('TEXT_ADDRESSES')?></span>,
		<span class="text-live font-featured"><?php echo JText::_('TEXT_PHONES')?></span>,
		<span class="text-live font-featured"><?php echo JText::_('TEXT_BANKS_ACCOUNTS')?></span>,
		<span class="text-live font-featured"><?php echo JText::_('TEXT_CONTACTS')?></span>
		<?php echo JText::_('MSG_ADD_AFTER_SAVE')?>
	</div>
	<div id="<?php echo $APPTAG?>-buttons-relations" hidden>
		<hr />
		<div class="row">
			<div class="col-6 col-lg-3 py-1">
				<button type="button" class="btn btn-primary btn-block base-icon-location btn-icon" onclick="<?php echo $APPTAG?>_viewAddresses()" data-toggle="modal" data-target="#modal-list-_addresses" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_ADDRESSES')?></button>
			</div>
			<div class="col-6 col-lg-3 py-1">
				<button type="button" class="btn btn-primary btn-block base-icon-phone btn-icon" onclick="<?php echo $APPTAG?>_viewPhones()" data-toggle="modal" data-target="#modal-list-_phones" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_PHONES')?></button>
			</div>
			<div class="col-6 col-lg-3 py-1">
				<button type="button" class="btn btn-primary btn-block base-icon-bank btn-icon" onclick="<?php echo $APPTAG?>_viewBanks()" data-toggle="modal" data-target="#modal-list-_banksAccountsProviders" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_BANKS_ACCOUNTS')?></button>
			</div>
			<div class="col-6 col-lg-3 py-1">
				<button type="button" class="btn btn-primary btn-block base-icon-user-add btn-icon" onclick="<?php echo $APPTAG?>_viewContacts()" data-toggle="modal" data-target="#modal-list-_providersContacts" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_CONTACTS')?></button>
			</div>
		</div>
	</div>
<?php endif;?>
