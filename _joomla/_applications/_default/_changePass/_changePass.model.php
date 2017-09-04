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
	$data = array();

	// database connect
	$db = JFactory::getDbo();

	// Importa os métodos auxiliares para usuários
	jimport('joomla.user.helper');

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef'])) :
		$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
		$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	endif;

	// fields 'Form' requests
	$newpass	= $input->get('newpass', '', 'password');
	$repass		= $input->get('repass', '', 'password');
	// password encode
	$pass = JUserHelper::hashPassword($newpass);

	if(!empty($newpass) && ($newpass == $repass)) :

		$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET password = '.$db->quote($pass).' WHERE '.$db->quoteName('id').' = '.$user->id;

		try {

			$db->setQuery($query);
			$db->execute();

			$data[] = array(
				'status'	=> 1,
				'msg'		=> JText::_('MSG_SAVE_SUCCESS')
			);

		} catch (RuntimeException $e) {

			$data[] = array(
				'status'	=> 0,
				'msg'		=> $e->getMessage()
			);

		}

	else :

		$msg = empty($newpass) ? JText::_('MSG_SAVE_ERROR') : '';
		$msg = ($newpass != $repass) ? JText::_('MSG_PASS_NOT_EQUAL') : $msg;

		$data[] = array(
			'status'		=> 0,
			'msg'			=> $msg
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
