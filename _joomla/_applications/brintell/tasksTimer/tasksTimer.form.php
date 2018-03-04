<?php
defined('_JEXEC') or die;

// Tasks
$taskFilter = (!$hasAdmin) ? 'FIND_IN_SET ('.$user->id.', T1.assign_to) OR T1.created_by = '.$user->id.' AND ' : '';
$query = '
	SELECT T1.*
	FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' T1
	WHERE '.$taskFilter.'T1.state = 1 ORDER BY T1.id DESC
';
$db->setQuery($query);
$tasks = $db->loadObjectList();

// Staff
// 11 => Manager
// 12 => Analyst
// 13 => Developer
// 14 => External
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('type') .' IN (0, 1) AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$users = $db->loadObjectList();

// FORM
?>
<div id="<?php echo $APPTAG?>-task-group" class="form-group field-required">
	<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TASK'); ?></label>
	<div class="input-group">
		<select name="task_id" id="<?php echo $APPTAG?>-task_id" class="form-control field-id">
			<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
			<?php
				foreach ($tasks as $obj) {
					echo '<option value="'.$obj->id.'">'.$obj->id.' - '.baseHelper::nameFormat($obj->subject).'</option>';
				}
			?>
		</select>
		<span class="input-group-btn">
			<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-tasks" data-backdrop="static" data-keyboard="false"></button>
			<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editTask()"></button>
		</span>
	</div>
</div>
<div id="<?php echo $APPTAG?>-task-info" class="pb-2 mb-2 b-bottom"><div class="text-live"></div></div>
<div class="form-group">
	<span class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
		<label class="btn btn-default btn-active-success">
			<input type="radio" name="timeType" id="<?php echo $APPTAG?>-timeType-0" value="0" onchange="<?php echo $APPTAG?>_setTime(0)" />
			<?php echo JText::_('TEXT_CURRENT_TIME'); ?>
		</label>
		<label class="btn btn-default btn-active-success">
			<input type="radio" name="timeType" id="<?php echo $APPTAG?>-timeType-1" value="1" onchange="<?php echo $APPTAG?>_setTime(1)" />
			<?php echo JText::_('TEXT_MANUAL_TIME'); ?>
		</label>
	</span>
</div>
<div class="<?php echo $APPTAG?>-toggle-manual-time collapse">
	<div class="row no-gutters">
		<div class="col-sm-5 col-lg-3">
			<div class="form-group">
				<label class="label-sm"><?php echo JText::_('FIELD_LABEL_START_HOUR'); ?></label>
				<input type="text" name="start_hour" id="<?php echo $APPTAG?>-start_hour" class="form-control field-time" data-second="false" />
			</div>
		</div>
		<div class="col-2 col-lg-1">
			<div class="form-group strong text-center">
				<label class="d-block">&#160;</label>
				<span class="base-icon-right-big"></span>
			</div>
		</div>
		<div class="col-sm-5 col-lg-3">
			<div class="form-group">
				<label class="label-sm"><?php echo JText::_('FIELD_LABEL_END_HOUR'); ?></label>
				<input type="text" name="end_hour" id="<?php echo $APPTAG?>-end_hour" class="form-control field-time" data-second="false" />
			</div>
		</div>
		<div class="col-2">
			<div class="form-group strong text-center">
				<label class="d-block">&#160;</label>
				<?php echo JText::_('TEXT_OR'); ?>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TOTAL_TIME'); ?></label>
				<select name="time" id="<?php echo $APPTAG?>-time" class="form-control">
					<option value="00:00:00">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
					for($i = 0; $i < 24; $i++) {
						$t = ($i < 10) ? '0'.$i : $i;
						if($i > 0) echo '<option value="'.$t.':00:00">'.$t.':00</option>';
						for($j = 1; $j <= 3; $j++) {
							$m = $j * 15;
							echo '<option value="'.$t.':'.$m.':00">'.$t.':'.$m.'</option>';
						}
					}
					?>
				</select>
				<input type="hidden" name="total_time" id="<?php echo $APPTAG?>-total_time" />
			</div>
		</div>
	</div>
</div>
<hr class="mt-0" />
<div class="row">
	<div class="col-lg-4 <?php echo $APPTAG?>-toggle-manual-time collapse">
		<div class="form-group">
			<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('MSG_TASKTIME_DATE')?>"><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
			<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-lg">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TO_ASSIGN'); ?></label>
			<div class="input-group">
				<span class="input-group-addon">
					<span class="base-icon-user"></span>
				</span>
				<select name="user_id" id="<?php echo $APPTAG?>-user_id" class="form-control">
					<option value="0">- <?php echo JText::_('FIELD_LABEL_SELECT_USER'); ?> -</option>
					<?php
						foreach ($users as $obj) {
							$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;
							$staff = ($obj->type == 1) ? '*' : '';
							echo '<option value="'.$obj->user_id.'">'.$staff.baseHelper::nameFormat($name).'</option>';
						}
					?>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="form-group mb-0">
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE'); ?>" />
</div>
