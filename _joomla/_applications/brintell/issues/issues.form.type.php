<?php
defined('_JEXEC') or die;
?>

<form name="form-type-<?php echo $APPTAG?>" id="form-type-<?php echo $APPTAG?>" method="post">

	<div class="modal-body">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    <input type="hidden" name="typeId" id="<?php echo $APPTAG?>-typeId" />
		<div class="form-group m-0">
			<div class="btn-group btn-group-justified" data-toggle="buttons">
				<?php
				for($i = 0; $i < 4; $i++) {
					$icon	= JText::_('TEXT_ICON_TYPE_'.$i);
					$color	= ($i == 2) ? 'warning' : JText::_('TEXT_COLOR_TYPE_'.$i);
					echo '
						<label class="base-icon-'.$icon.' btn btn-outline-'.$color.' btn-active-'.$color.' hasTooltip" title="'.JText::_('TEXT_TYPE_'.$i.'_DESC').'" onclick="'.$APPTAG.'_setType('.$i.');">
							<input type="radio" name="new_type" value="'.$i.'" />
						</label>
					';
				}
				?>
			</div>
		</div>
	</div>

</form>
