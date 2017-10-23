<?php
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO: Libera o acesso aos clients
// Atribui aos clientes o perfil de visualizador só para esse código
unset($cfg['groupId']['viewer']); // Limpa os valores padrão
$cfg['groupId']['viewer'][]	= 11; // Associado -> Efetivo
$cfg['groupId']['viewer'][]	= 12; // Associado -> Aposentado
$cfg['groupId']['viewer'][]	= 13; // Associado -> Contribuinte

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

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

// GET CLIENTS ANNIVERSARIES
$query = 'SELECT * FROM '.$db->quoteName($cfg['mainTable']).' WHERE MONTH(birthday) = MONTH(NOW()) AND DAY(birthday) = DAY(NOW())';
try {
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();
} catch (RuntimeException $e) {
	 echo $e->getMessage();
	 return;
}

if($num_rows) : // verifica se existe

	foreach($res as $item) {

		if($item->user_id == $user->id) :

			// Verifica se a mensagem já foi visualizada
			$query = 'SELECT COUNT(*) FROM '.$db->quoteName($cfg['mainTable'].'_birthday_message').' WHERE `client_id` = '.$item->id.' AND `viewed_date` = NOW()';
			$db->setQuery($query);
			$viewed = $db->loadResult();

			if(!$viewed) :
				// Verifica se a mensagem já foi visualizada
				$query = '
					INSERT INTO '. $db->quoteName($cfg['mainTable'].'_birthday_message') .' ('.
						$db->quoteName('client_id') .','.
						$db->quoteName('viewed_date')
					.') VALUES ('.$item->id.', NOW())
				';
				$error = '';
				try {
					$db->setQuery($query);
					$db->execute();
				} catch (RuntimeException $e) {
					 $error = '<p class="alert alert-danger">'.$e->getMessage().'</div>';
					 return;
				}
				echo '
					<div class="modal fade" id="'.$APPTAG.'modal-birthday-message" tabindex="-1" role="dialog" aria-labelledby="'.$APPTAG.'modal-month-birthdayLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-body">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									'.$error.JText::sprintf('MSG_BIRTHDAY_CONGRATULATIONS', baseHelper::nameFormat($item->name), 'base-apps/clients/birthday.jpg').'
								</div>
							</div>
						</div>
					</div>
					<script>
						jQuery(function() {
							setTimeout(function() {
								jQuery("#'.$APPTAG.'modal-birthday-message").modal("show");
							}, 5000);
						});
					</script>
				';
			endif;

		endif;
		break;

	}

endif;

?>
