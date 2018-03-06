<?php
defined('_JEXEC') or die;

// FORM
?>
<input type="hidden" name="issue_id" id="<?php echo $APPTAG?>-issue_id" />
<div class="form-group field-required">
	<label class="label-sm"><?php echo JText::_('FIELD_LABEL_COMMENT'); ?></label>
	<textarea name="comment" id="<?php echo $APPTAG?>-comment" class="field-html"></textarea>
</div>
<div class="form-group">
	<label class="base-icon-attach label-sm"> <?php echo JText::_('FIELD_LABEL_ATTACHMENTS'); ?></label>
	<button type="button" class="base-icon-plus btn btn-sm btn-success float-right hasTooltip" title="<?php echo JText::_('TEXT_ADD'); ?>" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', 'col-12')"></button>
	<div class="btn-file">
		<span class="btn-group">
			<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
		</span>
		<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
	</div>
</div>
<div id="<?php echo $APPTAG?>-files-group" class="row"></div>
