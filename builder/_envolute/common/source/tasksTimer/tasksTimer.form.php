<?php
defined('_JEXEC') or die;

$query = '
SELECT
	'. $db->quoteName('T1.id') .',
	'. $db->quoteName('T1.title') .',
	'. $db->quoteName('T1.billable') .',
	IF('. $db->quoteName('T1.price') .' = 0.00, 0, 1) priceFixed,
	'. $db->quoteName('T2.price') .' priceHour
FROM
	'. $db->quoteName('#__envolute_tasks') .' T1
	JOIN '. $db->quoteName('#__envolute_services') .' T2
	ON '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.service_id') .'
WHERE T1.type = 0 AND T1.status != 4 AND T1.state = 1 ORDER BY T1.id DESC';
$db->setQuery($query);
$tasks = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__users') .' WHERE block = 0 ORDER BY name';
$db->setQuery($query);
$users = $db->loadObjectList();
?>

<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">
			<?php
				echo JText::_('FORM_TITLE');
				if($cfg['showFormDesc']) :
					echo '<div class="small font-condensed">'.JText::_('FORM_DESCRIPTION').'</div>';
				endif;
			?>
		</h4>
	</div>
	<div class="modal-body">
		<fieldset>
			<div class="row">
				<div id="<?php echo $APPTAG?>-formPaginator" class="col-sm-4 hide">
					<div class="form-group field-required">
						<span class="input-group">
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-prev" class="base-icon-left-open btn btn-sm btn-default" disabled></button>
							</span>
							<input type="text" name="id" id="<?php echo $APPTAG?>-id" class="form-control input-sm" readonly="readonly" />
							<input type="hidden" name="relationId" id="<?php echo $APPTAG?>-relationId" value="<?php echo $_SESSION[$RTAG.'RelId']?>" />
							<input type="hidden" name="<?php echo $APPTAG?>-prev" id="<?php echo $APPTAG?>-prev" />
							<input type="hidden" name="<?php echo $APPTAG?>-next" id="<?php echo $APPTAG?>-next" />
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-next" class="base-icon-right-open btn btn-sm btn-default" disabled></button>
							</span>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-fieldState" class="col-sm-4">
					<div class="form-group">
						<span id="<?php echo $APPTAG?>-state-group" class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-default btn-active-success strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-1" value="1" />
								<?php echo JText::_('TEXT_ACTIVE'); ?>
							</label>
							<label class="btn btn-sm btn-default btn-active-danger strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-0" value="0" /> <?php echo JText::_('TEXT_INACTIVE'); ?>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-restart" class="col-sm-4 hide">
					<div class="form-group">
						<button type="button" id="btn-<?php echo $APPTAG?>-restart" class="base-icon-cw btn btn-sm btn-default btn-block">
							 <?php echo JText::_('TEXT_RESTART'); ?>
						</button>
					</div>
				</div>
			</div>

			<hr class="hr-xs" />

			<div class="row">
				<div class="col-sm-9">
					<div id="<?php echo $APPTAG?>-task-group" class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_TASK'); ?></label>
						<div class="input-group">
							<select name="task_id" id="<?php echo $APPTAG?>-task_id" class="form-control field-id">
								<option value="0" data-price-hour="" data-price-fixed="" data-billable="">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
									foreach ($tasks as $obj) {
										echo '<option value="'.$obj->id.'" data-price-hour="'.$obj->priceHour.'" data-price-fixed="'.$obj->priceFixed.'" data-billable="'.$obj->billable.'">#'.$obj->id.' - '.baseHelper::nameFormat($obj->title).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button id="<?php echo $APPTAG?>-task-add" type="button" class="base-icon-plus btn-add btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-tasks" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editTask()"></button>
							</span>
						</div>
						<input type="hidden" name="task_editId" id="<?php echo $APPTAG?>-task_editId" />
						<input type="hidden" name="price_hour" id="<?php echo $APPTAG?>-price_hour" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_TASKTIME_DATE')?>"><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
						<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_START_HOUR'); ?></label>
						<div class="input-group">
							<select name="start_hour" id="<?php echo $APPTAG?>-start_hour" class="form-control auto-tab" data-target="#<?php echo $APPTAG?>-end_hour">
								<option value="00:00:00">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
								for($i = 0; $i < 24; $i++) {
									$t = ($i < 10) ? '0'.$i : $i;
									$z = ($i == 0) ? '1' : '0';
									echo '<option value="'.$t.':00:0'.$z.'">'.$t.':00</option>';
									for($j = 1; $j <= 3; $j++) {
										$m = $j * 15;
										echo '<option value="'.$t.':'.$m.':00">'.$t.':'.$m.'</option>';
									}
								}
								?>
							</select>
							<span class="input-group-addon">
								<span class="base-icon-right-big"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_END_HOUR'); ?></label>
						<select name="end_hour" id="<?php echo $APPTAG?>-end_hour" class="form-control">
							<option value="00:00:00">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
							for($i = 0; $i < 24; $i++) {
								$t = ($i < 10) ? '0'.$i : $i;
								$z = ($i == 0) ? '1' : '0';
								echo '<option value="'.$t.':00:0'.$z.'">'.$t.':00</option>';
								for($j = 1; $j <= 3; $j++) {
									$m = $j * 15;
									echo '<option value="'.$t.':'.$m.':00">'.$t.':'.$m.'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group strong text-center">
						<label>&#160;</label><br />
						<?php echo JText::_('TEXT_OR'); ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_TIME'); ?></label>
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
			<hr class="hr top-space-sm" />
			<div class="row">
				<div class="col-sm-3">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-warning btn-active-success">
							<span class="base-icon-cancel btn-icon"></span>
							<input type="checkbox" name="billable" id="<?php echo $APPTAG?>-billable" value="1" />
							<?php echo JText::_('FIELD_LABEL_BILLABLE'); ?>
						</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-warning btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CLOSE_TASK_DESC'); ?>">
							<span class="base-icon-cancel btn-icon"></span>
							<input type="checkbox" name="closeTask" id="<?php echo $APPTAG?>-closeTask" value="1" />
							<?php echo JText::_('FIELD_LABEL_CLOSE_TASK'); ?>
						</label>
					</div>
				</div>
				<div class="col-sm-5">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon hasTooltip" title="<?php echo JText::_('FIELD_LABEL_TO_ASSIGN'); ?>">
								<span class="base-icon-user"></span>
							</span>
							<select name="user_id" id="<?php echo $APPTAG?>-user_id" class="form-control auto-tab" data-target="#<?php echo $APPTAG?>-note">
								<option value="0">- <?php echo JText::_('FIELD_LABEL_SELECT_USER'); ?> -</option>
								<?php
									foreach ($users as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE'); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="modal-footer">
		<div class="pull-left bottom-space-xs text-left text-overflow">
			<span class="base-icon-ok-circled2 set-success text-success hide"></span>
			<span class="base-icon-cancel-circled set-error text-danger hide"></span>
			<span class="ajax-loader hide"></span>
		</div>
		<div class="pull-right">
			<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save('reset');"> <?php echo JText::_('TEXT_SAVENEW'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-delete" id="btn-<?php echo $APPTAG?>-delete" class="base-icon-trash btn btn-danger btn-sm hide" onclick="<?php echo $APPTAG?>_del(0, true)"> <?php echo JText::_('TEXT_DELETE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-default btn-sm" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
		</div>
	</div>

</form>
