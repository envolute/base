<?php
defined('_JEXEC') or die;

$app = JFactory::getApplication('site');

// load Scripts
$doc = JFactory::getDocument();
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

//joomla get request data
$input      = $app->input;

// fields 'Form' requests
$request            = array();
// default
$request['rid']     = $input->get('rid', '', 'string');

$id = 0;
if($request['rid'] > 0) :
  // inscrição
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_registrations') .' WHERE id = '.$request['rid'];
  $db->setQuery($query);
  $reg = $db->loadObject();
  $id = $reg->id;
  // projeto
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE '. $db->quoteName('start_date') .' <= NOW() AND '. $db->quoteName('end_date') .' >= NOW() AND state = 1 AND id = '.$reg->project_id;
  $db->setQuery($query);
  $project = $db->loadObject();
  $type = $reg->projectType_id;
endif;

// verifica o acesso
if($id == 0 || $reg->user_id != $user->id) :
	$app->redirect(JURI::root().'error');
	exit();
endif;

$priceTotal = $price = $coupon = (float)0.00;
if(!empty($project->name)) :
  // dados da modalidade
  $query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T2.name') .' category,
      '. $db->quoteName('T3.name') .' disability,
      '. $db->quoteName('T1.disability_id') .' ,
      '. $db->quoteName('T1.distance') .',
      '. $db->quoteName('T1.distance_unit') .',
      '. $db->quoteName('T1.price') .',
      '. $db->quoteName('T1.limit') .'
    FROM
      '. $db->quoteName('#__zenite_projects_types') .' T1
      JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
      ON T2.id = T1.category_id
      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T3
      ON T3.id = T1.disability_id
    WHERE
    T1.id = '.$type.' AND T1.state = 1
  ';
  $db->setQuery($query);
  $categ = $db->loadObject();

  // dados do usuário
  $query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T1.cpf') .',
      '. $db->quoteName('T1.birthday') .',
      '. $db->quoteName('T1.gender') .',
      '. $db->quoteName('T1.phone_number') .',
      '. $db->quoteName('T1.address') .',
      '. $db->quoteName('T1.address_number') .',
      '. $db->quoteName('T1.address_info') .',
      '. $db->quoteName('T1.zip_code') .',
      '. $db->quoteName('T1.address_district') .',
      '. $db->quoteName('T1.address_city') .',
      '. $db->quoteName('T1.address_uf') .',
      '. $db->quoteName('T1.deficient') .',
      '. $db->quoteName('T2.name') .' deficient_type
    FROM
      '. $db->quoteName('#__zenite_user_info') .' T1
      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T2
      ON T2.id = T1.deficient_type
    WHERE
    T1.user_id = '.$user->id.'
  ';
  $db->setQuery($query);
  $userInfo = $db->loadObject();

  // valor
  $priceTotal = $reg->price - $reg->discount;

  if($priceTotal == 0 || $priceTotal == 0.00) :

    // informa que a fatura/boleto foi acessada
    $query = 'UPDATE '. $db->quoteName('#__zenite_registrations') .' SET '. $db->quoteName('status') .' = 2 WHERE '. $db->quoteName('id') .' = '.$id;
    $db->setQuery($query);
    $db->execute();
    // redireciona para a página de pagamento
    $app->redirect(JURI::root().'subsConfirm?r='.urlencode(base64_encode($id)));
    exit();

  else :

    // CRIA NOVA COBRANÇA F2B -------------------------------------------------
    require(JPATH_BASE."/templates/base/source/_f2b/WSBilling.php");
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
                                              "numero" => $id,
                                              "tipo_ws" => "WebService"));
    // Cria o elemento sacador
    $xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"sacador");
    $WSBilling->add_attributes($xmlObj, array("conta" => "9023010833690123"));
    $WSBilling->add_content($xmlObj,"Zenite Assessoria Esportiva e Eventos Ltda.");

    // Cria o elemento cobranca
    $xmlObjCobranca = $WSBilling->add_node($xmlObjF2bCobranca,"cobranca");
    // verifica o tipo de cobrança selecionada
    $tipoCobranca = ($project->payment_boleto == 1) ? 'B' : '';
    if($project->payment_card == 1) $tipoCobranca .= 'CD';

    $WSBilling->add_attributes($xmlObjCobranca,
      array(
        "valor" => $priceTotal,
        "tipo_cobranca" => $tipoCobranca,
        "num_document" => $id
      )
    );

    // Cria os elementos demonstrativos (Até 10 linhas com 80 caracteres cada)
    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
    $WSBilling->add_content($xmlObj, utf8_decode('Inscrição no evento: '.baseHelper::nameFormat($project->name)));

    $xmlObj = $WSBilling->add_node($xmlObjCobranca,"demonstrativo");
    $WSBilling->add_content($xmlObj,utf8_decode('Pagável em qualquer banco até a data do vencimento'));

    //Cria o elemento agendamento
    $xmlObj = $WSBilling->add_node($xmlObjF2bCobranca,"agendamento");
    $WSBilling->add_attributes($xmlObj,
      array(
        "vencimento" => $reg->due_date,
        "sem_vencimento" => "n"
      )
    );
    $WSBilling->add_content($xmlObj,utf8_decode('Pagamento à vista'));

    // Cria o elemento sacado
    $xmlObjSacado = $WSBilling->add_node($xmlObjF2bCobranca,"sacado");
    $WSBilling->add_attributes($xmlObjSacado,
      array(
        "grupo" => "Atletas",
        "codigo" => "ZEN-".$user->id,
        "envio" => "n"
      )
    );
    // Cria o elemento nome
    $xmlObj = $WSBilling->add_node($xmlObjSacado,"nome");
    $WSBilling->add_content($xmlObj,baseHelper::removeAcentos($user->name));
    // Cria o elemento email
    $xmlObj = $WSBilling->add_node($xmlObjSacado,"email");
    $WSBilling->add_content($xmlObj,$user->email);
    // Cria o elemento documento (cpf)
    $xmlObj = $WSBilling->add_node($xmlObjSacado, "cpf");
    $WSBilling->add_content($xmlObj, $userInfo->cpf);
    // Cria o elemento endereco
    $xmlObj = $WSBilling->add_node($xmlObjSacado,"endereco");
    $WSBilling->add_attributes($xmlObj,
      array(
        "logradouro" => utf8_decode($userInfo->address),
        "numero" => $userInfo->address_number,
        "complemento" => utf8_decode($userInfo->address_info),
        "bairro" => utf8_decode($userInfo->address_district),
        "cidade" => utf8_decode($userInfo->address_city),
        "estado" => $userInfo->address_uf,
        "cep" => str_replace('-', '', (!empty($userInfo->zip_code) ? $userInfo->zip_code : '50000000'))
      )
    );
    // envia dados
    $WSBilling->send($WSBilling->getXML());
    // retorno
    $resposta = $WSBilling->resposta;
    $retornoF2B = '';
    if(strlen($resposta) > 0) {
      // Reinicia a classe WSBlling, agora com uma string XML
      $WSBilling = new WSBilling($resposta);
      // LOG
      $log = $WSBilling->pegaLog();
      if($log["texto"] == "OK") {
        $cobranca = $WSBilling->pegaCobranca();
        $urlBoleto = $cobranca[0]["url"];
        // informa que a fatura/boleto foi acessada
        $query = 'UPDATE '. $db->quoteName('#__zenite_registrations') .' SET '. $db->quoteName('status') .' = 1 WHERE '. $db->quoteName('id') .' = '.$id;

        try {

          $db->setQuery($query);
          $db->execute();
          // redireciona para a página de pagamento
          $app->redirect($urlBoleto);
        	exit();

        } catch (RuntimeException $e) {

          $sendMail = 0;
          echo $e->getMessage();

        }

      } else {
        $msg = '';
        foreach($log as $key => $value){
          $erro .= $value."\n";
        }
        $sendMail = 0;
        echo $erro;
      }
    } else {
      $sendMail = 0;
      echo '[F2B] - '.JText::_('TEXT_NO_RESPONSE');
    }
    // CRIA NOVA COBRANÇA F2B -------------------------------------------------
  endif;

else :

  $app->enqueueMessage('Este evento não existe ou não está mais disponível!', 'warning');
  $app->redirect(JURI::root(true));
  exit();

endif;
?>
