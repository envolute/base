<?php
// RESET FILES
// Ações default no reset dos campos 'file'
?>
var s = (typeof single !== "null" && typeof single !== "undefined") ? single : false;
<?php
// remove dinamic files
if($cfg['dinamicFiles']) :
  echo '
  if(!s) {
    '.$APPTAG.'IndexFile = '.$APPTAG.'IndexFileInit;
    filesGroup.empty();
  }
  ';
endif;
?>
inputFiles.val('').prev('.btn-group').each(function() {
  var el = jQuery(this);
  // Reset selected button
  el.removeClass('has-error');
  el.find('.file-action').removeClass('active');
  // Remove file info/action buttons
  el.find('a').remove();
});
