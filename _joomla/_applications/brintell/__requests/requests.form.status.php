<?php
defined('_JEXEC') or die;
?>

<form name="form-status-<?php echo $APPTAG?>" id="form-status-<?php echo $APPTAG?>" method="post">

	<div class="modal-header">
		<h4 class="modal-title">
			<?php echo JText::_('FORM_STATUS_TITLE'); ?>
		</h4>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
	    <input type="hidden" name="statusId" id="<?php echo $APPTAG?>-statusId" />
	    <input type="hidden" name="statusOn" id="<?php echo $APPTAG?>-statusOn" />
		<div class="form-group">
			<input type="text" name="statusDs" id="<?php echo $APPTAG?>-statusDs" class="form-control input-sm" placeholder="<?php echo JText::_('Descrição do Status'); ?>" />
		</div>
		<div class="form-group no-margin">
			<div class="btn-group btn-group-justified" data-toggle="buttons">
				<label class="base-icon-clock text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_STATUS_0'); ?>" onclick="<?php echo $APPTAG?>_setStatus(0);">
					<input type="radio" name="new_status" value="0" />
				</label>
				<label class="base-icon-off text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_STATUS_1'); ?>" onclick="<?php echo $APPTAG?>_setStatus(1);">
					<input type="radio" name="new_status" value="1" />
				</label>
				<label class="base-icon-pause text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_STATUS_2'); ?>" onclick="<?php echo $APPTAG?>_setStatus(2);">
					<input type="radio" name="new_status" value="2" />
				</label>
				<label class="base-icon-ok text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_STATUS_3'); ?>" onclick="<?php echo $APPTAG?>_setStatus(3);">
					<input type="radio" name="new_status" value="3" />
				</label>
				<label class="base-icon-cancel text-success btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('TEXT_STATUS_4'); ?>" onclick="<?php echo $APPTAG?>_setStatus(4);">
					<input type="radio" name="new_status" value="4" />
				</label>
			</div>
		</div>
	</div>

</form>
