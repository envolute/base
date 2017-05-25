<?php
// CHANGE STATE
// Seta o valor do campo 'state' no form
?>
active.parent('label.btn').off('click').on('click', function() { if(formId.val() != 0) <?php echo $APPTAG?>_setState(formId.val(), 1) });
inactive.parent('label.btn').off('click').on('click', function() { if(formId.val() != 0) <?php echo $APPTAG?>_setState(formId.val(), 0) });
