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
				<div class="col-sm-6">
					<div class="form-group">
						<label class="field-required"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
						<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label>E-mail</label>
						<input type="text" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
						<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-success">
								<span class="base-icon-unset"></span>
								<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" value="1" />
								<?php echo JText::_('FIELD_LABEL_GENDER_MALE_ABBR'); ?>
							</label>
							<label class="btn btn-default btn-active-success">
								<span class="base-icon-unset"></span>
								<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" value="2" />
								<?php echo JText::_('FIELD_LABEL_GENDER_FEMALE_ABBR'); ?>
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-4 <?php echo $APPTAG?>-group-cpf">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
						<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
						<input type="text" name="phone" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" data-toggle-mask="true" />
					</div>
				</div>
				<div class="col-sm-2">
					<span class="input-group-btn">
						<div class="btn-group" data-toggle="buttons">
							<label>&#160;</label><br />
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
								<input type="checkbox" name="whatsapp" id="<?php echo $APPTAG?>-whatsapp" value="1" />
								<span class="base-icon-whatsapp icon-default text-success"></span>
							</label>
						</div>
					</span>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_PHONE_OPCIONAL'); ?></label>
						<input type="text" name="phone2" id="<?php echo $APPTAG?>-phone2" class="form-control field-phone" data-toggle-mask="true" />
					</div>
				</div>
				<div class="col-sm-2">
					<span class="input-group-btn">
						<div class="btn-group" data-toggle="buttons">
							<label>&#160;</label><br />
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
								<input type="checkbox" name="whatsapp2" id="<?php echo $APPTAG?>-whatsapp2" value="1" />
								<span class="base-icon-whatsapp icon-default"></span>
							</label>
						</div>
					</span>
				</div>
			</div>
			<hr class="hr-sm" />
			<div class="form-group">
				<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
				<textarea name="note" rows="3" id="<?php echo $APPTAG?>-note" class="form-control"></textarea>
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
