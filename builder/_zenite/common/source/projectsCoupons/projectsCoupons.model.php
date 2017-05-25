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
    $pID        = $input->get('pID', 0, 'int');
    $pCOD       = $input->get('pCOD', 0, 'int');

    // upload actions
    $fileMsg  = '';
    if($cfg['hasUpload']) :
      $fname    = $input->get('fname', '', 'string');
      $fileId   = $input->get('fileId', 0, 'int');
      // load 'uploader' class
      JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
    endif;

  	// fields 'Form' requests
    $request                    = array();
    // default
    $request['relationId']      = $input->get('relationId', 0, 'int');
  	$request['state']           = $input->get('state', 1, 'int');
    // app
    $request['project_id']      = $input->get('project_id', 0, 'int');
    $request['amount']          = $input->get('amount', 0, 'int');
    $request['price']           = $input->get('price', '0.00', 'float');
    $request['owner']          = $input->get('owner', '', 'string');

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
              'project_id'	=> $item->project_id,
              'cod'         => $item->cod,
              'price'       => $item->price,
              'owner'       => $item->owner
      			);
      		}

  			// DELETE
  			elseif($task == 'del') :

  				$query = 'DELETE FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

  				try {

  					$db->setQuery($query);
  					$db->execute();

  					$data[] = array(
  						'status'=> 3,
              'ids'	=> explode(',', $ids),
  						'msg'	=> JText::_('MSG_DELETED')
  					);

  				} catch (RuntimeException $e) {

  					$data[] = array(
  						'status'=> 0,
  						'msg'	=> $e->getMessage()
  					);

  				}

        // STATE
        elseif($task == 'state') :

          $stateVal = ($state == 2 ? 'IF(state = 1, 0, 1)' : $state);
          $query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

          try {
            $db->setQuery($query);
            $db->execute();

            $data[] = array(
              'status' => 4,
              'state' => $state,
              'ids'	=> explode(',', $ids),
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
        if($request['project_id'] != 0 && $request['amount'] > 0) :

          $sqlErr = '';
          for($i = 1; $i <= $request['amount']; $i++) {

            $cod = $request['project_id'].$i.mt_rand(100,999);

            // Prepare the insert query
      			$query  = '
            INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
              $db->quoteName('project_id') .','.
              $db->quoteName('cod') .','.
              $db->quoteName('price') .','.
              $db->quoteName('owner') .','.
      				$db->quoteName('state') .','.
      				$db->quoteName('created_by')
      			.') VALUES ('.
              $request['project_id'] .','.
              $cod .','.
              $db->quote($request['price']) .','.
              $db->quote($request['owner']) .','.
      				$request['state'] .','.
      				$user->id
      			.')';

      			try {

      				$db->setQuery($query);
      				$db->execute();
              $id = $db->insertid();

      			} catch (RuntimeException $e) {

              // Error treatment
              switch($e->getCode()) {
                case '1062':
                  $sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
                  break;
                default:
                  $sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
              }

      			}

          } // end 'for'

          if(empty($sqlErr)) :

            $data[] = array(
              'status'=> 1,
              'msg'	=> JText::_('MSG_SAVED'),
              'amount' => $request['amount']
            );

          else :

            $data[] = array(
              'status'=> 0,
              'msg'	=> $sqlErr
            );

          endif;

        else :

          $data[] = array(
            'status'=> 0,
            'msg'	=> JText::_('MSG_ERROR')
          );

        endif; // end validation

      // VERIFY
      elseif($task == 'verify') :

        $query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('project_id') .' = '.$pID.' AND '. $db->quoteName('cod') .' = '.$pCOD.' AND status = 0 AND state = 1';

        try {

          $db->setQuery($query);
          $exist = $db->loadResult();

          if($exist) :
            $data[] = array(
              'status'=> 1
            );
          else :
            $data[] = array(
              'status'=> 0
            );
          endif;

        } catch (RuntimeException $e) {

          $data[] = array(
            'status'=> 0,
            'msg'	=> $e->getMessage()
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
