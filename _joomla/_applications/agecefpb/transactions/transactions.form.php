<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();

// PROVIDERS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_providers') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$providers = $db->loadObjectList();

// INVOICES
$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		IF('. $db->quoteName('T1.group_id') .' = 1, "Contribuintes", "Associados Caixa") grp,
		'. $db->quoteName('T1.due_date') .'
	FROM
		'. $db->quoteName('#__'.$cfg['project'].'_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.due_date DESC, T1.group_id ASC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();

// FORM
?>
<div class="row justify-content-center text-center">
	<div class="col-sm-6 col-lg-4">
		<div class="form-group">
			<span class="btn-group btn-group-lg btn-group-justified" data-toggle="buttons">
				<label class="btn btn-default btn-active-success">
					<input type="radio" name="fixed" id="<?php echo $APPTAG?>-fixed-0" value="0" onchange="<?php echo $APPTAG?>_setFixed(0)" />
					<?php echo JText::_('TEXT_SEPARATE'); ?>
				</label>
				<label class="btn btn-default btn-active-warning">
					<input type="radio" name="fixed" id="<?php echo $APPTAG?>-fixed-1" value="1" onchange="<?php echo $APPTAG?>_setFixed(1)" />
					<?php echo JText::_('TEXT_RECURRENT'); ?>
				</label>
			</span>
		</div>
	</div>
</div>
<hr class="mt-0" />
<div class="row">
	<div class="col-lg-8">
		<div class="row">
			<div class="col-md-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_PROVIDER'); ?></label>
					<div class="input-group">
						<select name="provider_id" id="<?php echo $APPTAG?>-provider_id" class="form-control field-id">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								foreach ($providers as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-providers" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="providers_listReload(false)" data-toggle="modal" data-target="#modal-list-providers" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_IS_CARD_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_IS_CARD'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="isCard" id="<?php echo $APPTAG?>-isCard-0" value="0" onchange="<?php echo $APPTAG?>_setCard(false)" />
							<?php echo JText::_('TEXT_NO'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="isCard" id="<?php echo $APPTAG?>-isCard-1" value="1" onchange="<?php echo $APPTAG?>_setCard(true)" />
							<?php echo JText::_('TEXT_YES'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
					<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id">
						<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
						<?php
							foreach ($clients as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_BUYER_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_BUYER'); ?></label>
					<select name="dependent_id" id="<?php echo $APPTAG?>-dependent_id" class="form-control">
						<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
					</select>
				</div>
			</div>
			<div class="col-6 col-md-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_TRANSACTION_DATE'); ?></label>
					<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-autotab="true" data-convert="true" />
				</div>
			</div>
			<div class="col-6 col-md-4">
				<div class="form-group field-required">
					<label class="d-block">
						<?php echo JText::_('FIELD_LABEL_PRICE'); ?>
						<small class="base-icon-info-circled text-live float-right mt-1 cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PRICE_INSTALLMENT_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_INSTALLMENT'); ?></small>
					</label>
					<span class="input-group">
						<span class="input-group-addon">R$</span>
						<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
					</span>
					<input type="hidden" name="cardLimit" id="<?php echo $APPTAG?>-cardLimit" />
				</div>
			</div>
			<div class="col-6 col-md-4 <?php echo $APPTAG?>-no-fixed <?php echo $APPTAG?>-no-edit">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_INSTALLMENTS'); ?></label>
					<select name="total" id="<?php echo $APPTAG?>-total" class="form-control"></select>
				</div>
				<input type="hidden" name="installment" id="<?php echo $APPTAG?>-installment" />
			</div>
			<div class="col-6 col-md-4">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_DOC_NUMBER'); ?></label>
					<input type="text" name="doc_number" id="<?php echo $APPTAG?>-doc_number" class="form-control" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_BUYER_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" />
		</div>
	</div>
	<div class="col-lg-4 b-left">
		<div class="form-group <?php echo $APPTAG?>-no-fixed">
			<label><?php echo JText::_('FIELD_LABEL_INVOICE'); ?></label>
			<select name="invoice_id" id="<?php echo $APPTAG?>-invoice_id" class="form-control">
				<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
				<?php
					foreach ($invoices as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::getMonthName($obj->month).' de '.$obj->year.' - '.baseHelper::nameFormat($obj->grp).'</option>';
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<hr class="hr-tag" />
			<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
			<textarea type="text" name="note" id="<?php echo $APPTAG?>-note" rows="15" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"></textarea>
		</div>
	</div>
</div>
