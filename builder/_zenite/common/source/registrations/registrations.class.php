<?php

class registrations {

	// PEGA E ATUALIZA O STATUS ATUAL DA INSCRIÇÃO
	public static function getStatus($regID, $regStatus, $cfg){

		// database connect
		$db = JFactory::getDbo();

    // Inicia a classe WSBillingStatus
    $WSBillingStatus = new WSBillingStatus();
    // Cria o cabe�alho SOAP
    $xmlObj = $WSBillingStatus->add_node("","soap-env:Envelope");
    $WSBillingStatus->add_attributes($xmlObj, array("xmlns:soap-env" => "http://schemas.xmlsoap.org/soap/envelope/") );
    $xmlObj = $WSBillingStatus->add_node($xmlObj,"soap-env:Body");
    // Cria  o elemento m:F2bCobranca
    $xmlObjF2bCobranca = $WSBillingStatus->add_node($xmlObj,"m:F2bSituacaoCobranca");
    $WSBillingStatus->add_attributes($xmlObjF2bCobranca, array("xmlns:m" => "http://www.f2b.com.br/soap/wsbillingstatus.xsd") );
    // Cria o elemento mensagem
    $xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"mensagem");
    $WSBillingStatus->add_attributes($xmlObj, array("data" => date("Y-m-d"), "numero" => $regID));
    // Cria o elemento cliente
    $xmlObj = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cliente");
    $WSBillingStatus->add_attributes($xmlObj, array("conta" => "9023010833690123", "senha" => "170701"));
    // Cria o elemento cobranca
    $xmlObjCobranca = $WSBillingStatus->add_node($xmlObjF2bCobranca,"cobranca");
    // ********************** situa��o das cobran�as ************************************
    $WSBillingStatus->add_attributes($xmlObjCobranca, array("numero_documento" => $regID));
    // Teste: '0014317656', $regID
    // envia dados
    $WSBillingStatus->send($WSBillingStatus->getXML());
    $resposta = $WSBillingStatus->resposta;
    if(strlen($resposta) > 0) :
      // Reinicia a classe WSBillingStatus, agora com uma string XML
      $WSBillingStatus = new WSBillingStatus($resposta);
      // LOG
      $log = $WSBillingStatus->pegaLog();
      if($log["texto"] == "OK") :
        // COBRANCAS
        $cobranca = $WSBillingStatus->pegaCobranca();
				$paid = 0;
				for($i = 0; $i < count($cobranca); $i++) {
	        $status = $cobranca[$i]["situacao"];
	        if($status == 'Paga') :
	          // atualiza o valor
	          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET status = 2 WHERE id = '.$regID;
	          $db->setQuery($query);
	          $db->execute();
						$paid = 1;
	        elseif($status == 'Registrada' && $regStatus == 0) :
	          // atualiza o valor
	          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET status = 1 WHERE id = '.$regID;
	          $db->setQuery($query);
	          $db->execute();
	        endif;
				}
				$status = $paid ? 'Paga' : $status;
			else :
				$status = $log["texto"];
      endif;
    else :
      $status = 'Sem resposta';
    endif;

    return $status;
	}

}
?>
