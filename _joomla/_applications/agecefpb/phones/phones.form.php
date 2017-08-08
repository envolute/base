<?php
defined('_JEXEC') or die;

// CLIENTS
$query = '
	SELECT *
	FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .'
	WHERE
		'. $db->quoteName('state') .' = 1 AND
		'. $db->quoteName('access') .' = 1
	ORDER BY name
';
$db->setQuery($query);
$clients = $db->loadObjectList();

// PLANS
$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.name') .',
		'. $db->quoteName('T2.name') .' operator
	FROM '. $db->quoteName($cfg['mainTable'].'_plans') .' T1
		LEFT OUTER JOIN '. $db->quoteName($cfg['mainTable'].'_plans_operators') .' T2
		ON T2.id = T1.operator_id
	WHERE '. $db->quoteName('T1.state') .' = 1
	ORDER BY T2.name
';
$db->setQuery($query);
$plans = $db->loadObjectList();

// FORM
?>
<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
	<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-phone_number">
		<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
		<?php
			foreach ($clients as $obj) {
				echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
			}
		?>
	</select>
</div>
<div class="row">
	<div class="col-lg-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PHONE_NUMBER'); ?></label>
			<input type="text" name="phone_number" id="<?php echo $APPTAG?>-phone_number" class="form-control field-phone" />
		</div>
	</div>
	<div class="col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PLAN'); ?></label>
			<div class="input-group">
				<select name="plan_id" id="<?php echo $APPTAG?>-plan_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-client_id">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($plans as $obj) {
							echo '<option value="'.$obj->id.'">[ '.baseHelper::nameFormat($obj->operator).' ] '.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Plans" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Plans_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Plans" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
