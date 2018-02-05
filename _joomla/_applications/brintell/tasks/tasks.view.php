<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
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
$hasClient		= array_intersect($groups, $cfg['groupId']['client']); // se está na lista de grupos permitidos
$hasCManager	= array_intersect($groups, $cfg['groupId']['client']['manager']); // se está na lista de grupos permitidos

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
$a[0] = ($vID == 0) ? ' active' : '';
$a[1] = ($vID == 1) ? ' active' : '';
$a[2] = ($vID == 2) ? ' active' : '';
$a[3] = ($vID == 3) ? ' active' : '';
$l[0] = JText::_('TEXT_DASHBOARD');
$l[1] = JText::_('TEXT_REQUESTS');
$l[2] = JText::_('TEXT_TASKS');
$l[3] = JText::_('TEXT_TIMESHEET');
$icon[0] = 'gauge';
$icon[1] = 'megaphone';
$icon[2] = 'menu';
$icon[3] = 'clock';

$tabs = array();
$x = 0;
$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
if($hasAdmin || $hasAnalyst || $hasCManager) :
	$x = 1;
	$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
endif;
if(!$hasClient) :
	$x = 2;
	$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
	$x = 3;
	$tabs[] = '<li class="nav-item"><a href="apps/projects/view?'.$p.'vID='.$x.'" class="nav-link'.$a[$x].' base-icon-'.$icon[$x].'" href="#"> '.$l[$x].'</a></li>';
endif;

if($pID > 0 || $hasAdmin || $hasAnalyst || $hasCManager) :
		echo '<ul class="nav nav-tabs mt-3 text-sm">';
		for($i = 0; $i < count($tabs); $i++) {
			echo $tabs[$i];
		}
		echo '</ul>';
else :
	echo '<h1 class="display-1 font-condensed text-gray-400 text-embed text-center py-4">'.JText::_('TEXT_SELECT_PROJECT').'</h1>';
	return;
endif;

// DASHBOARD: All Team
if($vID == 0) :

	// Manager, Analyst & Client Manager
	if($pID == 0) :
		if($hasCManager) :
			// CLIENT MANAGER DASHBOARD
			// require($PATH_APP_FILE.'.dashboard.client.php');
			echo 'Dashboard do Cliente: Visualiza todas as solicitações do cliente';
		else :
			// MANAGER DASHBOARD
			// require($PATH_APP_FILE.'.dashboard.php');
			echo 'Dashboard Geral: Visão geral de todos os projetos';
		endif;
	// All Team
	else :
		if($hasCManager) :
			// PROJECT DASHBOARD
			// require($PATH_APP_FILE.'.dashboard.project.client.php');
			echo 'Dashboard do cliente no Projeto: Visualiza solicitações do cliente no projeto';
		elseif($hasAdmin || $hasAnalyst) :
			// PROJECT DASHBOARD
			// require($PATH_APP_FILE.'.dashboard.project.php');
			echo 'Dashboard do Projeto: Visão geral do projeto';
		// developer, external, client
		else :
			// USER DASHBOARD
			// require($PATH_APP_FILE.'.dashboard.user.php');
			echo 'Dashboard do Usuário: Visão geral do usuário no projeto';
		endif;
	endif;

// REQUESTS: Manager, Analyst & Client Manager
elseif($vID == 1 && ($hasAdmin || $hasAnalyst || $hasCManager)) :

	// Manager, Analyst & Client Manager
	// REQUESTS
	// $requestsListFull		= false;
	// $requestsShowAddBtn	= false;
	// $requestsRelTag		= 'projects';
	// $requestsRelListNameId= 'projects_id';
	// $requestsRelListId	= $pID;
	// $requestsOnlyChildList= true;
	echo '
		<h4 class="page-header base-icon-users pt-5">
			'.JText::_('TEXT_REQUESTS').'
			<a href="#" class="btn btn-xs btn-success float-right base-icon-plus" onclick="requests_setParent('.$pID.')" data-toggle="modal" data-target="#modal-requests" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>
		</h4>
	';
	if($hasCManager) :
		// REQUESTS OF CLIENT
		// require(JPATH_APPS.DS.'requests/requests.client.php');
		echo 'Solicitações do Cliente: Todos os projetos do cliente';
	else :
		// ALL REQUESTS
		// require(JPATH_APPS.DS.'requests/requests.projects.php');
		echo 'Solicitações Gerais: Todos os projetos';
	endif;

// TASKS: Brintell Team, Bloqueado para Clients
elseif($vID == 2 && !$hasClient) :

	// Brintell Team
	// TASKS
	// $tasksListFull		= false;
	// $tasksShowAddBtn	= false;
	// $tasksRelTag		= 'projects';
	// $tasksRelListNameId	= 'projects_id';
	// $tasksRelListId		= $pID;
	// $tasksOnlyChildList	= true;
	echo '
		<h4 class="page-header base-icon-users pt-5">
			'.JText::_('TEXT_TASKS').'
			<a href="#" class="btn btn-xs btn-success float-right base-icon-plus" onclick="tasks_setParent('.$pID.')" data-toggle="modal" data-target="#modal-tasks" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>
		</h4>
	';
	if($hasAdmin || $hasAnalyst) :
		// require(JPATH_APPS.DS.'tasks/tasks.php');
		echo 'Tarefas: Geral e do projeto';
	else :
		// require(JPATH_APPS.DS.'tasks/tasks.user.php');
		echo 'Tarefas do usuário: Todas do usuário no projeto';
	endif;

// Bloqueado para Clients
elseif($vID == 3 && !$hasClient) : // TIMESHEET

	if($hasAdmin || $hasAnalyst) :
		// require($PATH_APP_FILE.'.timesheet.php');
		echo 'Timesheet: Visão Geral e do projeto';
	else :
		// require($PATH_APP_FILE.'.timesheet.user.php');
		echo 'Timesheet: Visão do usuário no projeto';
	endif;

endif;
?>
