<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-4">
		<div class="form-group">
			<label>LOGO</label>
			<div class="image-file">
				<a href="#" class="image-action">
					<div class="image-file-label">
						<span class="image-file-off base-icon-file-image"><small>200 x 200</small></span>
						<span class="image-file-on text-sm base-icon-ok" hidden></span>
						<span class="image-file-edit base-icon-pencil" hidden></span>
					</div>
				</a>
				<span class="btn-group mt-2"></span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
</div>
