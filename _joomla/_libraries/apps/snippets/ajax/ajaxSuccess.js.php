<?php
// SUCCESS STATUS
// Executa quando houver sucesso na requisição ajax
?>
// MENSAGENS

  // Mensagem de sucesso
  mainForm.find('.set-success').prop('hidden', false).text(res.msg);

  // Mensagem de erro no processamento (envio/exclusão) do arquivo
  if(res.uploadError) mainForm.find('.set-error').prop('hidden', false).text(res.uploadError);
