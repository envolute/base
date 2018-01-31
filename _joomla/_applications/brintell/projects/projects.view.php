<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

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

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

if($pID > 0) :
	echo '
	<ul class="nav nav-tabs mt-3 text-sm">
		<li class="nav-item">
			<a href="apps/projects/view?pID='.$pID.'" class="nav-link active base-icon-gauge" href="#"> Dashboard</a>
		</li>
		<li class="nav-item">
			<a href="apps/projects/view?pID='.$pID.'&vID=1" class="nav-link base-icon-megaphone" href="#"> Requests</a>
		</li>
		<li class="nav-item">
			<a href="apps/projects/view?pID='.$pID.'&vID=2" class="nav-link base-icon-menu" href="#"> Tasks</a>
		</li>
		<li class="nav-item">
			<a href="apps/projects/view?pID='.$pID.'&vID=3" class="nav-link base-icon-clock" href="#"> Timesheet</a>
		</li>
	</ul>
	';

	if($vID == 0) :

		// DASHBOARD
		require($PATH_APP_FILE.'.dashboard.php');

	elseif($vID == 1) : // REQUESTS

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
		// require(JPATH_APPS.DS.'requests/requests.php');

	elseif($vID == 2) : // TASKS

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
		// require(JPATH_APPS.DS.'tasks/tasks.php');

	elseif($vID == 3) : // TIMESHEET

		require($PATH_APP_FILE.'.timesheet.php');

	endif;

endif;
?>
