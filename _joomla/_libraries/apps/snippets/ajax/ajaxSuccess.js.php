<?php
// SUCCESS STATUS
// Executa quando houver sucesso na requisição ajax
?>
// MENSAGENS

	// Mensagem de sucesso
	$.baseNotify({ msg: res.msg });

	// Mensagem de erro no processamento (envio/exclusão) do arquivo
	if(res.uploadError) $.baseNotify({ msg: res.uploadError, type: "danger"});
