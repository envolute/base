<?php
// FORM PAGINATOR
// Implementa os botões de paginação do formulário
?>
window.<?php echo $APPTAG?>_formPaginator = function(id, prev, next) {
  if(id != 0) {
    fPager.find('.formPaginator-pager').prop('hidden', false);
    btnReset.parent().prop('hidden', false);
    if(prev != 0) {
      btnPrev.prop('disabled', false);
      fPrev.val(prev);
    } else {
      btnPrev.prop('disabled', true);
      fPrev.val('');
    }
    if(next != 0) {
      btnNext.prop('disabled', false);
      fNext.val(next);
    } else {
      btnNext.prop('disabled', true);
      fNext.val('');
    }
  } else {
    fPager.find('.formPaginator-pager').prop('hidden', true);
    btnReset.parent().prop('hidden', true);
    fPrev.val('');
    fNext.val('');
  }
};
btnPrev.click(function() { <?php echo $APPTAG?>_loadEditFields(fPrev.val(), true, true) });
btnNext.click(function() { <?php echo $APPTAG?>_loadEditFields(fNext.val(), true, true) });
btnRest.click(function() { <?php echo $APPTAG?>_loadEditFields(displayId.val(), true, true) });
btnReset.click(function() { <?php echo $APPTAG?>_formReset() });
// Desabilita o 'enter' no display ID
displayId.keyup(function(e){
    if(e.keyCode == 13) return false;
});
