<?php
ini_set('max_execution_time', 600); // 600 seconds = 10 minutes
defined('_JEXEC') or die;
$app = JFactory::getApplication('site');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');
// classes customizadas para usuários Joomla
JLoader::register('baseUserHelper', JPATH_BASE.'/templates/base/source/helpers/user.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

function normaliza($value, $type = 'string') {
  if(empty($value) || $value == '-' || $value == 'NULL' || $value == 'null') :
    return ($type == 'int') ? 0 : '';
  else :
    return $value;
  endif;
}

function setEmail($email, $username) {
  if(empty($email) || strpos($email, '@invalid.com') !== false) :
    return $username.'@invalid.com';
  else :
    return $email;
  endif;
}

// salva os resultados
$file = JPATH_BASE.'/templates/base/source/contribuintes.csv';
if(file_exists($file)) :
  $l = array();
  $l = baseHelper::csvToArray($file); // array principal => contém as linhas em forma de array
  $queries = Array();
  $counter = 0;
  for($j = 1; $j < count($l); $j++) {

    // get values from file
    $username       = baseHelper::lengthFixed(normaliza($l[$j][0]), '6');
    $nome           = normaliza($l[$j][1]);
    $nomeCard       = '';
    $cardLimit      = 300;
    $email          = setEmail(normaliza($l[$j][2]), $username);
    $cpf            = normaliza($l[$j][3]);
    $rg             = normaliza($l[$j][4]);
    $rg_orgao       = normaliza($l[$j][5]);
    $dataNasc       = baseHelper::dateToSql(normaliza($l[$j][6]));
    $sexo           = (normaliza($l[$j][7]) == 'M' || normaliza($l[$j][7]) == 'Masculino') ? 1 : ((normaliza($l[$j][7]) == 'F' || normaliza($l[$j][7]) == 'Feminino') ? 2 : 0);
    $estadoCivil    = normaliza($l[$j][8]);
    $numFilhos      = normaliza($l[$j][9], 'int');
    $conjuge        = normaliza($l[$j][10]);
    $pai            = normaliza($l[$j][11]);
    $mae            = normaliza($l[$j][12]);
    $naturalidade   = normaliza($l[$j][13]);
    $codBanco       = normaliza($l[$j][14]);
    $nomeBanco      = normaliza($l[$j][15]);
    $agencia        = normaliza($l[$j][16]);
    $operacao       = normaliza($l[$j][17]);
    $conta          = normaliza($l[$j][18]);
    $phone[1]       = normaliza($l[$j][19]);
    $operadora[1]   = strtoupper(normaliza($l[$j][20]));
    $phone[2]       = normaliza($l[$j][21]);
    $operadora[2]   = strtoupper(normaliza($l[$j][22]));
    $phone[0]       = normaliza($l[$j][23]);
    $operadora[0]   = 'FIXO';
    $cep            = normaliza($l[$j][24]);
    $logradouro     = normaliza($l[$j][25]);
    $numero         = normaliza($l[$j][26]);
    $complemento    = normaliza($l[$j][27]);
    $bairro         = strtoupper(normaliza($l[$j][28]));
    $cidade         = strtoupper(normaliza($l[$j][29]));
    $uf             = strtoupper(normaliza($l[$j][30]));

    // define o grupo de usuário
    $usergroup = 13;

    echo '<p>';

    // CRIA USUÁRIO
    $newUserId = baseUserHelper::createJoomlaUser($nome, $username, $email, '', $usergroup, 0, 0);
    // INSERE OS DADOS DO ASSOCIADO
    if($newUserId) :
      // INSERE NA APP 'CLIENTS'
      $query = '
      INSERT INTO '. $db->quoteName('#__apcefpb_clients')
      .'('.
        $db->quoteName('user_id') .','.
        $db->quoteName('code') .','.
        $db->quoteName('usergroup') .','.
        $db->quoteName('name') .','.
        $db->quoteName('name_card') .','.
        $db->quoteName('email') .','.
        $db->quoteName('cpf') .','.
        $db->quoteName('rg') .','.
        $db->quoteName('rg_orgao') .','.
        $db->quoteName('gender') .','.
        $db->quoteName('birthday') .','.
        $db->quoteName('place_birth') .','.
        $db->quoteName('marital_status') .','.
        $db->quoteName('partner') .','.
        $db->quoteName('children') .','.
        $db->quoteName('mother_name') .','.
        $db->quoteName('father_name') .','.
        $db->quoteName('card_limit') .','.
        $db->quoteName('state') .','.
        $db->quoteName('created_by')
      .') VALUES ('.
        $newUserId .','.
        $db->quote($username) .','.
        $usergroup .','.
        $db->quote($nome) .','.
        $db->quote($nomeCard) .','.
        $db->quote($email) .','.
        $db->quote($cpf) .','.
        $db->quote($rg) .','.
        $db->quote($rgOrgao) .','.
        $sexo .','.
        $db->quote($dataNasc) .','.
        $db->quote($naturalidade) .','.
        $db->quote($estadoCivil) .','.
        $db->quote($conjuge) .','.
        $numFilhos .','.
        $db->quote($mae) .','.
        $db->quote($pai) .','.
        $cardLimit .','.
        $db->quote('1') .','.
        $user->id
      .')';
      $db->setQuery($query);
      $db->execute();
      $id = $db->insertid();
      echo '<br />'.$query;

      if($id) :

        // INSERE O ENDEREÇO
        $query = '
        INSERT INTO '. $db->quoteName('#__apcefpb_addresses')
        .'('.
          $db->quoteName('main') .','.
          $db->quoteName('description') .','.
          $db->quoteName('zip_code') .','.
          $db->quoteName('address') .','.
          $db->quoteName('address_number') .','.
          $db->quoteName('address_info') .','.
          $db->quoteName('address_district') .','.
          $db->quoteName('address_city') .','.
          $db->quoteName('address_state') .','.
          $db->quoteName('state') .','.
          $db->quoteName('created_by')
        .') VALUES ('.
          $db->quote('1') .','.
          $db->quote('') .','.
          $db->quote($cep) .','.
          $db->quote($logradouro) .','.
          $db->quote($numero) .','.
          $db->quote($complemento) .','.
          $db->quote($bairro) .','.
          $db->quote($cidade) .','.
          $db->quote($uf) .','.
          $db->quote('1') .','.
          $user->id
        .')';
        $db->setQuery($query);
        $db->execute();
        $addressId = $db->insertid();
        echo '<br />'.$query;

        // RELACIONAMENTO 'CLIENTE -> ENDEREÇO'
        $query = '
        INSERT INTO '. $db->quoteName('#__apcefpb_rel_clients_addresses')
        .'('.
          $db->quoteName('client_id') .','.
          $db->quoteName('address_id')
        .') VALUES ('.
          $id .','.
          $addressId
        .')';
        $db->setQuery($query);
        $db->execute();
        echo '<br />'.$query;

        // INSERE OS TELEFONES
        for($x = 0; $x <= 2; $x++) {

          if(!empty($phone[$x])) :

            $query = '
            INSERT INTO '. $db->quoteName('#__apcefpb_phones')
            .'('.
              $db->quoteName('phone_number') .','.
              $db->quoteName('operator') .','.
              $db->quoteName('main') .','.
              $db->quoteName('state') .','.
              $db->quoteName('created_by')
            .') VALUES ('.
              $db->quote($phone[$x]) .','.
              $db->quote($operadora[$x]) .','.
              $db->quote('0') .','.
              $db->quote('1') .','.
              $user->id
            .')';
            $db->setQuery($query);
            $db->execute();
            $phoneId = $db->insertid();
            echo '<br />'.$query;

            // RELACIONAMENTO 'CLIENTE -> TELEFONE'
            $query = '
            INSERT INTO '. $db->quoteName('#__apcefpb_rel_clients_phones')
            .'('.
              $db->quoteName('client_id') .','.
              $db->quoteName('phone_id')
            .') VALUES ('.
              $id .','.
              $phoneId
            .')';
            $db->setQuery($query);
            $db->execute();
            echo '<br />'.$query;

          endif;

        }

        // INSERE A CONTA BANCÁRIA
        $query = '
        INSERT INTO '. $db->quoteName('#__apcefpb_banks_accounts')
        .'('.
          $db->quoteName('bank_id') .','.
          $db->quoteName('agency') .','.
          $db->quoteName('account') .','.
          $db->quoteName('operation') .','.
          $db->quoteName('state') .','.
          $db->quoteName('created_by')
        .') VALUES ('.
          $db->quote('1') .','.
          $db->quote($agencia) .','.
          $db->quote($conta) .','.
          $db->quote($operacao) .','.
          $db->quote('1') .','.
          $user->id
        .')';
        $db->setQuery($query);
        $db->execute();
        $accountId = $db->insertid();
        echo '<br />'.$query;

        // RELACIONAMENTO 'CLIENTE -> ENDEREÇO'
        $query = '
        INSERT INTO '. $db->quoteName('#__apcefpb_rel_clients_banksAccounts')
        .'('.
          $db->quoteName('client_id') .','.
          $db->quoteName('bankAccount_id')
        .') VALUES ('.
          $id .','.
          $accountId
        .')';
        $db->setQuery($query);
        $db->execute();
        // echo '<br />'.$query.'</p>';

      endif;

    else :
      echo 'NÃO CADASTRADO: '.$nome.', '.$username.', '.$email.', '.$usergroup;
    endif;
    echo $counter++;

  }
else :
  echo 'arquivo não encontrado!';
endif;
?>
