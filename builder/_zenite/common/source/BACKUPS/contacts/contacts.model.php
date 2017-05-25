<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

header( 'Cache-Control: no-cache' );
header( 'content-type: application/json; charset=utf-8' );

  function is_valid_callback($subject) {
      $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

      $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
        'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
        'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
        'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
        'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
        'private', 'public', 'yield', 'interface', 'package', 'protected',
        'static', 'null', 'true', 'false');

      return preg_match($identifier_syntax, $subject)
          && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
  }

  // load Joomla's framework
  require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

  defined('_JEXEC') or die;
  $ajaxRequest = true;
  require('config.php');
  // IMPORTANTE: Carrega o arquivo 'helper' do template
  JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');
  // classes customizadas para usuários Joomla
  JLoader::register('baseUserHelper', JPATH_BASE.'/templates/base/source/helpers/user.php');

  // get current user's data
  $user = JFactory::getUser();
  $groups = $user->groups;

  //joomla get request data
  $input      = $app->input;

  // Default Params
  $APPTAG			= $input->get('aTag', $APPTAG, 'str');
  $RTAG				= $input->get('rTag', $APPTAG, 'str');
  $task       = $input->get('task', null, 'str');
  $data       = array();

  if($task != null) :

  	// database connect
  	$db = JFactory::getDbo();

    // Carrega o arquivo de tradução
    // OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
    // a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
    // Para possibilitar o carregamento da language 'default' de forma dinâmica,
    // é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
    if(isset($_SESSION[$APPTAG.'langDef']))
    $lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

    // params requests
    $id         = $input->get('id', 0, 'int');

  	// fields 'List' requests
    $listIds    = $input->get($APPTAG.'_ids', array(), 'array');
    $ids        = (count($listIds) > 0) ? implode($listIds, ',') : $id;
    $state      = $input->get('st', 2, 'int');
    $pid        = $input->get('pid', 0, 'int');
    $lock       = $input->get('lock', 0, 'int');

    // upload actions
    $fileMsg  = '';
    if($cfg['hasUpload']) :
      $fname    = $input->get('fname', '', 'string');
      $fileId   = $input->get('fileId', 0, 'int');
      // load 'uploader' class
      JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
    endif;

  	// fields 'Form' requests
    $request                 = array();
    // default
    $request['relationId']   = $input->get('relationId', 0, 'int');
  	$request['state']        = $input->get('state', 1, 'int');
    // app
  	$request['user_id']      = $input->get('user_id', 0, 'int');
  	$request['group_id']     = $input->get('group_id', 1, 'int');
  	$request['type']         = $input->get('type', 0, 'int');
  	$request['doc_number']   = $input->get('doc_number', '', 'string');
  	$request['cnpj_contact_name']  = $input->get('cnpj_contact_name', '', 'string');
  	$request['cnpj_contact_cpf']   = $input->get('cnpj_contact_cpf', '', 'string');
  	$request['gender']       = $input->get('gender', 1, 'int');
  	$request['name']         = $input->get('name', '', 'string');
  	$request['nickname']     = $input->get('nickname', '', 'string');
  	$request['email']        = $input->get('email', '', 'string');
  	$request['email_optional'] = $input->get('email_optional', '', 'string');
  	$request['birthday']     = $input->get('birthday', '', 'string');
  	$request['occupation']   = $input->get('occupation', '', 'string');
  	$request['description']  = $input->get('description', '', 'string');
  	$request['portfolio']    = $input->get('portfolio', 0, 'int');
  	$request['comment']      = $input->get('comment', '', 'string');
    // user registration action
  	$request['userRegistration'] = $input->get('userRegistration', 0, 'int');
  	$request['usergroup']    = $input->get('usergroup', $_SESSION[$APPTAG.'newUsertype'], 'int');
  	$request['username']     = $input->get('username', '', 'string');
  	$request['password']     = $input->get('password', '', 'string');
  	$request['password2']    = $input->get('password2', '', 'string');
  	$request['emailConfirm'] = $input->get('emailConfirm', 0, 'int');
  	$request['emailInfo']    = $input->get('emailInfo', '', 'string');

    // CUSTOM -> default vars for registration e-mail
    if($request['emailConfirm'] == 1) :
      $config = JFactory::getConfig();
      $sitename = $config->get('sitename');
      $domain = baseHelper::getDomain();
      $subject = JText::sprintf('MSG_ACTIVATION_EMAIL_SUBJECT', $sitename);
      $mailFrom = $config->get('mailfrom');
    endif;

  	if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

      $num_rows = $isUser = 0;
      if($id) :
        // GET FORM DATA
    		$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
    		$db->setQuery($query);
    		$db->execute();
    		$num_rows = $db->getNumRows();
    		$item = $db->loadObject();
        // VERIFY IF IS REGISTERED USER
        $usr = baseUserHelper::getUserData($item->user_id);
    		$isUser         = $usr['exist'];
        $userInfo       = $usr['obj'];
        // $usr['id']      =
        $userInfoId     = isset($userInfo[0]['id']) ? $userInfo[0]['id'] : 0;
        $userInfoName   = isset($userInfo[0]['name']) ? $userInfo[0]['name'] : '';
        $userInfoEmail  = isset($userInfo[0]['email']) ? $userInfo[0]['email'] : '';
        $userInfoBlock  = isset($userInfo[0]['block']) ? $userInfo[0]['block'] : 0;
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
          $query = 'SELECT * FROM '. $db->quoteName($cfg['fileTable']) .' WHERE '. $db->quoteName('id_parent') .' = '. $id;
      		$db->setQuery($query);
      		$listFiles = $db->loadAssocList();
        endif;
      else :
        // COUNT LIST IDS
    		$query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
    		$db->setQuery($query);
    		$exist = $db->loadResult();
      endif;

  		if($num_rows || $exist) : // verifica se existe

  			// GET DATA
  			if($task == 'get' && $num_rows) :

          $itemUID    = ($isUser) ? $userInfoId : 0;
          $itemName   = ($isUser) ? $userInfoName : $item->name;
          $itemEmail  = ($isUser) ? $userInfoEmail : $item->email;
          $itemBlock  = ($isUser) ? $userInfoBlock : 0;

    			$data[] = array(
            // Default Fields
            'id'		      => $item->id,
    				'state'       => $item->state,
      			'prev'	      => $prev,
        		'next'	      => $next,
            // App Fields
    				'user_id'	    => $item->user_id,
    				'group_id'    => $item->group_id,
    				'type'        => $item->type,
            'doc_number'  => $item->doc_number,
            'cnpj_contact_name' => $item->cnpj_contact_name,
            'cnpj_contact_cpf'  => $item->cnpj_contact_cpf,
    				'gender'	    => $item->gender,
    				'name'	      => $itemName,
    				'nickname'	  => $item->nickname,
    				'email'       => $itemEmail,
    				'email_optional' => $item->email_optional,
    				'birthday'	  => $item->birthday,
    				'occupation'  => $item->occupation,
    				'description' => $item->description,
    				'portfolio'   => $item->portfolio,
    				'comment'     => $item->comment,
            'block'       => $itemBlock,
            'files'       => $listFiles
    			);

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('user_id') 	.'='. $request['user_id'] .','.
            $db->quoteName('group_id') 	.'='. $request['group_id'] .','.
            $db->quoteName('type') 	.'='. $request['type'] .','.
  					$db->quoteName('doc_number') 	.'='. $db->quote($request['doc_number']) .','.
  					$db->quoteName('cnpj_contact_name') 	.'='. $db->quote($request['cnpj_contact_name']) .','.
  					$db->quoteName('cnpj_contact_cpf') 	.'='. $db->quote($request['cnpj_contact_cpf']) .','.
    				$db->quoteName('gender') 	.'='. $request['gender'] .','.
  					$db->quoteName('name') 	.'='. $db->quote($request['name']) .','.
  					$db->quoteName('nickname') 	.'='. $db->quote($request['nickname']) .','.
    				$db->quoteName('email') 	.'='. $db->quote($request['email']) .','.
    				$db->quoteName('email_optional') 	.'='. $db->quote($request['email_optional']) .','.
  					$db->quoteName('birthday') 	.'='. $db->quote($request['birthday']) .','.
  					$db->quoteName('occupation') 	.'='. $db->quote($request['occupation']) .','.
  					$db->quoteName('description') .'='. $db->quote($request['description']) .','.
            $db->quoteName('portfolio') 	.'='. $request['portfolio'] .','.
  					$db->quoteName('comment') .'='. $db->quote($request['comment']) .','.
  					$db->quoteName('state') .'='. $request['state']
  				;
  				$query .= ' WHERE '. $db->quoteName('id') .'='. $id;

  				try {

  					$db->setQuery($query);
  					$db->execute();
            // Upload
            if($cfg['hasUpload'])
            $fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $cfg);

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
            if($request['userRegistration'] == 1 && $request['user_id'] == 0 && !$isUser) :
              // define a senha
              $pwd = ($request['password'] && !empty($request['password'])) ? $request['password'] : baseHelper::randomPassword();
              // prepara os dados
              $isBlock = ($request['state'] == 1) ? 0 : 1;
              // se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
              $bodyData = empty($request['password']) ? JText::sprintf('MSG_ACTIVATION_EMAIL_PWD', $pwd) : JText::_('MSG_ACTIVATION_EMAIL_NOPWD');
              $eBody = JText::sprintf('MSG_ACTIVATION_EMAIL_BODY', baseHelper::nameFormat($request['name']), $domain, $request['email'], $bodyData, $request['username'], $request['emailInfo'], $sitename);
              $mailBody = baseHelper::mailTemplateDefault($eBody, JText::_('MSG_ACTIVATION_EMAIL_TITLE'), '', 'logo-news-team.png');
              // cria o usuário
              $newUserId = baseUserHelper::createJoomlaUser($request['name'], $request['username'], $request['email'], $pwd, $request['usergroup'], $isBlock, $request['emailConfirm'], $mailFrom, $subject, $mailBody);
              if($newUserId) :
                $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = '. $newUserId .' WHERE id = '.$id;
                $db->setQuery($query);
        				$db->execute();
              endif;
            // se existir, atualiza os dados 'name' e 'e-mail' para mantê-los sincronizados
            elseif($isUser) :
              $query = 'UPDATE '. $db->quoteName('#__users') .' SET name = '. $db->quote($request['name']) .', email = '. $db->quote($request['email']) .' WHERE id = '.$userInfoId;
              $db->setQuery($query);
      				$db->execute();
            endif;

  					$data[] = array(
  						'status' => 2,
  						'msg'	=> JText::_('MSG_SAVED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
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
    					'status'=> 0,
    					'msg'	=> $sqlErr,
              'uploadError' => $fileMsg
    				);

  				}

  			// DELETE
  			elseif($task == 'del') :

          // verifica os usuários relacionados ao(s) contato(s)
          $query = 'SELECT '. $db->quoteName('user_id') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
          $db->setQuery($query);
          $uList = $db->loadObjectList();

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
              $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_clients_contacts') .' WHERE '. $db->quoteName('contact_id') .' IN ('.$ids.')';
              $db->setQuery($query);
    					$db->execute();
              // PROJECTS -> remove os registros relacionados aos projetos
              $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_projects_contacts') .' WHERE '. $db->quoteName('contact_id') .' IN ('.$ids.')';
              $db->setQuery($query);
    					$db->execute();
              // TASKS -> remove os registros relacionados às tarefas
              $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_tasks_contacts') .' WHERE '. $db->quoteName('contact_id') .' IN ('.$ids.')';
              $db->setQuery($query);
    					$db->execute();
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
  						'status'=> 3,
              'ids'	=> explode(',', $ids),
  						'msg'	=> JText::_('MSG_DELETED').$userMsg,
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal
  					);

  				} catch (RuntimeException $e) {

  					$data[] = array(
  						'status'=> 0,
  						'msg'	=> $e->getMessage(),
              'uploadError' => $fileMsg
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
            // Bloqueia/desbloqueia o usuário de acordo com o 'state' do contato
            $query = 'SELECT '. $db->quoteName('user_id') .', '. $db->quoteName('state') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
            $db->setQuery($query);
            $uList = $db->loadObjectList();
            foreach ($uList as $usr) {
              if($usr->user_id != 0) baseUserHelper::stateToJoomlaUser($usr->user_id, $usr->state);
            }

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
              'msg'	=> '',
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
            );

          } catch (RuntimeException $e) {

            $data[] = array(
              'status'=> 0,
              'msg'	=> $e->getMessage()
            );

          }

  			// DELETE FILE
  			elseif($cfg['hasUpload'] && $task == 'delFile' && $fname) :

            // FILE: remove o arquivo
            $fileMsg = uploader::deleteFile($fname, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

  					$data[] = array(
  						'status'=> 5,
  						'msg'	=> JText::_('MSG_FILE_DELETED'),
              'uploadError' => $fileMsg
  					);

  			// DELETE FILES
  			elseif($cfg['hasUpload'] && $task == 'delFiles' && $fileId) :

            // FILE: remove o arquivo
            $fileMsg = uploader::deleteFiles($fileId, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

  					$data[] = array(
  						'status'=> 6,
  						'msg'	=> JText::_('MSG_FILE_DELETED'),
              'uploadError' => $fileMsg
  					);

  			endif; // end task

  		endif; // num rows

  	else :

  		// INSERT
  		if($task == 'save') :

        // validation
        if(!empty($request['name']) && $request['group_id'] != 0) :

          // Prepare the insert query
    			$query  = 'INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('user_id') .','.
            $db->quoteName('group_id') .','.
            $db->quoteName('type') .','.
    				$db->quoteName('doc_number') .','.
    				$db->quoteName('cnpj_contact_name') .','.
    				$db->quoteName('cnpj_contact_cpf') .','.
    				$db->quoteName('gender') .','.
    				$db->quoteName('name') .','.
    				$db->quoteName('nickname') .','.
    				$db->quoteName('email') .','.
    				$db->quoteName('email_optional') .','.
    				$db->quoteName('birthday') .','.
    				$db->quoteName('occupation') .','.
    				$db->quoteName('description') .','.
    				$db->quoteName('portfolio') .','.
    				$db->quoteName('comment') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['user_id'] .','.
            $request['group_id'] .','.
            $request['type'] .','.
            $db->quote($request['doc_number']) .','.
            $db->quote($request['cnpj_contact_name']) .','.
            $db->quote($request['cnpj_contact_cpf']) .','.
            $request['gender'] .','.
            $db->quote($request['name']) .','.
            $db->quote($request['nickname']) .','.
            $db->quote($request['email']) .','.
            $db->quote($request['email_optional']) .','.
    				$db->quote($request['birthday']) .','.
            $db->quote($request['occupation']) .','.
    				$db->quote($request['description']) .','.
            $request['portfolio'] .','.
            $db->quote($request['comment']) .','.
    				$request['state'] .','.
    				$user->id
    			.')';

    			try {

    				$db->setQuery($query);
    				$db->execute();
            $id = $db->insertid();
            // Upload
            if($cfg['hasUpload'] && $id)
            $fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $cfg);

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
              $query = 'SELECT '. $db->quoteName($_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE id='.$id.' AND state = 1';
              $db->setQuery($query);
              $elemLabel = $db->loadResult();
            endif;

            // CUSTOM -> user registration
            if($request['userRegistration'] == 1 && $request['user_id'] == 0) :
              // define a senha
              $pwd = ($request['password'] && !empty($request['password'])) ? $request['password'] : baseHelper::randomPassword();
              // prepare data
              $isBlock = ($request['state'] == 1) ? 0 : 1;
              // se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
              $bodyData = empty($request['password']) ? JText::sprintf('MSG_ACTIVATION_EMAIL_PWD', $pwd) : JText::_('MSG_ACTIVATION_EMAIL_NOPWD');
              $eBody = JText::sprintf('MSG_ACTIVATION_EMAIL_BODY', baseHelper::nameFormat($request['name']), $domain, $request['email'], $bodyData, $request['username'], $request['emailInfo'], $sitename);
              $mailBody = baseHelper::mailTemplateDefault($eBody, JText::_('MSG_ACTIVATION_EMAIL_TITLE'), '', 'logo-news-team.png');
              // cria o usuário
              $newUserId = baseUserHelper::createJoomlaUser($request['name'], $request['username'], $request['email'], $pwd, $request['usergroup'], $isBlock, $request['emailConfirm'], $mailFrom, $subject, $mailBody);
              if($newUserId) :
                $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = '. $newUserId .' WHERE id = '.$id;
                $db->setQuery($query);
        				$db->execute();
              endif;
            endif;

    				$data[] = array(
    					'status'=> 1,
    					'msg'	=> JText::_('MSG_SAVED'),
              'regID'	=> $id,
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
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
    					'status'=> 0,
    					'msg'	=> $sqlErr,
              'uploadError' => $fileMsg
    				);

    			}

        else :

          $data[] = array(
            'status'=> 0,
            'msg'	=> JText::_('MSG_ERROR'),
            'uploadError' => $fileMsg
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
          '. $db->quoteName('T2.name') .' uName,
          '. $db->quoteName('T2.email') .' uEmail
        FROM
          '. $db->quoteName($cfg['mainTable']).' T1
          LEFT JOIN '. $db->quoteName('#__users').' T2
          ON T2.id = T1.user_id
        WHERE T1.user_id != 0';
        $db->setQuery($query);
        $cUsers = $db->loadObjectList();
        foreach ($cUsers as $usr) {
          // se o nome do usuário vier vazio significa que o usuário não existe
          if(empty($usr->uName)) :
            // reseta o user_id
            $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = 0 WHERE id = '.$usr->id;
            $db->setQuery($query);
            $db->execute();
          // se um dos dados for diferente entre os sistema, atualiza...
          elseif($usr->cName != $usr->uName || $usr->cEmail != $usr->uEmail) :
            $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET name = UPPER('. $db->quote($usr->uName) .'), email = '. $db->quote($usr->uEmail) .' WHERE id = '.$usr->id;
            $db->setQuery($query);
            $db->execute();
          endif;
        }

        $data[] = array(
          'status' => $query
        );

      // USER BLOCK
      elseif($task == 'userBlock' && $pid != 0) :

        $query = 'UPDATE '. $db->quoteName('#__users') .' SET block = '.$lock.' WHERE id = '.$pid;

        try {

          $db->setQuery($query);
          $db->execute();

          $data[] = array(
            'status' => 1
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
            'status'=> 0,
            'msg'	=> $sqlErr,
            'uploadError' => $fileMsg
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
