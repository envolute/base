<?php
defined('_JEXEC') or die;
?>

<form name="form-status-<?php echo $APPTAG?>" id="form-status-<?php echo $APPTAG?>" method="post">

	<div class="modal-body">
	    <input type="hidden" name="statusId" id="<?php echo $APPTAG?>-statusId" />
		<div class="form-group no-margin">
			<div class="btn-group btn-group-justified" data-toggle="buttons">
				<?php
				for($i = 0; $i < 4; $i++) {
					$icon	= JText::_('TEXT_ICON_STATUS_'.$i);
					$color	= ($i == 0) ? 'warning' : JText::_('TEXT_COLOR_STATUS_'.$i);
					echo '
						<label class="base-icon-'.$icon.' btn btn-outline-'.$color.' btn-active-'.$color.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$i).'" onclick="'.$APPTAG.'_setStatus('.$i.');">
							<input type="radio" name="new_status" value="'.$i.'" />
						</label>
					';
				}
				?>
			</div>
		</div>
	</div>

</form>
