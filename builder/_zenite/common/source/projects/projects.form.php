<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__zenite_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();
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

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tabMain" aria-controls="tabMain" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_MAIN'); ?></a></li>
				<li role="presentation"><a href="#tabInfo" aria-controls="tabInfo" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_INFO')?></a></li>
				<li role="presentation"><a href="#tabSubs" aria-controls="tabSubs" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_SUBSCRIPTIONS')?></a></li>
				<li role="presentation"><a href="#tabExtra" aria-controls="tabExtra" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_RULES')?></a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tabMain">
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
								<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-name">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($clients as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
								<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
								<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_LOCATION'); ?></label>
								<input type="text" name="location" id="<?php echo $APPTAG?>-location" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_LIMIT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_LIMIT'); ?></label>
								<input type="number" name="limit" id="<?php echo $APPTAG?>-limit" class="form-control" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_START_DATE'); ?></label>
								<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" data-time="true" data-seconds="false" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_END_DATE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_END_DATE'); ?></label>
								<input type="text" name="end_date" id="<?php echo $APPTAG?>-end_date" data-time="true" data-seconds="false" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_IMAGE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_IMAGE')?> <span class="label label-warning"><?php echo JText::_('TEXT_COVER')?></span></label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
								</span>
								<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control field-image element-invisible" />
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="tabInfo">
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_URL_INFO_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_URL_INFO'); ?></label>
								<input type="text" name="url_info" id="<?php echo $APPTAG?>-url_info" class="form-control field-url" placeholder="http://" />
							</div>
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_SIZE_SHIRTS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_SIZE_SHIRTS'); ?></label>
								<select name="sizeShirts[]" id="<?php echo $APPTAG?>-sizeShirts" class="form-control" multiple="multiple">
									<option value="PP">PP</option>
									<option value="P">P</option>
									<option value="M">M</option>
									<option value="G">G</option>
									<option value="GG">GG</option>
									<option value="Baby Look - P">Baby Look - P</option>
									<option value="Baby Look - M">Baby Look - M</option>
									<option value="Baby Look - G">Baby Look - G</option>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_IMAGE_SHIRT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_IMAGE_SHIRT')?></label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
								</span>
								<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control field-image element-invisible" />
							</div>
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_IMAGE_MEDALS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_IMAGE_MEDALS')?></label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
								</span>
								<input type="file" name="file[2]" id="<?php echo $APPTAG?>-file2" class="form-control field-image element-invisible" />
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
								<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
							</div>
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
								<textarea rows="8" name="description" id="<?php echo $APPTAG?>-description" class="field-editor" data-editor-full="true"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="tabSubs">
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_URL_REGISTRATION_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_URL_REGISTRATION'); ?></label>
								<input type="text" name="url_registration" id="<?php echo $APPTAG?>-url_registration" class="form-control field-url" placeholder="http://" />
							</div>
						</div>
						<div class="col-xs-6 col-sm-4">
							<label class="no-margin iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PAYMENT_METHOD_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PAYMENT_METHOD'); ?></label>
							<div class="checkbox no-margin">
								<label>
									<input type="checkbox" name="payment_card" id="<?php echo $APPTAG?>-payment_card" value="1" />
									<?php echo JText::_('FIELD_LABEL_CREDIT_CARD'); ?>
								</label>
							</div>
							<div class="checkbox no-margin">
								<label>
									<input type="checkbox" name="payment_boleto" id="<?php echo $APPTAG?>-payment_boleto" value="1" />
									<?php echo JText::_('FIELD_LABEL_BOLETO'); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_EXTRA_FIELD'); ?></label>
								<span id="<?php echo $APPTAG?>-extra-field-group" class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-danger">
										<input type="radio" name="extra_field" id="<?php echo $APPTAG?>-extra_field-0" value="0" class="auto-tab" data-target="<?php echo $APPTAG?>-extra_field_label_group" data-target-display="false" />
										<?php echo JText::_('TEXT_NO'); ?>
									</label>
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="extra_field" id="<?php echo $APPTAG?>-extra_field-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-extra_field_label_group" data-target-display="true" />
										<?php echo JText::_('TEXT_YES'); ?>
									</label>
								</span>
							</div>
						</div>
						<span id="<?php echo $APPTAG?>-extra_field_label_group" class="hide">
							<div class="col-sm-4">
								<div class="form-group">
									<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_EXTRA_FIELD_LABEL_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_EXTRA_FIELD_LABEL'); ?></label>
									<input type="text" name="extra_field_label" id="<?php echo $APPTAG?>-extra_field_label" class="form-control" />
								</div>
							</div>
							<div class="col-xs-6 col-sm-4">
								<label class="no-margin">&nbsp;</label>
								<div class="checkbox no-margin">
									<label>
										<input type="checkbox" name="extra_field_required" id="<?php echo $APPTAG?>-extra_field_required" value="1" />
										<?php echo JText::_('FIELD_LABEL_EXTRA_FIELD_REQUIRED'); ?>
									</label>
								</div>
							</div>
						</span>
					</div>
					<hr class="hr-label" />
					<span class="label label-warning"><?php echo JText::_('FIELD_LABEL_DISCOUNT'); ?></span>
					<div class="row">
						<div class="col-sm-4 col-md-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DISCOUNT_COUPON_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DISCOUNT_COUPON'); ?></label>
								<div class="input-group">
									<input type="number" name="discount_coupon" id="<?php echo $APPTAG?>-discount_coupon" class="form-control" />
									<span class="input-group-addon">%</span>
								</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DISCOUNT_AGE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DISCOUNT_AGE'); ?></label>
								<div class="input-group">
									<input type="number" name="discount_age" id="<?php echo $APPTAG?>-discount_age" class="form-control" />
									<span class="input-group-addon">%</span>
								</div>
							</div>
						</div>
						<div class="col-sm-4 col-md-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DISCOUNT_DEFICIENT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DISCOUNT_DEFICIENT'); ?></label>
								<div class="input-group">
									<input type="number" name="discount_deficient" id="<?php echo $APPTAG?>-discount_deficient" class="form-control" />
									<span class="input-group-addon">%</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="tabExtra">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_RULES_FILE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_RULES_FILE')?></label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
								</span>
								<input type="file" name="file[3]" id="<?php echo $APPTAG?>-file3" class="form-control element-invisible" />
							</div>
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_RULES_TEXT_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_RULES_TEXT'); ?></label>
								<textarea rows="8" name="rules_content" id="<?php echo $APPTAG?>-rules_content" class="field-editor"></textarea>
							</div>
						</div>
					</div>
				</div>
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
