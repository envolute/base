<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_students') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$students = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_sports') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$sports = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-sm-7">
		<div class="form-group field-required">
			<label class="label-sm"> <?php echo JText::_('FIELD_LABEL_STUDENT'); ?></label>
			<div class="input-group">
				<select name="student_id" id="<?php echo $APPTAG?>-student_id" class="form-control field-id">
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
		<div class="form-group field-required">
			<label class="label-sm"> <?php echo JText::_('FIELD_LABEL_SPORT'); ?></label>
			<div class="input-group">
				<select name="sport_id" id="<?php echo $APPTAG?>-sport_id" class="form-control field-id">
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
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<label class="label-sm"> <?php echo JText::_('FIELD_LABEL_REGISTRY_DATE'); ?></label>
					<input type="text" name="registry_date" id="<?php echo $APPTAG?>-registry_date" class="form-control field-date" data-convert="true" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="label-sm hidden-xs hidden-sm">&#160;</label>
					<div class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="label-sm btn btn-block btn-warning btn-active-success">
							<span class="base-icon-cancel btn-icon"></span>
							<input type="checkbox" name="coupon_free" id="<?php echo $APPTAG?>-coupon_free" value="1" />
							<?php echo JText::_('FIELD_LABEL_COUPON_FREE'); ?>
						</label>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PRICE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
					<span class="input-group">
						<span class="input-group-addon">R$</span>
						<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="label-sm"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
			<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
		</div>
	</div>
	<div class="col-sm-5 pl-0">
		<label>Carteira do Aluno</label>
		<iframe id="<?php echo $APPTAG?>-form-card-iframe" style="width:325px; height:205px; border:1px dashed #ddd"></iframe>
		<button type="button" class="base-icon-print btn block btn-warning hidden-print" onclick="<?php echo $APPTAG?>_setPrintCard('<?php echo $APPTAG?>-form-card-iframe')"> IMPRIMIR</button>
	</div>
</div>
