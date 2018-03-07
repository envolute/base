<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();

// ISSUES
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_issues') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY id DESC';
$db->setQuery($query);
$issues = $db->loadObjectList();

// TAGS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_tags') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$tags = $db->loadObjectList();

// STAFF
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('type') .' IN (0, 1) AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$staff = $db->loadObjectList();
// current user
$staffList	= '';
foreach ($staff as $obj) {
	$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;
	$staff = ($obj->type == 1) ? '*' : '';
	$me = ($obj->user_id == $user->id) ? ' ('.JText::_('TEXT_TO_ME').')' : '';
	$staffList .= '<option value="'.$obj->user_id.'">'.$staff.baseHelper::nameFormat($name).$me.'</option>';
}

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-extra" data-toggle="tab" href="#<?php echo $APPTAG?>TabExtra" role="tab" aria-controls="extra"> <?php echo JText::_('TEXT_EXTRA'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-lg-8">
				<div class="row">
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
							<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id">
								<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
									foreach ($projects as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" />
									<?php echo JText::_('TEXT_TYPE_0'); ?>
								</label>
								<label class="btn btn-default btn-active-danger">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" />
									<?php echo JText::_('TEXT_TYPE_1'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TAGS'); ?></label>
							<div class="input-group">
								<select name="tags[]" id="<?php echo $APPTAG?>-tags" class="form-control" multiple>
									<?php
										foreach ($tags as $obj) {
											echo '<option value="'.$obj->name.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<?php if($hasAdmin) :?>
									<span class="input-group-btn">
										<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Tags" data-backdrop="static" data-keyboard="false"></button>
									</span>
								<?php endif;?>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PRIORITY'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_PRIORITY_0'); ?>">
									<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-0" value="0" />
									<small class="base-icon-attention icon-default"></small>
								</label>
								<label class="btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_PRIORITY_1'); ?>">
									<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-1" value="1" />
									<small class="base-icon-attention icon-default"></small><small class="base-icon-attention icon-default"></small>
								</label>
								<label class="btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('TEXT_PRIORITY_2'); ?>">
									<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-2" value="2" />
									<small class="base-icon-attention icon-default"></small><small class="base-icon-attention icon-default"></small><small class="base-icon-attention icon-default"></small>
								</label>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_SUBJECT'); ?></label>
					<input type="text" name="subject" id="<?php echo $APPTAG?>-subject" class="form-control" />
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
					<textarea rows="12" name="description" id="<?php echo $APPTAG?>-description" class="form-control" style="height:102px;"></textarea>
				</div>
				<div class="form-group">
					<hr class="hr-tag" />
					<span class="badge badge-primary base-icon-attach"> <?php echo JText::_('TEXT_ATTACHMENTS'); ?></span>
					<button type="button" class="base-icon-plus btn btn-success float-right hasTooltip" title="<?php echo JText::_('TEXT_ADD'); ?>" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', '')"></button>
					<div class="btn-file">
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
						</span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-files-group"></div>
			</div>
			<div class="col-lg-4 b-left b-left-dashed">
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ISSUES_IDS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ISSUES_IDS'); ?></label>
					<select name="issues[]" id="<?php echo $APPTAG?>-issues" class="form-control" multiple>
						<?php
							foreach ($issues as $obj) {
								echo '<option value="'.$obj->id.'">'.$obj->id.' - '.baseHelper::nameFormat($obj->subject).'</option>';
							}
						?>
					</select>
					<input type="hidden" name="cissues" id="<?php echo $APPTAG?>-cissues" />
				</div>
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DEADLINE_DESC'); ?>">
						<?php echo JText::_('FIELD_LABEL_DEADLINE'); ?>
						[ <?php echo JText::_('TEXT_TIME_IN_BRAZIL'); ?>: <iframe src="//free.timeanddate.com/clock/i63smlsf/n45/fs13/fcf80/tct/pct/ahl/ftb/ts1" frameborder="0" width="58" height="16" style="margin-bottom:-3px;" allowTransparency="true"></iframe> ]
					</label>
					<div class="form-inline">
						<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="field-date mr-1" data-width="142px" data-time="true" data-seconds="false" data-tab-disable="true" data-convert="true" />
						<select name="timePeriod" id="<?php echo $APPTAG?>-timePeriod">
							<option value="<?php echo JText::_('TEXT_AM'); ?>"><?php echo JText::_('TEXT_AM'); ?></option>
							<option value="<?php echo JText::_('TEXT_PM'); ?>"><?php echo JText::_('TEXT_PM'); ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ASSIGN_TO'); ?></label>
					<div class="input-group">
						<select name="assign_to[]" id="<?php echo $APPTAG?>-assign_to" class="form-control" multiple>
							<?php echo $staffList?>
						</select>
						<input type="hidden" name="cassign_to" id="<?php echo $APPTAG?>-cassign_to" />
						<span class="input-group-btn">
							<button type="button" class="base-icon-sitemap btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ACTIVITY_BOARD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>activityBoard" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_VISIBILITY'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-danger base-icon-lock hasTooltip" title="<?php echo JText::_('TEXT_VISIBILITY_0_DESC'); ?>">
							<input type="radio" name="visibility" id="<?php echo $APPTAG?>-visibility-0" value="0" />
							<?php echo JText::_('TEXT_VISIBILITY_0'); ?>
						</label>
						<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_VISIBILITY_1_DESC'); ?>">
							<input type="radio" name="visibility" id="<?php echo $APPTAG?>-visibility-1" value="1" />
							<?php echo JText::_('TEXT_VISIBILITY_1'); ?>
						</label>
						<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_VISIBILITY_2_DESC'); ?>">
							<input type="radio" name="visibility" id="<?php echo $APPTAG?>-visibility-2" value="2" />
							<?php echo JText::_('TEXT_VISIBILITY_2'); ?>
						</label>
					</span>
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<?php
						for($i = 0; $i < 5; $i++) {
							$icon	= JText::_('TEXT_ICON_STATUS_'.$i);
							$color	= ($i == 1) ? 'warning' : JText::_('TEXT_COLOR_STATUS_'.$i);
							echo '
								<label class="base-icon-'.$icon.' btn btn-outline-'.$color.' btn-active-'.$color.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$i).'">
									<input type="radio" name="status" id="'.$APPTAG.'-status-'.$i.'" value="'.$i.'" />
								</label>
							';
						}
						?>
					</span>
				</div>
				<div class="form-group">
					<div id="<?php echo $APPTAG?>-alert-toDo">
						<hr class="hr-tag" />
						<span class="badge badge-primary base-icon-menu"> <?php echo JText::_('TEXT_TODO_LIST'); ?></span>
						<div class="alert alert-info text-sm p-2"><?php echo JText::_('MSG_TODO_LIST_AFTER_SAVE'); ?></div>
					</div>
					<div id="<?php echo $APPTAG?>-btn-toDo" class="pt-2" hidden>
						<hr class="mt-0" />
						<button type="button" class="btn btn-primary btn-block text-left base-icon-list btn-icon" onclick="<?php echo $APPTAG?>_viewToDo()" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Todo" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_TODO_LIST')?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabExtra" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-extra">
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ESTIMATE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ESTIMATE'); ?> (<?php echo JText::_('FIELD_LABEL_ESTIMATE_UNIT'); ?>)</label>
					<select type="text" name="estimate" id="<?php echo $APPTAG?>-estimate" class="form-control">
						<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
						<?php
							for($i = 1; $i < 100; $i++) {
								echo '<option value="'.$i.'">'.$i.JText::_('FIELD_LABEL_ESTIMATE_UNIT').'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_EXECUTED'); ?></label>
					<div class="input-group">
						<input type="text" name="executed" id="<?php echo $APPTAG?>-executed" class="form-control field-integer" />
						<span class="input-group-addon">%</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
