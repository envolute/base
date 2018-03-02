<?php
// ERROR STATUS
// Executa quando houver um erro na requisição ajax
?>
// Notificação de erro
if(error) $.baseNotify({ msg: error, type: "danger", alertTime: 60000 });
console.log(xhr);
console.log(status);
console.log(error);
