<?php
// APP JS DEFAULT VARS
// Variáveis default da app
?>
var mainForm	= jQuery('#form-<?php echo $APPTAG?>');
window.mainForm_<?php echo $APPTAG?> = mainForm;
  // form paginator
  var fPager	= jQuery('#<?php echo $APPTAG?>-formPaginator');
  var fPrev		= jQuery('#<?php echo $APPTAG?>-prev');
  var fNext		= jQuery('#<?php echo $APPTAG?>-next');
  var fRest		= jQuery('#<?php echo $APPTAG?>-restart');
  var btnPrev	= jQuery('#btn-<?php echo $APPTAG?>-prev');
  var btnNext	= jQuery('#btn-<?php echo $APPTAG?>-next');
  var btnRest	= jQuery('#btn-<?php echo $APPTAG?>-restart');
  var btnReset= jQuery('#btn-<?php echo $APPTAG?>-reset');
var popup			= jQuery('#modal-<?php echo $APPTAG?>');
var fReload		= false;
var list			= jQuery('#list-<?php echo $APPTAG?>');
var listPopup	= jQuery('#modal-list-<?php echo $APPTAG?>');
  // lista completa
  var formFilter	= jQuery('#filter-<?php echo $APPTAG?>');
  var formList		= jQuery('#form-list-<?php echo $APPTAG?>');
  var formLimit		= jQuery('#form-limit-<?php echo $APPTAG?>');
  var formOrder		= jQuery('#form-order-<?php echo $APPTAG?>');
  // relacionamento
  window.<?php echo $APPTAG?>oCHL	= 0;
  window.<?php echo $APPTAG?>rNID	= '';
  window.<?php echo $APPTAG?>rID	= 0;

// DEFAULT FIELDS
var formId	 			= jQuery('#<?php echo $APPTAG?>-id');
var displayId	 		= jQuery('#<?php echo $APPTAG?>-displayId');
window.formId_<?php echo $APPTAG?> = formId;
var relationId		= jQuery('#<?php echo $APPTAG?>-relationId');
// state is default
var state					= mainForm.find('input[name=state]:radio');
  var active			= mainForm.find('#<?php echo $APPTAG?>-state-1');
  var inactive		= mainForm.find('#<?php echo $APPTAG?>-state-0');
// se houver upload
<?php if($cfg['hasUpload']) :?>
  var files 			= mainForm.find("input:file");
  <?php if($cfg['dinamicFiles']) :?>
    // valor inicial do index do arquivo... considerando '0' o campo estático
    window.<?php echo $APPTAG?>IndexFile = window.<?php echo $APPTAG?>IndexFileInit = <?php echo $cfg['indexFileInit']?>;
  <?php endif;?>
<?php endif;?>
