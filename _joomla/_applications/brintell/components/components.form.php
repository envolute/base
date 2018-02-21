<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-description" data-toggle="tab" href="#<?php echo $APPTAG?>TabDesc" role="tab" aria-controls="description"><?php echo JText::_('FIELD_LABEL_CONFIG_CODE'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group field-required">
					<label class="text-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="text-sm"><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
					<div class="input-group">
						<select name="group_id" id="<?php echo $APPTAG?>-group_id" class="form-control field-id">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								foreach ($groups as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Groups_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="text-sm"><?php echo JText::_('FIELD_LABEL_URL'); ?></label>
					<input type="text" name="url" id="<?php echo $APPTAG?>-url" class="form-control" />
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label class="text-sm"><?php echo JText::_('FIELD_LABEL_DESCRIPTION')?></label>
					<textarea name="description" id="<?php echo $APPTAG?>-description" rows="4" class="form-control"></textarea>
				</div>
			</div>
		</div>
		<hr class="mt-2" />
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<label class="text-sm"><?php echo JText::_('FIELD_LABEL_IMAGE'); ?></label>
					<div class="image-file">
						<a href="#" class="image-action">
							<div class="image-file-label">
								<span class="image-file-off base-icon-file-image"></span>
								<span class="image-file-on text-sm base-icon-ok" hidden></span>
								<span class="image-file-edit base-icon-pencil" hidden></span>
							</div>
						</a>
						<span class="btn-group mt-2"></span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
					</div>
				</div>
			</div>
			<div class="col-sm-8 b-left">
				<label class="label-sm"><?php echo JText::_('FIELD_LABEL_IMAGES_VARIATIONS'); ?></label>
				<div class="text-muted small lh-1 mb-2"><?php echo JText::_('FIELD_LABEL_IMAGES_VARIATIONS_DESC'); ?></div>
				<button type="button" class="base-icon-plus btn btn-xs btn-success" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-images-group', 'image', 'col-sm-6 mb-3')">
					<?php echo JText::_('TEXT_ADD'); ?>
				</button>
				<hr class="my-2" />
				<div id="<?php echo $APPTAG?>-images-group" class="row"></div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabDesc" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-description">
		<div class="form-group">
			<label class="text-sm"><?php echo JText::_('FIELD_LABEL_CONFIG_CODE')?></label>
			<textarea name="config_code" id="<?php echo $APPTAG?>-config_code" rows="4" class="form-control field-html"></textarea>
		</div>
	</div>
</div>
