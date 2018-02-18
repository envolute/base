<?php
defined('_JEXEC') or die;

// FORM
?>
<input type="hidden" name="id" id="<?php echo $APPTAG?>-id" />
<div class="row">
	<div class="col-sm-9">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
					<input type="text" name="role" id="<?php echo $APPTAG?>-role" class="form-control upper" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<label class="d-block">
						<?php echo JText::_('FIELD_LABEL_PASSWORD'); ?>
						<small class="text-live font-condensed float-right pt-1"><?php echo JText::_('TEXT_OPTIONAL'); ?></small>
					</label>
					<div class="input-group">
						<input type="password" name="password" id="<?php echo $APPTAG?>-password" class="form-control" />
						<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_PASSWORD_INFO'); ?>" data-placement="top">
							<span class="base-icon-info-circled"></span>
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
					<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
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
<hr class="mt-5" />
<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="btn btn-lg btn-success base-icon-ok btn-icon" onclick="<?php echo $APPTAG?>_save('<?php echo $cfg['saveTrigger']?>');"> <?php echo JText::_('TEXT_SAVE'); ?></button>
<button type="button" class="btn btn-lg btn-default base-icon-cancel" onclick="javascript:history.back()"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
