<div class="modal-header">
  <h5 class="modal-title">
    <?php
      echo JText::_('FORM_TITLE');
      if($cfg['showFormDesc']) :
        echo '<div class="small font-condensed">'.JText::_('FORM_DESCRIPTION').'</div>';
      endif;
    ?>
  </h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
