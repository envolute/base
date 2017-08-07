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
		$request['relationId']			= $input->get('relationId', 0, 'int');
		$request['state']				= $input->get('state', 1, 'int');
		// app
		$request['newsUser']			= $input->get('newsUser', 0, 'int');
		$request['user_id']				= $input->get('user_id', 0, 'int');
			// Define o usuário
			$userID							= $request['newsUser'] ? $request['newsUser'] : $request['user_id'];
		$request['name']				= $input->get('name', '', 'string');
		$request['email']				= $input->get('email', '', 'string');
		$request['cpf']					= $input->get('cpf', '', 'string');
		$request['rg']					= $input->get('rg', '', 'string');
		$request['rg_orgao']			= $input->get('rg_orgao', '', 'string');
		$request['gender']				= $input->get('gender', 1, 'int');
		$request['birthday']			= $input->get('birthday', '', 'string');
		$request['marital_status']		= $input->get('marital_status', '', 'string');
		$request['partner']				= $input->get('partner', '', 'string');
	  	$request['cx_email']			= $input->get('cx_email', '', 'string');
			// formata o email da caixa
		    $cx_email = $request['cx_email'];
		    if(!empty($cx_email)) $cx_email = (strpos($cx_email, '@') === false) ? $cx_email.'@caixa.gov.br' : $cx_email;
		$request['cx_code']				= $input->get('cx_code', '', 'string');
		$request['cx_role']				= $input->get('cx_role', '', 'string');
		$request['cx_situated']			= $input->get('cx_situated', '', 'string');
		$request['cx_date']				= $input->get('cx_date', '', 'string');
		$request['zip_code']			= $input->get('zip_code', '', 'string');
		$request['address']				= $input->get('address', '', 'string');
		$request['address_number']		= $input->get('address_number', '', 'string');
		$request['address_info']		= $input->get('address_info', '', 'string');
		$request['address_district']	= $input->get('address_district', '', 'string');
		$request['address_city']		= $input->get('address_city', '', 'string');
		$request['phones']				= isset($_POST['phone']) ? implode(',', $_POST['phone']) : '';
		$request['agency']				= $input->get('agency', '', 'string');
		$request['account']				= $input->get('account', '', 'string');
		$request['operation']			= $input->get('operation', '', 'string');
	    // user registration action
	  	$request['access']				= $input->get('access', 0, 'int');
	    $username						= $request['cx_code'];
	  	$usergroup						= $_SESSION[$APPTAG.'newUsertype'];
	  	$request['password']			= $input->get('password', '', 'string');
	  	$request['repassword']			= $input->get('repassword', '', 'string');
	  	$request['emailConfirm']		= $input->get('emailConfirm', 0, 'int');
	  	$request['emailInfo']			= $input->get('emailInfo', '', 'string');
	  	$request['reasonStatus']		= $input->get('reasonStatus', '', 'string');
		// Se o acesso for liberado, limpa o valor do campo 'motivo'
		$reason = $request['access'] ? '' : $request['reasonStatus'];

	    // CUSTOM -> default vars for registration e-mail
	    if($request['emailConfirm'] == 1) :
		    $config			= JFactory::getConfig();
		    $sitename		= $config->get('sitename');
		    $domain			= baseHelper::getDomain();
		    $subject		= JText::sprintf('MSG_ACTIVATION_EMAIL_SUBJECT', $sitename);
		    $mailFrom		= $config->get('mailfrom');
		endif;

		// SAVE CONDITION
		// Condição para inserção e atualização dos registros
		$save_condition = (!empty($request['name']) && !empty($request['email']));

		if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

			$exist = 0;
			if($id) :
				// GET FORM DATA
				$query	= 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
				$db->setQuery($query);
				$item	= $db->loadObject();
	    		$exist	= (isset($item->id) && !empty($item->id) && $item->id > 0);
				// CUSTOM -> VERIFY IF IS REGISTERED USER
				$isUser = $userInfoId = $userInfoBlock = 0;
				$userInfoName = $userInfoEmail = '';
				if($item->user_id != 0) :
					$usr			= baseUserHelper::getUserData($item->user_id);
					$isUser         = $usr['exist'];
					$userInfo       = $usr['obj'];
					// $usr['id']      =
					$userInfoId     = isset($userInfo[0]['id']) ? $userInfo[0]['id'] : 0;
					$userInfoName   = isset($userInfo[0]['name']) ? $userInfo[0]['name'] : '';
					$userInfoEmail  = isset($userInfo[0]['email']) ? $userInfo[0]['email'] : '';
			        $userInfoBlock  = isset($userInfo[0]['block']) ? $userInfo[0]['block'] : 0;
				endif;
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

				// GET FORM DATA
				if($task == 'get') :

					$itemUID    = ($isUser) ? $userInfoId : 0;
					$itemName   = ($isUser) ? $userInfoName : $item->name;
					$itemEmail  = ($isUser) ? $userInfoEmail : $item->email;
					$itemBlock  = ($isUser) ? $userInfoBlock : 1; // inverso do 'access'
					// Obs: Se 'block' = 1 / 'access' = 0;
					// O padrão do 'Acesso' na edição é 'Não => 0' ('block = 1'),
					// pois caso não exista um usuário associado ao cliente
					// o campo de acesso aparece como falso 'Não'

					$data[] = array(
						// Default Fields
						'id'				=> $item->id,
						'state'				=> $item->state,
						'prev'				=> $prev,
						'next'				=> $next,
						// App Fields
						'user_id'			=> $itemUID,
						'name'				=> $itemName,
						'email'				=> $itemEmail,
						'cpf'				=> $item->cpf,
						'rg'				=> $item->rg,
						'rg_orgao'			=> $item->rg_orgao,
						'gender'			=> $item->gender,
						'birthday'			=> $item->birthday,
						'marital_status'	=> $item->marital_status,
						'partner'			=> $item->partner,
						// remove '@caixa.gov.br'
	    				'cx_email'			=> (!empty($item->cx_email) ? substr($item->cx_email, 0, strpos($item->cx_email, '@')) : ''),
						'cx_code'			=> $item->cx_code,
			            'cx_role'			=> $item->cx_role,
						'cx_situated'		=> $item->cx_situated,
						'cx_date'			=> $item->cx_date,
						'zip_code'			=> $item->zip_code,
						'address'			=> $item->address,
						'address_number'	=> $item->address_number,
						'address_info'		=> $item->address_info,
						'address_district'	=> $item->address_district,
						'address_city'		=> $item->address_city,
						'phones'			=> $item->phones,
						'agency'			=> $item->agency,
						'account'			=> $item->account,
						'operation'			=> $item->operation,
						'access'			=> ($itemBlock ? 0 : 1),
						'reasonStatus'		=> $item->reasonStatus,
						'files'				=> $listFiles
					);

				// UPDATE
				elseif($task == 'save' && $save_condition && $id) :

					$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
					$query .=
						$db->quoteName('user_id')			.'='. $userID .','.
						$db->quoteName('name')				.'='. $db->quote($request['name']) .','.
						$db->quoteName('email')				.'='. $db->quote($request['email']) .','.
						$db->quoteName('cpf')				.'='. $db->quote($request['cpf']) .','.
						$db->quoteName('rg')				.'='. $db->quote($request['rg']) .','.
						$db->quoteName('rg_orgao')			.'='. $db->quote($request['rg_orgao']) .','.
						$db->quoteName('gender')			.'='. $request['gender'] .','.
						$db->quoteName('birthday')			.'='. $db->quote($request['birthday']) .','.
						$db->quoteName('marital_status') 	.'='. $db->quote($request['marital_status']) .','.
						$db->quoteName('partner')			.'='. $db->quote($request['partner']) .','.
						$db->quoteName('cx_code')			.'='. $db->quote($request['cx_code']) .','.
						$db->quoteName('cx_email')			.'='. $db->quote($cx_email) .','.
						$db->quoteName('cx_role')			.'='. $db->quote($request['cx_role']) .','.
						$db->quoteName('cx_situated')		.'='. $db->quote($request['cx_situated']) .','.
						$db->quoteName('cx_date')			.'='. $db->quote($request['cx_date']) .','.
						$db->quoteName('zip_code')			.'='. $db->quote($request['zip_code']) .','.
						$db->quoteName('address')			.'='. $db->quote($request['address']) .','.
						$db->quoteName('address_number')	.'='. $db->quote($request['address_number']) .','.
						$db->quoteName('address_info')		.'='. $db->quote($request['address_info']) .','.
						$db->quoteName('address_district')	.'='. $db->quote($request['address_district']) .','.
						$db->quoteName('address_city')		.'='. $db->quote($request['address_city']) .','.
						$db->quoteName('phones')			.'='. $db->quote($request['phones']) .','.
						$db->quoteName('agency')			.'='. $db->quote($request['agency']) .','.
						$db->quoteName('account')			.'='. $db->quote($request['account']) .','.
						$db->quoteName('operation')			.'='. $db->quote($request['operation']) .','.
						$db->quoteName('access')			.'='. $request['access'] .','.
						$db->quoteName('reasonStatus')		.'='. $db->quote($reason) .','.
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
							$query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

						// CUSTOM -> user registration
						// cria o usuário se não existir
						if($request['access'] == 1) :
							if($userID == 0 && !$isUser) :
								// define a senha
								$pwd = ($request['password'] && !empty($request['password'])) ? $request['password'] : baseHelper::randomPassword();
								// prepara os dados
								$isBlock = ($request['state'] == 1) ? 0 : 1;
								// se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
								$bodyData = empty($request['password']) ? JText::sprintf('MSG_ACTIVATION_EMAIL_PWD', $pwd) : JText::_('MSG_ACTIVATION_EMAIL_NOPWD');
								$emailInfo = !empty($request['emailInfo']) ? '<p>'.$request['emailInfo'].'</p>' : '';
								$eBody = JText::sprintf('MSG_ACTIVATION_EMAIL_BODY', baseHelper::nameFormat($request['name']), $domain, $request['email'], $bodyData, $emailInfo);
								// Email Template
								$boxStyle	= array('bg' => '#eee', 'color' => '#555', 'border' => '3px solid #303b4d');
								$headStyle	= array('bg' => '#303b4d', 'color' => '#fff', 'border' => 'none');
								$bodyStyle	= array('bg' => '#fff');
								$mailLogo	= 'logo-news.png';
								$mailHtml	= baseHelper::mailTemplateDefault($eBody, JText::_('MSG_ACTIVATION_EMAIL_TITLE'), '', $mailLogo, $boxStyle, $headStyle, $bodyStyle);
								// cria o usuário
								$newUserId = baseUserHelper::createJoomlaUser($request['name'], $username, $request['email'], $pwd, $usergroup, $isBlock, $request['emailConfirm'], $mailFrom, $subject, $mailHtml);
								if($newUserId) :
									$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = '. $newUserId .' WHERE id = '.$id;
									$db->setQuery($query);
									$db->execute();
								endif;
							// se existir, atualiza os dados 'name' e 'e-mail' para mantê-los sincronizados
							elseif($isUser && $userInfoId) :
								// verifica se ha atualização de senha
								if(!empty($request['password']) && ($request['password'] == $request['repassword'])) :
									$newPass  = ', password = '. $db->quote(JUserHelper::hashPassword($request['password']));
								endif;
								// Atualiza os dados so usuário
								$query = 'UPDATE '. $db->quoteName('#__users') .' SET name = '. $db->quote($request['name']) .', email = '. $db->quote($request['email']). $newPass .', block = 0 WHERE id = '.$userInfoId;
								$db->setQuery($query);
								$db->execute();
							endif;
						elseif($isUser && $userInfoId) :
							baseUserHelper::stateToJoomlaUser($userInfoId, 0);
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
						else :
							// FORCE DELETE RELATIONSHIPS
							// força a exclusão do(s) relacionamento(s) caso os parâmetros não sejam setados
							// isso é RECOMENDÁVEL sempre que houver um ou mais relacionamentos
							// CLIENTS -> remove os registros relacionados aos clientes
							// $query = 'DELETE FROM '. $db->quoteName('#__escob_clients') .' WHERE '. $db->quoteName('phone_id') .' IN ('.$ids.')';
							// $db->setQuery($query);
							// $db->execute();
						endif;

						// UPDATE FIELD
						// executa apenas com valores individuais
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $ids;
						endif;

			            // DELETE USER REGISTERED
			            $userMsg = '';
						foreach ($uList as $usr) {
							if($usr->user_id != 0) {
								if(baseUserHelper::deleteJoomlaUser($usr->user_id)) $userMsg = JText::_('MSG_USER_DELETED');
							}
						}

						$data[] = array(
							'status'			=> 3,
							'ids'				=> explode(',', $ids),
							'msg'				=> JText::_('MSG_DELETED').$userMsg,
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
							$query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$ids;
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

			            // ALTER USER STATE (block)
			            // Bloqueia/desbloqueia o usuário de acordo com o 'state' do cliente
						$query = 'SELECT '. $db->quoteName('user_id') .', '. $db->quoteName('state') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
						$db->setQuery($query);
						$uList = $db->loadObjectList();
						foreach ($uList as $usr) {
							if($usr->user_id != 0) baseUserHelper::stateToJoomlaUser($usr->user_id, $usr->state);
						}

						$data[] = array(
							'status'			=> 4,
							'state'				=> $state,
							'ids'				=> explode(',', $ids),
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

					if(empty($fileMsg)) {
						// IMPORTANTE: Reorganiza a ordem
						// remove os saltos entre os "index", pois não deve haver!!
						$sIndex = $cfg['indexFileInit'] - 1;
						uploader::rebuildIndexFiles($cfg['fileTable'], $id, $sIndex);
					}

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
							$db->quoteName('user_id') .','.
							$db->quoteName('name') .','.
							$db->quoteName('email') .','.
							$db->quoteName('cpf') .','.
							$db->quoteName('rg') .','.
							$db->quoteName('rg_orgao') .','.
							$db->quoteName('gender') .','.
							$db->quoteName('birthday') .','.
							$db->quoteName('marital_status') .','.
							$db->quoteName('partner') .','.
							$db->quoteName('cx_email') .','.
							$db->quoteName('cx_code') .','.
							$db->quoteName('cx_role') .','.
							$db->quoteName('cx_situated') .','.
							$db->quoteName('cx_date') .','.
							$db->quoteName('zip_code') .','.
							$db->quoteName('address') .','.
							$db->quoteName('address_number') .','.
							$db->quoteName('address_info') .','.
							$db->quoteName('address_district') .','.
							$db->quoteName('address_city') .','.
							$db->quoteName('phones') .','.
							$db->quoteName('agency') .','.
							$db->quoteName('account') .','.
							$db->quoteName('operation') .','.
							$db->quoteName('access') .','.
							$db->quoteName('reasonStatus') .','.
							$db->quoteName('state') .','.
							$db->quoteName('created_by')
						.') VALUES ('.
							$userID .','.
							$db->quote($request['name']) .','.
							$db->quote($request['email']) .','.
							$db->quote($request['cpf']) .','.
							$db->quote($request['rg']) .','.
							$db->quote($request['rg_orgao']) .','.
							$request['gender'] .','.
							$db->quote($request['birthday']) .','.
							$db->quote($request['marital_status']) .','.
							$db->quote($request['partner']) .','.
							$db->quote($cx_email) .','.
							$db->quote($request['cx_code']) .','.
							$db->quote($request['cx_role']) .','.
							$db->quote($request['cx_situated']) .','.
							$db->quote($request['cx_date']) .','.
							$db->quote($request['zip_code']) .','.
							$db->quote($request['address']) .','.
							$db->quote($request['address_number']) .','.
							$db->quote($request['address_info']) .','.
							$db->quote($request['address_district']) .','.
							$db->quote($request['address_city']) .','.
							$db->quote($request['phones']) .','.
							$db->quote($request['agency']) .','.
							$db->quote($request['account']) .','.
							$db->quote($request['operation']) .','.
							$request['access'] .','.
							$db->quote($reason) .','.
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
								.')
							';
							$db->setQuery($query);
							$db->execute();
						endif;

						// UPDATE FIELD
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $id;
							$query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

						// CUSTOM -> user registration
						if($request['access'] == 1) :
							if($userID == 0) :
								// define a senha
								$pwd = ($request['password'] && !empty($request['password'])) ? $request['password'] : baseHelper::randomPassword();
								// prepare data
								$isBlock = ($request['state'] == 1) ? 0 : 1;
								// se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
								$bodyData = empty($request['password']) ? JText::sprintf('MSG_ACTIVATION_EMAIL_PWD', $pwd) : JText::_('MSG_ACTIVATION_EMAIL_NOPWD');
								$eBody = JText::sprintf('MSG_ACTIVATION_EMAIL_BODY', $request['name'], $domain, $request['email'], $bodyData, $username, $sitename);
								// Email Template
								$boxStyle	= array('bg' => '#eee', 'color' => '#555', 'border' => '3px solid #303b4d');
								$headStyle	= array('bg' => '#303b4d', 'color' => '#fff', 'border' => 'none');
								$bodyStyle	= array('bg' => '#fff');
								$mailLogo	= 'logo-news.png';
								$mailHtml	= baseHelper::mailTemplateDefault($eBody, JText::_('MSG_ACTIVATION_EMAIL_TITLE'), '', $mailLogo, $boxStyle, $headStyle, $bodyStyle);
								// cria o usuário
								$newUserId = baseUserHelper::createJoomlaUser(baseHelper::nameFormat($request['name']), $username, $request['email'], $pwd, $usergroup, $isBlock, $request['emailConfirm'], $mailFrom, $subject, $mailHtml);
								// atribui o usuário ao cliente
								if($newUserId) :
									$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = '. $newUserId .' WHERE id = '.$id;
									$db->setQuery($query);
									$db->execute();
								endif;
							// se existir, atualiza os dados 'name' e 'e-mail' para mantê-los sincronizados
							else :
								$query = 'UPDATE '. $db->quoteName('#__users') .' SET name = '. $db->quote($request['name']) .', email = '. $db->quote($request['email']) .' WHERE id = '.$userID;
								$db->setQuery($query);
								$db->execute();
							endif;
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

			// USERS SYNCRONIZE
			elseif($task == 'userSync') :

				// seleciona os contatos com relação
				$query = '
					SELECT
						'. $db->quoteName('T1.id') .',
						'. $db->quoteName('T1.user_id') .',
						'. $db->quoteName('T1.name') .' cName,
						'. $db->quoteName('T1.email') .' cEmail,
						'. $db->quoteName('T1.state') .' cState,
						'. $db->quoteName('T2.name') .' uName,
						'. $db->quoteName('T2.email') .' uEmail,
						'. $db->quoteName('T2.block') .' uBlock
					FROM
						'. $db->quoteName($cfg['mainTable']).' T1
						LEFT JOIN '. $db->quoteName('#__users').' T2
						ON T2.id = T1.user_id
					WHERE T1.user_id != 0
				';
				$db->setQuery($query);
				$cUsers = $db->loadObjectList();
				foreach ($cUsers as $usr) {
					// se o nome do usuário vier vazio significa que o usuário não existe
					if(empty($usr->uName)) :
						// reseta o user_id
						$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = 0, state = 0 WHERE id = '.$usr->id;
						$db->setQuery($query);
						$db->execute();
					// se um dos dados for diferente entre os sistema, atualiza...
					elseif($usr->cName != $usr->uName || $usr->cEmail != $usr->uEmail) :
						// verifica se o usuário está bloqueado 'uBlock == 1'
						$newState = ($usr->uBlock == 1) ? 0 : 1;
						$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET name = UPPER('. $db->quote($usr->uName) .'), email = '. $db->quote($usr->uEmail) .', state = '. $newState .' WHERE id = '.$usr->id;
						$db->setQuery($query);
						$db->execute();
					endif;
				}

				$data[] = array(
					'status' => $query
				);

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
