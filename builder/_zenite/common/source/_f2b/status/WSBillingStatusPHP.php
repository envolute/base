<?php
require_once("WSBillingStatus.php");
// Inicia a classe WSBillingStatus
$WSBillingStatus = new WSBillingStatus();

// Cria o cabeçalho SOAP
$xmlObj = $WSBillingStatus->add_node("","soap-env:Envelope");
$WSBillingStatus->add_attributes($xmlObj, array("xmlns:soap-env" => "http://schemas.xmlsoap.org/soap/envelope/") );
$xmlObj = $WSBillingStatus->add_node($xmlObj,"soap-env:Body");

// Cria  o elemento m:F2bCobranca
$xmlObjF2bCobranca = $WSBillingStatus->add_node($xmlObj,"m:F2bSituacaoCobranca");
$WSBillingStatus->add_attributes($xmlObjF2bCobranca, array("xmlns:m" => "http://www.f2b.com.br/soap/wsbillingstatus.xsd") );

// Cria o elemento mensagem
$xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"mensagem");
$WSBillingStatus->add_attributes($xmlObj, array("data" => date("Y-m-d"),
                                          "numero" => date("His")));

// Cria o elemento cliente
$xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cliente");
$WSBillingStatus->add_attributes($xmlObj, array("conta" => "9023010001230123",
                                          "senha" => "senha123"));

// Cria o elemento cobranca
$xmlObjCobranca = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cobranca");

// ********************** situação das cobranças ************************************
$WSBillingStatus->add_attributes($xmlObjCobranca, array(
// Deve ser enviado
// ********************** Intervalos de cobranças ************************************
	"numero" => "85423", "numero_final" => "85430",
// ***********************************************************************************

// ou ---------------

// ********************** Intervalo (Data de registro) *******************************
//	"registro" => "2004-10-07", "registro_final" => "2004-10-17",
// ***********************************************************************************

// ou ---------------

// ********************** Intervalo (Data de vencimento) *****************************
//	"vencimento" => "2004-10-30", "vencimento_final" => "2004-11-30",
// ***********************************************************************************

// ou ---------------

// ********************** Intervalo (Data de processamento) **************************
//	"processamento" => "2004-10-11", "processamento_final" => "2004-10-21",
// ***********************************************************************************

// ou ---------------

// ********************** Intervalo (Data de crédito) ********************************
//	"credito" => "2004-10-11", "credito_final" => "2004-10-21",
// ***********************************************************************************

// e/ou -------------
//	"cod_sacado" => "XYZ1234",
// e/ou -------------
//	"cod_grupo" => "GrupoTeste",
// e/ou -------------
//	"tipo_pagamento" => "B",
		//"B" - Boleto; "C" - Cartão de Crédito, "D" - Cartão de Débito, "F" - Entre contas F2b, 
		//"M" - Registrado pela F2b, "S" - Registrado pelo sacador, "T" - Transferência on-line.
// e/ou -------------
//	"numero_documento" => "123456",
// e/ou -------------
//	"situacao" => "0"
		//0 = todas; 1=somente registradas;2=somente pagas;3 = somente vencidas
));
// envia dados
$WSBillingStatus->send($WSBillingStatus->getXML());
$resposta = $WSBillingStatus->resposta;
if(strlen($resposta) > 0){
	// Reinicia a classe WSBillingStatus, agora com uma string XML
	$WSBillingStatus = new WSBillingStatus($resposta);

	// LOG 
	$log = $WSBillingStatus->pegaLog();
	echo "<html><head><title>WSBillingStatus</title></head><body>";
	if($log["texto"] == "OK"){
		// TOTAL
		$total = $WSBillingStatus->pegaTotal();
		// CLIENTE
		$cliente = $WSBillingStatus->pegaCliente();
		// COBRANCAS
		$cobranca = $WSBillingStatus->pegaCobranca();

		echo "<table border=1><tr><td colspan='2' bgcolor='gray'><b>Log</b></td></tr>";
		foreach($log as $key => $value){
			echo '<tr><td>$log["'.$key.'"]</td><td>'.$value.'</td></tr>';
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Cliente</b></td></tr>";
		foreach($cliente as $key => $value){
			foreach($cliente[$key] as $key2 => $value2){
				echo '<tr><td>$cliente['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Total</b></td></tr>";
		foreach($total as $key => $value){
			foreach($total[$key] as $key2 => $value2){
				echo '<tr><td>$total['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Cobranças</b></td></tr>";
		foreach($cobranca as $key => $value){
			foreach($cobranca[$key] as $key2 => $value2){
				echo '<tr><td>$cobranca['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}

		echo "</table>";
	} else {
		echo "<table border=1><tr><td colspan='2' bgcolor='gray'><b>Log</b></td></tr>";
		foreach($log as $key => $value){
			echo '<tr><td>$log["'.$key.'"]</td><td><font color="red">'.$value.'</font></td></tr>';
		}
		echo "</table>";
	}
	echo "</body></html>";
} else {
	echo '<font color="red">Sem resposta</font>';
}
?>
