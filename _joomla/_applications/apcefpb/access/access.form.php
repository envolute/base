<?php
defined('_JEXEC') or die;

// CLIENTS
$query = '
	SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .'
	WHERE '. $db->quoteName('state') .' = 1
	ORDER BY name
';
$db->setQuery($query);
$clients = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-lg-8">
		<div class="form-group">
			<label class="field-required"><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
			<div class="input-group">
				<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-clientDescGroup">
					<option value="0" data-target-value="" data-target-display="false">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<option value="0" data-target-value="<?php echo JText::_('TEXT_SELECT_DIRECTORS_GUEST'); ?>" data-target-display="true">- <?php echo JText::_('TEXT_SELECT_DIRECTORS_GUEST'); ?></option>
					<option value="0" data-target-value="<?php echo JText::_('TEXT_SELECT_OTHER_STATE'); ?>" data-target-display="true">- <?php echo JText::_('TEXT_SELECT_OTHER_STATE'); ?></option>
					<option value="0" data-target-value="" data-target-display="true">- <?php echo JText::_('TEXT_SELECT_OTHER'); ?></option>
					<?php
						foreach ($clients as $obj) {
							echo '<option value="'.$obj->id.'" data-target-value="" data-target-display="false">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-clientDescGroup" class="form-group" hidden>
			<div class="input-group">
				<span class="input-group-addon">
					<span class="base-icon-info-circled curso-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CLIENT_DESC'); ?>"></span>
				</span>
				<input type="text" name="client_desc" id="<?php echo $APPTAG?>-client_desc" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CLIENT_DESC'); ?>" />
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
			<input type="text" name="accessDate" id="<?php echo $APPTAG?>-accessDate" class="form-control field-date" data-time="true" data-second="false" data-convert="true" />
		</div>
	</div>
</div>
<div id="<?php echo $APPTAG?>-clientInfo" hidden>
	<div class="form-check">
		<label class="form-check-label">
			<input type="checkbox" name="presence" value="1" onchange="<?php echo $APPTAG?>_resetClient('all')" id="<?php echo $APPTAG?>-presence" class="form-check-input auto-tab" data-target="#<?php echo $APPTAG?>-cExamGroup" data-target-display="true" />
			<?php echo JText::_('FIELD_LABEL_PRESENCE')?>
		</label>
	</div>
	<?php if(!$portaria) :?>
		<div id="<?php echo $APPTAG?>-cExamGroup" hidden>
			<div class="form-check form-check-inline">
				<label class="form-check-label">
					<input type="checkbox" name="cExam" value="1" onchange="<?php echo $APPTAG?>_resetClient('exam')" id="<?php echo $APPTAG?>-cExam" class="form-check-input auto-tab" data-target=".<?php echo $APPTAG?>-cExamDescGroup" data-target-display="true" />
					<?php echo JText::_('FIELD_LABEL_EXAM')?>
				</label>
			</div>
			<span class="<?php echo $APPTAG?>-cExamDescGroup" hidden>
				<span class="base-icon-right-big mx-2"></span>
				<div class="form-check form-check-inline">
					<label class="form-check-label text-danger">
						<input type="checkbox" name="cForbidden" value="1" onchange="<?php echo $APPTAG?>_resetClient('forbidden')" id="<?php echo $APPTAG?>-cForbidden" class="form-check-input auto-tab" data-target="#<?php echo $APPTAG?>-cReason" data-target-display="true" />
						<?php echo JText::_('FIELD_LABEL_FORBIDDEN')?>
					</label>
				</div>
				<input type="text" name="cReason" class="form-control form-control-sm" id="<?php echo $APPTAG?>-cReason" placeholder="Motivo" hidden />
			</span>
		</div>
	<?php endif;?>
</div>

<div id="<?php echo $APPTAG?>-dependentGroups"></div>
<hr class="hr-tag b-live" /><span class="badge badge-warning bg-live"><?php echo JText::_('TEXT_GUESTS')?></span>
<div class="row">
	<div class="col-lg-8">
		<div class="form-group">
			<button type="button" class="btn btn-sm btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_guestAdd()"> <?php echo JText::_('TEXT_GUEST_ADD')?></button>
		</div>
	</div>
	<?php if(!$portaria) :?>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="input-group input-group-sm">
					<span class="input-group-addon"><?php echo JText::_('TEXT_TAX')?></span>
					<input type="text" name="tax_price" id="<?php echo $APPTAG?>-tax_price" class="form-control field-price" data-convert="true" />
				</div>
			</div>
		</div>
	<?php endif;?>
</div>
<div id="<?php echo $APPTAG?>-guestGroups"></div>
