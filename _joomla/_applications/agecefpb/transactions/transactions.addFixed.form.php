<?php
defined('_JEXEC') or die;
?>

<div class="modal-header">
	<h5 class="modal-title"><?php echo JText::_('FORM_INVOICED_TITLE'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<fieldset id="form-<?php echo $APPTAG?>-addFixed" method="post">
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group">
					<select name="groupID" id="<?php echo $APPTAG?>-groupID" class="form-control">
						<option value="">- <?php echo JText::_('TEXT_INVOICE_GROUP')?> -</option>
						<option value="0"><?php echo JText::_('TEXT_CLIENT_EFFECTIVE')?></option>
						<option value="1"><?php echo JText::_('TEXT_CLIENT_CONTRIBUTOR')?></option>
					</select>
				</div>
			</div>
			<div class="col-sm-4 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-add-fixed" id="btn-<?php echo $APPTAG?>-add-fixed" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_addFixed()">
						<span class="base-icon-plus btn-icon"></span>
						<?php echo JText::_('TEXT_ADD_FIXED'); ?>
					</button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
