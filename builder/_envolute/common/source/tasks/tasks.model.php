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
    $sID        = $input->get('sID', 0, 'int');
    $pID        = $input->get('pID', 0, 'int');
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
  	$request['type']           = $input->get('type', 0, 'int');
    $request['ctype']          = $input->get('ctype', 0, 'int');
    $request['template']       = $input->get('template', 0, 'int');
  	$request['service_id']     = $input->get('service_id', 0, 'int');
  	$request['cservice_id']    = $input->get('cservice_id', 0, 'int');
  	$request['project_id']     = $input->get('project_id', 0, 'int');
  	$request['priority']       = $input->get('priority', 0, 'int');
  	$request['title']          = $input->get('title', '', 'string');
  	$request['description']    = $input->get('description', '', 'raw');
  	$request['price']          = $input->get('price', 0.00, 'float');
  	$request['estimate']       = $input->get('estimate', '', 'string');
  	$request['billable']       = $input->get('billable', 0, 'int');
  	$request['period']         = $input->get('period', 0, 'int');
  	$request['start_date']     = $input->get('start_date', '', 'string');
  	$request['deadline']       = $input->get('deadline', '', 'string');
  	$request['recurrent_type'] = $input->get('recurrent_type', 0, 'int');
  	$request['weekly']         = isset($_POST['weekly']) ? implode(',', $_POST['weekly']) : '';
  	$request['monthly']        = isset($_POST['monthly']) ? implode(',', $_POST['monthly']) : '';
  	$request['yearly']         = $input->get('yearly', '', 'string');
    $request['percent']        = $input->get('percent', 0, 'int');
  	$request['hour']           = $input->get('hour', '', 'string');
  	$request['visible']        = $input->get('visible', 1, 'int');
  	$request['status']         = $input->get('status', 1, 'int');
      // fechamento
      $start_date = $request['start_date'];
      $end_date = '0000-00-00 00:00:00';
      if($request['status'] == 1 && (empty($request['start_date']) || $request['start_date'] = '0000-00-00')) :
        $start_date = date('Y-m-d');
      elseif($request['status'] == 3) :
        if(empty($request['start_date']) || $request['start_date'] = '0000-00-00') $start_date = date('Y-m-d');
        $end_date = date('Y-m-d H:i:s');
        $request['percent'] = 100;
      elseif($request['status'] == 4) :
        $end_date = date('Y-m-d H:i:s');
      endif;
  	$request['status_desc']    = $input->get('status_desc', '', 'string');
      // alter status form
      $request['statusOn']     = $input->get('statusOn', 0, 'int');
    	$request['statusDs']     = $input->get('statusDs', '', 'string');
  	$request['ordering']       = $input->get('ordering', 0, 'int');
    $request['cordering']      = $input->get('cordering', 0, 'int');

    // CUSTOM -> Set Order
    function setOrder($ID, $serviceId, $cservId, $ord, $cord, $cfg) {
      if(!empty($ID) && $ID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        // last item
        $query = 'SELECT MAX('.$db->quoteName('ordering').') FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('id').' != '.$ID.' AND '. $db->quoteName('service_id') .' = '. $serviceId;
    		$db->setQuery($query);
        $max = $db->loadResult();
        if(!empty($ord) && $ord != 0 && ($ord != $cord || $serviceId != $cservId)) :
          // this item position
          $query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('ordering').' = '.$ord.' AND  '. $db->quoteName('id') .' != '. $ID .' AND '. $db->quoteName('service_id') .' = '. $serviceId;
      		$db->setQuery($query);
          $exist = $db->loadResult();
          if($ord <= $max) :
            // define a posição quando a ordem é definida no insert, altera todos a partir da nova ordem
            if($cord == 0 || $serviceId != $cservId) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' + 1)
              WHERE '.
              $db->quoteName('ordering') .' >= ' .$ord.' AND '. // ordem ocupada
              $db->quoteName('id') .' != '. $ID.' AND '.
              $db->quoteName('service_id') .' = '. $serviceId;
          		$db->setQuery($query);
              $db->execute();
              return true;
            // se o item subir na ordem, altera todos os que estão entre a nova ordem e a anterior
            elseif($exist && $ord < $cord) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' + 1)
              WHERE '.
              $db->quoteName('ordering') .' >= ' .$ord.' AND '. // ordem ocupada
              $db->quoteName('ordering') .' < ' .$cord.' AND '. // ordem anterior -> abaixo
              $db->quoteName('id') .' != '. $ID.' AND '.
              $db->quoteName('service_id') .' = '. $serviceId;
          		$db->setQuery($query);
              $db->execute();
              return true;
            // se o item descer na ordem, altera todos os que estão entre a nova ordem e a anterior
            elseif($exist && $ord > $cord) :
              $query = '
              UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
                $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' - 1)
              WHERE '.
                $db->quoteName('ordering') .' <= ' .$ord.' AND '. // ordem ocupada
                $db->quoteName('ordering') .' > ' .$cord.' AND '.  // ordem anterior -> acima
                $db->quoteName('id') .' != '. $ID.' AND '.
                $db->quoteName('service_id') .' = '. $serviceId;
          		$db->setQuery($query);
              $db->execute();
              return true;
            else :
              return false;
            endif;
          // se o item for maior do que o máx, seta o máximo e define a ordem
          else :
            $nord = ($cord >= $max) ? $max + 1 : $max; // caso exista outro item na mesma posição
            $query = '
            UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
              $db->quoteName('ordering').' = '.$nord.'
            WHERE '.
              $db->quoteName('id') .' = '. $ID;
        		$db->setQuery($query);
            $db->execute();
            if($cord < $max) setOrder($ID, $serviceId, $cservId, $nord, $cord, $cfg);
            return true;
          endif;
        elseif(empty($ord) || $ord == 0) :
          $query = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.$db->quoteName('ordering').' = ('.$max.' + 1) WHERE '. $db->quoteName('id') .' = '. $ID;
      		$db->setQuery($query);
          $db->execute();
          if($ord != $cord && $cord <= $max) :// this item position
            // verifica se existe outro item na mesma posição
            $query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('ordering').' = '.$cord.' AND '. $db->quoteName('id') .' != '. $ID .' AND '. $db->quoteName('service_id') .' = '. $serviceId;
            $db->setQuery($query);
            $cexist = $db->loadResult();
            if(!$cexist) setOrder($ID, $serviceId, $serviceId, $max + 1, $cord, $cfg);
          endif;
          return true;
        else :
          return false;
        endif;
      else :
        return false;
      endif;
    }
    // CUSTOM -> Re-Order after delete item
    function reOrder($ID, $serviceId, $ord, $cfg) {
      if(!empty($ID) && $ID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        // last item
        $query = 'SELECT MAX('.$db->quoteName('ordering').') FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '.$db->quoteName('id').' != '.$ID.' AND '. $db->quoteName('service_id') .' = '. $serviceId;
    		$db->setQuery($query);
        $max = $db->loadResult();
        if(!empty($ord) && $ord != 0 && $ord < $max) :
          // altera todos os que estão abaixo
          $query = '
          UPDATE '.$db->quoteName($cfg['mainTable']).' SET '.
            $db->quoteName('ordering').' = ('.$db->quoteName('ordering').' - 1)
          WHERE '.
            $db->quoteName('ordering') .' > ' .$ord.' AND '. // ordem ocupada
            $db->quoteName('service_id') .' = '. $serviceId;
      		$db->setQuery($query);
          $db->execute();
          return true;
        else :
          return false;
        endif;
      else :
        return false;
      endif;
    }
    // CUSTOM -> Copy To-Do List
    function copyTodoList($tmplID, $taskID, $userID) {
      if(!empty($tmplID) && $tmplID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        $query = '
        INSERT INTO '. $db->quoteName('#__envolute_todoList') .' (task_id, name, url, description, ordering, state, created_by)
        SELECT '.$taskID.', name, url, description, ordering, '. $db->quote('1') .', '. $db->quote($userID) .' FROM '. $db->quoteName('#__envolute_todoList') .' WHERE task_id = '.$tmplID.' ORDER BY ordering
        ';
        $db->setQuery($query);
        $db->execute();
        return true;
      else :
        return false;
      endif;
    }
    // CUSTOM -> Copy Task from Template
    function copyTaskFromTemplate($tmplID, $projID, $userID, $cfg) {
      if(!empty($tmplID) && $tmplID != 0) :
        // database connect
      	$db = JFactory::getDbo();
        $query = '
        INSERT INTO '. $db->quoteName($cfg['mainTable']) .' (service_id, project_id, priority, title, description, price, estimate, billable, period, start_date, deadline, end_date, recurrent_type, weekly, monthly, yearly, percent, hour, visible, status, status_desc, ordering, state, created_by)
        SELECT service_id, '.(!empty($projID) ? $projID : 0).', priority, title, description, price, estimate, billable, period, start_date, deadline, end_date, recurrent_type, weekly, monthly, yearly, percent, hour, visible, status, status_desc, 0, 1, '.$userID.' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE id = '.$tmplID.' ORDER BY ordering, id
        ';
        $db->setQuery($query);
        $db->execute();
        $taskID = $db->insertid();
        copyTodoList($tmplID, $taskID, $userID);
        return true;
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
      				'type'            => $item->type,
      				'service_id'      => $item->service_id,
      				'project_id'      => $item->project_id,
      				'priority'        => $item->priority,
      				'title'           => $item->title,
      				'description'     => $item->description,
      				'price'           => $item->price,
      				'estimate'        => $item->estimate,
      				'billable'        => $item->billable,
      				'period'          => $item->period,
      				'start_date'      => $item->start_date,
      				'deadline'        => $item->deadline,
      				'end_date'        => $item->end_date,
      				'recurrent_type'  => $item->recurrent_type,
      				'weekly'          => explode(',', $item->weekly),
      				'monthly'         => explode(',', $item->monthly),
      				'yearly'          => $item->yearly,
      				'hour'            => $item->hour,
      				'percent'         => $item->percent,
      				'visible'         => $item->visible,
      				'status'          => $item->status,
      				'status_desc'     => $item->status_desc,
      				'ordering'        => $item->ordering
      			);
      		}

  			// UPDATE
  			elseif($task == 'save' && $id) :

  				$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
  				$query .=
            $db->quoteName('type') 	.'='. $request['type'] .','.
            $db->quoteName('service_id') 	.'='. $request['service_id'] .','.
            $db->quoteName('project_id') 	.'='. $request['project_id'] .','.
            $db->quoteName('priority') 	.'='. $request['priority'] .','.
  					$db->quoteName('title') .'='. $db->quote($request['title']) .','.
  					$db->quoteName('description') .'='. $db->quote($request['description']) .','.
  					$db->quoteName('price') .'='. $db->quote($request['price']) .','.
            $db->quoteName('estimate') 	.'='. $db->quote($request['estimate']) .','.
            $db->quoteName('billable') 	.'='. $request['billable'] .','.
            $db->quoteName('period') 	.'='. $request['period'] .','.
  					$db->quoteName('start_date') .'='. $db->quote($start_date) .','.
  					$db->quoteName('deadline') 	.'='. $db->quote($request['deadline']) .','.
  					$db->quoteName('end_date') .'='. $db->quote($end_date) .','.
  					$db->quoteName('recurrent_type') 	.'='. $request['recurrent_type'] .','.
  					$db->quoteName('weekly') .'='. $db->quote($request['weekly']) .','.
  					$db->quoteName('monthly') .'='. $db->quote($request['monthly']) .','.
  					$db->quoteName('yearly') .'='. $db->quote($request['yearly']) .','.
  					$db->quoteName('percent') .'='. $request['percent'] .','.
  					$db->quoteName('hour') .'='. $db->quote($request['hour']) .','.
            $db->quoteName('visible') 	.'='. $request['visible'] .','.
            $db->quoteName('status') 	.'='. $request['status'] .','.
  					$db->quoteName('status_desc') 	.'='. $db->quote($request['status_desc']) .','.
  					$db->quoteName('ordering') .'='. ($request['type'] == 1 ? $request['ordering'] : 0) .','.
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
            $element = $elemVal = $elemLabel = $elemBillable = $elemPriceFixed = $elemPriceHour = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $id;
              $query = '
              SELECT '.
                $db->quoteName('T1.title') .', '.
                $db->quoteName('T1.billable').',
                IF('. $db->quoteName('T1.price') .' = 0.00, 0, 1) priceFixed, '.
              	$db->quoteName('T2.price') .' priceHour
              FROM '. $db->quoteName($cfg['mainTable']).' T1
              	JOIN '. $db->quoteName('#__envolute_services') .' T2
              	ON '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.service_id') .'
              WHERE T1.id='.$id.' AND T1.state = 1';
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = $elems->title;
              $elemBillable = $elems->billable;
              $elemPriceFixed = $elems->priceFixed;
              $elemPriceHour = $elems->priceHour;
            endif;

            // SET ORDER
            if($request['type'] == 1) :
              setOrder($id, $request['service_id'], $request['cservice_id'], $request['ordering'], $request['cordering'], $cfg);
              if($request['ctype'] == 1) :
                if($request['service_id'] != $request['cservice_id']) :
                  // RE-ORDER
                  reOrder($id, $request['cservice_id'], $request['cordering'], $cfg);
                endif;
              endif;
            endif;

            // CUSTOM -> Copy To-Do List
            if($request['template'] > 0) copyTodoList($request['template'], $id, $user->id);

  					$data[] = array(
  						'status' => 2,
  						'msg'	=> JText::_('MSG_SAVED'),
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
    					'parentFieldBillable'	=> $elemBillable,
    					'parentFieldPriceFixed'	=> $elemPriceFixed,
    					'parentFieldPriceHour'	=> $elemPriceHour
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

          $Ids = explode(',', $ids);

          // RE-ORDER ITEMS
          for($i = 0; $i < count($Ids); $i++) {
            // GET PARENT INFO
            $queryOrder = 'SELECT type, service_id, ordering FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '.$Ids[$i].' AND type = 1';
            $db->setQuery($queryOrder);
            $obj = $db->loadObject();
            if($obj->type == 1) :
              // RE-ORDER
              reOrder($Ids[$i], $obj->service_id, $obj->ordering, $cfg);
            endif;
          }

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
                // TodoList -> remove os registros relacionados às Atividades (To-Do)
                $query = 'DELETE FROM '. $db->quoteName('#__envolute_todoList') .' WHERE '. $db->quoteName('task_id') .' IN ('.$ids.')';
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

  					$data[] = array(
  						'status'=> 3,
              'ids'	=> $Ids,
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
            $element = $elemVal = $elemLabel = $elemBillable = $elemPriceFixed = $elemPriceHour = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $id;
              $query = '
              SELECT '.
                $db->quoteName('T1.title') .', '.
                $db->quoteName('T1.billable').',
                IF('. $db->quoteName('T1.price') .' = 0.00, 0, 1) priceFixed, '.
              	$db->quoteName('T2.price') .' priceHour
              FROM '. $db->quoteName($cfg['mainTable']).' T1
              	JOIN '. $db->quoteName('#__envolute_services') .' T2
              	ON '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.service_id') .'
              WHERE T1.id='.$id.' AND T1.state = 1';
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = $elems->title;
              $elemBillable = $elems->billable;
              $elemPriceFixed = $elems->priceFixed;
              $elemPriceHour = $elems->priceHour;
            endif;

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
              'msg'	=> '',
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
    					'parentFieldBillable'	=> $elemBillable,
    					'parentFieldPriceFixed'	=> $elemPriceFixed,
    					'parentFieldPriceHour'	=> $elemPriceHour
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

        // STATE
        elseif($task == 'status') :

          $date = $ds = '';
          $sDate = ($request['statusOn'] == '0') ? ', '.$db->quoteName('start_date').' = '.$db->quote(date('Y-m-d')) : '';
          $sDesc = ', '.$db->quoteName('status_desc').' = '.$db->quote($request['statusDs']);
          $nDesc = ', '.$db->quoteName('status_desc').' = '.$db->quote('');
          if($state == 0) : // aguardando início
            $date = ', '.$db->quoteName('end_date').' = '.$db->quote('0000-00-00 00:00:00').', '.$db->quoteName('percent').' = 0';
            $ds = $sDesc;
            $sDesc = $request['statusDs'];
          elseif($state == 1 || $state == 2) : // em produção ou pausado
            $date = $sDate.', '.$db->quoteName('end_date').' = '.$db->quote('0000-00-00 00:00:00');
            $ds = ($state == 1) ? $nDesc : $sDesc;
            $sDesc = ($state == 1) ? '' : $request['statusDs'];
          elseif($state == 3) : // finalizado
            $date = $sDate.', '.$db->quoteName('end_date').' = '.$db->quote(date('Y-m-d H:i:s')).', '.$db->quoteName('percent').' = 100';
            $ds = $nDesc;
            $sDesc = '';
          else : // cancelado
            $date = ', '.$db->quoteName('end_date').' = '.$db->quote(date('Y-m-d H:i:s'));
            $ds = $sDesc;
            $sDesc = $request['statusDs'];
          endif;

          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('status') .' = '.$state.$date.$ds.' WHERE '. $db->quoteName('id') .' = '.$id;

          try {
            $db->setQuery($query);
            $db->execute();

            $data[] = array(
              'status' => 7,
              'newStatus' => $state,
              'statusDesc' => $sDesc,
              'id'	=> $id,
              'msg'	=> $query
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
        if(!empty($request['title'])) :

          // Prepare the insert query
    			$query  = '
          INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
            $db->quoteName('type') .','.
            $db->quoteName('service_id') .','.
            $db->quoteName('project_id') .','.
            $db->quoteName('priority') .','.
            $db->quoteName('title') .','.
    				$db->quoteName('description') .','.
    				$db->quoteName('price') .','.
            $db->quoteName('estimate') .','.
            $db->quoteName('billable') .','.
    				$db->quoteName('period') .','.
    				$db->quoteName('start_date') .','.
    				$db->quoteName('deadline') .','.
    				$db->quoteName('end_date') .','.
    				$db->quoteName('recurrent_type') .','.
    				$db->quoteName('weekly') .','.
    				$db->quoteName('monthly') .','.
    				$db->quoteName('yearly') .','.
    				$db->quoteName('percent') .','.
    				$db->quoteName('hour') .','.
    				$db->quoteName('visible') .','.
    				$db->quoteName('status') .','.
    				$db->quoteName('status_desc') .','.
    				$db->quoteName('ordering') .','.
    				$db->quoteName('state') .','.
    				$db->quoteName('created_by')
    			.') VALUES ('.
            $request['type'] .','.
            $request['service_id'] .','.
            $request['project_id'] .','.
    				$request['priority'] .','.
            $db->quote($request['title']) .','.
    				$db->quote($request['description']) .','.
    				$db->quote($request['price']) .','.
    				$db->quote($request['estimate']) .','.
    				$request['billable'] .','.
            $request['period'] .','.
    				$db->quote($start_date) .','.
    				$db->quote($request['deadline']) .','.
    				$db->quote($end_date) .','.
    				$request['recurrent_type'] .','.
            $db->quote($request['weekly']) .','.
            $db->quote($request['monthly']) .','.
            $db->quote($request['yearly']) .','.
    				$request['percent'] .','.
    				$db->quote($request['hour']) .','.
            $request['visible'] .','.
            $request['status'] .','.
    				$db->quote($request['status_desc']) .','.
    				($request['type'] == 1 ? $request['ordering'] : 0) .','.
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
            $element = $elemVal = $elemLabel = $elemBillable = $elemPriceFixed = $elemPriceHour = '';
            if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
              $element = $_SESSION[$RTAG.'FieldUpdated'];
              $elemVal = $id;
              $query = '
              SELECT '.
                $db->quoteName('T1.title') .', '.
                $db->quoteName('T1.billable').',
                IF('. $db->quoteName('T1.price') .' = 0.00, 0, 1) priceFixed, '.
              	$db->quoteName('T2.price') .' priceHour
              FROM '. $db->quoteName($cfg['mainTable']).' T1
              	JOIN '. $db->quoteName('#__envolute_services') .' T2
              	ON '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.service_id') .'
              WHERE T1.id='.$id.' AND T1.state = 1';
              $db->setQuery($query);
              $elems = $db->loadObject();
              $elemLabel = $elems->title;
              $elemBillable = $elems->billable;
              $elemPriceFixed = $elems->priceFixed;
              $elemPriceHour = $elems->priceHour;
            endif;

            // SET ORDER
            if($request['type'] == 1) setOrder($id, $request['service_id'], $request['cservice_id'], $request['ordering'], $request['cordering'], $cfg);

            // CUSTOM -> Copy To-Do List
            if($request['template'] > 0) copyTodoList($request['template'], $id, $user->id);

    				$data[] = array(
    					'status'=> 1,
    					'msg'	=> JText::_('MSG_SAVED'),
              'regID'	=> $id,
              'uploadError' => $fileMsg,
    					'parentField'	=> $element,
    					'parentFieldVal'	=> $elemVal,
    					'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel),
    					'parentFieldBillable'	=> $elemBillable,
    					'parentFieldPriceFixed'	=> $elemPriceFixed,
    					'parentFieldPriceHour'	=> $elemPriceHour
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

      // TEMPLATES SERVICE
      elseif($task == 'tmplService') :

        $query = 'SELECT id FROM '. $db->quoteName($cfg['mainTable']) .' WHERE service_id = '.$sID.' AND type = 1 ORDER BY ordering';
        $db->setQuery($query);
        $tmpls = $db->loadObjectList();

        try {

          // CUSTOM -> Copy Task from Template
          foreach ($tmpls as $tmpl) copyTaskFromTemplate($tmpl->id, $pID, $user->id, $cfg);

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
