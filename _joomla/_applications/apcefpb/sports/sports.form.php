<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-6 col-md-4">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_IMAGE'); ?></label>
			<div class="btn-file">
				<span class="btn-group w-100">
					<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
				</span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
			</div>
		</div>
	</div>
</div>
