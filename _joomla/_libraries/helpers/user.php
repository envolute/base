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
    else :
      return false;
  	endif; // end 'id'
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
        $r = ($exist > 0) ? true : false;
        // para a validação, se o e-mail existe deve retornar 'false'
        if($validation) return ($r) ? false : true;
        else return $r;
  		} catch (RuntimeException $e) {
  			if($validation) return true;
				else return false;
  		}
    else :
      if($validation) return true;
			else return false;
  	endif; // end 'id'
	}

	// VERIFICA SE O NOME DE USUÁRIO EXISTE
	public static function checkUsername($username, $validation) {
    if(!empty($username)) :
			// database connect
			$db = JFactory::getDbo();
  		$query  = 'SELECT COUNT(*) FROM '.$db->quoteName('#__users').' WHERE '.$db->quoteName('username').' = '.$db->quote($username);
  		try {
  			$db->setQuery($query);
  			$exist = $db->loadResult();
        $r = ($exist > 0) ? true : false;
        // para a validação, se o e-mail existe deve retornar 'false'
        if($validation) return ($r) ? false : true;
        else return $r;
  		} catch (RuntimeException $e) {
				if($validation) return true;
				else return false;
  		}
    else :
			if($validation) return true;
			else return false;
  	endif; // end 'id'
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
      'groups' => array($usergroup)
    );
    $newUser = new JUser;
    if(!$newUser->bind($userData)) return 0;
    if(!$newUser->save()) return 0;
    if($emailConfirm && $mailFrom && $subject && $content) :
      // Send activation email
      $mailer = JFactory::getMailer();
      $mailer->setSender($mailFrom);
      $mailer->addRecipient($email);
      $mailer->setSubject($subject);
      $mailer->setBody($content);
      $mailer->isHTML();
      $mailer->send();
    endif;
    return $newUser->id;
    // mensagem de erro => $newUser->getError();
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
			else :
				return false;
			endif;
    }
		return false;
  }

}

?>
