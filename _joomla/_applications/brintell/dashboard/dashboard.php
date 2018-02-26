<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
require('config.php');

// ACESSO
$cfg['isPublic'] = 1; // Público -> Todos podem visualizar

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// database connect
$db		= JFactory::getDbo();

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Get request data
$pID = $app->input->get('pID', 0, 'int'); // PROJECT 'ID'

// verifica o acesso
$brintellGrp	= array_merge($cfg['groupId']['manager'], $cfg['groupId']['analyst'], $cfg['groupId']['developer']);
$hasBrintell	= array_intersect($groups, $brintellGrp);
$hasManager		= array_intersect($groups, $cfg['groupId']['manager']);
$hasAnalyst		= array_intersect($groups, $cfg['groupId']['analyst']);
$hasDeveloper	= array_intersect($groups, $cfg['groupId']['developer']);
$clientGrp		= array_merge($cfg['groupId']['client'], $cfg['groupId']['clientManager']);
$hasClient		= array_intersect($groups, $clientGrp);
$hasCManager	= array_intersect($groups, $cfg['groupId']['clientManager']);

// Carrega o arquivo de tradução das Apps
// $lang->load('base_projects', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_requests', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_tasks', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_staff', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_clientsStaff', JPATH_BASE, $lang->getTag(), true);

// BRINTELL DASHBOARD
if($hasBrintell) {
	// METRICS
	echo '<div class="row">';
		// QTD. DE PROJETOS ATIVOS
		$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE state = 1';
		$db->setQuery($query);
		$projects_actives = $db->loadResult();
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-primary bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-cogs text-primary float-left"></span>
						'.$projects_actives.'
						<div class="text-sm text-muted">'.JText::_('TEXT_PROJECTS_ACTIVES').'</div>
					</div>
				</div>
			</div>
		';
		// QTD. DE SOLICITAÇÕES ABERTAS
		$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__'.$cfg['project'].'_requests') .' WHERE status < 3 AND state = 1';
		$db->setQuery($query);
		$requests_actives = $db->loadResult();
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-live bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-bell text-live float-left"></span>
						'.$requests_actives.'
						<div class="text-sm text-muted">'.JText::_('TEXT_REQUESTS_ACTIVES').'</div>
					</div>
				</div>
			</div>
		';
		// QTD. DE TAREFAS ABERTOS
		$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' WHERE `status` < 3 AND `state` = 1 AND `visibility` = 0';
		$db->setQuery($query);
		$tasks_actives = $db->loadResult();
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-danger bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-tasks text-danger float-left"></span>
						'.$tasks_actives.'
						<div class="text-sm text-muted">'.JText::_('TEXT_TASKS_ACTIVES').'</div>
					</div>
				</div>
			</div>
		';
		// QTD. DE TIME WORKED
		$query = '
			SELECT SUM(T1.hours)
			FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks_timer') . ' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_tasks') . ' T2
				ON T2.id = T1.task_id AND T2.visibility = 0
		';
		$db->setQuery($query);
		$time_worked = $db->loadResult();
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-success bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-clock text-success float-left"></span>
						'.$time_worked.'
						<div class="text-sm text-muted">'.JText::_('TEXT_TIME_WORKED').'</div>
					</div>
				</div>
			</div>
		';
	echo '</div>';

	// BRINTELL TEAM
	$team = '';
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' role,
			'. $db->quoteName('T3.session_id') .' online
		FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff_roles') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.role_id
			LEFT JOIN '. $db->quoteName('#__session') .' T3
			ON '.$db->quoteName('T3.userid') .' = T1.user_id AND T3.client_id = 0
		WHERE '. $db->quoteName('T1.type') .' = 0 AND T1.state = 1
		ORDER BY '. $db->quoteName('T1.name') .' ASC
	';
	$db->setQuery($query);
	$res = $db->loadObjectList();
	foreach($res as $obj) {
		if($obj->online) :
			$lStatus = JText::_('TEXT_USER_STATUS_1');
			$iStatus = '<small class="base-icon-circle text-success pos-absolute pos-right-0 pos-bottom-0"></small>';
		else :
			$lStatus = JText::_('TEXT_USER_STATUS_0');
			$iStatus = '';
		endif;
		$name = baseHelper::nameFormat((!empty($obj->nickname) ? $obj->nickname : $obj->name));
		$role = baseHelper::nameFormat((!empty($obj->role) ? $obj->role : $obj->occupation));
		if(!empty($role)) $role = '<br />'.$role;
		$info = baseHelper::nameFormat($name).$role.'<br />'.$lStatus;

		// Imagem Principal -> Primeira imagem (index = 0)
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		$img = uploader::getFile('#__brintell_staff_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/staff/');
		if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/staff/'.$img['filename'], 32, 32);
		else $imgPath = JURI::root().'images/apps/icons/user_'.$obj->gender.'.png';
		$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:32px; height:32px;" />';

		$team .= '
			<a href="apps/staff/view?vID='.$obj->user_id.'" class="d-inline-block pos-relative hasTooltip" title="'.$info.'">
				'.$img.$iStatus.'
			</a>
		';
	}

	// MONTH ACTIVITY

	// Closed Requests
	$query = '
		SELECT
			COUNT(*) total,
			(
				SELECT COUNT(*)
				FROM '. $db->quoteName('#__'.$cfg['project'].'_requests') .'
				WHERE MONTH(`created_date`) = 2 AND YEAR(`created_date`) = 2018 AND (`status` = 3 OR `state` = 0)
			) amount
		FROM '. $db->quoteName('#__'.$cfg['project'].'_requests') .'
		WHERE MONTH(`created_date`) = 2 AND YEAR(`created_date`) = 2018
	';
	$db->setQuery($query);
	$metricsMonth = $db->loadObject();
	$requestsMonth = '
		<div class="h2 pb-3 b-bottom b-bottom-dashed">
			<span class="text-success">'.$metricsMonth->amount.'</span> <span class="text-muted">/ '.$metricsMonth->total.'</span>
			<div class="text-sm text-muted">'.JText::_('TEXT_REQUESTS_FINISHED').'</div>
		</div>
	';

	// Finished Tasks
	$query = '
		SELECT
			COUNT(*) total,
			(
				SELECT COUNT(*)
				FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .'
				WHERE MONTH(`created_date`) = 2 AND YEAR(`created_date`) = 2018 AND (`status` = 3 OR `state` = 0) AND `visibility` = 0
			) amount
		FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .'
		WHERE MONTH(`created_date`) = 2 AND YEAR(`created_date`) = 2018 AND `visibility` = 0
	';
	$db->setQuery($query);
	$metricsMonth = $db->loadObject();

	// monthly worked time
	$query = '
		SELECT SUM(T1.hours)
		FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks_timer') . ' T1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_tasks') . ' T2
			ON T2.id = T1.task_id AND T2.visibility = 0
		 WHERE MONTH(`date`) = 2 AND YEAR(`date`) = 2018
	';
	$db->setQuery($query);
	$timeMonthly = $db->loadResult();

	$tasksMonth = '
		<div class="h2 pb-3 b-bottom b-bottom-dashed d-flex justify-content-between align-items-center">
			<div>
				<span class="text-success">'.$metricsMonth->amount.'</span> <span class="text-muted">/ '.$metricsMonth->total.'</span>
				<div class="text-sm text-muted">'.JText::_('TEXT_TASKS_FINISHED').'</div>
			</div>
			<div class="h4 m-0 text-right">
				<div class="text-success mb-1 base-icon-clock"> '.$timeMonthly.'</div>
				<div class="small text-muted">'.JText::_('TEXT_TIME_WORKED').'</div>
			</div>
		</div>
	';


	$months = '';
	for($i = 1; $i <= 12; $i++) {
		$months .= '<option value="'.$i.($i == date('m') ? ' select' : '').'">'.baseHelper::getMonthName($i).'</option>';
	}
	echo '
		<div class="row">
			<div class="col-md-9">
				<div class="rounded b-top-2 b-info mb-4 set-shadow">
					<h6 class="page-header p-2 m-0 pos-relative bg-white">
						'.JText::_('TEXT_MONTHLY').'
						<span class="pos-absolute pos-top-0 pos-right-0">
							<select class="from-control">
								'.$months.'
							</select>
						</span>
					</h6>
					<div class="row no-gutters">
						<div class="col">
							<div class="p-2 bg-live base-icon-bell"> '.JText::_('TEXT_REQUESTS').'</div>
							<div class="p-2 bg-white">
								'.$requestsMonth.'
								<h6 class="page-header">'.JText::_('TEXT_RECENTS').'</h6>
								<div class="alert alert-warning text-sm p-2 m-0">'.JText::_('TEXT_NO_REQUESTS_THIS_MONTH').'</div>
							</div>
						</div>
						<div class="col b-left">
							<div class="p-2 bg-danger base-icon-tasks"> '.JText::_('TEXT_TASKS').'</div>
							<div class="p-2 bg-white">
								'.$tasksMonth.'
								<h6 class="page-header">'.JText::_('TEXT_RECENTS').'</h6>
								<div class="alert alert-warning text-sm p-2 m-0">'.JText::_('TEXT_NO_TASKS_THIS_MONTH').'</div>
							</div>
						</div>
					</div>
				</div>
				<div class="rounded b-top-2 b-primary bg-white set-shadow">
					<h6 class="page-header p-2 m-0 text-primary base-icon-users"> '.JText::_('TEXT_TEAM').'</h6>
					<div class="px-2 pt-2">'.$team.'</div>
				</div>
			</div>
			<div class="col-md">
				<div class="rounded b-top-2 b-live bg-white set-shadow">
					<h6 class="page-header p-2 m-0 text-live base-icon-attention"> '.JText::_('TEXT_ALERTS').'</h6>
					<div class="p-2">Alerts</div>
				</div>
			</div>
		</div>
	';

// CLIENT DASHBOARD
} else if($hasClient) {
	// METRICS
	echo 'Client Dashboard';
} else {
	// METRICS
	echo 'No Access';
}

?>
<!--
<h1 class="mb-4 text-live">Esse é o dashboard do sistema<div class="text-md text-muted py-2">O dashboard terá 3 tipos de visualizações:</div></h1>
<div class="row">
	<div class="col-md">
		<h5 class="page-header">Manager<div class="text-sm text-muted">Essa será a visão mais completa com opções como:</div></h5>
		<ul>
			<li>Quantidade de projetos</li>
			<li>Quantidade de chamados ativos</li>
			<li>Quantidade de chamados abertos (mês)</li>
			<li>Quantidade de chamados finalizados (mês)</li>
			<li>Quantidade de tarefas ativas</li>
			<li>Quantidade de tarefas criadas (mês)</li>
			<li>Quantidade de tarefas finalizadas (mês)</li>
			<li>Lista de clientes</li>
			<li>Chamados Recentes</li>
			<li>Tarefas Recentes</li>
			<li>Alertas</li>
			<li>Lista de Membros da Equipe Brintell (online/offline)</li>
			<li>
				Ações Extras de CRM:
				<ul>
					<li>Aniversariantes do mês/dia<div class="small">Obs: O sistema envia mensagem de email automática no dia do aniversário</div></li>
					<li>Mensagem de Feliz dia dos Pais (pra que for pai/mãe)</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class="col-md">
		<h5 class="page-header">Developer<div class="text-sm text-muted">Uma visão particular do usuário:</div></h5>
		<ul>
			<li>Quantidade de projetos</li>
			<li>Quantidade de tarefas relacionadas</li>
			<li>Quantidade de tarefas atribuídas (mês)</li>
			<li>Quantidade de tarefas finalizadas (mês)</li>
			<li>Lista de Membros da Equipe Brintell (online/offline)</li>
			<li>Tarefas Recentes (do usuário)</li>
			<li>Alertas</li>
		</ul>
	</div>
	<div class="col-md">
		<h5 class="page-header">Client Manager<div class="text-sm text-muted">Visão específica do usuário Manager cliente:</div></h5>
		<ul>
			<li>Quantidade de projetos</li>
			<li>Quantidade de chamados ativos</li>
			<li>Quantidade de chamados abertos (mês)</li>
			<li>Quantidade de chamados finalizados (mês)</li>
			<li>Lista de Membros da Equipe do Cliente (online/offline)</li>
			<li>Lista de clientes (os quais o usuário está cadastrado)</li>
			<li>Alertas do usuário/todos</li>
		</ul>
		<hr />
		<h5 class="text-live base-icon-attention"> Importante:</h5>
		<div>O cliente pode cadastrar um usuário "developer" que não acessará o dashboard. Ele será direcionado diretamente para os chamados, onde poderá acompanhar e abrir novos chamados.</div>
		<strong>O objetivo é possibilitar que o Cliente Manager possa criar usuários (com algumas restrições) para a sua equipe. Podendo disponibilizar acesso para outros membros sem depender da Brintell para isso.
	</div>
</div>
-->
