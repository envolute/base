<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_categories') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$categories = $db->loadObjectList();
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
						<span id="<?php echo $APPTAG?>-state-group" class="btn-group w-full" data-toggle="buttons">
							<label class="col btn btn-sm btn-default btn-active-success strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-1" value="1" />
								<?php echo JText::_('TEXT_ACTIVE'); ?>
							</label>
							<label class="col btn btn-sm btn-default btn-active-danger strong">
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
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_CATEGORY'); ?></label>
						<div class="input-group">
							<select name="category_id" id="<?php echo $APPTAG?>-category_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-description">
								<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
									foreach ($categories as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn-add btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Categories" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Categories_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Categories" data-backdrop="static" data-keyboard="false"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
						<span class="btn-group w-full" data-toggle="buttons">
							<label class="col btn btn-default btn-active-success" onclick="<?php echo $APPTAG?>_setPaymentInfo(0)">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" class="auto-tab" data-target="#<?php echo $APPTAG?>-description" />
								<?php echo JText::_('FIELD_LABEL_TYPE_LINK'); ?>
							</label>
							<label class="col btn btn-default btn-active-success" onclick="<?php echo $APPTAG?>_setPaymentInfo(1)">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-description" />
								<?php echo JText::_('FIELD_LABEL_TYPE_SERVICE'); ?>
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
						<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" />
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_URL'); ?></label>
						<input type="text" name="url" id="<?php echo $APPTAG?>-url" class="form-control field-url" placeholder="http://" />
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-paid-btnGroup" class="col-sm-4 hide">
					<div class="hidden-xs"><label>&#160;</label></div>
					<span class="btn-group width-full" data-toggle="buttons">
						<label class="col btn btn-block btn-warning btn-active-success" onclick="toggleFieldsetEmbed(this, '#<?php echo $APPTAG?>-paid-fieldsGroup', 0)">
							<span class="base-icon-cancel btn-icon"></span>
							<input type="checkbox" name="paid" id="<?php echo $APPTAG?>-paid" value="1" />
							<?php echo JText::_('FIELD_LABEL_PAID'); ?>
						</label>
					</span>
				</div>
			</div>
			<fieldset id="<?php echo $APPTAG?>-paid-fieldsGroup" class="fieldset-embed closed top-expand-sm">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_PERIOD'); ?></label><br />
							<span class="btn-group w-full" data-toggle="buttons">
								<label class="col btn btn-default btn-active-success">
									<input type="radio" name="period" id="<?php echo $APPTAG?>-period-1" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-price" />
									<?php echo JText::_('FIELD_LABEL_MONTH'); ?>
								</label>
								<label class="col btn btn-default btn-active-success">
									<input type="radio" name="period" id="<?php echo $APPTAG?>-period-2" value="2" class="auto-tab" data-target="#<?php echo $APPTAG?>-price" />
									<?php echo JText::_('FIELD_LABEL_QUARTERLY'); ?>
								</label>
								<label class="col btn btn-default btn-active-success">
									<input type="radio" name="period" id="<?php echo $APPTAG?>-period-3" value="3" class="auto-tab" data-target="#<?php echo $APPTAG?>-price" />
									<?php echo JText::_('FIELD_LABEL_SEMESTER'); ?>
								</label>
								<label class="col btn btn-default btn-active-success">
									<input type="radio" name="period" id="<?php echo $APPTAG?>-period-4" value="4" class="auto-tab" data-target="#<?php echo $APPTAG?>-price" />
									<?php echo JText::_('FIELD_LABEL_YEARLY'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group m-0">
							<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label><br />
							<span class="form-inline">
								<select name="currency" id="<?php echo $APPTAG?>-currency" class="auto-tab" data-target="#<?php echo $APPTAG?>-price">
									<option value="BRL">R$</option>
									<option value="USD">U$</option>
									<option value="EUR">&euro;</option>
								</select>
								<input type="text" name="price" id="<?php echo $APPTAG?>-price" size="6" class="form-control field-price" data-convert="true" />
							</span>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group m-0">
							<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
							<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control" />
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group m-0">
							<label><?php echo JText::_('FIELD_LABEL_START_DATE'); ?></label>
							<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" class="form-control field-date" data-convert="true" />
						</div>
					</div>
				</div>
			</fieldset>
			<hr class="hr-label" />
			<span class="label label-warning"><?php echo JText::_('TEXT_ACCESS_DATA'); ?></span>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('TEXT_USER'); ?></label>
						<input type="text" name="user" id="<?php echo $APPTAG?>-user" class="form-control" />
					</div>
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PASSWORD_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PASSWORD'); ?></label>
						<input type="text" name="password" id="<?php echo $APPTAG?>-password" class="form-control" />
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
						<textarea rows="5" name="note" id="<?php echo $APPTAG?>-note" class="form-control"></textarea>
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
