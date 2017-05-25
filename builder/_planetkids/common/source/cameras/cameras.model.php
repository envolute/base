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

  // get current user's data
  $user = JFactory::getUser();
  $groups = $user->groups;

  // Default Params
  $task       = (isset($_REQUEST['task']) && !empty($_REQUEST['task'])) ? $_REQUEST['task'] : null;
  $data       = array();

  if($task == 'view') :

  	// database connect
  	$db = JFactory::getDbo();

    $query  = '
    SELECT TIMESTAMPDIFF(SECOND,NOW(),MAX(access)) as timeRemaining FROM '. $db->quoteName('#__cameras_acessos') .'
    WHERE '.
    $db->quoteName('user').' = '.$user->id.' AND '.
    $db->quoteName('access').' >= NOW() - INTERVAL '.$_SESSION[$APPTAG.'accessTime'].' MINUTE';

    try {

      $db->setQuery($query);
      $remaining = $db->loadResult();
      if($remaining) : // acima do limite -> 10 acessos simultâneos
        $data[] = array(
          'status'=> 1,
          'remaining' => $remaining
        );
      else :
        // Prepare the insert query
    		$query  = 'INSERT INTO '. $db->quoteName('#__cameras_acessos') .'('. $db->quoteName('user') .') VALUES ('. $user->id .')';

    		try {

    			$db->setQuery($query);
    			$db->execute();

    			$data[] = array(
    				'status'=> 1,
            'remaining' => ($_SESSION[$APPTAG.'accessTime'] * 60)
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
    				'msg'	=> $sqlErr
    			);

    		}
      endif;

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
        'msg'	=> $sqlErr
      );

    }

	elseif($task == 'access') :

  	// database connect
  	$db = JFactory::getDbo();

    // Prepare the insert query
		$query  = 'SELECT COUNT(*) FROM '. $db->quoteName('#__cameras_acessos') .' WHERE '.$db->quoteName('access').' >= NOW() - INTERVAL '.$_SESSION[$APPTAG.'periodTime'].' MINUTE';

		try {

			$db->setQuery($query);
      $total = $db->loadResult();
      if($total >= $_SESSION[$APPTAG.'limitAccess']) : // acima do limite -> 10 acessos simultâneos

  			$data[] = array(
  				'status'=> 2 // bloqueado por limite de acessos
  			);

      else :

        // Prepare the insert query
        $query  = '
        SELECT TIMESTAMPDIFF(SECOND,NOW(),MAX(access)) FROM '. $db->quoteName('#__cameras_acessos') .'
        WHERE '.
        $db->quoteName('user').' = '.$user->id.' AND '.
        $db->quoteName('access').' >= NOW() - INTERVAL '.($_SESSION[$APPTAG.'periodTime'] + $_SESSION[$APPTAG.'accessTime']).' MINUTE AND '.
        $db->quoteName('access').' < NOW() - INTERVAL '.$_SESSION[$APPTAG.'accessTime'].' MINUTE';
        try {

    			$db->setQuery($query);
          $remaining = $db->loadResult();
          if($remaining) { // existe um acesso em período de espera
      			$data[] = array(
      				'status'=> 3, // bloqueado por período de espera
              'remaining' => (int)$remaining
      			);
          } else {
            $data[] = array(
      				'status'=> 1, // acesso liberado
              'remaining' => ($_SESSION[$APPTAG.'periodTime'] * 60)
      			);
          }

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
    				'msg'	=> $sqlErr
    			);

    		}
      endif;

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
				'msg'	=> $sqlErr
			);

		}

  elseif($task == 'group') :

  	// database connect
  	$db = JFactory::getDbo();
    // Prepare the insert query
		$query  = 'SELECT * FROM '. $db->quoteName('#__cameras_grupos') .' WHERE '.$db->quoteName('group').' = '.$_SESSION[$APPTAG.'Group'];

		try {

			$db->setQuery($query);
      $db->execute();
      $num_rows = $db->getNumRows();
      $obj = $db->loadObject();

      if($num_rows > 0) { // acima do limite -> 10 acessos simultâneos

  			$data[] = array(
  				'status'=> 1, // bloqueado por limite de acessos
          'group' => $obj->group,
          'login' => $obj->username,
          'key' => $obj->password
  			);

      } else {

        $data[] = array(
  				'status'=> 0,
  				'msg'	=> $query
  			);
      }

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
				'msg'	=> $sqlErr
			);

		}

  endif; // end 'task'

	$json = json_encode($data);

	# JSON if no callback
	if(!isset($_GET['callback'])) exit($json);

	# JSONP if valid callback
	if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

else :

  # Otherwise, bad request
  header('status: 400 Bad Request', true, 400);

endif;

?>
