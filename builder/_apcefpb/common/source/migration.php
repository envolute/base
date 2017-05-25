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

function setEmail($email, $emailCx, $username) {
  if(empty($email) || strpos($email, '@invalid.com') !== false) :
    return $username.'@invalid.com';
  else :
    if(strpos($email, '@caixa.gov.br') === false) :
      return $email;
    else :
      if(!empty($emailCx) && strpos($emailCx, '@caixa.gov.br') === false) :
        return $emailCx;
      else :
        return $email;
      endif;
    endif;
  endif;
}

function setEmailCx($emailCx, $email) {
  if(empty($emailCx)) :
    return '';
  else :
    if(strpos($emailCx, '@caixa.gov.br') === false) :
      if(!empty($email) && strpos($email, '@caixa.gov.br') !== false) :
        return $email;
      else :
        return '';
      endif;
    else :
      return $emailCx;
    endif;
  endif;
}

// salva os resultados
$file = JPATH_BASE.'/templates/base/source/associados_4.csv';
if(file_exists($file)) :
  $l = array();
  $l = baseHelper::csvToArray($file); // array principal => contém as linhas em forma de array
  $queries = Array();
  $counter = 0;
  for($j = 1; $j < count($l); $j++) {

    // get values from file
    $username       = baseHelper::lengthFixed(normaliza($l[$j][38]), '6');
    $att            = normaliza($l[$j][0]);
    $nome           = normaliza($l[$j][1]);
    $nomeCard       = '';
    $cardLimit      = 300;
    $email          = setEmail(normaliza($l[$j][2]), normaliza($l[$j][3]), $username);
    $emailCaixa     = setEmailCx(normaliza($l[$j][3]), normaliza($l[$j][2]));
    $statusCaixa    = normaliza($l[$j][4]);
    $cargo          = normaliza($l[$j][5]);
    $lotacao        = normaliza($l[$j][6]);
    $phone[1]       = normaliza($l[$j][7]);
    $operadora[1]   = strtoupper(normaliza($l[$j][8]));
    $phone[2]       = normaliza($l[$j][9]);
    $operadora[2]   = strtoupper(normaliza($l[$j][10]));
    $phone[0]       = normaliza($l[$j][11]);
    $operadora[0]   = 'FIXO';
    $cep            = normaliza($l[$j][12]);
    $logradouro     = normaliza($l[$j][13]);
    $numero         = normaliza($l[$j][14]);
    $complemento    = normaliza($l[$j][15]);
    $bairro         = strtoupper(normaliza($l[$j][16]));
    $cidade         = strtoupper(normaliza($l[$j][17]));
    $uf             = strtoupper(normaliza($l[$j][18]));
    $dataNasc       = baseHelper::dateToSql(normaliza($l[$j][19]));
    $estadoCivil    = normaliza($l[$j][20]);
    $conjuge        = normaliza($l[$j][21]);
    $numFilhos      = normaliza($l[$j][22], 'int');
    $conta          = normaliza($l[$j][23]);
    $agencia        = normaliza($l[$j][24]);
    $operacao       = normaliza($l[$j][25]);
    $cpf            = normaliza($l[$j][26]);
    $rg             = normaliza($l[$j][27]);
    $rgOrgao        = normaliza($l[$j][28]);
    $sexo           = (normaliza($l[$j][29]) == 'M' || normaliza($l[$j][29]) == 'Masculino') ? 1 : ((normaliza($l[$j][29]) == 'F' || normaliza($l[$j][29]) == 'Feminino') ? 2 : 0);
    $pai            = normaliza($l[$j][30]);
    $mae            = normaliza($l[$j][31]);
    $matriculaCx    = normaliza($l[$j][32]);
    $admissaoCx     = baseHelper::dateToSql(normaliza($l[$j][33]));
    $funcaoCx       = normaliza($l[$j][34]);
    $naturalidade   = normaliza($l[$j][35]);
    $codBanco       = normaliza($l[$j][36]);
    $nomeBanco      = normaliza($l[$j][37]);

    // define o grupo de usuário
    $usergroup = ($statusCaixa == 'Aposentado' || $statusCaixa == 'Pensionista') ? 12 : 11;

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
        $db->quoteName('cx_email') .','.
        $db->quoteName('cx_matricula') .','.
        $db->quoteName('cx_admissao') .','.
        $db->quoteName('cx_lotacao') .','.
        $db->quoteName('cx_cargo') .','.
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
        $db->quote($emailCaixa) .','.
        $db->quote($matriculaCx) .','.
        $db->quote($admissaoCx) .','.
        $db->quote($lotacao) .','.
        $db->quote($cargo) .','.
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
