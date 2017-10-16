<?php
/**
 * @copyright  Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license    GNU/GPL, see LICENSE.php
 * Developed by Ivo Junior.
 */

// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// SYSTEM VARIABLES
require_once(__DIR__.'/../_system.vars.php');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

class baseUserHelper {

	// VERIFICA SE O USUÁRIO EXISTE
	public static function userExist($userId){
		if($userId != 0) :
			// database connect
			$db = JFactory::getDbo();
			$query  = 'SELECT COUNT(*) FROM '.$db->quoteName('#__users').' WHERE '.$db->quoteName('id').' = '.$userId;
			try {
				$db->setQuery($query);
				$exist = $db->loadResult();
				return ($exist > 0) ? true : false;
			} catch (RuntimeException $e) {
				return false;
			}
		endif; // end 'id'
		return false;
	}

	// VERIFICA SE O USUÁRIO EXISTE
	public static function getUserData($userIds){
		if(!empty($userIds) && $userIds != 0) :
			// database connect
			$db = JFactory::getDbo();
			$query  = 'SELECT * FROM '.$db->quoteName('#__users').' WHERE '.$db->quoteName('id').' IN ('.$userIds.')';
			try {
				$db->setQuery($query);
				$db->execute();
				$total = $db->getNumRows();
				$data = array(
					'exist'     => $total,
					'obj'  => (array) $db->loadAssocList()
				);
				return $data;
			} catch (RuntimeException $e) {
				$data = array(
					'exist'     => 0,
					'obj'  => array()
				);
			}
		else :
			$data = array(
				'exist'     => 0,
				'obj'  => array()
			);
		endif; // end 'id'
	}

	// VERIFICA SE O E-MAIL EXISTE
	public static function checkEmail($email, $cmail, $validation) {
		if(!empty($email) && $email != $cmail) :
			// database connect
			$db = JFactory::getDbo();
			$query  = 'SELECT COUNT(*) FROM '.$db->quoteName('#__users').' WHERE '.$db->quoteName('email').' = '.$db->quote($email);
			try {
				$db->setQuery($query);
				$exist = $db->loadResult();
				$r = $exist ? true : false;
				// para a validação, se o email existe deve retornar 'false'
				if($validation) $r = $exist ? false : true;
				return $r;
			} catch (RuntimeException $e) {
				error_log('Erro: '.$e->getCode().'. '.$e->getMessage());
			}
		endif; // end 'id'
		return $validation ? true : false;
	}

	// VERIFICA SE O NOME DE USUÁRIO EXISTE
	public static function checkUsername($username, $cusername, $validation) {
		if(!empty($username) && $username != $cusername) :
			// database connect
			$db = JFactory::getDbo();
			$query  = 'SELECT COUNT(*) FROM '.$db->quoteName('#__users').' WHERE '.$db->quoteName('username').' = '.$db->quote($username);
			try {
				$db->setQuery($query);
				$exist = $db->loadResult();
				$r = $exist ? true : false;
				// para a validação, se o usuário existe deve retornar 'false'
				if($validation) $r = $exist ? false : true;
				return $r;
			} catch (RuntimeException $e) {
				error_log('Erro: '.$e->getCode().'. '.$e->getMessage());
			}
		endif; // end 'id'
		return $validation ? true : false;
	}

	// CRIA UM NOVO USUÁRIO
	public static function createJoomlaUser($name, $username, $email, $password, $usergroup, $block, $emailConfirm = 0, $mailFrom = null, $subject = null, $content = null) {
		jimport( 'joomla.user.helper');
		$userData = array(
			'name' => $name,
			'username' => $username,
			'password' => $password,
			'password2' => $password,
			'email' => $email,
			'block' => $block,
			'groups' => is_array($usergroup) ? $usergroup : array($usergroup)
		);
		$newUser = new JUser;
		if(!$newUser->bind($userData)) return $newUser->getError();
		if(!$newUser->save()) return $newUser->getError();
		// Send activation email
		if($emailConfirm) baseHelper::sendMail($mailFrom, $email, $subject, $content);
		return $newUser->id;
	}

	// DELETA O USUÁRIO
	public static function deleteJoomlaUser($userId) {
		if($userId) {
			jimport( 'joomla.user.helper');
			$instance = JUser::getInstance($userId);
			return ($instance->delete()) ? true : false;
		}
	}

	// STATE TO BLOCK JOOMLA USER
	public static function stateToJoomlaUser($userId, $state = 0) {
		if($userId) {
			// database connect
			$db = JFactory::getDbo();
			// inverte o sentido, pois 'state = 1' é relativo à 'block = 0'
			$setBlock = ($state == 1 ? 0 : 1);
			$query = 'UPDATE '. $db->quoteName('#__users') .' SET '. $db->quoteName('block') .' = '.$setBlock.' WHERE '. $db->quoteName('id') .' = '.$userId;
			if($db->setQuery($query)) :
				$db->execute();
				return true;
			endif;
		}
		return false;
	}

	// GET ADMIN USERS
	public static function getAdminData($groups) {
		if($groups) :
			// database connect
			$db = JFactory::getDbo();
			$query = '
				SELECT
					'. $db->quoteName('id') .',
					'. $db->quoteName('name') .',
					'. $db->quoteName('email') .'
				FROM  '. $db->quoteName('#__users') .' T1
					JOIN  '. $db->quoteName('#__user_usergroup_map') .' T2
					ON T1.id = T2.user_id
				WHERE T2.group_id IN ( '.$groups.' ) AND T1.block = 0
			';
			try {
				$db->setQuery($query);
				return $db->loadObjectList();
			} catch (RuntimeException $e) {
				return false;
			}
		endif;
		return false;
	}

}

?>
