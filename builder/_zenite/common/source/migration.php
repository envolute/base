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

// salva os resultados
$file = JPATH_BASE.'/templates/base/source/cadastros_1.csv';
if(file_exists($file)) :
  $l = array();
  $l = baseHelper::csvToArray($file); // array principal => contém as linhas em forma de array
  $queries = Array();
  $counter = $counterOff = 0;
  for($j = 1; $j < count($l); $j++) {

    // echo '<p>';

    // get values from file
    $username       = baseHelper::lengthFixed($l[$j][0], '11');
    $nome           = $l[$j][1];
    $email          = strtolower($l[$j][3]);
    // $ddd            = $l[$j][4];
    // $phone          = normaliza($l[$j][5]);
    $senha          = $l[$j][6];

    // define o grupo de usuário
    $usergroup = 2;

    // echo $nome.', '.$username.', '.$email.', '.$usergroup;
    // echo '</p>';

    // CRIA USUÁRIO
    $newUserId = baseUserHelper::createJoomlaUser($nome, $username, $email, $senha, $usergroup, 0, 0);
    // INSERE OS DADOS DO ASSOCIADO
    if($newUserId) :
      $counter++;
    else :
      $counterOff++;
      echo 'NÃO CADASTRADO: '.$nome.' - '.$email.'<br />';
    endif;

  }
  echo '<hr /><p>CADASTRADOS: '.$counter.'</p>';
  echo '<p>TOTAL: '.$j.'</p>';
else :
  echo 'arquivo não encontrado!';
endif;
?>
