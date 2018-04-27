<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-lg-8">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
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
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-bannersGroups" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="bannersGroups_listReload(false)" data-toggle="modal" data-target="#modal-list-bannersGroups" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_URL'); ?></label>
			<input type="text" name="url" id="<?php echo $APPTAG?>-url" class="form-control" />
		</div>
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_START_DATE'); ?></label>
					<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" class="form-control field-date" data-convert="true" />
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_END_DATE'); ?></label>
					<input type="text" name="end_date" id="<?php echo $APPTAG?>-end_date" class="form-control field-date" data-convert="true" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BANNER'); ?></label>
			<div class="image-file">
				<a href="#" class="image-action">
					<div class="image-file-label">
						<span class="image-file-off base-icon-file-image"><small></small></span>
						<span class="image-file-on text-sm base-icon-ok" hidden></span>
						<span class="image-file-edit base-icon-pencil" hidden></span>
					</div>
				</a>
				<span class="btn-group mt-2"></span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
			</div>
		</div>
	</div>
</div>
