<?php
defined('_JEXEC') or die;

// template
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE type = 1 AND state = 1 ORDER BY title';
$db->setQuery($query);
$templates = $db->loadObjectList();

// service
$query = 'SELECT * FROM '. $db->quoteName('#__envolute_services') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$services = $db->loadObjectList();

// projects
$query = 'SELECT * FROM '. $db->quoteName('#__envolute_projects') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();
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

			<!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#tabMain" aria-controls="tabMain" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_MAIN'); ?></a></li>
		    <li role="presentation"><a href="#tabInfo" aria-controls="tabInfo" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_INFO')?></a></li>
		  </ul>
			<!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="tabMain">
					<div class="row">
						<div id="<?php echo $APPTAG?>-type-group" class="col-sm-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_TASK_TYPE'); ?></label>
								<span class="btn-group" data-toggle="buttons">
									<label class="btn btn-default btn-active-success" onclick="<?php echo $APPTAG?>_setTaskType(0)">
										<span class="base-icon-unset"></span>
										<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" />
										<?php echo JText::_('FIELD_LABEL_TASK'); ?>
									</label>
									<label class="btn btn-default btn-active-success hasTooltip" onclick="<?php echo $APPTAG?>_setTaskType(1)" title="<?php echo JText::_('FIELD_LABEL_TEMPLATE_DESC'); ?>">
										<span class="base-icon-unset"></span>
										<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" />
										<?php echo JText::_('FIELD_LABEL_TEMPLATE'); ?>
									</label>
								</span>
								<input type="hidden" name="ctype" id="<?php echo $APPTAG?>-ctype" />
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-template-group" class="col-sm-8">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_TEMPLATE'); ?>s</label>
								<select name="template" id="<?php echo $APPTAG?>-template" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-service" onchange="<?php echo $APPTAG?>_setTaskTemplate(this)">
									<option value="0">- <?php echo JText::_('FIELD_LABEL_SELECT_TEMPLATE'); ?> -</option>
									<?php
										foreach ($templates as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->title).'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-service-group" class="col-sm-6">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_SERVICE'); ?></label>
								<select name="service_id" id="<?php echo $APPTAG?>-service_id" class="form-control">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($services as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<input type="hidden" name="cservice_id" id="<?php echo $APPTAG?>-cservice_id" />
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-order-group" class="col-sm-2 hide">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ORDER_DESC')?>"><?php echo JText::_('FIELD_LABEL_ORDER')?></label>
								<input type="number" name="ordering" id="<?php echo $APPTAG?>-ordering" class="form-control field-number" />
								<input type="hidden" name="cordering" id="<?php echo $APPTAG?>-cordering" />
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-project-group" class="col-sm-6">
							<div class="form-group">
								<label class="field-required"><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
								<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-title">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($projects as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-title-group" class="col-sm-6">
							<div class="form-group">
								<label class="field-required"><?php echo JText::_('FIELD_LABEL_TITLE'); ?></label>
								<input type="text" name="title" id="<?php echo $APPTAG?>-title" class="form-control input-required" />
							</div>
						</div>
						<div class="col-xs-6 col-sm-3">
							<div class="form-group">
								<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_PRICE_FIXED_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE_FIXED'); ?></label>
								<input type="text" name="price" id="<?php echo $APPTAG?>-price" size="6" class="form-control field-price" data-convert="true" />
							</div>
						</div>
						<div class="col-xs-6 col-sm-3">
							<div class="checkbox top-space-lg no-margin-bottom">
								<label>
									<input type="checkbox" name="billable" id="<?php echo $APPTAG?>-billable" value="1" />
									<?php echo JText::_('FIELD_LABEL_BILLABLE'); ?>
								</label>
							</div>
							<div class="checkbox no-margin">
								<label>
									<input type="checkbox" name="priority" id="<?php echo $APPTAG?>-priority" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-description" />
									<?php echo JText::_('FIELD_LABEL_PRIORITY'); ?>
								</label>
							</div>
						</div>
					</div>
					<hr class="hr-xs" />
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_TASK_PERIOD'); ?></label>
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_SAPARATE'); ?>" onclick="<?php echo $APPTAG?>_setTaskPeriod(0)">
										<input type="radio" name="period" id="<?php echo $APPTAG?>-period-0" value="0" />
										<span class="base-icon-down-circled icon-default"></span>
									</label>
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_RECURRENT'); ?>" onclick="<?php echo $APPTAG?>_setTaskPeriod(1)">
										<input type="radio" name="period" id="<?php echo $APPTAG?>-period-1" value="1" />
										<span class="base-icon-arrows-cw icon-default"></span>
									</label>
								</span>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-separate-group" class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_START_DATE'); ?></label>
								<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<span id="<?php echo $APPTAG?>-recurrent-group" class="hide">
							<div class="col-sm-3">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_RECURRENT_TYPE')?></label>
									<select name="recurrent_type" id="<?php echo $APPTAG?>-recurrent_type" class="form-control" onchange="<?php echo $APPTAG?>_setRecurrentType(this)">
										<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
										<option value="1"><?php echo JText::_('FIELD_LABEL_DAYLY')?></option>
										<option value="2"><?php echo JText::_('FIELD_LABEL_WEEKLY')?></option>
										<option value="3"><?php echo JText::_('FIELD_LABEL_MONTHLY')?></option>
										<option value="4"><?php echo JText::_('FIELD_LABEL_YEARLY')?></option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div id="<?php echo $APPTAG?>-weekly-group" class="form-group hide">
									<label><?php echo JText::_('FIELD_LABEL_SELECT_DAYS'); ?></label>
									<select name="weekly[]" id="<?php echo $APPTAG?>-weekly" class="form-control" multiple="multiple">
										<option value="2"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_2'); ?></option>
										<option value="3"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_3'); ?></option>
										<option value="4"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_4'); ?></option>
										<option value="5"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_5'); ?></option>
										<option value="6"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_6'); ?></option>
										<option value="7"><?php echo JText::_('FIELD_LABEL_WEEKLY_DAY_7'); ?></option>
									</select>
								</div>
								<div id="<?php echo $APPTAG?>-monthly-group" class="form-group hide">
									<label><?php echo JText::_('FIELD_LABEL_SELECT_DAYS'); ?></label>
									<select name="monthly[]" id="<?php echo $APPTAG?>-monthly" class="form-control" multiple="multiple">
										<option value="01">01</option>
										<option value="02">02</option>
										<option value="03">03</option>
										<option value="04">04</option>
										<option value="05">05</option>
										<option value="06">06</option>
										<option value="07">07</option>
										<option value="08">08</option>
										<option value="09">09</option>
										<option value="10">10</option>
										<option value="11">11</option>
										<option value="12">12</option>
										<option value="13">13</option>
										<option value="14">14</option>
										<option value="15">15</option>
										<option value="16">16</option>
										<option value="17">17</option>
										<option value="18">18</option>
										<option value="19">19</option>
										<option value="20">20</option>
										<option value="21">21</option>
										<option value="22">22</option>
										<option value="23">23</option>
										<option value="24">24</option>
										<option value="25">25</option>
										<option value="26">26</option>
										<option value="27">27</option>
										<option value="28">28</option>
										<option value="29">29</option>
										<option value="30">30</option>
										<option value="31">31</option>
									</select>
								</div>
								<div id="<?php echo $APPTAG?>-yearly-group" class="form-group hide">
									<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_SELECT_DAYS_YEAR')?>"><?php echo JText::_('FIELD_LABEL_SELECT_DAYS')?></label>
									<input name="yearly" id="<?php echo $APPTAG?>-yearly" class="form-control" />
								</div>
							</div>
						</span>
					</div>
					<hr class="hr-xs" />
					<div class="row">
						<div class="col-sm-9">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label><br />
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="base-icon-clock text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_WAITING'); ?>">
										<input type="radio" name="status" id="<?php echo $APPTAG?>-status-0" value="0" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
									</label>
									<label class="base-icon-off text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_ACTIVE'); ?>">
										<input type="radio" name="status" id="<?php echo $APPTAG?>-status-1" value="1" class="auto-tab" data-target="status_desc-group" data-target-display="false" data-target-value="" />
									</label>
									<label class="base-icon-pause text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_PAUSED'); ?>">
										<input type="radio" name="status" id="<?php echo $APPTAG?>-status-2" value="2" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
									</label>
									<label class="base-icon-ok text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_COMPLETED'); ?>">
										<input type="radio" name="status" id="<?php echo $APPTAG?>-status-3" value="3" class="auto-tab" data-target="status_desc-group" data-target-display="false" data-target-value="" />
									</label>
									<label class="base-icon-cancel text-danger btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_CANCELED'); ?>">
										<input type="radio" name="status" id="<?php echo $APPTAG?>-status-4" value="4" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
									</label>
								</span>
							</div>
							<div id="status_desc-group" class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_STATUS_DESC'); ?></label>
								<input type="text" name="status_desc" id="<?php echo $APPTAG?>-status_desc" class="form-control" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_VISIBLE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_VISIBLE'); ?></label>
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="visible" id="<?php echo $APPTAG?>-visible-1" value="1" />
										<?php echo JText::_('TEXT_YES'); ?>
									</label>
									<label class="btn btn-default btn-active-danger">
										<input type="radio" name="visible" id="<?php echo $APPTAG?>-visible-0" value="0" />
										<?php echo JText::_('TEXT_NO'); ?>
									</label>
								</span>
							</div>
						</div>
					</div>
				</div>
		    <div role="tabpanel" class="tab-pane" id="tabInfo">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ESTIMATE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ESTIMATE'); ?></label>
								<span class="input-group">
									<input type="text" name="estimate" id="<?php echo $APPTAG?>-estimate" class="form-control field-time" />
									<span class="input-group-addon">h:m</span>
								</span>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_DEADLINE'); ?></label>
								<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_HOUR'); ?></label>
								<select name="hour" id="<?php echo $APPTAG?>-hour" class="form-control">
									<option value="">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
									for($i = 0; $i < 24; $i++) {
										$t = ($i < 10) ? '0'.$i : $i;
										echo '<option value="'.$t.':00">'.$t.':00</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_PERCENT')?></label>
								<select name="percent" id="<?php echo $APPTAG?>-percent" class="form-control">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										for($i = 1; $i <= 20; $i++) {
											$j = $i * 5;
											echo '<option value="'.$j.'">'.$j.'%</option>';
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<hr class="hr-sm" />
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
						<textarea rows="8" name="description" id="<?php echo $APPTAG?>-description" class="field-html" data-editor-full="true"></textarea>
					</div>
				</div>
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
