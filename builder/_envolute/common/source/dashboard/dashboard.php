<?php
defined('_JEXEC') or die;

$ajaxRequest = false;
// require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// init general css/js files
// require(JPATH_BASE.'/templates/base/source/_init.app.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

//joomla get request data
$input = $app->input;

// Default Params
$dateMin	= $app->input->get('dateMin', '', 'string');
$dateMax	= $app->input->get('dateMax', '', 'string');
$dtmin = !empty($dateMin) ? $dateMin : date('Y-m-d');
$dtmax = !empty($dateMax) ? $dateMax : date('Y-m-d');

function moduleHead($title) {
  return '
    <div class="module bottom-space clearfix">
      <h3 class="page-header clearfix ">
        <span class="head-container font-featured text-primary">'.$title.'</span>
      </h3>
      <div class="module-body">
        <div class="module-content">
  ';
}
function moduleFoot() {
  return '
        </div>
      </div>
    </div>
  ';
}

// database connect
$db = JFactory::getDbo();
$html = '
  <div class="row">
    <div class="col-sm-4">
      <div class="base-icon-dollar all-expand-xs bg-primary text-lg bottom-space"> Faturamento</div>
';

// FECHAMENTO
$query = '
  SELECT
    '. $db->quoteName('T1.id') .',
    '. $db->quoteName('T1.name') .',
    '. $db->quoteName('T1.due_date') .'
  FROM
    '. $db->quoteName('#__envolute_clients').' T1
	WHERE '.$db->quoteName('T1.due_date').' != 0 AND T1.state = 1
  ORDER BY '.$db->quoteName('T1.due_date').' ASC
';
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();
$list = $db->loadObjectList();
$html .= moduleHead('Vencimentos');
if($num_rows > 0) :
$html .= '<ul class="list list-no-space">';
  foreach($list as $item) {
    $html .= '<li>'.baseHelper::nameFormat($item->name).'<strong class="text-sm pull-right">'.($item->due_date < 10 ? '0'.$item->due_date : $item->due_date).'</strong></li>';
  }
$html .= '</ul>';
endif;
$html .= moduleFoot();

// FATURAS ABERTAS
$query = '
  SELECT
    '. $db->quoteName('T1.id') .',
    '. $db->quoteName('T2.name') .',
    '. $db->quoteName('T1.due_date') .'
  FROM
    '. $db->quoteName('#__envolute_invoices').' T1
    JOIN '. $db->quoteName('#__envolute_projects').' T2
    ON T2.id = T1.project_id
    JOIN '. $db->quoteName('#__envolute_clients').' T3
    ON T3.id = T2.client_id
	WHERE
    T1.sent = 1 AND T1.paid = 0 AND T1.state = 1
  ORDER BY '.$db->quoteName('T1.due_date').' ASC
';
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();
$list = $db->loadObjectList();
$html .= moduleHead('Faturas Abertas');
if($num_rows > 0) :
$html .= '<ul class="list list-no-space">';
  foreach($list as $item) {
    $html .= '<li>'.baseHelper::nameFormat($item->name).'<strong class="text-sm pull-right">'.baseHelper::dateFormat($item->due_date, 'd.m').'</strong></li>';
  }
$html .= '</ul>';
endif;
$html .= moduleFoot();

$html .= '
    </div>
    <div class="col-sm-4">
      <div class="base-icon-tools all-expand-xs bg-primary text-lg bottom-space"> Atividades</div>
';

// FECHAMENTO
$query = '
  SELECT
    '. $db->quoteName('T1.id') .',
    '. $db->quoteName('T1.title') .',
    '. $db->quoteName('T1.priority') .',
    '. $db->quoteName('T1.recurrent_type') .',
    '. $db->quoteName('T1.start_date') .',
    '. $db->quoteName('T2.name') .' project
  FROM
    '. $db->quoteName('#__envolute_tasks').' T1
    LEFT JOIN '. $db->quoteName('#__envolute_projects').' T2
    ON T2.id = T1.project_id
	WHERE T1.type = 0
    AND (
      '.$db->quoteName('T1.start_date').' = CURDATE()
      OR T1.recurrent_type = 1
      OR FIND_IN_SET(DAYOFWEEK(CURDATE()), T1.weekly)
      OR FIND_IN_SET(DATE_FORMAT(CURDATE(),"%d"), T1.monthly)
      OR T1.yearly LIKE CONCAT("%",DATE_FORMAT(CURDATE(),"%d/%m"),"%")
    )
    AND T1.state = 1
  ORDER BY '.$db->quoteName('T1.id').' ASC
';
$db->setQuery($query);
$db->execute();
$num_rows = $db->getNumRows();
$list = $db->loadObjectList();
$html .= moduleHead('Tarefas do Dia');
if($num_rows > 0) :
$html .= '<ul class="list list-no-space">';
  foreach($list as $item) {

    $priority = ($item->priority == 1) ? '<span class="base-icon-attention text-live cursor-help hasTooltip" title="Prioridade"></span> ' : '';

    switch ($item->recurrent_type) {
      case 1:
        $icon = '<span class="base-icon-arrows-cw label label-primary pull-right"> diário</span> ';
        break;
      case 2:
        $icon = '<span class="base-icon-arrows-cw label label-primary pull-right"> semanal</span> ';
        break;
      case 3:
        $icon = '<span class="base-icon-arrows-cw label label-primary pull-right"> mensal</span> ';
        break;
      case 4:
        $icon = '<span class="base-icon-arrows-cw label label-primary pull-right"> anual</span> ';
        break;
      default:
        $icon = '<span class="label label-primary pull-right"> '.baseHelper::dateFormat($item->start_date).'</span> ';
        break;
    }
    $html .= '<li>'.$icon.baseHelper::nameFormat($item->title).'<div class="text-xs text-muted font-featured">'.$priority.'<span class="cursor-help hasTooltip" title="Projeto">'.baseHelper::nameFormat($item->project).'</span></div></li>';
  }
$html .= '</ul>';
endif;
$html .= moduleFoot();

// // FATURAS ABERTAS
// $query = '
//   SELECT
//     '. $db->quoteName('T1.id') .',
//     '. $db->quoteName('T2.name') .',
//     '. $db->quoteName('T1.due_date') .'
//   FROM
//     '. $db->quoteName('#__envolute_invoices').' T1
//     JOIN '. $db->quoteName('#__envolute_projects').' T2
//     ON T2.id = T1.project_id
//     JOIN '. $db->quoteName('#__envolute_clients').' T3
//     ON T3.id = T2.client_id
// 	WHERE
//     T1.sent = 1 AND T1.paid = 0 AND T1.state = 1
//   ORDER BY '.$db->quoteName('T1.due_date').' ASC
// ';
// $db->setQuery($query);
// $db->execute();
// $num_rows = $db->getNumRows();
// $list = $db->loadObjectList();
// $html .= moduleHead('Faturas Abertas');
// if($num_rows > 0) :
// $html .= '<ul class="list list-no-space">';
//   foreach($list as $item) {
//     $html .= '<li>'.baseHelper::nameFormat($item->name).'<span class="pull-right">'.baseHelper::dateFormat($item->due_date, 'd.m').'</span></li>';
//   }
// $html .= '</ul>';
// endif;
// $html .= moduleFoot();

$html .= '
    </div>
    <div class="col-sm-4">
      <div class="base-icon-bell-alt all-expand-xs bg-live text-lg bottom-space"> Acompanhamento</div>
';


$html .= '
    </div>
  </div>
';


echo $html;

?>
