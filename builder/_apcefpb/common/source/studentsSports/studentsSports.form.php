<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_students') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$students = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_sports') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$sports = $db->loadObjectList();
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
							<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" value="0" />
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
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_STUDENT'); ?></label>
						<div class="input-group">
							<select name="student_id" id="<?php echo $APPTAG?>-student_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-sport_id">
								<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<?php
									foreach ($students as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-students" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editStudent()"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_SPORT'); ?></label>
						<div class="input-group">
							<select name="sport_id" id="<?php echo $APPTAG?>-sport_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-registry_date">
								<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<?php
									foreach ($sports as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-sports" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="sports_listReload(false)" data-toggle="modal" data-target="#modal-list-sports" data-backdrop="static" data-keyboard="false"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_REGISTRY_DATE'); ?></label>
						<input type="text" name="registry_date" id="<?php echo $APPTAG?>-registry_date" class="form-control field-date" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
						<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="hidden-xs hidden-sm">&#160;</label>
						<div class="btn-group width-full" data-toggle="buttons">
							<label class="btn btn-block btn-warning btn-active-success">
								<span class="base-icon-cancel btn-icon"></span>
								<input type="checkbox" name="coupon_free" id="<?php echo $APPTAG?>-coupon_free" value="1" />
								<?php echo JText::_('FIELD_LABEL_COUPON_FREE'); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PRICE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
						<span class="input-group">
							<span class="input-group-addon">R$</span>
							<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
						</span>
					</div>
				</div>
			</div>
			<hr class="hr-label" />
			<span class="label label-warning">Carteira do Aluno</span>
			<iframe id="<?php echo $APPTAG?>-form-card-iframe" style="width:325px; height:205px; border:1px dashed #ddd"></iframe>
			<button type="button" class="base-icon-print btn btn-lg btn-warning all-space-lg pull-right hidden-print" style="height:80px" onclick="<?php echo $APPTAG?>_setPrintCard('<?php echo $APPTAG?>-form-card-iframe')"> IMPRIMIR</button>
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
