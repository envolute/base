<?php
// ERROR STATUS
// Executa quando houver um erro na requisição ajax
?>
// Notificação de erro
if(error) $.baseNotify({ msg: error, type: "danger" });
console.log(xhr);
console.log(status);
console.log(error);
