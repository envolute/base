<?php
// SET FILTER
// Submit o filtro no evento 'onchange'
?>
formFilter.find('.set-filter').change(function() {
  setTimeout(function() { formFilter.find('#<?php echo $APPTAG.'-submit-filter'?>').click() }, 100);
});
