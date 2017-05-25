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
    $idx        = $input->get('idx', 0, 'int');

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
  	$request['task_id']      = $input->get('task_id', 0, 'int');
    $request['task_editId']  = $input->get('task_editId', 0, 'int');
  	$request['user_id']      = $input->get('user_id', 0, 'int');
  	$request['date']         = $input->get('date', '', 'string');
  	$request['date']         = !empty($request['date']) ? $request['date'] : date('Y-m-d');
  	$request['start_hour']   = $input->get('start_hour', '', 'string');
  	$request['end_hour']     = $input->get('end_hour', '', 'string');
  	$request['time']         = $input->get('time', '', 'string');
  	$request['billable']     = $input->get('billable', 0, 'int');
  	// get total time
    $time = array();
    $total_time = $time['time'] = '00:00:00';
    $hours = $time['hours'] = 0;
    if(!empty($request['time']) && $request['time'] != '00:00:00') :
      $time = baseHelper::timeDiff('00:00:00', $request['time']);
      $total_time = $time['time'];
      $hours = $time['hours'];
    elseif(!empty($request['end_hour']) && $request['end_hour'] != '00:00:00') :
      $time = baseHelper::timeDiff($request['start_hour'], $request['end_hour']);
      $total_time = $time['time'];
      $hours = $time['hours'];
    endif;
  	$request['price_hour']   = $input->get('price_hour', 0.00, 'float');
    $price = 0.00;
    if($request['billable'] == 1) $price = ($request['price_hour'] * $hours);
  	$request['closeTask']    = $input->get('closeTask', 0, 'int');
  	$request['note']         = $input->get('note', '', 'string');

    // CLOSE TASK
    function closeTask($taskId) {
      // database connect
    	$db = JFactory::getDbo();
      $query  = 'UPDATE '.$db->quoteName('#__envolute_tasks').' SET '. $db->quoteName('status') .' = 3, '. $db->quoteName('end_date') .' = '.$db->quote(date('Y-m-d H:i:s')).', '. $db->quoteName('percent') .' = 100 WHERE '. $db->quoteName('id') .'='. $taskId;
      $db->setQuery($query);
      $db->execute();
    }

    // START TASK
    function startTask($taskId) {
      // database connect
    	$db = JFactory::getDbo();
      $query  = 'SELECT '. $db->quoteName('status') .' FROM '.$db->quoteName('#__envolute_tasks').' WHERE '. $db->quoteName('id') .'='. $taskId;
      $db->setQuery($query);
      $st = $db->loadResult();
      if($st == 0 || $st == 2) :
        $query  = 'UPDATE '.$db->quoteName('#__envolute_tasks').' SET '. $db->quoteName('status') .' = 1, '. $db->quoteName('start_date') .' = '.$db->quote(date('Y-m-d')).' WHERE '. $db->quoteName('id') .'='. $taskId;
        $db->setQuery($query);
        $db->execute();
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
      				'task_id'	    => $item->task_id,
      				'user_id'	    => $item->user_id,
      				'date'        => $item->date,
      				'start_hour'  => $item->start_hour,
      				'end_hour'    => $item->end_hour,
      				'time'        => $item->time,
      				'total_time'  => $item->total_time,
      				'hours'	      => $item->hours,
      				'price_hour'  => $item->price_hour,
      				'price'       => $item->price,
      				'billable'    => $item->billable,
      				'closeTask'   => $item->closeTask,
      				'note'        => $item->note
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            // $db->quoteName('task_id') 	.'='. $request['task_id'] .','.
            $db->quoteName('user_id') 	.'='. $request['user_id'] .','.
  					$db->quoteName('date') .'='. $db->quote($request['date']) .','.
  					$db->quoteName('start_hour') .'='. $db->quote($request['start_hour']) .','.
  					$db->quoteName('end_hour') .'='. $db->quote($request['end_hour']) .','.
  					$db->quoteName('time') .'='. $db->quote($request['time']) .','.
  					$db->quoteName('total_time') .'='. $db->quote($total_time) .','.
  					$db->quoteName('hours') .'='. $hours .','.
  					$db->quoteName('price_hour') .'='. $db->quote($request['price_hour']) .','.
  					$db->quoteName('price') .'='. $db->quote($price) .','.
  					$db->quoteName('billable') .'='. $request['billable'] .','.
  					$db->quoteName('closeTask') .'='. $request['closeTask'] .','.
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

            // CLOSE TASK
            if($request['closeTask'] == 1) closeTask($request['task_editId']);

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
                // [RELACIONAMENTO] -> remove os registros relacionados aos [relacionados]
                // $query = 'DELETE FROM '. $db->quoteName('??') .' WHERE '. $db->quoteName('??') .' IN ('.$ids.')';
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
  						'msg'	=> JText::_('MSG_FILE_DELETED'),
              'uploadError' => $fileMsg
  					);

        // INVOICE
        elseif($task == 'invoice') :

          $query = '
          UPDATE '. $db->quoteName($cfg['mainTable']) .'
          SET
            '. $db->quoteName('invoice_id') .' = '.$state.',
            '. $db->quoteName('billed') .' = '.($state != 0 ? 1 : 0).',
            '. $db->quoteName('billed_date') .' = '. $db->quote(($state != 0 ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00')) .'
          WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            $data[] = array(
              'status' => 1,
              'msg'	=> ''
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
        if(!empty($request['task_id']) && $request['task_id'] != 0) :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('task_id') .','.
            $db->quoteName('user_id') .','.
    				$db->quoteName('date') .','.
    				$db->quoteName('start_hour') .','.
    				$db->quoteName('end_hour') .','.
    				$db->quoteName('time') .','.
    				$db->quoteName('total_time') .','.
    				$db->quoteName('hours') .','.
    				$db->quoteName('price_hour') .','.
    				$db->quoteName('price') .','.
    				$db->quoteName('billable') .','.
    				$db->quoteName('closeTask') .','.
    				$db->quoteName('note') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['task_id'] .','.
            $request['user_id'] .','.
    				$db->quote($request['date']) .','.
    				$db->quote($request['start_hour']) .','.
    				$db->quote($request['end_hour']) .','.
    				$db->quote($request['time']) .','.
    				$db->quote($total_time) .','.
    				$hours .','.
    				$db->quote($request['price_hour']) .','.
    				$db->quote($price) .','.
    				$request['billable'] .','.
    				$request['closeTask'] .','.
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

            // CLOSE TASK
            if($request['closeTask'] == 1) closeTask($request['task_id']);
            // START TASK
            startTask($request['task_id']);

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
