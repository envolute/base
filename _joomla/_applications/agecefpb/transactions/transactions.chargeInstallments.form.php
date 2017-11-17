<?php
defined('_JEXEC') or die;

// usergroups -> select
$group = '';
$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('parent_id') .' = 10 ORDER BY id';
$db->setQuery($query);
$userGrps = $db->loadObjectList();
?>

<div class="modal-header">
	<h5 class="modal-title"><?php echo JText::_('TEXT_CHARGE_INSTALLMENTS_DESC'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<fieldset id="form-<?php echo $APPTAG?>-chargeInstallments" method="post">
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<label class="font-weight-bold field-required"><?php echo JText::_('TEXT_SELECT_GROUP'); ?></label>
					<?php
					foreach ($userGrps as $obj) {
						echo '
							<div class="form-check">
								<label class="form-check-label">
									<input name="'.$APPTAG.'GroupID" class="form-check-input" type="checkbox" value="'.$obj->id.'" /> '.baseHelper::nameFormat($obj->title).'
								</label>
							</div>
						';
					}
					?>
				</div>
			</div>
			<div class="col-sm-4 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-charge-installments" id="btn-<?php echo $APPTAG?>-charge-installments" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_chargeInstallments()">
						<span class="base-icon-plus btn-icon"></span>
						<?php echo JText::_('TEXT_IMPORT'); ?>
					</button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
