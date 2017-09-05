<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_types') .' ORDER BY name';
$db->setQuery($query);
$types = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__users') .' WHERE block = 0 ORDER BY name';
$db->setQuery($query);
$users = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group field-required">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_TYPE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
			<div class="input-group">
				<select name="type_id" id="<?php echo $APPTAG?>-type_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($types as $obj) {
							echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Types" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Types_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Types" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control" />
		</div>
	</div>
	<div class="col-sm-3">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_FILE'); ?></label><br />
			<span class="btn-group w-full">
				<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
			</span>
			<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
		</div>
	</div>
	<div class="col-sm-3">
		<div class="form-group field-required">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ACCESS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ACCESS'); ?></label>
			<span class="btn-group w-full" data-toggle="buttons">
				<label class="col btn btn-default btn-active-success">
					<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" class="auto-tab" data-target="#<?php echo $APPTAG?>-group-user" data-target-display="true" />
					<?php echo JText::_('FIELD_LABEL_ACCESS_0'); ?>
				</label>
				<label class="col btn btn-default btn-active-success">
					<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-group-user" data-target-display="false" data-target-value="0" />
					<?php echo JText::_('FIELD_LABEL_ACCESS_1'); ?>
				</label>
			</span>
		</div>
	</div>
	<div class="col-sm-6">
		<div id="<?php echo $APPTAG?>-group-user" class="form-group field-required" hidden>
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_USER_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_USER'); ?></label>
			<select name="user_id" id="<?php echo $APPTAG?>-user_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-description">
				<option value="0">- <?php echo JText::_('TEXT_USER_SELECT'); ?> -</option>
				<?php
					foreach ($users as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
					}
				?>
			</select>
		</div>
	</div>
</div>
<div class="form-group">
	<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DESCRIPTION_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
	<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" />
</div>
