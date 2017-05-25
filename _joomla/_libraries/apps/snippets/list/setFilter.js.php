<?php
// SET FILTER
// Submit o filtro no evento 'onchange'
?>
formFilter.find('.set-filter').change(function() {
  setTimeout(function() { formFilter.submit() }, 100);
});
