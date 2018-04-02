<?php
defined('_JEXEC') or die;

// STAFF
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('type') .' IN (0, 1) AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$staff = $db->loadObjectList();
// current user
$staffList	= '';
foreach ($staff as $obj) {
	$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;
	$staff = ($obj->type == 1) ? '*' : '';
	if($obj->user_id == $user->id) {
		$me = ' ('.JText::_('TEXT_TO_ME').')';
	} else {
		$me = '';
	}
	$staffList .= '<option value="'.$obj->user_id.'">'.$staff.baseHelper::nameFormat($name).$me.'</option>';
}

// FORM
?>
<input type="hidden" name="task_id" id="<?php echo $APPTAG?>-task_id" />
<div class="row">
	<div class="col-lg-6">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TITLE'); ?></label>
			<input type="text" name="title" id="<?php echo $APPTAG?>-title" class="form-control"></textarea>
		</div>
	</div>
	<div class="col-sm-4 col-lg-2">
		<div id="<?php echo $APPTAG?>-deadline-group" class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DEADLINE'); ?></label>
			<div class="form-inline">
				<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="field-date" data-tab-disable="true" data-convert="true" />
			</div>
		</div>
	</div>
	<div class="col-sm-8 col-lg-4">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ASSIGN_TO'); ?></label>
			<div class="input-group">
				<select name="assign_to[]" id="<?php echo $APPTAG?>-assign_to" class="form-control" multiple>
					<?php echo $staffList?>
				</select>
				<input type="hidden" name="cassign_to" id="<?php echo $APPTAG?>-cassign_to" />
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-8">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<textarea name="description" id="<?php echo $APPTAG?>-description" class="form-control" rows="20"></textarea>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group">
			<label class="base-icon-attach label-sm"> <?php echo JText::_('FIELD_LABEL_ATTACHMENTS'); ?></label>
			<button type="button" class="base-icon-plus btn btn-sm btn-success float-right hasTooltip" title="<?php echo JText::_('TEXT_ADD'); ?>" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', 'col-12')"></button>
			<div class="btn-file">
				<span class="btn-group">
					<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
				</span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-files-group" class="row"></div>
	</div>
</div>
