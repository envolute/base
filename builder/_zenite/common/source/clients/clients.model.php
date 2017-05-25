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
  	$request['type']         = $input->get('type', 1, 'int');
  	$request['name']         = $input->get('name', '', 'string');
  	$request['name_company'] = $input->get('name_company', '', 'string');
  	$request['email']        = $input->get('email', '', 'string');
  	$request['cemail']       = $input->get('cemail', '', 'string');
  	$request['email_optional'] = $input->get('email_optional', '', 'string');
  	$request['doc_number']   = $input->get('doc_number', '', 'string');
  	$request['description']  = $input->get('description', '', 'string');
  	$request['date']         = $input->get('date', date('Y-m-d'), 'string');
    $request['date'] = !empty($request['date']) ? $request['date'] : date('Y-m-d');
  	$request['note']         = $input->get('note', '', 'string');

  	if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

      $num_rows = 0;
      if($id) :
        // GET FORM DATA
    		$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
    		$db->setQuery($query);
    		$db->execute();
    		$num_rows = $db->getNumRows();
    		$list = $db->loadObjectList();
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
  			if($task == 'get' && $list) :

          foreach($list as $item) {
      			$data[] = array(
              // Default Fields
              'id'		      => $item->id,
      				'state'       => $item->state,
        			'prev'	      => $prev,
          		'next'	      => $next,
              // App Fields
      				'type'	      => $item->type,
      				'name'	      => $item->name,
      				'name_company'=> $item->name_company,
      				'email'       => $item->email,
      				'email_optional' => $item->email_optional,
      				'doc_number'  => $item->doc_number,
      				'description' => $item->description,
      				'date'	      => $item->date,
      				'note'	      => $item->note,
              'files'       => $listFiles
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('type') 	.'='. $request['type'] .','.
            $db->quoteName('name') 	.'='. $db->quote($request['name']) .','.
    				$db->quoteName('name_company') 	.'='. $db->quote($request['name_company']) .','.
    				$db->quoteName('email') 	.'='. $db->quote($request['email']) .','.
    				$db->quoteName('email_optional') 	.'='. $db->quote($request['email_optional']) .','.
  					$db->quoteName('doc_number') 	.'='. $db->quote($request['doc_number']) .','.
  					$db->quoteName('description') .'='. $db->quote($request['description']) .','.
  					$db->quoteName('date') 	.'='. $db->quote($request['date']) .','.
  					$db->quoteName('note') 	.'='. $db->quote($request['note']) .','.
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
                  '. $db->quoteName('#__zenite_rel_clients_addresses') .' T1
                  JOIN '. $db->quoteName('#__zenite_addresses') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.address_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui os endereços
                  $query = 'DELETE FROM '. $db->quoteName('#__zenite_addresses') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
                  $db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_clients_addresses') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $db->execute();

                // PHONES -> remove os registros relacionados aos telefones
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__zenite_rel_clients_phones') .' T1
                  JOIN '. $db->quoteName('#__zenite_phones') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.phone_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui os telefones
                  $query = 'DELETE FROM '. $db->quoteName('#__zenite_phones') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
                  $db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_clients_phones') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $db->execute();

                // WEB SOCIALS -> remove os registros relacionados às redes sociais
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__zenite_rel_clients_socials') .' T1
                  JOIN '. $db->quoteName('#__zenite_web_socials') .' T2
                  ON '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui as redes sociais
                  $query = 'DELETE FROM '. $db->quoteName('#__zenite_web_socials') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
                  $db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_clients_socials') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $db->execute();

                // ACCOUNT BANKS -> remove os registros relacionados às contas bancárias
                $query = '
                SELECT T2.id FROM
                  '. $db->quoteName('#__zenite_rel_clients_banksAccounts') .' T1
                  JOIN '. $db->quoteName('#__zenite_banks_accounts') .' T2
                  ON  '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.bankAccount_id') .'
                  WHERE '. $db->quoteName('T1.client_id') .' = '.$rID
                ;
                $db->setQuery($query);
                $relId = $db->loadColumn();
                $dIDs = implode(',', $relId);
                if(!empty($dIDs)) :
                  // exclui as contas bancárias
                  $query = 'DELETE FROM '. $db->quoteName('#__zenite_banks_accounts') .' WHERE '. $db->quoteName('id') .' IN ('.$dIDs.')';
                  $db->setQuery($query);
                  $db->execute();
                endif;
                // exclui o relacionamento
                $query = 'DELETE FROM '. $db->quoteName('#__zenite_rel_clients_banksAccounts') .' WHERE '. $db->quoteName('client_id') .' = '.$rID;
                $db->setQuery($query);
                $db->execute();

              }
            endif;

            // UPDATE FIELD
            // executa apenas com valores individuais
            $element = $elemVal = $elemLabel = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $ids;
            endif;

  					$data[] = array(
  						'status'=> 3,
              'ids'	=> explode(',', $ids),
  						'msg'	=> JText::_('MSG_DELETED'),
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
        if(!empty($request['name'])) :

          // Prepare the insert query
    			$query  = 'INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('type') .','.
            $db->quoteName('name') .','.
    				$db->quoteName('name_company') .','.
    				$db->quoteName('email') .','.
    				$db->quoteName('email_optional') .','.
    				$db->quoteName('doc_number') .','.
    				$db->quoteName('description') .','.
    				$db->quoteName('date') .','.
    				$db->quoteName('note') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['type'] .','.
            $db->quote($request['name']) .','.
            $db->quote($request['name_company']) .','.
            $db->quote($request['email']) .','.
            $db->quote($request['email_optional']) .','.
            $db->quote($request['doc_number']) .','.
    				$db->quote($request['description']) .','.
    				$db->quote($request['date']) .','.
    				$db->quote($request['note']) .','.
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
