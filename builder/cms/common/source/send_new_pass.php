<?php
/* SISTEMA PARA REENVIO DE SENHA RANDOMICA PARA USUÁRIOS
 * AUTOR: IVO JUNIOR
 * EM: 26/11/2014
*/
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$db = JFactory::getDbo();
$config = JFactory::getConfig();
$mailer = JFactory::getMailer();
$user = JFactory::getUser();
$groups = $user->groups;

// Verifica se o usuário é um 'Administrador'
if(!$groups[7] && !$groups[8]) :
	$app->redirect(JURI::base().'error');
	return;
endif;

// GERADOR DE SENHA RANDOMICA
function random_password( $length = 8 ) {
	$password = '';
	//Original -> $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#_";
	for ($i = 0; $i < $length; $i++) {
		$password = substr( str_shuffle( $chars ), 0, $length );
	}
	return $password;
}

// seleciona todos os usuários do grupo 'desenvolvedor'
$query = "SELECT DISTINCT(id) FROM #__users as T1, #__user_usergroup_map as T2 WHERE T2.user_id = T1.id AND T2.group_id = 8 AND T1.block = 0 ORDER BY name";
$db->setQuery($query);
$dev = $db->loadColumn();

// seleciona todos os usuários do grupo 'desenvolvedor'
$query = "SELECT * FROM #__usergroups WHERE id <> 8";
$db->setQuery($query);
$grp = $db->loadObjectList();

// seleciona todos os usuários, exceto os do grupo 'desenvolvedor'
$query = "SELECT * FROM #__users WHERE id NOT IN(".implode(',', $dev).") AND block = 0 ORDER BY name";
$db->setQuery($query);
$list = $db->loadObjectList();
?>	
<fieldset class="fieldset-bordered">
	<legend>Reenvio de senha</legend>
	<form name="voting" action="" method="post" onsubmit="return confirm('Tem certeza?')">
		<div class="row">
			<div class="col-sm-6 col-md-5 col-lg-4">
				<div class="form-group">
					<label class="clear">Usuários</label>
					<select name="users[]" class="form-control chzn-select" multiple>
						<option value="0">Todos</option>
						<?php foreach ($list as $obj) { echo '<option value="'.$obj->id.'">'.$obj->name.'</div>'; } ?>
					</select>
				</div>
			</div>
			<div class="col-sm-6 col-md-5 col-lg-4">
				<div class="form-group">
					<label class="clear"><span class="hasTooltip" title="Selecione os grupos que deseja remover do reenvio">Remover Grupos</span></label>
					<select name="groups[]" class="form-control chzn-select" multiple>
						<?php foreach ($grp as $obj) { echo '<option value="'.$obj->id.'">'.$obj->title.'</div>'; } ?>
					</select>
				</div>
			</div>
			<div class="col-sm-6 col-md-5 col-lg-4">
				<div class="form-group">
					<label class="clear"><span class="hasTooltip" title="Essa opção só é válida para envio de senha para um único usuário">E-mail Alternativo</span> (envio individual)</label>
					<input type="email" name="email_opt" class="form-control" />
				</div>
			</div>
			<div class="col-sm-6 col-md-5 col-lg-8">
				<div class="form-group">
					<label class="clear"><span class="hasTooltip" title="Mensagem opcional no email de reenvio de senha">Mensagem (opcional)</span></label>
					<textarea name="message" class="form-control" cols="3"></textarea>
				</div>
			</div>
			<div class="col-sm-12 col-lg-4">
				<div class="form-group no-margin">
					<label class="clear visible-lg-block">&nbsp;</label>
					<input type="submit" class="btn btn-lg btn-primary btn-block" value="Enviar" />
				</div>
			</div>
		</div>
	</form>
</fieldset>

<?php

if(count($_POST['users']) > 0) :

	$users = $_POST['users'];
	$email_opt = $app->input->post->getString('email_opt');
	$message = $app->input->post->getString('message');
	
	//remove o grupo 'desenvolvedor', que é fixo, mais os grupos selecionados
	$notIN = (count($_POST['groups']) > 0) ? '8,'.implode(',', $_POST['groups']) : '8';
	
	$where = '';
	if($users[0] != '0') :
		$query = "SELECT * FROM #__users WHERE id IN(".implode(',',$users).") AND block = 0 ORDER BY id";
	else :
		$query = "SELECT DISTINCT(id),name,username,email FROM #__users as T1, #__user_usergroup_map as T2 WHERE T2.user_id = T1.id AND T2.group_id NOT IN(".$notIN.") AND T1.block = 0 ORDER BY id";
	endif;
	
	$db->setQuery($query);
	$list = $db->loadObjectList();
	
	$countOn = $countOff = 0;
	$report = '<ul class="list list-condensed">';
	foreach($list as $obj) {
		// registra o voto e envia confirmação
		$sender = array($config->get('mailfrom'), $config->get('fromname'));
		$mailer->setSender($sender);
		$reciver =  array();
		$reciver[] = $obj->email;
		if(count($users) == 1 && !empty($email_opt)) $reciver[] = $email_opt;
		$mailer->addRecipient($reciver);
		$mailer->setSubject('Reenvio de senha de usuário');
		
		$setPass = random_password();
		jimport('joomla.user.helper');
		$newPass = JUserHelper::hashPassword($setPass);
		
		$msg = (isset($message) && $message != '') ? $message."\n\n" : 'por questões de segurança estamos reenviando seus dados de acesso ao nosso site:';
		$msg = "
			Olá ".$obj->name.",\n\n".$msg."\n\nUsuário:  ".$obj->username."\nSenha:  ".$setPass."\n\nVocê pode alterar sua senha a qualquer momento. Para isso acesse nosso website:\n".JURI::root()."profile\n\n Atenciosamente,
		";
		$mailer->setBody($msg);
		
		$query = "UPDATE #__users SET password='".$newPass."' WHERE id=".$obj->id;
		$update = $db->setQuery($query);
		$db->execute();
		
		if($mailer->Send() && $update) :
			$report .= '<li class="text-success"><span class="base-icon-check"></span> A senha (<strong>'.$setPass.'</strong>) foi enviada com sucesso para o usuário '.$obj->name.' ('.implode(', ',$reciver).')</li>';
			$countOn++;
		else :
			$report .= '<li class="bg-danger text-danger strong"><span class="base-icon-cancel"></span> A nova senha <strong>NÃO</strong> foi enviada para o usuário '.$obj->name.' ('.implode(', ',$reciver).')</li>';
			$countOff++;
		endif;	
	}
	$report .= '</ul>';
	$report = '<p>Envios realizados (<strong>'.$countOn.'</strong>) / Envios <strong>NÃO</strong> realizados (<strong>'.$countOff.'</strong>) / <strong>Total de envios ('.($countOn + $countOff).')</strong></p>'.$report;
	
	$app->redirect(JURI::current(), $alert.$report);
	
endif;

?>