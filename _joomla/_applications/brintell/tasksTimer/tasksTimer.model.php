<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	header( 'Cache-Control: no-cache' );
	header( 'content-type: application/json; charset=utf-8' );

	function is_valid_callback($subject) {
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reserved_words = array(
			'break', 'do', 'instanceof', 'typeof', 'case',
			'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
			'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
			'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
			'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
			'private', 'public', 'yield', 'interface', 'package', 'protected',
			'static', 'null', 'true', 'false'
		);

		return preg_match($identifier_syntax, $subject) && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}

	// load Joomla's framework
	// _DIR_ => apps/THIS_APP
	require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	defined('_JEXEC') or die;
	$app = JFactory::getApplication('site');

	$ajaxRequest = true;
	require('config.php');

	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
    // classes customizadas para usuários Joomla
    JLoader::register('baseUserHelper',  JPATH_CORE.DS.'helpers/user.php');

	// get current user's data
	$user		= JFactory::getUser();
	$groups		= $user->groups;

	//joomla get request data
	$input      = $app->input;

	// Default Params
	$APPTAG		= $input->get('aTag', $APPTAG, 'str');
	$RTAG		= $input->get('rTag', $APPTAG, 'str');
	$task       = $input->get('task', null, 'str');
	$data       = array();

	if($task != null) :

		// database connect
		$db = JFactory::getDbo();

		// Carrega o arquivo de tradução
		// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
		// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
		// Para possibilitar o carregamento da language 'default' de forma dinâmica,
		// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
		if(isset($_SESSION[$APPTAG.'langDef'])) :
			$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
			$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
		endif;

		// params requests
		$id         = $input->get('id', 0, 'int');

		// fields 'List' requests
		$listIds    = $input->get($APPTAG.'_ids', array(), 'array');
		$ids        = (count($listIds) > 0) ? implode($listIds, ',') : $id;
		$state      = $input->get('st', 2, 'int');

		// upload actions
		$fileMsg 	= '';
		if($cfg['hasUpload']) :
			$fname		= $input->get('fname', '', 'string');
			$fileId		= $input->get('fileId', 0, 'int');
			// image groups
			$fileGrp	= isset($_POST[$cfg['fileField'].'Group']) ? $_POST[$cfg['fileField'].'Group'] : '';
			$fileGtp	= isset($_POST[$cfg['fileField'].'Gtype']) ? $_POST[$cfg['fileField'].'Gtype'] : '';
			$fileCls	= isset($_POST[$cfg['fileField'].'Class']) ? $_POST[$cfg['fileField'].'Class'] : '';
			// image description
			$fileLbl	= isset($_POST[$cfg['fileField'].'Label']) ? $_POST[$cfg['fileField'].'Label'] : '';
			// load 'uploader' class
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		endif;

		// fields 'Form' requests
		$request						= array();
		// default
		$request['relationId']   		= $input->get('relationId', 0, 'int');
		$request['state']				= $input->get('state', 1, 'int');
		// app
		$request['task_id']      		= $input->get('task_id', 0, 'int');
	    $request['ctask_id']  			= $input->get('ctask_id', 0, 'int');
	  	$request['user_id']      		= $input->get('user_id', 0, 'int');
		if($request['user_id'] == 0) $request['user_id'] = $user->id;
	  	$request['date']         		= $input->get('date', '', 'string');
	  	if(empty($request['date'])) $request['date'] = date('Y-m-d');
		$request['timeType']   			= $input->get('timeType', 0, 'int');
	  	$request['start_hour']   		= $input->get('start_hour', '', 'string');
		if(!empty($request['start_hour'])) $request['start_hour'] = $request['start_hour'].':00';
		else $request['start_hour'] = '00:00:00';
		$request['end_hour']     		= $input->get('end_hour', '', 'string');
		if(!empty($request['end_hour'])) $request['end_hour'] = $request['end_hour'].':00';
		else $request['end_hour'] = '00:00:00';
	  	$request['time']         		= $input->get('time', '', 'string');
		if(empty($request['time'])) $request['time'] = '00:00:00';

		if($request['time'] != '00:00:00') {
			$request['start_hour'] = $request['end_hour'] = '';
		} else {
			// Set Time
			// => Current Time
			$cTime = date('H:i:s');
			if($request['timeType'] == 0) {
				// Insert
				if($id == 0) $request['start_hour'] = $cTime;
				// Update
				else if($request['end_hour'] == '00:00:00') $request['end_hour'] = $cTime;
			}
		}
	  	// get total time
	    $time = array();
	    $total_time = $time['time'] = '00:00:00';
	    $hours = $time['hours'] = 0;
	    if($request['time'] != '00:00:00') :
	      $time = baseHelper::timeDiff('00:00:00', $request['time']);
	      $total_time = $time['time'];
	      $hours = $time['hours'];
	    elseif($request['end_hour'] != '00:00:00') :
	      $time = baseHelper::timeDiff($request['start_hour'], $request['end_hour']);
	      $total_time = $time['time'];
	      $hours = $time['hours'];
	    endif;
		// round hour value
		$hours = str_replace(',', '.', round($hours, 2)); // float em pt-BR usa vírgula ao invés de ponto...
	  	$request['note']         = $input->get('note', '', 'string');

		// SAVE CONDITION
		// Condição para inserção e atualização dos registros
		$save_condition = ($request['task_id'] > 0 && $request['user_id'] > 0 && ($request['start_hour'] != '00:00:00' || $request['time'] != '00:00:00'));

		if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

			$exist = 0;
			if($id) :
				// GET FORM DATA
				$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
				$db->setQuery($query);
				$item	= $db->loadObject();
	    		$exist	= (isset($item->id) && !empty($item->id) && $item->id > 0);
				// get previous ID
				$query = 'SELECT MAX(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' < '. $id;
				$db->setQuery($query);
				$prev = $db->loadResult();
				$prev = !empty($prev) ? $prev : 0;
				// get next ID
				$query = 'SELECT MIN(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' > '. $id;
				$db->setQuery($query);
				$next = $db->loadResult();
				$next = !empty($next) ? $next : 0;
				if($cfg['hasUpload']) :
					// get files
					$query = 'SELECT *, TO_BASE64('. $db->quoteName('filename') .') fn, TO_BASE64('. $db->quoteName('mimetype') .') mt FROM '. $db->quoteName($cfg['fileTable']) .' WHERE '. $db->quoteName('id_parent') .' = '. $id . ' ORDER BY '. $db->quoteName('index');
					$db->setQuery($query);
					$listFiles = $db->loadAssocList();
				endif;
			else :
				// COUNT LIST IDS
				$query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
				$db->setQuery($query);
				$exist = $db->loadResult();
			endif;

			if($exist) : // verifica se existe

				// GET DATA
				if($task == 'get') :

					// Get 'subject' from Task
					$query = 'SELECT `subject` FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' WHERE '. $db->quoteName('id') .' = '.$item->task_id;
					$db->setQuery($query);
					$taskInfo = $db->loadResult();

					$data[] = array(
						// Default Fields
						'id'				=> $item->id,
						'state'				=> $item->state,
						'prev'				=> $prev,
						'next'				=> $next,
						// App Fields
						'task_id'	    	=> $item->task_id,
						'task_info'			=> $taskInfo,
	      				'user_id'	    	=> $item->user_id,
	      				'date'        		=> $item->date,
						'timetype'  		=> $item->timetype,
	      				'start_hour'  		=> $item->start_hour,
	      				'end_hour'    		=> $item->end_hour,
	      				'time'        		=> $item->time,
	      				'total_time'  		=> $item->total_time,
	      				'hours'	      		=> $item->hours,
	      				'note'        		=> $item->note
					);

				// UPDATE
				elseif($task == 'save' && $id) :

					if($save_condition) {

						$timer = 0;
						// Verifica se apenas o tempo inicial é informado (contador ativo)
						if($request['start_hour'] != '00:00:00' && $request['end_hour'] == '00:00:00' && $request['time'] == '00:00:00') {
							// Se for, verifica se já existe algum contador ativo para o mesmo usuário
							$query	= '
								SELECT COUNT(*) FROM '.$db->quoteName($cfg['mainTable']) .'
								WHERE
									'. $db->quoteName('user_id') .' = '. $request['user_id'] .' AND
									'. $db->quoteName('start_hour') .' != "00:00:00" AND
									'. $db->quoteName('end_hour') .' = "00:00:00" AND
									'. $db->quoteName('time') .' = "00:00:00"
							';
							$db->setQuery($query);
							$timer = $db->loadResult();
							// Se houver, verifica se o contador é do mesmo registro (atualização do registro ativo)
							if($timer) {
								$query	= '
									SELECT COUNT(*) FROM '.$db->quoteName($cfg['mainTable']) .'
									WHERE
										'. $db->quoteName('user_id') .' = '. $request['user_id'] .' AND
										'. $db->quoteName('start_hour') .' != "00:00:00" AND
										'. $db->quoteName('end_hour') .' = "00:00:00" AND
										'. $db->quoteName('time') .' = "00:00:00" AND
										'. $db->quoteName('id') .' = '.$id.'
								';
								$db->setQuery($query);
								$ok = $db->loadResult();
								// Se o contador ativo for do mesmo registro, libera a atualização
								if($ok) $timer = 0;
							}
						}

						// save if haven't timer count active or complete time
						if(!$timer) {

							$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
							$query .=
								// $db->quoteName('task_id') 		.'='. $request['task_id'] .','.
					            // $db->quoteName('user_id') 		.'='. $request['user_id'] .','.
			  					$db->quoteName('date')			.'='. $db->quote($request['date']) .','.
								$db->quoteName('timeType') 		.'='. $request['timeType'] .','.
			  					$db->quoteName('start_hour') 	.'='. $db->quote($request['start_hour']) .','.
			  					$db->quoteName('end_hour') 		.'='. $db->quote($request['end_hour']) .','.
			  					$db->quoteName('time') 			.'='. $db->quote($request['time']) .','.
			  					$db->quoteName('total_time') 	.'='. $db->quote($total_time) .','.
			  					$db->quoteName('hours') 		.'='. $db->quote($hours) .','.
								$db->quoteName('state')			.'='. $request['state'] .','.
								$db->quoteName('alter_date')	.'= NOW(),'.
								$db->quoteName('alter_by')		.'='. $user->id
							;
							$query .= ' WHERE '. $db->quoteName('id') .'='. $id;

							try {

								$db->setQuery($query);
								$db->execute();

								// Upload
								if($cfg['hasUpload'])
								$fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $fileGrp, $fileGtp, $fileCls, $fileLbl, $cfg);

								// UPDATE FIELD
								$element = $elemVal = $elemLabel = '';
								if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
									$element = $_SESSION[$RTAG.'FieldUpdated'];
									$elemVal = $id;
									$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$id.' AND state = 1';
									$db->setQuery($query);
									$elemLabel = $db->loadResult();
								endif;

								$data[] = array(
									'status'			=> 2,
									'msg'				=> JText::_('MSG_SAVED'),
									'uploadError'		=> $fileMsg,
									'parentField'		=> $element,
									'parentFieldVal'	=> $elemVal,
									'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
								);

							} catch (RuntimeException $e) {

								// Error treatment
								switch($e->getCode()) {
									case '1062':
									$sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
									break;
									default:
									$sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
								}

								$data[] = array(
									'status'			=> 0,
									'msg'				=> $sqlErr,
									'uploadError'		=> $fileMsg
								);

							}

						} else {

							$data[] = array(
								'status'				=> 0,
								'msg'					=> JText::_('MSG_TIME_COUNT_ACTIVE')
							);

						}

					} else {

						$data[] = array(
							'status'				=> 0,
							'msg'					=> JText::_('MSG_ERROR'),
							'uploadError'			=> $fileMsg
						);

					}

				// DELETE
				elseif($task == 'del') :

					$query = 'DELETE FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

					try {

						$db->setQuery($query);
						$db->execute();

						// FILE: remove o(s) arquivo(s)
						if($cfg['hasUpload'] && !empty($ids) && $ids != 0)
						$fileMsg = uploader::deleteFiles($ids, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

						// DELETE RELATIONSHIP
						if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'AppNameId'])) :
							$query = 'DELETE FROM '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' WHERE '. $db->quoteName($_SESSION[$RTAG.'AppNameId']) .' IN ('.$ids.')';
							$db->setQuery($query);
							$db->execute();
						endif;
						// FORCE DELETE RELATIONSHIPS
						// força a exclusão do(s) relacionamento(s) caso os parâmetros não sejam setados
						// isso é RECOMENDÁVEL sempre que houver um ou mais relacionamentos
						// SAMPLES -> remove os registros relacionados aos exemplos
						// $query = 'DELETE FROM '. $db->quoteName('#__'.$cfg['project'].'_app_sample') .' WHERE '. $db->quoteName('type_id') .' IN ('.$ids.')';
						// $db->setQuery($query);
						// $db->execute();

						// UPDATE FIELD
						// executa apenas com valores individuais
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $ids;
						endif;

						$setIds = explode(',', $ids);
						if(count($setIds) > 1) :
							$_SESSION[$APPTAG.'baseAlert']['message'] = JText::_('MSG_ITEMS_DELETED_SUCCESS');
							$_SESSION[$APPTAG.'baseAlert']['context'] = 'success';
						endif;

						$data[] = array(
							'status'			=> 3,
							'ids'				=> $setIds,
							'msg'				=> JText::_('MSG_DELETED'),
							'uploadError'		=> $fileMsg,
							'parentField'		=> $element,
							'parentFieldVal'	=> $elemVal
						);

					} catch (RuntimeException $e) {

						$data[] = array(
							'status'			=> 0,
							'msg'				=> $e->getMessage(),
							'uploadError'		=> $fileMsg
						);

					}

				// STATE
				elseif($task == 'state') :

					$stateVal = ($state == 2 ? 'IF(state = 1, 0, 1)' : $state);
					$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.', '. $db->quoteName('alter_date') .' = NOW() WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

					try {

						$db->setQuery($query);
						$db->execute();

						// UPDATE FIELD
						// executa apenas com valores individuais
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $ids;
							$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$ids;
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

						$setIds = explode(',', $ids);
						if(count($setIds) > 1) :
							$_SESSION[$APPTAG.'baseAlert']['message'] = JText::_('MSG_ITEMS_ALTER_SUCCESS');
							$_SESSION[$APPTAG.'baseAlert']['context'] = 'success';
						endif;

						$data[] = array(
							'status'			=> 4,
							'state'				=> $state,
							'ids'				=> $setIds,
							'msg'				=> '',
							'parentField'		=> $element,
							'parentFieldVal'	=> $elemVal,
							'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
						);

					} catch (RuntimeException $e) {

						$data[] = array(
							'status'			=> 0,
							'msg'				=> $e->getMessage()
						);

					}

				// DELETE FILE
				elseif($cfg['hasUpload'] && $task == 'delFile' && $fname) :

					// FILE: remove o arquivo
					$fileMsg = uploader::deleteFile($fname, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

					$data[] = array(
						'status'				=> 5,
						'msg'					=> JText::_('MSG_FILE_DELETED'),
						'uploadError'			=> $fileMsg
					);

					// DELETE FILES
					elseif($cfg['hasUpload'] && $task == 'delFiles' && $fileId) :

					// FILE: remove o arquivo
					$fileMsg = uploader::deleteFiles($fileId, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

					$data[] = array(
						'status'				=> 6,
						'msg'					=> JText::_('MSG_FILES_DELETED'),
						'uploadError'			=> $fileMsg
					);

				endif; // end task

			endif; // num rows

		else :

			// INSERT
			if($task == 'save') :

				// validation
				if($save_condition) :

					// CHECK IF EXIST A TIMER COUNT IN ACTIVITY
					$query	= '
						SELECT COUNT(*) FROM '.$db->quoteName($cfg['mainTable']) .'
						WHERE
							'. $db->quoteName('user_id') .' = '. $request['user_id'] .' AND
							'. $db->quoteName('start_hour') .' != "00:00:00" AND
							'. $db->quoteName('end_hour') .' = "00:00:00" AND
							'. $db->quoteName('time') .' = "00:00:00"
					';
					$db->setQuery($query);
					$timer = $db->loadResult();

					// verifica se será registrado o tempo completo
					// início & fim <ou> tempo total
					$timeClosed = (($request['start_hour'] != '00:00:00' && $request['end_hour'] != '00:00:00') || $request['time'] != '00:00:00');

					// save if haven't timer count active or complete time
					if(!$timer || $timeClosed) {
						// Prepare the insert query
						$query  = '
							INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
								$db->quoteName('task_id') .','.
					            $db->quoteName('user_id') .','.
			    				$db->quoteName('date') .','.
								$db->quoteName('timeType') .','.
			    				$db->quoteName('start_hour') .','.
			    				$db->quoteName('end_hour') .','.
			    				$db->quoteName('time') .','.
			    				$db->quoteName('total_time') .','.
			    				$db->quoteName('hours') .','.
			    				$db->quoteName('note') .','.
			    				$db->quoteName('state') .','.
			    				$db->quoteName('created_by')
							.') VALUES ('.
								$request['task_id'] .','.
								$request['user_id'] .','.
								$db->quote($request['date']) .','.
								$request['timeType'] .','.
								$db->quote($request['start_hour']) .','.
								$db->quote($request['end_hour']) .','.
								$db->quote($request['time']) .','.
								$db->quote($total_time) .','.
								$hours .','.
								$db->quote($request['note']) .','.
								$request['state'] .','.
								$user->id
							.')
						';

						try {

							$db->setQuery($query);
							$db->execute();
							$id = $db->insertid();
							// Upload
							if($cfg['hasUpload'] && $id)
							$fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $fileGrp, $fileGtp, $fileCls, $fileLbl, $cfg);

							// CREATE RELATIONSHIP
							if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelNameId']) && !empty($_SESSION[$RTAG.'AppNameId']) && !empty($request['relationId'])) :
								$query  = '
									INSERT INTO '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .'('.
									$db->quoteName($_SESSION[$RTAG.'AppNameId']) .','.
									$db->quoteName($_SESSION[$RTAG.'RelNameId'])
									.') VALUES ('.
									$id .','.
									$request['relationId']
								.')';
								$db->setQuery($query);
								$db->execute();
							endif;

							// UPDATE FIELD
							$element = $elemVal = $elemLabel = '';
							if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
								$element = $_SESSION[$RTAG.'FieldUpdated'];
								$elemVal = $id;
								$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$id.' AND state = 1';
								$db->setQuery($query);
								$elemLabel = $db->loadResult();
							endif;

							$data[] = array(
								'status'			=> 1,
								'msg'				=> JText::_('MSG_SAVED'),
								'regID'				=> $id,
								'uploadError'		=> $fileMsg,
								'parentField'		=> $element,
								'parentFieldVal'	=> $elemVal,
								'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
							);

						} catch (RuntimeException $e) {

							// Error treatment
							switch($e->getCode()) {
								case '1062':
								$sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
								break;
								default:
								$sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
							}

							$data[] = array(
								'status'			=> 0,
								'msg'				=> $sqlErr,
								'uploadError'		=> $fileMsg
							);

						}

					} else {

						$data[] = array(
							'status'				=> 0,
							'msg'					=> JText::_('MSG_TIME_COUNT_ACTIVE')
						);

					}

				else :

					$data[] = array(
						'status'				=> 0,
						'msg'					=> JText::_('MSG_ERROR'),
						'uploadError'			=> $fileMsg
					);

				endif; // end validation

			// CUSTOM: get tasks list to 'tasksTimer'
			elseif($task == 'tList' && $state != 2) :

				$taskFilter = (!$state) ? 'FIND_IN_SET ('.$user->id.', T1.assign_to) OR T1.created_by = '.$user->id.' AND ' : '';
				$query = '
					SELECT T1.*
					FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' T1
					WHERE '.$taskFilter.'T1.state = 1 ORDER BY T1.id DESC
				';

				try {
					$db->setQuery($query);
					$db->execute();
					$num_itens = $db->getNumRows();
					$list = $db->loadObjectList();

					if($num_itens) :
						foreach($list as $item) {
							$data[] = array(
								// Default Fields
								'status'		=> 1,
								// App Fields
								'id'			=> $item->id,
								'subject'		=> $item->subject
							);
						}
					else :
						$data[] = array(
							'status'			=> 0
						);
					endif;

				} catch (RuntimeException $e) {

					$data[] = array(
						'status'				=> 0,
						'msg'					=> $e->getMessage()
					);

				}

			endif; // end 'task'

		endif; // end 'id'

		$json = json_encode($data);

		# JSON if no callback
		if(!isset($_GET['callback'])) exit($json);

		# JSONP if valid callback
		if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

		# Otherwise, bad request
		header('status: 400 Bad Request', true, 400);

	endif;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
