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
    $request['client_id']    = $input->get('client_id', 0, 'int');
    $request['contact_id']   = $input->get('contact_id', 0, 'int');
  	$request['place_id']     = $input->get('place_id', 0, 'int');
  	$request['confirmed']    = $input->get('confirmed', 0, 'int');
  	$request['date']         = $input->get('date', '', 'string');
  	$request['time']         = $input->get('time', '', 'string');
  	$request['amount']       = $input->get('amount', 0, 'int');
  	$request['guests']       = $input->get('guests', 0, 'int');
  	$request['price']        = $input->get('price', 0.00, 'float');
  	$request['phones']       = $input->get('phones', '', 'string');
  	$request['note']         = $input->get('note', '', 'string');
  	$request['client_alert'] = $input->get('client_alert', 0, 'int');
    $request['extra_info']   = $input->get('extra_info', '', 'string');
    $extra_info = !empty($request['extra_info']) ? '<p>'.$request['extra_info'].'</p>' : '';

    // CUSTOM
    // get user data
    $query = '';
    if($request['client_id'] != 0) :
      $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_clients') .' WHERE '. $db->quoteName('id') .' = '. $request['client_id'];
    elseif($request['contact_id'] != 0) :
      $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_contacts') .' WHERE '. $db->quoteName('id') .' = '. $request['contact_id'];
    endif;
    if(!empty($query)) :
      $db->setQuery($query);
      $u = $db->loadObject();
      $name = $u->name;
      $email = $u->email;
    endif;

    if($request['client_alert'] == 1) :
      // default vars for registration e-mail
      $config = JFactory::getConfig();
      $sitename = $config->get('sitename');
      $domain = baseHelper::getDomain();
      $subjectReceive = JText::sprintf('MSG_RECEIVE_EMAIL_SUBJECT', $sitename);
      $subjectConfirm = JText::sprintf('MSG_CONFIRM_EMAIL_SUBJECT', $sitename);
      $mailFrom = $config->get('mailfrom');
    endif;

    function setConfirm($itemID, $mailFrom, $domain, $sitename, $subjectConfirm, $extra_info) {

      // dados da reserva
      $query = '
      SELECT
        T1.id,
        T2.name place,
        T3.name client,
        T3.email emailClient,
        T4.name contact,
        T4.email emailContact,
        T1.confirmed,
        T1.date,
        T1.time,
        T1.amount,
        T1.guests,
        T1.price,
        T1.phones,
        T1.note,
        T1.state
      FROM
        '. $db->quoteName($cfg['mainTable']) .' T1
        JOIN '. $db->quoteName('#__apcefpb_places') .' T2
        ON T2.id = T1.place_id
        LEFT JOIN '. $db->quoteName('#__apcefpb_clients') .' T3
        ON T3.id = T1.client_id
        LEFT JOIN '. $db->quoteName('#__apcefpb_contacts') .' T4
        ON T4.id = T1.contact_id
      WHERE T1.id = '.$itemID;
      $db->setQuery($query);
      $conf = $db->loadObject();

      $name = !empty($conf->client) ? $conf->client : $conf->contact;
      $email = !empty($conf->emailClient) ? $conf->emailClient : $conf->emailContact;
      $local = $conf->place;
      $dt = baseHelper::dateFormat($conf->date);
      if(!empty($conf->time)) $periodo = '<strong>Tempo</strong>: '.substr($conf->time, 0, 2).' hs';
      else if($conf->amount > 0) $periodo = '<strong>Diárias</strong>: '.$conf->amount;
      $convidados = $conf->guests;
      if(!empty($email)) :
        $eBody = JText::sprintf('MSG_CONFIRM_EMAIL_BODY', $name, $name, $local, $dt, $periodo, $convidados, $request['extra_info']);
        $eBody .= JText::sprintf('MSG_CONFIRM_EMAIL_FOOTER', $sitename);
        // Send activation email
        $mailer = JFactory::getMailer();
        $mailer->setSender($mailFrom);
        $mailer->addRecipient($email);
        $mailer->setSubject($subjectConfirm);
        $mailer->setBody($eBody);
        $mailer->isHTML();
        return $mailer->send() ? true : false;
      else :
        return false;
      endif;

    }

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
              'client_id'   => $item->client_id,
              'contact_id'  => $item->contact_id,
      				'place_id'    => $item->place_id,
      				'confirmed'   => $item->confirmed,
      				'date'	      => $item->date,
      				'time'        => $item->time,
      				'amount'	    => $item->amount,
      				'guests'	    => $item->guests,
      				'price'	      => $item->price,
      				'phones'	    => $item->phones,
      				'note'	      => $item->note
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('client_id') 	.'='. $request['client_id'] .','.
            $db->quoteName('contact_id') 	.'='. $request['contact_id'] .','.
            $db->quoteName('place_id') 	.'='. $request['place_id'] .','.
  					$db->quoteName('confirmed') .'='. $request['confirmed'] .','.
            $db->quoteName('date') 	.'='. $db->quote($request['date']) .','.
  					$db->quoteName('time') .'='. $db->quote($request['time']) .','.
            $db->quoteName('amount') 	.'='. $request['amount'] .','.
            $db->quoteName('guests') 	.'='. $request['guests'] .','.
  					$db->quoteName('price') .'='. $db->quote($request['price']) .','.
  					$db->quoteName('phones') .'='. $db->quote($request['phones']) .','.
  					$db->quoteName('note') .'='. $db->quote($request['note']) .','.
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
              // CLIENTS -> remove os registros relacionados aos clientes
              // $query = 'DELETE FROM '. $db->quoteName('#__apcefpb_rel_clients_phones') .' WHERE '. $db->quoteName('phone_id') .' IN ('.$ids.')';
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
  						'msg'	=> JText::_('MSG_FILES_DELETED'),
              'uploadError' => $fileMsg
  					);

        // STATE
        elseif($task == 'confirm') :

          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('confirmed') .' = '.$state.' WHERE '. $db->quoteName('id') .' = '.$id;

          try {
            $db->setQuery($query);
            $db->execute();

            if($request['client_alert'] == 1) setConfirm($id, $mailFrom, $domain, $sitename, $subjectConfirm, $extra_info);

            $data[] = array(
              'status' => 7,
              'state' => $state,
              'id'	=> $id,
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

        endif; // end task

  		endif; // num rows

  	else :

  		// INSERT
  		if($task == 'save') :

        // validation
        if($request['place_id'] != 0) :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('client_id') .','.
            $db->quoteName('contact_id') .','.
            $db->quoteName('place_id') .','.
            $db->quoteName('confirmed') .','.
    				$db->quoteName('date') .','.
    				$db->quoteName('time') .','.
    				$db->quoteName('amount') .','.
    				$db->quoteName('guests') .','.
    				$db->quoteName('price') .','.
    				$db->quoteName('phones') .','.
    				$db->quoteName('note') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['client_id'] .','.
            $request['contact_id'] .','.
            $request['place_id'] .','.
    				$request['confirmed'] .','.
            $db->quote($request['date']) .','.
    				$db->quote($request['time']) .','.
    				$request['amount'] .','.
    				$request['guests'] .','.
    				$db->quote($request['price']) .','.
    				$db->quote($request['phones']) .','.
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

            // CUSTOM -> send e-mail admin
            if(!empty($email) && $request['client_alert'] == 1) :

              if($request['confirmed'] == 1) :

                setConfirm($id, $mailFrom, $domain, $sitename, $subjectConfirm, $extra_info);

              else :

                // dados da reserva
                $query = 'SELECT '. $db->quoteName('name') .' FROM '. $db->quoteName('#__apcefpb_places').' WHERE id = '.$request['place_id'];
                $db->setQuery($query);
                $local = $db->loadResult();
                $dt = baseHelper::dateFormat($request['date']);
                if(!empty($request['time'])) $periodo = '<strong>Tempo</strong>: '.substr($request['time'], 0, 2).' hs';
                else if($request['amount'] > 0) $periodo = '<strong>Diárias</strong>: '.$request['amount'];
                $convidados = $request['guests'];
                $obs = $request['note'];

                $eBody = JText::sprintf('MSG_RECEIVE_EMAIL_BODY', $domain, $name, $local, $dt, $periodo, $convidados, $obs);
                // Send activation email
                $mailer = JFactory::getMailer();
                $mailer->setSubject($subjectReceive);
                // admin alert
                $mailer->setSender($mailFrom);
                $mailer->addRecipient($mailFrom);
                $mailer->setBody($eBody);
                $mailer->isHTML();
                $mailer->send();
                // user return
                $eBody = JText::sprintf('MSG_RETURN_EMAIL_BODY', $name, $domain, $name, $local, $dt, $periodo, $convidados, $extra_info);
                $eBody .= JText::sprintf('MSG_RETURN_EMAIL_FOOTER', $sitename);
                $mailer->setSender($mailFrom);
                $mailer->addRecipient($email);
                $mailer->setBody($eBody);
                $mailer->isHTML();
                $mailer->send();

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
