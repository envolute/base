<?php
defined('_JEXEC') or die;
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title"><?php echo JText::_('FORM_PAY_TITLE'); ?></h4>
</div>
<div class="modal-body">
	<form id="form-<?php echo $APPTAG?>-pay" method="post">
    <input type="hidden" name="pay_id" value="0" id="<?php echo $APPTAG?>-pay_id" />
    <div class="form-group">
      <label><?php echo JText::_('FIELD_LABEL_PAY_DATE'); ?></label>
      <div class="input-group">
        <input type="text" name="pay_date" id="<?php echo $APPTAG?>-pay_date" class="form-control field-date width-full" data-convert="true" />
        <span class="input-group-btn">
          <button type="button" name="btn-<?php echo $APPTAG?>-pay-add" id="btn-<?php echo $APPTAG?>-pay-add" class="btn btn-success" onclick="<?php echo $APPTAG?>_pay()">
            <span class="base-icon-dollar"><?php echo JText::_('TEXT_SAVE_PAYMENT'); ?>
          </button>
        </span>
      </div>
    </div>
    <div class="checkbox no-margin-bottom">
      <label>
        <input type="checkbox" name="pay_mail" id="<?php echo $APPTAG?>-pay_mail" value="1" checked="checked" />
        <?php echo JText::_('FIELD_LABEL_PAY_MAIL_CONFIRM'); ?>
      </label>
    </div>
	</form>
</div>
