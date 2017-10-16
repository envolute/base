<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	require(__DIR__.'/../../_init.joomla.php');
	$app = JFactory::getApplication('site');

	defined('_JEXEC') or die;

	//joomla get request data
	$input = $app->input;
	// fields 'Form' requests
	$dbTable	= $input->get('dbTable', '', 'str');
	$dbField	= $input->get('dbField', '', 'str');
	$val		= $input->get('val', '', 'str');
	$cval		= $input->get('cval', '', 'str');
	$valida		= $input->get('valida', 1, 'int');

	if(!empty($val) && $val != $cval) :
		// database connect
		$db = JFactory::getDbo();
		$query  = 'SELECT COUNT(*) FROM '.$db->quoteName($dbTable).' WHERE '.$db->quoteName($dbField).' = '.$db->quote($val);
		try {
			$db->setQuery($query);
			$exist = $db->loadResult();
			$r = $exist ? 'true' : 'false';
			// para a validação, se o usuário existe deve retornar 'false'
			if($valida) $r = $exist ? 'false' : 'true';
			echo $r;
			return false;
		} catch (RuntimeException $e) {
			error_log('Erro: '.$e->getCode().'. '.$e->getMessage());
		}
	endif; // end 'val'
	echo $valida ? 'true' : 'false';

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
