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
  require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	$app = JFactory::getApplication('site');

  defined('_JEXEC') or die;

  // get current user's data
  $user = JFactory::getUser();

  // Default Params
  $data = array();

	// database connect
	$db = JFactory::getDbo();

  jimport('joomla.user.helper');

	//joomla get request data
	$input = $app->input;
	// fields 'Form' requests
  $newpass      = $input->get('newpass', '', 'password');
	$repass       = $input->get('repass', '', 'password');

  $pass = JUserHelper::hashPassword($newpass);

	if(!empty($newpass) && ($newpass == $repass)) :

		$query  = 'UPDATE #__users SET password='.$db->quote($pass).' WHERE id='.$user->id;

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

  else :

    $data[] = array(
      'status' => 2,
      'msg'	=> ''
    );

	endif; // end 'id'

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
