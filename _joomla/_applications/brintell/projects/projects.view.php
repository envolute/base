<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = 1; // Público -> Todos podem visualizar

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINAPP	= $APPNAME;
$MAINTAG	= $APPTAG;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Get request data
$pID = $app->input->get('pID', 0, 'int'); // PROJECT 'ID'
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'

// verifica o acesso
$hasAnalyst		= array_intersect($groups, $cfg['groupId']['analyst']); // se está na lista de grupos permitidos
$clientGrp		= array_merge($cfg['groupId']['client'], $cfg['groupId']['clientManager']);
$hasClient		= array_intersect($groups, $clientGrp); // se está na lista de grupos permitidos
$hasCManager	= array_intersect($groups, $cfg['groupId']['clientManager']); // se está na lista de grupos permitidos

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

$p = ($pID > 0) ? 'pID='.$pID.'&' : '';

if($pID == 0) :
	echo '
		<div class="font-condensed text-gray-400 text-embed text-center py-4">
			<h1 class="display-1 d-none d-lg-block">'.JText::_('TEXT_SELECT_PROJECT').'</h1>
			<h1 class="d-lg-none">'.JText::_('TEXT_SELECT_PROJECT').'</h1>
		</div>
	';
	return;
endif;

// DEFINE VIEW
$a[0] = $a[1] = $a[2] = '';
// REQUESTS: Manager, Analyst & Client Manager
if($vID == 0 && ($hasAdmin || $hasAnalyst || $hasClient)) :
	$a[0] = ' active';
// TASKS: Brintell Staff, Bloqueado para Clients
elseif(!$hasClient) :
	$a[1] = ' active';
	if($vID == 2) $a[2] = ' active';
endif;
$l[0] = JText::_('TEXT_REQUESTS');
$l[1] = JText::_('TEXT_TASKS');
$l[2] = JText::_('TEXT_TIMESHEET');
$icon[0] = 'megaphone';
$icon[1] = 'menu';
$icon[2] = 'clock';

$tabs = array();
if($hasAdmin || $hasAnalyst || $hasClient) :
	$x = 0;
	$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
endif;
if(!$hasClient) :
	$x = 1;
	$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
	// $x = 2;
	// $tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
endif;

echo '<ul class="nav nav-tabs mb-2">';
for($i = 0; $i < count($tabs); $i++) {
	echo $tabs[$i];
}
echo '</ul>';

if(!empty($a[0])) :

	// REQUESTS
	// Manager, Analyst & Client Manager
	$requestsListFull		= false;
	$requestsListAjax		= "list.full.ajax.php";
	$requestsAjaxFilter		= 1;
	$requestsRelTag			= 'projects';
	$requestsRelListNameId	= 'project_id';
	$requestsRelListId		= $pID;
	$requestsOnlyChildList	= true;
	require(JPATH_APPS.DS.'requests/requests.php');

elseif(!empty($a[1])) :

	// TASKS
	// Brintell Staff
	$tasksListFull		= false;
	$tasksListAjax		= "list.full.ajax.php";
	$tasksAjaxFilter	= 1;
	$tasksRelTag		= 'projects';
	$tasksRelListNameId	= 'project_id';
	$tasksRelListId		= $pID;
	$tasksOnlyChildList	= true;
	require(JPATH_APPS.DS.'tasks/tasks.php');

elseif(!empty($a[2])) : // TIMESHEET

	if($hasAdmin || $hasAnalyst) :
		// require($PATH_APP_FILE.'.timesheet.php');
		echo '<div class="alert alert-info">Timesheet: Visão Geral e do projeto</div>';
	else :
		// require($PATH_APP_FILE.'.timesheet.user.php');
		echo '<div class="alert alert-info">Timesheet: Visão do usuário no projeto</div>';
	endif;

endif;
?>
