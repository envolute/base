<?php
defined('_JEXEC') or die;
?>

<form name="form-status-<?php echo $APPTAG?>" id="form-status-<?php echo $APPTAG?>" method="post">

	<div class="modal-body">
	    <input type="hidden" name="statusId" id="<?php echo $APPTAG?>-statusId" />
		<div class="form-group no-margin">
			<div class="btn-group btn-group-justified" data-toggle="buttons">
				<label class="base-icon-lightbulb btn btn-outline-info btn-active-info hasTooltip" title="<?php echo JText::_('TEXT_STATUS_0'); ?>" onclick="<?php echo $APPTAG?>_setStatus(0);">
					<input type="radio" name="new_status" value="0" />
				</label>
				<label class="base-icon-clock btn btn-outline-danger btn-active-danger hasTooltip" title="<?php echo JText::_('TEXT_STATUS_1'); ?>" onclick="<?php echo $APPTAG?>_setStatus(1);">
					<input type="radio" name="new_status" value="1" />
				</label>
				<label class="base-icon-off btn btn-outline-warning btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_STATUS_2'); ?>" onclick="<?php echo $APPTAG?>_setStatus(2);">
					<input type="radio" name="new_status" value="2" />
				</label>
				<label class="base-icon-ok btn btn-outline-success btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_STATUS_3'); ?>" onclick="<?php echo $APPTAG?>_setStatus(3);">
					<input type="radio" name="new_status" value="3" />
				</label>
			</div>
		</div>
	</div>

</form>
