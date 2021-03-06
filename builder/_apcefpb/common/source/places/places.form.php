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
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_ACCESS'); ?></label><br />
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ACCESS_CLIENT_DESC'); ?>" onclick="<?php echo $APPTAG?>_priceDisable(true)">
								<span class="base-icon-unset"></span>
								<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" />
								<?php echo JText::_('FIELD_LABEL_ACCESS_CLIENT'); ?>
							</label>
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ACCESS_CLIENT_CX_DESC'); ?>" onclick="<?php echo $APPTAG?>_priceDisable(true)">
								<span class="base-icon-unset"></span>
								<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" />
								<?php echo JText::_('FIELD_LABEL_ACCESS_CLIENT_CX'); ?>
							</label>
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ACCESS_PUBLIC_DESC'); ?>" onclick="<?php echo $APPTAG?>_priceDisable(false)">
								<span class="base-icon-unset"></span>
								<input type="radio" name="access" id="<?php echo $APPTAG?>-access-2" value="2" />
								<?php echo JText::_('FIELD_LABEL_ACCESS_PUBLIC'); ?>
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div class="hidden-xs hidden-sm"><label>&#160;</label></div>
						<div class="btn-group width-full" data-toggle="buttons">
							<label class="btn btn-block btn-warning btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_HOSTING_DESC'); ?>">
								<span class="base-icon-cancel btn-icon"></span>
								<input type="checkbox" name="hosting" id="<?php echo $APPTAG?>-hosting" value="1" /> <?php echo JText::_('FIELD_LABEL_HOSTING'); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
						<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_CAPACITY'); ?></label>
						<span class="input-group">
							<input type="text" name="capacity" id="<?php echo $APPTAG?>-capacity" class="form-control field-number" />
							<span class="base-icon-male input-group-addon hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CAPACITY_DESC'); ?>"></span>
						</span>
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
						<label><?php echo JText::_('FIELD_LABEL_PRICE_BRAZIL'); ?></label>
						<input type="text" name="price_brazil" id="<?php echo $APPTAG?>-price_brazil" size="6" class="form-control field-price" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PRICE_PUBLIC_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE_PUBLIC'); ?></label>
						<input type="text" name="price_public" id="<?php echo $APPTAG?>-price_public" size="6" class="form-control field-price" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MAX_TIME_DESC')?>"><?php echo JText::_('FIELD_LABEL_MAX_TIME'); ?></label>
						<select name="max_time" id="<?php echo $APPTAG?>-max_time" class="form-control">
							<option value=""><?php echo JText::_('TEXT_SELECT'); ?></option>
							<?php
							for($i = 1; $i <= 24; $i++) {
								$d = $i < 10 ? '0'.$i : $i;
								echo '<option value="'.$d.':00:00">'.$d.':00</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_END_HOUR_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_END_HOUR'); ?></label>
						<input type="text" name="end_hour" id="<?php echo $APPTAG?>-end_hour" class="form-control field-time" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_START_HOUR_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_START_HOUR'); ?></label>
						<input type="text" name="start_hour" id="<?php echo $APPTAG?>-start_hour" class="form-control field-time" />
					</div>
				</div>
				<div class="col-sm-9">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
						<textarea rows="8" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Foto 1 (capa)</label><br />
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
						</span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control element-invisible" />
					</div>
					<div class="form-group">
						<label>Foto 2</label><br />
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
						</span>
						<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control element-invisible" />
					</div>
					<div class="form-group">
						<label>Foto 3</label><br />
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
						</span>
						<input type="file" name="file[2]" id="<?php echo $APPTAG?>-file2" class="form-control element-invisible" />
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
						<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
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
