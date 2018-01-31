<?php
$action	= $cfg['saveTrigger'];
$save_close = '<a class="dropdown-item" href="#" name="btn-'.$APPTAG.'-save-close" id="btn-'.$APPTAG.'-save-close" onclick="'.$APPTAG.'_save(\'close\')"> '.JText::_('TEXT_SAVECLOSE').'</a>';
$btn_delete = '<button name="btn-'.$APPTAG.'-delete" id="btn-'.$APPTAG.'-delete" class="base-icon-trash btn btn-danger" hidden onclick="'.$APPTAG.'_del(0, true)"> '.JText::_('TEXT_DELETE').'</button>';
if($cfg['formBtnAction']) :
	$action	= 'close';
	$save_close = $btn_delete = '';
endif;
?>
<div class="modal-footer text-right">
	<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-sm btn-default mr-auto" data-dismiss="modal"> <?php echo JText::_('TEXT_CLOSE'); ?></button>
	<div class="btn-group">
		<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success" onclick="<?php echo $APPTAG?>_save('<?php echo $action?>')"> <?php echo JText::_('TEXT_SAVE'); ?></button>
		<button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
		<div class="dropdown-menu dropdown-menu-right">
			<a class="dropdown-item" href="#" name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" onclick="<?php echo $APPTAG?>_save('reset')"> <?php echo JText::_('TEXT_SAVENEW'); ?></a>
			<?php echo $save_close?>
		</div>
	</div>
	<?php echo $btn_delete?>
</div>
