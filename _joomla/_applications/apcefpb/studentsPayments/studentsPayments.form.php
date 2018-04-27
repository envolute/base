<?php
defined('_JEXEC') or die;

$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T2.name') .' student,
		'. $db->quoteName('T3.name') .' sport,
		'. $db->quoteName('T1.price') .'
	FROM
		'. $db->quoteName('#__'.$cfg['project'].'_students_sports') .' T1
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_students') .' T2
		ON T2.id = T1.student_id
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_sports') .' T3
		ON T3.id = T1.sport_id
	WHERE T1.state = 1 ORDER BY T2.name ASC
';
$db->setQuery($query);
$students = $db->loadObjectList();

// INVOICES
$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T2.name') .',
		'. $db->quoteName('T1.due_date') .'
	FROM
		'. $db->quoteName('#__'.$cfg['project'].'_students_invoices') .' T1
		JOIN '. $db->quoteName('#__'.$cfg['project'].'_sports') .' T2
		ON T2.id = T1.sport_id
	WHERE T1.state = 1 ORDER BY T1.due_date DESC, T2.name ASC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();

// FORM
?>

<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_INVOICE'); ?></label>
	<div class="input-group">
		<select name="invoice_id" id="<?php echo $APPTAG?>-invoice_id" class="form-control field-id">
			<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
			<?php
				foreach ($invoices as $obj) {
					echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date).' - '.baseHelper::nameFormat($obj->name).'</option>';
				}
			?>
		</select>
		<span class="input-group-btn">
			<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-studentsInvoices" data-backdrop="static" data-keyboard="false"></button>
			<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editInvoice()"></button>
		</span>
	</div>
</div>
<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_REGISTRY'); ?></label>
	<select name="registry_id" id="<?php echo $APPTAG?>-registry_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-price">
		<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
		<?php
			foreach ($students as $obj) {
				echo '<option value="'.$obj->id.'" data-target-value="'.baseHelper::priceFormat($obj->price).'">'.baseHelper::nameFormat($obj->student).' - '.baseHelper::nameFormat($obj->sport).'</option>';
			}
		?>
	</select>
</div>
<div class="row">
	<div class="col-md-4 col-lg-3">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
			<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
		</div>
	</div>
	<div class="col-md">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
			<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
		</div>
	</div>
</div>
