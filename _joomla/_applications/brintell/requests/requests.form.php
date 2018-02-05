<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-attachments" data-toggle="tab" href="#<?php echo $APPTAG?>TabAttachments" role="tab" aria-controls="attachments"><?php echo JText::_('TEXT_ATTACHMENTS'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
					<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id">
						<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						<?php
							foreach ($projects as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</div>
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_SUBJECT'); ?></label>
					<input type="text" name="subject" id="<?php echo $APPTAG?>-subject" class="form-control" />
				</div>
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
					<textarea rows="10" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
				</div>
				<div class="form-group mb-1">
					<label><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label><br />
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="base-icon-clock text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_STATUS_0'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-0" value="0" class="auto-tab" data-target="<?php echo $APPTAG?>StatusDesc-group" data-target-display="true" />
						</label>
						<label class="base-icon-off text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_STATUS_1'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>StatusDesc-group" data-target-display="false" data-target-value="q" />
						</label>
						<label class="base-icon-pause text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('TEXT_STATUS_2'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-2" value="2" class="auto-tab" data-target="<?php echo $APPTAG?>StatusDesc-group" data-target-display="true" />
						</label>
						<label class="base-icon-ok text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_STATUS_3'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-3" value="3" class="auto-tab" data-target="<?php echo $APPTAG?>StatusDesc-group" data-target-display="false" data-target-value="" />
						</label>
						<label class="base-icon-cancel text-success btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('TEXT_STATUS_4'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-4" value="4" class="auto-tab" data-target="<?php echo $APPTAG?>StatusDesc-group" data-target-display="true" />
						</label>
					</span>
				</div>
				<div id="<?php echo $APPTAG?>StatusDesc-group" class="form-group">
					<label class="label-xs text-muted"><?php echo JText::_('FIELD_LABEL_STATUS_DESC'); ?></label>
					<input type="text" name="status_desc" id="<?php echo $APPTAG?>-status_desc" class="form-control" />
				</div>
			</div>
			<div class="col-lg-4  b-left b-left-dashed">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PRIORITY'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-0" value="0" />
							<?php echo JText::_('TEXT_PRIORITY_0'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-1" value="1" />
							<?php echo JText::_('TEXT_PRIORITY_1'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-2" value="2" />
							<?php echo JText::_('TEXT_PRIORITY_2'); ?>
						</label>
					</span>
				</div>
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DEADLINE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DEADLINE'); ?></label>
					<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="form-control field-date" data-convert="true" />
				</div>
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ESTIMATE_DESC'); ?>"><?php echo JText::_(''); ?></label>
					<div class="input-group">
						<select type="text" name="estimate" id="<?php echo $APPTAG?>-estimate" class="form-control">
							<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
							<?php
								for($i = 1; $i < 100; $i++) {
									echo '<option value="'.$i.'">'.$i.'</option>';
								}
							?>
						</select>
						<span class="input-group-addon"><?php echo JText::_('FIELD_LABEL_ESTIMATE_UNIT')?></span>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_EXECUTED'); ?></label>
							<div class="input-group">
								<input type="text" name="executed" id="<?php echo $APPTAG?>-executed" class="form-control field-integer" />
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					<div class="col-6">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ORDER'); ?></label>
							<input type="number" name="orderer" id="<?php echo $APPTAG?>-orderer" class="form-control field-integer" />
							<input type="hidden" name="corderer" id="<?php echo $APPTAG?>-corderer" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_VISIBILITY'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_PROJECT_DESC'); ?>">
							<input type="radio" name="visibility" id="<?php echo $APPTAG?>-visibility-0" value="0" />
							<?php echo JText::_('TEXT_PROJECT'); ?>
						</label>
						<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_PRIVATE_DESC'); ?>">
							<input type="radio" name="visibility" id="<?php echo $APPTAG?>-visibility-1" value="1" />
							<?php echo JText::_('TEXT_PRIVATE'); ?>
						</label>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabAttachments" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-attachments">
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_FILE'); ?></label>
					<div class="btn-file">
						<span class="btn-group w-100">
							<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
						</span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label>&#160;</label>
					<button type="button" class="base-icon-plus btn btn-block btn-success" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', 'col-md-6 col-lg-3')">
						<?php echo JText::_('TEXT_ADD'); ?>
					</button>
				</div>
			</div>
		</div>
		<hr />
		<div id="<?php echo $APPTAG?>-files-group" class="row"></div>
	</div>
</div>
