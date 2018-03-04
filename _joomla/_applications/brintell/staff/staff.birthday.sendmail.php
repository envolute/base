<?php
$ajaxRequest = false;
require('config.php');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

//joomla get request data
$view = $app->input->get('cli', 0, 'int');

// ACESSO
$cfg['isPublic'] = ($view == 1 ? true : false); // Público -> acesso do servidor

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// database connect
$db = JFactory::getDbo();

// GET DATA
$query = '
	SELECT
		'.$db->quoteName('T1.id').',
		'.$db->quoteName('T1.user_id').',
		'.$db->quoteName('T1.name').',
		'.$db->quoteName('T1.nickname').',
		'.$db->quoteName('T1.email').',
		DAY('.$db->quoteName('T1.birthday').') birthDay,
		MONTH('.$db->quoteName('T1.birthday').') birthMonth,
		YEAR(NOW()) birthYear
	FROM '.$db->quoteName($cfg['mainTable']).' T1
		LEFT OUTER JOIN '.$db->quoteName($cfg['mainTable'].'_birthday_sendmail').' T2
		ON '.$db->quoteName('T2.user_id').' = '.$db->quoteName('T1.user_id').' AND '.$db->quoteName('T2.year').' = YEAR(NOW())
	WHERE
		MONTH('.$db->quoteName('T1.birthday').') = MONTH(NOW()) AND
		DAY('.$db->quoteName('T1.birthday').') = DAY(NOW()) AND
		'.$db->quoteName('T2.user_id').' IS NULL
';
try {
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();
} catch (RuntimeException $e) {
	 error_log($e->getMessage());
	 return;
}

if($num_rows) : // verifica se existe
	// CUSTOM -> default vars for registration e-mail
	$config			= JFactory::getConfig();
	$sitename		= $config->get('sitename');
	$domain			= baseHelper::getDomain();
	$mailFrom		= $config->get('mailfrom');
	$subject		= JText::_('MSG_EMAIL_BIRTHDAY_SUBJECT');
	$title			= JText::_('MSG_EMAIL_BIRTHDAY_TITLE');
	// Email Template
	$boxStyle	= array('bg' => '#fafafa', 'color' => '#555', 'border' => 'border: 4px solid #eee');
	$headStyle	= array('bg' => '#fff', 'color' => '#5EAB87', 'border' => '1px solid #eee');
	$bodyStyle	= array('bg' => '');
	$mailLogo	= 'logo-news.png';
	$sendTo = $sendNo = '';
	foreach($res as $item) {

		$name = !empty($item->nickname) ? $item->nickname : $item->name;

		$eBody		= JText::sprintf('MSG_EMAIL_BIRTHDAY_BODY', baseHelper::nameFormat($name), $mailFrom);
		$mailHtml	= baseHelper::mailTemplateDefault($eBody, $title, '', $mailLogo, $boxStyle, $headStyle, $bodyStyle, JURI::root());
		$sendMail	= baseHelper::sendMail($mailFrom, $item->email, $subject, $mailHtml);

		if($sendMail) :
			$query = '
				INSERT INTO '.$db->quoteName($cfg['mainTable'].'_birthday_sendmail').' ('.
					$db->quoteName('user_id') .','.
					$db->quoteName('name') .','.
					$db->quoteName('email') .','.
					$db->quoteName('day') .','.
					$db->quoteName('month') .','.
					$db->quoteName('year')
				.') VALUES ('.
					$item->user_id .','.
					$db->quote($name) .','.
					$db->quote($item->email) .','.
					$item->birthDay .','.
					$item->birthMonth .','.
					$item->birthYear
				.')
			';
			try {
				$db->setQuery($query);
				$db->execute();
				$sendTo .= '<div class="text-success">- '.$name.' ('.$item->email.')</div>';
			} catch (RuntimeException $e) {
				error_log($e->getMessage());
				echo '<p class="alert alert-danger">'.$e->getMessage().'</p>';
				return;
			}
		else :
			$sendNo .= '<div class="text-danger">- '.$name.' ('.$item->email.')</div>';
		endif;

	}

	if(!empty($sendTo)) echo '<h6 class="mt-4">'.JText::_('TEXT_MESSAGES_SEND_TO').'</h6>'.$sendTo;
	if(!empty($sendTo) && !empty($sendNo)) echo '<hr />';
	if(!empty($sendNo)) echo '<h6 class="mt-4">'.JText::_('TEXT_MESSAGES_NOT_SEND_TO').'</h6>'.$sendNo;
	echo '<hr />';

else :

	echo '
		<div class="alert alert-warning lh-1-3">
			<h4 class="mb-1 base-icon-attention"> '.JText::_('MSG_NO_MESSAGES_TO_SEND').'</h4>
			'.JText::_('MSG_NO_MESSAGES_TO_SEND_DESC').'
		</div>
	';

endif;

?>
