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
    $request['name']         = $input->get('name', '', 'string');
    $request['name_card']    = $input->get('name_card', '', 'string');
  	$request['email']        = $input->get('email', '', 'string');
  	$request['cpf']          = $input->get('cpf', '', 'string');
  	$request['rg']           = $input->get('rg', '', 'string');
  	$request['rg_orgao']     = $input->get('rg_orgao', '', 'string');
  	$request['gender']       = $input->get('gender', 1, 'int');
  	$request['birthday']     = $input->get('birthday', '', 'string');
  	$request['place_birth']  = $input->get('place_birth', '', 'string');
  	$request['marital_status'] = $input->get('marital_status', '', 'string');
  	$request['partner']      = $input->get('partner', '', 'string');
  	$request['children']     = $input->get('children', 0, 'int');
  	$request['mother_name']  = $input->get('mother_name', '', 'string');
  	$request['father_name']  = $input->get('father_name', '', 'string');
  	$request['card_limit']   = $input->get('card_limit', '', 'string');
  	$request['cx_email']  = $input->get('cx_email', '', 'string');
    $cx_email = $request['cx_email'];
    if(!empty($cx_email)) :
      $cx_email = (strpos($cx_email, '@') === false) ? $cx_email.'@caixa.gov.br' : $cx_email;
    endif;
  	$request['cx_matricula'] = $input->get('cx_matricula', '', 'string');
  	$request['cx_admissao']  = $input->get('cx_admissao', '', 'string');
  	$request['cx_lotacao']   = $input->get('cx_lotacao', '', 'string');
  	$request['cx_cargo']     = $input->get('cx_cargo', '', 'string');
    // user registration action
      // get 'client code' -> 'username' -> only INSERT
      if(empty($id) || $id == 0) :
        $query = 'SELECT '. $db->quoteName('code').' FROM '. $db->quoteName('#__apcefpb_clients_code');
        $db->setQuery($query);
        $code = $db->loadResult();
        $username = baseHelper::lengthFixed($code, 6);
      else :
        $code = $input->get('code', '', 'string');
        $username = baseHelper::lengthFixed($code, 6);
      endif;
  	$request['usergroup']    = $input->get('usergroup', $_SESSION[$APPTAG.'newUsertype'], 'int');
  	$request['password']     = $input->get('password', '', 'string');
  	$request['password2']    = $input->get('password2', '', 'string');
  	$request['emailConfirm'] = $input->get('emailConfirm', '', 'string');

    // CUSTOM -> default vars for registration e-mail
    $config = JFactory::getConfig();
    $sitename = $config->get('sitename');
    $domain = baseHelper::getDomain();
    $subject = JText::sprintf('MSG_ACTIVATION_EMAIL_SUBJECT', $sitename);
    $mailFrom = $config->get('mailfrom');

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

    			$data[] = array(
            // Default Fields
            'id'		      => $item->id,
    				'state'       => $item->state,
      			'prev'	      => $prev,
        		'next'	      => $next,
            // App Fields
    				'user_id'	    => $item->user_id,
            'code'	      => $item->code,
            'usergroup'	  => $item->usergroup,
            'name'	      => $itemName,
            'name_card'	  => $item->name_card,
    				'email'       => $itemEmail,
    				'cpf'         => $item->cpf,
            'rg'          => $item->rg,
            'rg_orgao'    => $item->rg_orgao,
    				'gender'	    => $item->gender,
    				'birthday'	  => $item->birthday,
            'place_birth'	=> $item->place_birth,
            'marital_status'=> $item->marital_status,
            'partner'	    => $item->partner,
            'children'	  => $item->children,
            'mother_name'	=> $item->mother_name,
            'father_name'	=> $item->father_name,
            'card_limit'	=> $item->card_limit,
            // remove '@caixa.gov.br'
    				'cx_email'    => (!empty($item->cx_email) ? substr($item->cx_email, 0, strpos($item->cx_email, '@')) : ''),
            'cx_matricula'=> $item->cx_matricula,
            'cx_admissao'	=> $item->cx_admissao,
            'cx_lotacao'	=> $item->cx_lotacao,
    				'cx_cargo'    => $item->cx_cargo,
            'files'       => $listFiles
    			);

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('code') 	.'='. $username .','.
            $db->quoteName('user_id') 	.'='. $request['user_id'] .','.
            $db->quoteName('usergroup') 	.'='. $request['usergroup'] .','.
            $db->quoteName('name') 	.'='. $db->quote($request['name']) .','.
            $db->quoteName('name_card') 	.'='. $db->quote($request['name_card']) .','.
    				$db->quoteName('email') 	.'='. $db->quote($request['email']) .','.
  					$db->quoteName('cpf') 	.'='. $db->quote($request['cpf']) .','.
            $db->quoteName('rg') 	.'='. $db->quote($request['rg']) .','.
            $db->quoteName('rg_orgao') 	.'='. $db->quote($request['rg_orgao']) .','.
            $db->quoteName('gender') 	.'='. $request['gender'] .','.
  					$db->quoteName('birthday') 	.'='. $db->quote($request['birthday']) .','.
            $db->quoteName('place_birth') 	.'='. $db->quote($request['place_birth']) .','.
            $db->quoteName('marital_status') 	.'='. $db->quote($request['marital_status']) .','.
  					$db->quoteName('partner') 	.'='. $db->quote($request['partner']) .','.
            $db->quoteName('children') 	.'='. $request['children'] .','.
            $db->quoteName('mother_name') 	.'='. $db->quote($request['mother_name']) .','.
            $db->quoteName('father_name') 	.'='. $db->quote($request['father_name']) .','.
            $db->quoteName('card_limit') 	.'='. $db->quote($request['card_limit']) .','.
            $db->quoteName('cx_email') 	.'='. $db->quote($cx_email) .','.
            $db->quoteName('cx_matricula') 	.'='. $db->quote($request['cx_matricula']) .','.
            $db->quoteName('cx_admissao') 	.'='. $db->quote($request['cx_admissao']) .','.
            $db->quoteName('cx_lotacao') 	.'='. $db->quote($request['cx_lotacao']) .','.
            $db->quoteName('cx_cargo') 	.'='. $db->quote($request['cx_cargo']) .','.
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

            // CUSTOM -> update user registration
            // cria o usuário se não existir
            $query = 'UPDATE '. $db->quoteName('#__users') .' SET name = '. $db->quote($request['name']) .', email = '. $db->quote($request['email']) .' WHERE id = '.$userInfoId;
            $db->setQuery($query);
    				$db->execute();

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

              $rIDs = explode(',', $ids);
              foreach ($rIDs as $rID) {

                // ADDRESSES -> remove os registros relacionados aos endereços
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__apcefpb_rel_clients_addresses') .' T1
                  JOIN '. $db->quoteName('#__apcefpb_addresses') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.address_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui os endereços
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_addresses') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
        					$db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_clients_addresses') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
      					$db->execute();

                // PHONES -> remove os registros relacionados aos telefones
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__apcefpb_rel_clients_phones') .' T1
                  JOIN '. $db->quoteName('#__apcefpb_phones') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.phone_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui os telefones
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_phones') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
        					$db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_clients_phones') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
      					$db->execute();

                // WEB SOCIALS -> remove os registros relacionados às redes sociais
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__apcefpb_rel_clients_socials') .' T1
                  JOIN '. $db->quoteName('#__apcefpb_web_socials') .' T2
                  ON '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui as redes sociais
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_web_socials') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
                  $db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_clients_socials') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $db->execute();

                // ACCOUNT BANKS -> remove os registros relacionados às contas bancárias
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__apcefpb_rel_clients_banksAccounts') .' T1
                  JOIN '. $db->quoteName('#__apcefpb_banks_accounts') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.bankAccount_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui as contas bancárias
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_banks_accounts') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
        					$db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_clients_banksAccounts') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
      					$db->execute();

                // DEPENDENTS -> remove os registros relacionados aos dependentes
                $query = 'SELECT id FROM '. $db->quoteName('#__apcefpb_dependents') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $depIds = $db->loadColumn();

                foreach ($depIds as $depID) {

                  // exclui os dependentes
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_dependents') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                  $db->setQuery($query);
        					$db->execute();
                  // remove as fotos dos dependentes
                  $fileDepMsg = uploader::deleteFiles($depID, '#__apcefpb_dependents_files', JPATH_BASE.'images/uploads/dependents/', JText::_('MSG_FILEERRODEL'));

                  // ADDRESSES -> remove os registros relacionados aos endereços
                  $query = '
                  SELECT T2.id FROM
                    '. $db->quoteName('#__apcefpb_rel_dependents_addresses') .' T1
                    JOIN '. $db->quoteName('#__apcefpb_addresses') .' T2
                    ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.address_id') .'
                    WHERE '. $db->quoteName('T1.dependent_id') .' = '.$depID
                  ;
                  $db->setQuery($query);
                  $relId = $db->loadColumn();
                  $dIDs = implode(',', $relId);
                  if(!empty($dIDs)) :
                    // exclui os endereços
                    $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_addresses') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                    $db->setQuery($query);
          					$db->execute();
                  endif;
                  // exclui o relacionamento
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_dependents_addresses') .' WHERE '. $db->quoteName('dependent_id') .' = '.$depID;
                  $db->setQuery($query);
        					$db->execute();

                  // PHONES -> remove os registros relacionados aos telefones
                  $query = '
                  SELECT T2.id FROM
                    '. $db->quoteName('#__apcefpb_rel_dependents_phones') .' T1
                    JOIN '. $db->quoteName('#__apcefpb_phones') .' T2
                    ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.phone_id') .'
                    WHERE '. $db->quoteName('T1.dependent_id') .' = '.$depID
                  ;
                  $db->setQuery($query);
                  $relId = $db->loadColumn();
                  $dIDs = implode(',', $relId);
                  if(!empty($dIDs)) :
                    // exclui os telefones
                    $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_phones') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                    $db->setQuery($query);
          					$db->execute();
                  endif;
                  // exclui o relacionamento
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_dependents_phones') .' WHERE '. $db->quoteName('dependent_id') .' = '.$depID;
                  $db->setQuery($query);
        					$db->execute();

                  // WEB SOCIALS -> remove os registros relacionados às redes sociais
                  $query = '
                  SELECT T2.id FROM
                    '. $db->quoteName('#__apcefpb_rel_dependents_socials') .' T1
                    JOIN '. $db->quoteName('#__apcefpb_web_socials') .' T2
                    ON '. $db->quoteName('T1.dependent_id') .' = '.$depID
                  ;
                  $db->setQuery($query);
                  $relId = $db->loadColumn();
                  $dIDs = implode(',', $relId);
                  if(!empty($dIDs)) :
                    // exclui as redes sociais
                    $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_web_socials') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                    $db->setQuery($query);
          					$db->execute();
                  endif;
                  // exclui o relacionamento
                  $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_dependents_socials') .' WHERE '. $db->quoteName('dependent_id') .' = '.$depID;
                  $db->setQuery($query);
        					$db->execute();

                }
              }
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
  						'msg'	=> JText::_('MSG_DELETED').$userMsg.implode(',', $depIds),
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

			// DEFINE O ESTADO DOS RELACIONAMENTOS
            $query = 'SELECT '. $db->quoteName('id') .', '. $db->quoteName('user_id') .', '. $db->quoteName('state') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
            $db->setQuery($query);
            $uList = $db->loadObjectList();
            foreach ($uList as $usr) {
				// ALTER USER STATE (block)
	            // Bloqueia/desbloqueia o usuário de acordo com o 'state' do contato
	            if($usr->user_id != 0) baseUserHelper::stateToJoomlaUser($usr->user_id, $usr->state);
  				// MOVIMENTAÇÕES FIXAS (PAI)
				// Bloqueia/desbloqueia as movimentações fixas 'pai'
				$query = 'UPDATE '. $db->quoteName('#__apcefpb_transactions') .' SET '. $db->quoteName('state') .' = '.$usr->state.' WHERE fixed = 1 AND '. $db->quoteName('client_id') .' = '. $usr->id;
				$db->setQuery($query);
				$db->execute();
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
        if(!empty($request['name']) && !empty($request['email'])) :

          // Prepare the insert query
    			$query  = 'INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('user_id') .','.
            $db->quoteName('code') .','.
            $db->quoteName('usergroup') .','.
            $db->quoteName('name') .','.
            $db->quoteName('name_card') .','.
    				$db->quoteName('email') .','.
            $db->quoteName('cpf') .','.
    				$db->quoteName('rg') .','.
    				$db->quoteName('rg_orgao') .','.
    				$db->quoteName('gender') .','.
    				$db->quoteName('birthday') .','.
            $db->quoteName('place_birth') .','.
            $db->quoteName('marital_status') .','.
            $db->quoteName('partner') .','.
            $db->quoteName('children') .','.
            $db->quoteName('mother_name') .','.
            $db->quoteName('father_name') .','.
            $db->quoteName('card_limit') .','.
    				$db->quoteName('cx_email') .','.
            $db->quoteName('cx_matricula') .','.
            $db->quoteName('cx_admissao') .','.
            $db->quoteName('cx_lotacao') .','.
            $db->quoteName('cx_cargo') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['user_id'] .','.
            $db->quote($username) .','.
            $request['usergroup'] .','.
            $db->quote($request['name']) .','.
            $db->quote($request['name_card']) .','.
            $db->quote($request['email']) .','.
            $db->quote($request['cpf']) .','.
            $db->quote($request['rg']) .','.
            $db->quote($request['rg_orgao']) .','.
    				$request['gender'] .','.
            $db->quote($request['birthday']) .','.
            $db->quote($request['place_birth']) .','.
            $db->quote($request['marital_status']) .','.
            $db->quote($request['partner']) .','.
            $request['children'] .','.
            $db->quote($request['mother_name']) .','.
            $db->quote($request['father_name']) .','.
            $db->quote($request['card_limit']) .','.
            $db->quote($cx_email) .','.
            $db->quote($request['cx_matricula']) .','.
            $db->quote($request['cx_admissao']) .','.
            $db->quote($request['cx_lotacao']) .','.
            $db->quote($request['cx_cargo']) .','.
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
            if(!empty($request['email'])) :
              // define a senha
              $pwd = ($request['password'] && !empty($request['password'])) ? $request['password'] : baseHelper::randomPassword();
              // prepare data
              $isBlock = ($request['state'] == 1) ? 0 : 1;
              // se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
              $bodyData = empty($request['password']) ? JText::sprintf('MSG_ACTIVATION_EMAIL_PWD', $pwd) : JText::_('MSG_ACTIVATION_EMAIL_NOPWD');
              $eBody = JText::sprintf('MSG_ACTIVATION_EMAIL_BODY', $request['name'], $domain, $request['email'], $bodyData, $username, $sitename);
              $mailBody = baseHelper::mailTemplateDefault($eBody, JText::_('MSG_ACTIVATION_EMAIL_TITLE'));
              // cria o usuário
              $newUserId = baseUserHelper::createJoomlaUser(baseHelper::nameFormat($request['name']), $username, $request['email'], $pwd, $request['usergroup'], $isBlock, $request['emailConfirm'], $mailFrom, $subject, $mailBody);
              // atribui o usuário ao contato
              if($newUserId) :
                $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = '. $newUserId .' WHERE id = '.$id;
                $db->setQuery($query);
        				$db->execute();
                // UPDATE 'CLIENT' CODE
                $query = 'UPDATE '. $db->quoteName('#__apcefpb_clients_code') .' SET '. $db->quoteName('code').' = '.($code + 1);
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
          '. $db->quoteName('T1.state') .' cState,
          '. $db->quoteName('T2.name') .' uName,
          '. $db->quoteName('T2.email') .' uEmail,
          '. $db->quoteName('T2.block') .' uState
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
            $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET user_id = 0, state = 0 WHERE id = '.$usr->id;
            $db->setQuery($query);
            $db->execute();
          // se um dos dados for diferente entre os sistema, atualiza...
          elseif($usr->cName != $usr->uName || $usr->cEmail != $usr->uEmail) :
            $newState = ($usr->uState == 1) ? 0 : 1;
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
