<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	header( 'Cache-Control: no-cache' );
	header( 'content-type: application/json; charset=utf-8' );

	function is_valid_callback($subject) {
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reserved_words = array(
			'break', 'do', 'instanceof', 'typeof', 'case',
			'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
			'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
			'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
			'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
			'private', 'public', 'yield', 'interface', 'package', 'protected',
			'static', 'null', 'true', 'false'
		);

		return preg_match($identifier_syntax, $subject) && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}

	// load Joomla's framework
	// _DIR_ => apps/THIS_APP
	require(__DIR__.'/libraries/envolute/_init.joomla.php');
	defined('_JEXEC') or die;
	$app = JFactory::getApplication('site');

	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
    // classes customizadas para usuários Joomla
    JLoader::register('baseUserHelper',  JPATH_CORE.DS.'helpers/user.php');

	//joomla get request data
	$input      = $app->input;

	// Default Params
	$task		= $input->get('task', '', 'str');
	$name		= $input->get('name', '', 'str');
	$email		= $input->get('email', '', 'str');
	$phone		= $input->get('phone', '', 'str');
	$data       = array();

	// CUSTOM -> default vars for registration e-mail
	$config			= JFactory::getConfig();
	$sitename		= $config->get('sitename');
	$domain			= baseHelper::getDomain();
	$mailRecipient	= $config->get('mailfrom');

	if($task == 'mail') :

		// Email Template
		$boxStyle	= array('bg' => '#fafafa', 'color' => '#555', 'border' => 'border: 4px solid #eee');
		$headStyle	= array('bg' => '#fff', 'color' => '#5EAB87', 'border' => '1px solid #eee');
		$bodyStyle	= array('bg' => '');
		$mailLogo	= 'logo-news.png';
		// se a senha for gerada pelo sistema, envia a senha. Senão, não envia...
		$subject = 'Contato pelo Site da Brintell';
		$eBody = '
			<p>O Usuário '.$name.' entrou em contato através do website da Brintell. Seguem abaixo os dados de contato do usuário:</p>
			<p><strong>Nome</strong>: '.$name.'<br /><strong>Email</strong>: '.$email.'<br /><strong>Phone</strong>: '.$phone.'</p>
		';
		$mailHtml	= baseHelper::mailTemplateDefault($eBody, 'Get In Touch', '', $mailLogo, $boxStyle, $headStyle, $bodyStyle, JURI::root());

		try {

			// envia o email
			baseHelper::sendMail($email, $mailRecipient, $subject, $mailHtml);

			$data[] = array(
				'status'			=> 1
			);

		} catch (RuntimeException $e) {

			$data[] = array(
				'status'			=> 0
			);

		}

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
