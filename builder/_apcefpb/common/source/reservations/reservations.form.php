<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_contacts') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$contacts = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_places') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$places = $db->loadObjectList();
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
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-0" value="0" /> <?php echo JText::_('TEXT_INACTIVE'); ?>
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

			<div class="row">
				<div class="col-sm-9">
					<div id="<?php echo $APPTAG?>-group-client" class="form-group">
						<label class="field-required"><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
						<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-place" onchange="<?php echo $APPTAG?>_setClient(false)">
							<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
							<?php
								foreach ($clients as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
					</div>
					<div id="<?php echo $APPTAG?>-group-contact" class="form-group">
						<label class="field-required"><?php echo JText::_('FIELD_LABEL_CONTACT'); ?></label>
						<div class="input-group">
							<select name="contact_id" id="<?php echo $APPTAG?>-contact_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-place" onchange="<?php echo $APPTAG?>_setContact(false)">
								<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
								<?php
									foreach ($contacts as $obj) {
										echo '<option value="'.$obj->id.'" data-group="'.$obj->group_id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-contacts" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editContact()"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_PLACE'); ?></label>
						<div class="input-group">
							<select name="place_id" id="<?php echo $APPTAG?>-place_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-date" onchange="<?php echo $APPTAG?>_setPlace(this.value)">
								<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
								<?php
									foreach ($places as $obj) {
										$mTime = (int) substr($obj->max_time, 0, 2);
										echo '<option value="'.$obj->id.'" data-host="'.$obj->hosting.'" data-max-time="'.$mTime.'" data-capacity="'.$obj->capacity.'" data-price="'.$obj->price.'" data-price-brazil="'.$obj->price_brazil.'" data-price-public="'.$obj->price_public.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-places" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="places_listReload(false)" data-toggle="modal" data-target="#modal-list-places" data-backdrop="static" data-keyboard="false"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
						<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-time" class="col-sm-3 hide">
					<div class="form-group">
						<label class="field-required"><?php echo JText::_('FIELD_LABEL_TIME'); ?></label>
						<select name="time" id="<?php echo $APPTAG?>-time" class="form-control">
							<option value="00:00:00">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						</select>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-amount" class="col-sm-3 hide">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_AMOUNT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_AMOUNT'); ?></label>
						<select name="amount" id="<?php echo $APPTAG?>-amount" class="form-control">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								for($i = 1; $i <= 90; $i++) {
									echo '<option value="'.$i.'">'.$i.'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group field-required">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_GUESTS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_GUESTS'); ?></label>
						<select name="guests" id="<?php echo $APPTAG?>-guests" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-price">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
						<input type="text" name="price" id="<?php echo $APPTAG?>-price" size="6" class="form-control field-price" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="display-block">&#160;</label>
						<div class="btn-group width-full" data-toggle="buttons">
							<label class="btn btn-block btn-warning btn-active-success">
								<span class="base-icon-cancel"></span>
								<input type="checkbox" name="confirmed" id="<?php echo $APPTAG?>-confirmed" value="1" /> <?php echo JText::_('FIELD_LABEL_CONFIRMED'); ?>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div id="<?php echo $APPTAG?>-group-client_alert" class="well well-sm">
				<div class="row">
					<div class="col-sm-6">
						<div class="checkbox no-margin-top">
							<label>
								<input type="checkbox" name="client_alert" id="<?php echo $APPTAG?>-client_alert" value="1" />
								<?php echo JText::_('MSG_RESERVATION_ALERT'); ?>
							</label>
						</div>
						<?php echo JText::_('MSG_RESERVATION_ALERT_DESC'); ?>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_EXTRA_INFO_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_EXTRA_INFO'); ?></label>
							<textarea rows="4" name="extra_info" id="<?php echo $APPTAG?>-extra_info" class="form-control"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
				<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
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
