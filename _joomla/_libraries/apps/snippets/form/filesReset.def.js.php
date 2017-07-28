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
		jQuery(".'.$APPTAG.'-btnFileGroup").empty();
		jQuery(".'.$APPTAG.'-imgFileGroup").empty();
	}
	';
endif;
?>
inputFiles.val('').each(function() {
	var el = jQuery(this);
	var gr = jQuery(this).prev('.btn-group');
	// Reset selected button
	gr.removeClass('has-error');
	var btn = gr.find('.file-action');
	var img = gr.closest('.image-file').find('.image-action');
	if(btn.length) btnFileDefault(el);
	else if(img.length) fieldImageDefault(el);
	// Remove file info/action buttons
	gr.find('a').remove();
});
