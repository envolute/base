<?php
defined('_JEXEC') or die;

// invoices -> select
$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.due_date') .',
		IF(`T1`.`custom_desc` <> "", `T1`.`custom_desc`, `T1`.`description`) invoice_desc
	FROM
		'. $db->quoteName($cfg['mainTable'].'_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.due_date DESC, T1.description ASC, T1.custom_desc ASC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();
?>

<div class="modal-header">
	<h5 class="modal-title"><?php echo JText::_('TEXT_ADD_FIXED'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<fieldset id="form-<?php echo $APPTAG?>-addSelectedFixed" method="post">
		<label class="label-sm"><?php echo JText::_('TEXT_IMPORT_TO')?>:</label>
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<select name="invID" id="<?php echo $APPTAG?>-invID" class="form-control">
						<option value="0">- <?php echo JText::_('TEXT_NOT_INVOICED')?> -</option>
						<?php
						foreach ($invoices as $obj) {
							$desc = ' - '.baseHelper::nameFormat($obj->invoice_desc, 20);
							echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date).$desc.'</option>';
						}
						?>
  					</select>
				</div>
			</div>
			<div class="col-sm-4 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-addSelected-fixed" id="btn-<?php echo $APPTAG?>-addSelected-fixed" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_addSelectedFixed()">
						<span class="base-icon-plus btn-icon"></span>
						<?php echo JText::_('TEXT_IMPORT'); ?>
					</button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
