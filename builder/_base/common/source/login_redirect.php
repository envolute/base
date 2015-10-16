<?php
/* SCRIPT PARA CORRIGIR/IMPLEMENTAR O REDIRECIONAMENTO APÓS O LOGIN
 * AUTOR: IVO JUNIOR
 * EM: 29/05/2015
*/
defined('_JEXEC') or die;
// redireciona para a página setada na variável de sessão 'return'
$app = JFactory::getApplication();
$url = (isset($_SESSION['pageReturn'])) ? $_SESSION['pageReturn'] : 'index.php';
$app->redirect($url ,false);
?>