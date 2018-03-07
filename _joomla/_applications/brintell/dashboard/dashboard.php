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
$hasExternal	= array_intersect($groups, $cfg['groupId']['external']);
$clientGrp		= array_merge($cfg['groupId']['client'], $cfg['groupId']['clientManager']);
$hasClient		= array_intersect($groups, $clientGrp);
$hasCManager	= array_intersect($groups, $cfg['groupId']['clientManager']);

// Carrega o arquivo de tradução das Apps
// $lang->load('base_projects', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_issues', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_tasks', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_staff', JPATH_BASE, $lang->getTag(), true);
// $lang->load('base_clientsStaff', JPATH_BASE, $lang->getTag(), true);

$defaultColor	= 'primary';
$projectColor	= 'info';
$issueColor		= 'live';
$taskColor		= 'danger';
$timeColor		= 'success';

// DASHBOARD

if($hasClient) {
	// Get Client ID
	$client_id = 0;
	$client_users = '';
	if($hasClient) {
		$query = 'SELECT client_id FROM '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' WHERE user_id = '.$user->id.' AND state = 1';
		$db->setQuery($query);
		$client_id = $db->loadResult();
		// get users IDs of client
		$query = 'SELECT GROUP_CONCAT(user_id) FROM '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' WHERE client_id = '.$client_id.' AND state = 1';
		$db->setQuery($query);
		$client_users = $db->loadResult();
	}
	// filtro de projetos e usuários do cliente
	$cProj = $client_id ? 'client_id = '.$client_id.' AND ' : '';
}

// METRICS
echo '<div class="row">';

	$filterProject_1 = $filterProject_2 = '';
	if($pID) {

		$filterProject_1 = 'project_id = '.$pID.' AND ';
		$filterProject_2 = 'T2.project_id = '.$pID.' AND ';

	} else {

		// QTD. DE PROJETOS ATIVOS
		$projectsClient = ($hasClient) ? ' AND client_id = '.$client_id : '';
		$query = 'SELECT COUNT(*) FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE state = 1'.$projectsClient;
		$db->setQuery($query);
		$projects_actives = $db->loadResult();
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-'.$projectColor.' bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-cogs text-'.$projectColor.' float-left"></span>
						'.$projects_actives.'
						<div class="text-sm text-'.$projectColor.'">'.JText::_('TEXT_PROJECTS_ACTIVES').'</div>
					</div>
				</div>
			</div>
		';

	}


	// QTD. DE SOLICITAÇÕES ABERTAS
	$issuesClient = ($hasClient) ? ' AND author_type = 2' : '';
	$query = 'SELECT COUNT(*) FROM '. $db->quoteName('vw_'.$cfg['project'].'_issues') .' WHERE state = 1'.$issuesClient;
	$db->setQuery($query);
	$issues_actives = $db->loadResult();
	echo '
		<div class="col-sm-6 col-md pb-4">
			<div class="pos-relative rounded b-top-2 b-'.$issueColor.' bg-white set-shadow">
				<div class="h1 m-0 p-3 text-right">
					<span class="base-icon-bell text-'.$issueColor.' float-left"></span>
					'.$issues_actives.'
					<div class="text-sm text-'.$issueColor.'">'.JText::_('TEXT_ISSUES_ACTIVES').'</div>
				</div>
			</div>
		</div>
	';

	// QTD. DE TAREFAS ABERTOS
	$tasksClient = $hasClient ? ' AND visibility = 2 AND client_id = '.$client_id : '';
	$query = 'SELECT COUNT(*) FROM '. $db->quoteName('vw_'.$cfg['project'].'_tasks') .' WHERE '.$filterProject_1.'state = 1 AND visibility > 0'.$tasksClient;
	$db->setQuery($query);
	$tasks_actives = $db->loadResult();
	echo '
		<div class="col-sm-6 col-md pb-4">
			<div class="pos-relative rounded b-top-2 b-'.$taskColor.' bg-white set-shadow">
				<div class="h1 m-0 p-3 text-right">
					<span class="base-icon-tasks text-'.$taskColor.' float-left"></span>
					'.$tasks_actives.'
					<div class="text-sm text-'.$taskColor.'">'.JText::_('TEXT_TASKS_ACTIVES').'</div>
				</div>
			</div>
		</div>
	';

	// QTD. DE TIME WORKED
	if(!$hasClient) {
		$query = '
			SELECT SUM(T1.hours)
			FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks_timer') . ' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_tasks') . ' T2
				ON '.$filterProject_2.'T2.id = T1.task_id AND T2.visibility > 0
		';
		$db->setQuery($query);
		$time_worked = $db->loadResult();
		$time_worked = !empty($time_worked) ? round($time_worked, 0) : 0;
		echo '
			<div class="col-sm-6 col-md pb-4">
				<div class="pos-relative rounded b-top-2 b-'.$timeColor.' bg-white set-shadow">
					<div class="h1 m-0 p-3 text-right">
						<span class="base-icon-clock text-'.$timeColor.' float-left"></span>
						'.$time_worked.'
						<div class="text-sm text-'.$timeColor.'">'.JText::_('TEXT_TIME_WORKED').'</div>
					</div>
				</div>
			</div>
		';
	}

echo '</div>';

// TEAM

	$team = '';
	$teamClient	= ($hasClient) ? $client_id : 1;
	$teamType	= ($hasClient) ? 2 : 0;
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.session_id') .' online
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' T1
			LEFT JOIN '. $db->quoteName('#__session') .' T2
			ON '.$db->quoteName('T2.userid') .' = T1.user_id AND T2.client_id = 0
		WHERE '. $db->quoteName('T1.client_id') .' = '.$teamClient.' AND '. $db->quoteName('T1.type') .' = '.$teamType.' AND T1.state = 1
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
		$role = baseHelper::nameFormat($obj->role);
		if(!empty($role)) $role = '<br />'.$role;
		$info = baseHelper::nameFormat($name).$role.'<br />'.$lStatus;

		// Imagem Principal -> Primeira imagem (index = 0)
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		$img = uploader::getFile('#__brintell_'.$obj->app_table.'_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/'.$obj->app.'/');
		if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$obj->app.'/'.$img['filename'], 32, 32);
		else $imgPath = JURI::root().'images/apps/icons/user_'.$obj->gender.'.png';
		$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:32px; height:32px;" />';

		$team .= '
			<a href="apps/'.$obj->app.'/view?vID='.$obj->user_id.'" class="d-inline-block pos-relative hasTooltip" title="'.$info.'">
				'.$img.$iStatus.'
			</a>
		';
	}
	if(!empty($team) && !$pID) {
		$team = '
			<div class="rounded b-top-2 b-'.$defaultColor.' set-shadow">
				<h6 class="page-header p-2 m-0 text-'.$defaultColor.' base-icon-users"> '.JText::_('TEXT_TEAM').'</h6>
				<div class="px-2 pt-2">'.$team.'</div>
			</div>
		';
	}

// MONTH ACTIVITY

	// Closed Issues
	$issuesClient = ($hasClient) ? ' AND author_type = 2' : '';
	$query = '
		SELECT
			COUNT(*) total,
			(
				SELECT COUNT(*) FROM '. $db->quoteName('vw_'.$cfg['project'].'_issues') .'
				WHERE '.$filterProject_1.' MONTH(created_date) = 2 AND YEAR(created_date) = 2018 AND state = 0 '.$issuesClient.'
			) amount
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_issues') .'
		WHERE '.$filterProject_1.' MONTH(created_date) = 2 AND YEAR(created_date) = 2018 '.$issuesClient.'
	';
	$db->setQuery($query);
	$metricsMonth = $db->loadObject();
	$issuesMonth = '
		<div class="h2 pb-3 b-bottom b-bottom-dashed">
			<span class="text-success">'.$metricsMonth->amount.'</span> <span class="text-muted">/ '.$metricsMonth->total.'</span>
			<div class="text-sm text-'.$issueColor.'">'.JText::_('TEXT_SOLVED').'</div>
		</div>
	';

	// Finished Tasks
	$tasksClient = $hasClient ? ' AND visibility = 2 AND client_id = '.$client_id : '';
	$query = '
		SELECT
			COUNT(*) total,
			(
				SELECT COUNT(*)
				FROM '. $db->quoteName('vw_'.$cfg['project'].'_tasks') .'
				WHERE '.$filterProject_1.'MONTH(created_date) = 2 AND YEAR(created_date) = 2018 AND state = 0 AND visibility > 0'.$tasksClient.'
			) amount
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_tasks') .'
		WHERE '.$filterProject_1.'MONTH(created_date) = 2 AND YEAR(created_date) = 2018 AND visibility > 0'.$tasksClient.'
	';
	$db->setQuery($query);
	$metricsMonth = $db->loadObject();

	$timeMonthly = '';
	if(!$hasClient) {
		// monthly worked time
		$query = '
			SELECT SUM(T1.hours)
			FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks_timer') . ' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_tasks') . ' T2
				ON T2.id = T1.task_id AND T2.visibility > 0
			 WHERE '.$filterProject_2.'MONTH(date) = 2 AND YEAR(date) = 2018
		';
		$db->setQuery($query);
		$t = $db->loadResult();
		if(!empty($t) && $t > 0) {
			$timeMonthly = '
				<div class="h4 m-0 text-right">
					<div class="text-success mb-1 base-icon-clock"> '.$t.'</div>
					<div class="small text-muted">'.JText::_('TEXT_TIME_WORKED').'</div>
				</div>
			';
		}
	}

	$tasksMonth = '
		<div class="h2 pb-3 b-bottom b-bottom-dashed d-flex justify-content-between align-items-center">
			<div>
				<span class="text-success">'.$metricsMonth->amount.'</span> <span class="text-muted">/ '.$metricsMonth->total.'</span>
				<div class="text-sm text-'.$taskColor.'">'.JText::_('TEXT_FINISHED').'</div>
			</div>
			'.$timeMonthly.'
		</div>
	';

// RECENTS

	// Recent Issues
	$issuesClient = ($hasClient) ? ' AND client_id = '.$client_id.' AND author_type = 2' : '';
	$query	= '
		SELECT *
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_issues') .'
		WHERE '. $filterProject_1 .'state = 1'.$issuesClient.'
		ORDER BY '. $db->quoteName('created_date') .' DESC
		LIMIT '.(!$pID ? 4 : 15).'
	';
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();

	$issuesList = '';
	if($num_rows) { // verifica se existe
		$issuesList .= '<ul class="set-list list-sm bordered">';
		foreach($res as $item) {
			$project = (!$pID) ? ' '.JText::_('TEXT_IN').' '.baseHelper::nameFormat($item->project_name) : '';
			$issuesList .= '
				<li>
					<a class="text-'.$issueColor.'" href="'.JURI::root().'apps/issues/view?vID='.$item->id.'">#'.$item->id.' - '.$item->subject.'</a>
					<div class="small text-muted">
						'.baseHelper::dateFormat($item->created_date, 'd.m').' - '.baseHelper::nameFormat($item->author_name).$project.'
					</div>
				</li>
			';
		}
		$issuesList .= '</ul>';
	} else {
		$issuesList .= '<div class="alert alert-warning text-sm p-2 m-0">'.JText::_('TEXT_NO_ISSUES_THIS_MONTH').'</div>';
	}

	$issuesView = '
		<div class="col">
			<div class="p-2 bg-live base-icon-bell"> '.JText::_('TEXT_ISSUES').'</div>
			<div class="p-2">
				'.$issuesMonth.'
				<h6 class="page-header small base-icon-history"> '.JText::_('TEXT_RECENTS').'</h6>
				'.$issuesList.'
			</div>
		</div>
	';

	// Recent Tasks
	$tasksClient = ($hasClient) ? ' AND visibility = 2 AND client_id = '.$client_id : '';
	$query	= '
		SELECT *
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_tasks') .'
		WHERE '. $filterProject_1 .'state = 1 AND visibility > 0 '.$tasksClient.'
		ORDER BY '. $db->quoteName('created_date') .' DESC
		LIMIT '.(!$pID ? 4 : 15).'
	';
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();

	$tasksList = '';
	if($num_rows) { // verifica se existe
		$tasksList .= '<ul class="set-list list-sm bordered">';
		foreach($res as $item) {
			$project = (!$pID) ? ' - '.baseHelper::nameFormat($item->project_name) : '';
			$tasksList .= '
				<li>
					<a class="text-'.$taskColor.'" href="'.JURI::root().'apps/tasks/view?vID='.$item->id.'">#'.$item->id.' - '.$item->subject.'</a>
					<div class="small text-muted">'.baseHelper::dateFormat($item->created_date, 'd.m').$project.'</div>
				</li>
			';
		}
		$tasksList .= '</ul>';
	} else {
		$tasksList .= '<div class="alert alert-warning text-sm p-2 m-0">'.JText::_('TEXT_NO_TASKS_THIS_MONTH').'</div>';
	}

	$months = '';
	for($i = 1; $i <= 12; $i++) {
		$months .= '<option value="'.$i.($i == date('m') ? ' select' : '').'">'.baseHelper::getMonthName($i).'</option>';
	}

	// MONTH AND RECENTS VIEW
	echo '
		<div class="row">
			<div class="col-md">
				<div class="rounded b-top-2 b-'.$projectColor.' mb-4 bg-white set-shadow">
					<h6 class="page-header p-2 m-0 pos-relative">
						'.JText::_('TEXT_MONTHLY').'
						<span class="pos-absolute pos-top-0 pos-right-0">
							<select class="from-control">
								'.$months.'
							</select>
						</span>
					</h6>
					<div class="row no-gutters">
						'.$issuesView.'
						<div class="col'.(!$hasDeveloper ? ' b-left' : '').'">
							<div class="p-2 bg-'.$taskColor.' base-icon-tasks"> '.JText::_('TEXT_TASKS').'</div>
							<div class="p-2">
								'.$tasksMonth.'
								<h6 class="page-header small base-icon-history"> '.JText::_('TEXT_RECENTS').'</h6>
								'.$tasksList.'
							</div>
						</div>
					</div>
				</div>
				'.$team.'
			</div>
	';

// ANIVERSARIANTES BRINTELL

	if(!$hasClient) {
		$query = 'SELECT name, nickname, "0" type, DAY(birthday) day, DAY(NOW()) today FROM '.$db->quoteName('#__'.$cfg['project'].'_staff').' WHERE MONTH(birthday) = MONTH(NOW()) ORDER BY '.$db->quoteName('day').', name';
		try {
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			$res = $db->loadObjectList();
		} catch (RuntimeException $e) {
			 echo $e->getMessage();
			 return;
		}

		$birthdays = '';
		if($num_rows) : // verifica se existe
			$birthdays .= '<ul class="set-list bordered list-trim text-sm text-muted">';
			foreach($res as $item) {

				$today = ($item->day == $item->today);
				$cake = $today ? '<span class="base-icon-birthday text-live"></span> ' : '';
				$day = $item->day < 10 ? '0'.$item->day : $item->day;
				$rowState = $today ? 'text-success' : '';
				$name = !empty($item->nickname) ? $item->nickname : $item->name;
				$birthdays .= '
					<li class="'.$rowState.'">
						<div class="float-right">'.$day.'</div>
						<div class="text-truncate pr-2">'.$cake.baseHelper::nameFormat($name).'</div>
					</li>
				';
			}
			$birthdays .= '</ul>';
		else :
			$birthdays = '<p class="base-icon-info-circled alert alert-info p-2 text-sm m-0"> '.JText::_('MSG_NO_MONTH_BIRTHDAY').'</p>';
		endif;

		// MENSAGEM PARA O ANIVERSARIANTE
		$query = 'SELECT name, nickname FROM '.$db->quoteName('#__'.$cfg['project'].'_staff').' WHERE MONTH(birthday) = MONTH(NOW()) AND DAY(birthday) = DAY(NOW()) AND user_id = '.$user->id;
		$db->setQuery($query);
		$obj = $db->loadObject();
		if(!empty($obj->name)) {
			// Verifica se a mensagem já foi visualizada
			$query = 'SELECT COUNT(*) FROM '.$db->quoteName('#__'.$cfg['project'].'_staff_birthday_message').' WHERE user_id = '.$user->id.' AND viewed_date = CURDATE()';
			$db->setQuery($query);
			$viewed = $db->loadResult();

			if(!$viewed) {
				// Verifica se a mensagem já foi visualizada
				$query = '
					INSERT INTO '. $db->quoteName('#__'.$cfg['project'].'_staff_birthday_message') .' ('.
						$db->quoteName('user_id') .','.
						$db->quoteName('viewed_date')
					.') VALUES ('.$user->id.', NOW())
				';
				$db->setQuery($query);
				$db->execute();

				$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;

				echo '
					<div class="modal fade" id="'.$APPTAG.'modal-birthday-message" tabindex="-1" role="dialog" aria-labelledby="'.$APPTAG.'modal-month-birthdayLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-body">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									'.$error.JText::sprintf('MSG_BIRTHDAY_CONGRATULATIONS', baseHelper::nameFormat($name), 'base-apps/dashboard/birthday.jpg').'
								</div>
							</div>
						</div>
					</div>
					<script>
						jQuery(function() {
							setTimeout(function() {
								jQuery("#'.$APPTAG.'modal-birthday-message").modal("show");
							}, 2000);
						});
					</script>
				';
			}
		}

		if(!$pID) {
			echo '
					<div class="col-md-3">
						<div class="rounded b-top-2 b-live bg-white mb-4 set-shadow">
							<h6 class="page-header p-2 m-0 text-live base-icon-attention"> '.JText::_('TEXT_ALERTS').'</h6>
							<div class="p-2">Coming soon!</div>
						</div>
						<div class="rounded b-top-2 b-'.$defaultColor.' bg-white mb-4 set-shadow">
							<h6 class="page-header p-2 m-0 text-'.$defaultColor.' base-icon-birthday"> '.JText::_('TEXT_MONTH_BIRTHDAY').'</h6>
							<div class="p-2">'.$birthdays.'</div>
						</div>
					</div>
				</div>
			';
		}
	}

?>
