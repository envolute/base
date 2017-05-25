<?php
require_once("WSBilling.php");
// Inicia a classe WSBilling
$WSBilling = new WSBilling();

// Cria o cabeçalho SOAP
$xmlObj = $WSBilling->add_node("","soap-env:Envelope");
$WSBilling->add_attributes($xmlObj, array("xmlns:soap-env" => "http://schemas.xmlsoap.org/soap/envelope/") );
$xmlObj = $WSBilling->add_node($xmlObj,"soap-env:Body");

// Cria  o elemento m:F2bCobranca
$xmlObjF2bCobranca = $WSBilling->add_node($xmlObj,"m:F2bCobranca");
$WSBilling->add_attributes($xmlObjF2bCobranca, array("xmlns:m" => "http://www.f2b.com.br/soap/wsbilling.xsd") );

// Cria o elemento mensagem
$xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"mensagem");
$WSBilling->add_attributes($xmlObj, array("data" => date("Y-m-d"), 
                                          "numero" => date("His"),
                                          "tipo_ws" => "WebService"));

// Cria o elemento sacador
$xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"sacador");
$WSBilling->add_attributes($xmlObj, array("conta" => "9023010001230123"));
$WSBilling->add_content($xmlObj,"José da Silva");

// Cria o elemento cobranca
$xmlObjCobranca = $WSBilling->add_node($xmlObjF2bCobranca,"cobranca");
$WSBilling->add_attributes($xmlObjCobranca, array("valor" => "10.00", 
                                                  "tipo_cobranca" => "",
                                                  "num_document" => "",
                                                  "cod_banco" => ""));
// Se tipo_taxa = 0 a taxa será cobrada em reais (R$), se tipo_taxa = 1 a taxa será cobrada em porcentagem (%)

// Tipo de cobrança:
// B - Boleto; C - Cartão de crédito; D - Cartão de débito; T - Transferência On-line
// Caso queira permitir cobrança por mais de um tipo, enviar as letras juntas. Ex.: "BCD" (Aceitar Boleto, Crédito e Débito)

// num_document:
// serve para enviar à F2b um número de controle próprio, facilitando a busca na administração

// Cria os elementos demonstrativos (Até 10 linhas com 80 caracteres cada)
$xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
$WSBilling->add_content($xmlObj,"Cobrança F2b");
$xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
$WSBilling->add_content($xmlObj,"Pague em qualquer banco");

// Cria o elemento desconto
$xmlObj = $WSBilling->add_node($xmlObjCobranca,"desconto");
$WSBilling->add_attributes($xmlObj, array("valor" => "2.0", "tipo_desconto" => "0", 
                                          "antecedencia" => "5"));
// Cria o elemento multa
$xmlObj = $WSBilling->add_node($xmlObjCobranca,"multa");
$WSBilling->add_attributes($xmlObj, array("valor" => "1.0",  "tipo_multa" => "0", 
                                          "valor_dia" => "0.10", "tipo_multa_dia" => "0", 
                                          "atraso" => "20"));

//Cria o elemento agendamento
$xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"agendamento");
$WSBilling->add_attributes($xmlObj, array("vencimento" => date('Y-m-d',time()+604800), 
//  Descomentar os atributos abaixo caso queria realizar cobranças com Agendamento //////
//                                          "ultimo_dia" => "n",
//                                          "antecedencia" => 10,
//                                          "periodicidade" => "1",
//                                          "periodos" => "12",
//  Descomentar os atributos abaixo caso queria realizar cobranças como Carnê //////
//                                          "carne" => "s",
//                                          "periodos" => "6",
                                          "sem_vencimento" => "n"));
$WSBilling->add_content($xmlObj,"Pagamento a vista");

// Cria o elemento sacado
$xmlObjSacado = $WSBilling->add_node($xmlObjF2bCobranca,"sacado");
$WSBilling->add_attributes($xmlObjSacado, array("grupo" => "web service", 
                                                "codigo" => "000001", 
                                                "envio" => "e"));
// Cria o elemento nome
$xmlObj = $WSBilling->add_node($xmlObjSacado,"nome");
$WSBilling->add_content($xmlObj,"José Oliveira");
// Cria o elemento email
$xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
$WSBilling->add_content($xmlObj,"joseoliveira@f2b.com.br");
$xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
$WSBilling->add_content($xmlObj,"joseoliveira@f2b.locaweb.com.br");
// Cria o elemento endereco
$xmlObj = $WSBilling->add_node($xmlObjSacado,"endereco");
$WSBilling->add_attributes($xmlObj, array("logradouro" => "Rua das Pedras",
                                          "numero" => "455",
                                          "complemento" => "sala 23",
                                          "bairro" => "Itaim Bibi",
                                          "cidade" => "São Paulo",
                                          "estado" => "SP",
                                          "cep" => "04536000"));
// Cria o elemento telefone
$xmlObj = $WSBilling->add_node($xmlObjSacado,"telefone");
$WSBilling->add_attributes($xmlObj, array("ddd" => "11",
                                          "numero" => "35551234"));

// Cria o elemento telefone comercial
$xmlObj = $WSBilling->add_node($xmlObjSacado,"telefone_com");
$WSBilling->add_attributes($xmlObj, array("ddd_com" => "22",
                                          "numero_com" => "22222222"));

// Cria o elemento telefone celular
$xmlObj = $WSBilling->add_node($xmlObjSacado,"telefone_cel");
$WSBilling->add_attributes($xmlObj, array("ddd_cel" => "33",
                                          "numero_cel" => "33333333"));

// Cria o elemento cpf
$xmlObj = $WSBilling->add_node($xmlObjSacado,"cpf");
$WSBilling->add_content($xmlObj,"12345678909");

// **** É possível registrar a mesma cobrança para vários sacados ao mesmo tempo ****
/*
// Cria um novo elemento sacado
$xmlObjSacado = $WSBilling->add_node($xmlObjF2bCobranca,"sacado");
// Cria o elemento nome
$xmlObj = $WSBilling->add_node($xmlObjSacado,"nome");
$WSBilling->add_content($xmlObj,"Maria Oliveira");
// Cria o elemento email
$xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
$WSBilling->add_content($xmlObj,"mariaoliveira@f2b.com.br");

// Cria um novo elemento sacado
$xmlObjSacado = $WSBilling->add_node($xmlObjF2bCobranca,"sacado");
// Cria o elemento nome
$xmlObj = $WSBilling->add_node($xmlObjSacado,"nome");
$WSBilling->add_content($xmlObj,"Pedro Oliveira");
// Cria o elemento email
$xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
$WSBilling->add_content($xmlObj,"pedrooliveira@f2b.com.br");
*/

// envia dados
$WSBilling->send($WSBilling->getXML());
$resposta = $WSBilling->resposta;
//$resposta = implode("",file("WSBillingResposta.xml"));
if(strlen($resposta) > 0){
	// Reinicia a classe WSBlling, agora com uma string XML
	$WSBilling = new WSBilling($resposta);

	// LOG 
	$log = $WSBilling->pegaLog();
	echo "<html><head><title>WSBilling</title></head><body>";
	if($log["texto"] == "OK"){

        // **** Para abrir a cobrança em uma nova janela ****
		$cobranca = $WSBilling->pegaCobranca();
        $urlBoleto = $cobranca[0]["url"];
        $_SESSION['url_cobranca'] = $urlBoleto;

		echo "<script>\r\n";
		echo "var abrir = window.open ('" . $urlBoleto . "','jan','toolbar=no,location=no,menubar=no,resizable=no,scrollbars=yes,width=600,height=500')\r\n";

		echo "window.location.href= 'agradecimento.php'";
		echo "</script>\r\n";

        // **** Recebendo todos os dados para tratamento do retorno conforme necessidade do cliente ****
        /*
		// AGENDAMENTO
		$agendamento = $WSBilling->pegaAgendamento();
		// COBRANCAS
		$cobranca = $WSBilling->pegaCobranca();
		// SACADOS 
		$sacado = $WSBilling->pegaSacado();
		// CARNÊ 
		$carne = $WSBilling->pegaCarne();

		echo "<table border=1><tr><td colspan='2' bgcolor='gray'><b>Log</b></td></tr>";
		foreach($log as $key => $value){
			echo '<tr><td>$log["'.$key.'"]</td><td>'.$value.'</td></tr>';
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Agendamento</b></td></tr>";
		foreach($agendamento as $key => $value){
			echo '<tr><td>$agendamento["'.$key.'"]</td><td>'.$value.'</td></tr>';
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Cobranca</b></td></tr>";
		foreach($cobranca as $key => $value){
			foreach($cobranca[$key] as $key2 => $value2){
				echo '<tr><td>$cobranca['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Sacado</b></td></tr>";
		foreach($sacado as $key => $value){
			foreach($sacado[$key] as $key2 => $value2){
				echo '<tr><td>$sacado['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}
		echo "<tr><td colspan='2' bgcolor='gray'><b>Carnê</b></td></tr>";
		foreach($carne as $key => $value){
			foreach($carne[$key] as $key2 => $value2){
				echo '<tr><td>$carne['.$key.']["'.$key2.'"]</td><td>'.$value2.'</td></tr>';
			}
		}
		echo "</table>";
       */
       
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
