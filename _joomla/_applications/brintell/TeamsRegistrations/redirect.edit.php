<?php
// REDIRECT TO CLIENT PROFILE
defined('_JEXEC') or die;

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// Grupos de clients
$clients[] = 11;	// Efetivo
$clients[] = 12;	// Aposentado
$clients[] = 13;	// Contribuinte

// se está na lista de grupos permitidos
$hasClient = array_intersect($groups, $clients);

// Redireciona se for um client
if($hasClient) $app->redirect(JURI::root().'user/edit-client-profile');

?>
