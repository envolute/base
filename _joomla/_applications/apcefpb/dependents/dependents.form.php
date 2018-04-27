<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// CLIENTS
$query = '
	SELECT *
	FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .'
	WHERE
		'. $db->quoteName('state') .' = 1 AND
		'. $db->quoteName('access') .' = 1
	ORDER BY name
';
$db->setQuery($query);
$clients = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="row">
			<div class="col-sm-8 col-lg-6">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<div class="input-group">
						<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						<span class="btn-group input-group-btn" data-toggle="buttons">
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('MSG_CARD_NAME'); ?>">
								<input type="checkbox" name="toggleName" id="<?php echo $APPTAG?>-toggleName" value="1" class="no-validate auto-tab" data-target="<?php echo $APPTAG?>-group-nameCard" data-target-display="true" />
								<span class="base-icon-plus icon-default"></span>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-nameCard" class="form-group" hidden>
					<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_CARD_NAME'); ?>"><?php echo JText::_('FIELD_LABEL_CARD_NAME'); ?></label>
					<input type="text" name="card_name" id="<?php echo $APPTAG?>-card_name" class="form-control upper" maxlength="30" />
				</div>
			</div>
			<div class="col-sm-8 col-lg-6">
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
			<div class="col-sm-8 col-lg-6">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
					<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-phone_number">
						<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						<?php
							foreach ($clients as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
					<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" value="1" />
							<?php echo JText::_('TEXT_GENDER_1_ABBR'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" value="2" />
							<?php echo JText::_('TEXT_GENDER_2_ABBR'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-sm-8 col-lg-6">
				<div class="form-group">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
				</div>
			</div>
			<div class="col-sm-4 col-lg-3">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DOCUMENTS_CONFIRM_DESC'); ?>">
						<?php echo JText::_('FIELD_LABEL_DOCUMENTS'); ?>
					</label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-danger">
							<input type="radio" name="docs" id="<?php echo $APPTAG?>-docs-0" value="0" />
							<?php echo JText::_('TEXT_NO'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="docs" id="<?php echo $APPTAG?>-docs-1" value="1" />
							<?php echo JText::_('TEXT_YES'); ?>
						</label>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_PHOTO'); ?></label>
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
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></span>
<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="form-group">
			<button type="button" class="btn btn-xs btn-success base-icon-plus float-right" onclick="<?php echo $APPTAG?>_phoneAdd()"> <?php echo JText::_('TEXT_PHONES_ADD')?></button>
			<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
			<div class="row">
				<div class="col-sm-5 col-lg-4">
					<div class="input-group">
						<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" />
						<span class="input-group-btn btn-group" data-toggle="buttons">
							<label class="btn btn-outline-success btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
								<input type="checkbox" name="wapp[]" id="<?php echo $APPTAG?>-wapp" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />
								<span class="base-icon-whatsapp icon-default"></span>
								<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp" />
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-7 col-lg-8 pt-2 pt-sm-0">
					<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />
				</div>
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-phoneGroups" class="newFieldsGroup"></div>
	</div>
	<div class="col-lg-3">
		<div id="<?php echo $APPTAG?>-group-btnPrint" class="form-group" hidden>
			<label class="d-block text-center pt-1"><?php echo JText::_('TEXT_CLIENT_CARD'); ?></label>
			<button type="button" class="btn btn-lg btn-block btn-success base-icon-print btn-icon" onclick="<?php echo $APPTAG?>_printCard()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
