<?php
defined('_JEXEC') or die;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db = JFactory::getDbo();

//joomla get request data
$input      = $app->input;

// params requests
$id = base64_decode($input->get('p', '', 'string'));

// STATS
$access = 1;
$grp = array(10); // cliente
$hasGroup = array_intersect($groups, $grp);
if($hasGroup) : // verifica se é um cliente
  $query = '
  SELECT COUNT(*) FROM
    '. $db->quoteName('#__zenite_projects') .' T1
    JOIN '. $db->quoteName('#__zenite_clients') .' T2
    ON T2.id = T1.client_id
    JOIN '. $db->quoteName('#__zenite_rel_clients_contacts') .' T3
    ON T3.client_id = T1.client_id
    JOIN '. $db->quoteName('#__zenite_contacts') .' T4
    ON T4.id = T3.contact_id
    JOIN '. $db->quoteName('#__users') .' T5
    ON T5.id = T4.user_id
  WHERE T1.id =  '.$id.' AND T5.id = '.$user->id.'
  ';
  $db->setQuery($query);
  $access = $db->loadResult();
endif;

if($access) :

  // dados do projeto
  $query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' WHERE id = '.$id;
  $db->setQuery($query);
  $item = $db->loadObject();

  // quantidade de inscritos
  $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND state = 1';
  $db->setQuery($query);
  $totalReg = $db->loadResult();

  // porcentagem de inscritos
  $porcent = '';
  if($item->limit > 0) :
    $totalReg = $totalReg.' / '.$item->limit;
    $porcent = round(($totalReg*100) / $item->limit).'%';
    $porcent = '<span class="label label-default pull-right right-space-xs">'.$porcent.'</span>';
  endif;

  // quantidade de inscrições ativas
  $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND status < 2 AND state = 1';
  $db->setQuery($query);
  $totalRegActive = $db->loadResult();

  // quantidade de inscrições canceladas
  $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND state = 0';
  $db->setQuery($query);
  $totalRegNull = $db->loadResult();

  // quantidade de inscritos pagos
  $query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND status = 2';
  $db->setQuery($query);
  $totalPaid = $db->loadResult();

  // arrecadação ativa
  $query = 'SELECT SUM((price - discount)) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND state = 1';
  $db->setQuery($query);
  $priceActive = $db->loadResult();

  // arrecadação real
  $query = 'SELECT SUM((price - discount)) FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND status = 2';
  $db->setQuery($query);
  $priceReal = $db->loadResult();

  // tamanhos das camisas
  $query = 'SELECT sizeShirt, COUNT(sizeShirt) AS total FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$id.' AND sizeShirt != "" AND status = 2 GROUP BY sizeShirt';
  $db->setQuery($query);
  $db->execute();
  $hasSizes = $db->getNumRows();
  $sizeShirts = $db->loadObjectList();
  $shirtList = '';
  if($hasSizes) :
    $shirtList .= '<h4 class="page-header top-expand">Camisas</h4>';
    $shirtList .= '<ul class="list">';
    foreach ($sizeShirts as $size) {
      $shirtList .= '<li>'.$size->sizeShirt.': <span class="label label-primary pull-right">'.$size->total.'</span></li>';
    }
    $shirtList .= '</ul>';
  endif;

  echo '
    <ul class="list">
      <li>Total de Inscritos: <span class="label label-primary pull-right">'.$totalReg.'</span>'.$porcent.'</li>
      <li class="text-success">Inscrições Confirmadas: <span class="label label-success pull-right">'.$totalPaid.'</span></li>
      <li class="text-live">Inscrições Ativas: <span class="label label-warning pull-right">'.$totalRegActive.'</span></li>
      <li class="text-danger">Inscrições Canceladas: <span class="label label-danger pull-right">'.$totalRegNull.'</span></li>
      <li>Arrecadação Ativa: <span class="label label-primary pull-right">R$ '.baseHelper::priceFormat($priceActive).'</span></li>
      <li>Arrecadação Real: <span class="label label-primary pull-right">R$ '.baseHelper::priceFormat($priceReal).'</span></li>
    </ul>
    '.$shirtList.'
    <hr class="hr-sm" />
    <a class="btn btn-primary btn-block" href="dashboard/subscriptions?pID='.$id.'" target="_blank">Ver Inscrições</a>
  ';

endif;

?>
