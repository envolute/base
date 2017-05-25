<div class="modal-footer">
  <div class="col-3 p-0">
    <h5 class="base-icon-ok-circled2 set-success text-success m-0" hidden></h5>
  </div>
  <div class="col p-0 text-right">
    <div class="btn-group">
      <button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
      <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#" name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" onclick="<?php echo $APPTAG?>_save('reset');"> <?php echo JText::_('TEXT_SAVENEW'); ?></a>
        <a class="dropdown-item" href="#" name="btn-<?php echo $APPTAG?>-save-close" id="btn-<?php echo $APPTAG?>-save-close" onclick="<?php echo $APPTAG?>_save('close');"> <?php echo JText::_('TEXT_SAVECLOSE'); ?></a>
        <a class="dropdown-item text-danger font-weight-bold" href="#" name="btn-<?php echo $APPTAG?>-delete" id="btn-<?php echo $APPTAG?>-delete" hidden onclick="<?php echo $APPTAG?>_del(0, true)"><span class="base-icon-trash"></span> <?php echo JText::_('TEXT_DELETE'); ?></a>
      </div>
    </div>
  </div>
</div>
