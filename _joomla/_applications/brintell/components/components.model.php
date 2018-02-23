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
		$request['group_id']			= $input->get('group_id', 0, 'int');
		$request['name']				= $input->get('name', '', 'string');
		$request['url']					= $input->get('url', '', 'string');
		$request['description']			= $input->get('description', '', 'raw');
		$request['config_code']			= $input->get('config_code', '', 'raw');

		// SAVE CONDITION
		// Condição para inserção e atualização dos registros
		$save_condition = !empty($request['name']);

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

					$data[] = array(
						// Default Fields
						'id'				=> $item->id,
						'state'				=> $item->state,
						'prev'				=> $prev,
						'next'				=> $next,
						// App Fields
						'group_id'			=> $item->group_id,
						'name'				=> $item->name,
						'url'				=> $item->url,
						'description'		=> $item->description,
						'config_code'		=> $item->config_code,
						'files'				=> $listFiles
					);

				// UPDATE
				elseif($task == 'save' && $save_condition && $id) :

					$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
					$query .=
						$db->quoteName('group_id')			.'='. $request['group_id'] .','.
						$db->quoteName('name')				.'='. $db->quote($request['name']) .','.
						$db->quoteName('url')				.'='. $db->quote($request['url']) .','.
						$db->quoteName('description')		.'='. $db->quote($request['description']) .','.
						$db->quoteName('config_code')		.'='. $db->quote($request['config_code']) .','.
						$db->quoteName('state')				.'='. $request['state'] .','.
						$db->quoteName('alter_date')		.'= NOW(),'.
						$db->quoteName('alter_by')			.'='. $user->id
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

						$rIDs = explode(',', $ids);
						foreach ($rIDs as $rID) {

							// ACCOUNT BANKS -> remove os registros relacionados às contas bancárias
							// $query = '
							// 	SELECT T2.id
							// 	FROM '. $db->quoteName('#__base_rel_contacts_banksAccounts') .' T1
							// 		JOIN '. $db->quoteName('#__base_banks_accounts') .' T2
							// 		ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.bankAccount_id') .'
							// 	WHERE '. $db->quoteName('T1.contact_id') .' = '.$rID
							// ;
							// $db->setQuery($query);
							// $relId = $db->loadColumn();
							// $dIDs = implode(',', $relId);
							// if(!empty($dIDs)) :
							// 	// exclui as contas
							// 	$query = 'DELETE FROM '. $db->quoteName('#__base_banks_accounts') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
							// 	$db->setQuery($query);
							// 	$db->execute();
							// endif;
							// // exclui o relacionamento
							// $query = 'DELETE FROM '. $db->quoteName('#__base_rel_contacts_banksAccounts') .' WHERE '. $db->quoteName('contact_id') .' = '.$rID;
							// $db->setQuery($query);
							// $db->execute();

						}

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
					$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

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

					// Prepare the insert query
					$query  = '
						INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
							$db->quoteName('group_id') .','.
							$db->quoteName('name') .','.
							$db->quoteName('url') .','.
							$db->quoteName('description') .','.
							$db->quoteName('config_code') .','.
							$db->quoteName('state') .','.
							$db->quoteName('created_by')
						.') VALUES ('.
							$request['group_id'] .','.
							$db->quote($request['name']) .','.
							$db->quote($request['url']) .','.
							$db->quote($request['description']) .','.
							$db->quote($request['config_code']) .','.
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

				else :

					$data[] = array(
						'status'				=> 0,
						'msg'					=> JText::_('MSG_ERROR'),
						'uploadError'			=> $fileMsg
					);

				endif; // end validation

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
