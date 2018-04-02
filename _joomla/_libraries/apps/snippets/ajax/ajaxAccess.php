<?php
// Verifica o acesso em arquivos Ajax
if(!$cfg['isPublic'] && $user->guest) {
	$app->redirect($_ROOT.'login?tmpl=component');
	exit;
}
$hasViewer	= ($cfg['isPublic'] == 1) ? true : array_intersect($groups, $cfg['groupId']['viewer']);
$hasAuthor	= ($cfg['isPublic'] == 2) ? true : array_intersect($groups, $cfg['groupId']['author']);
$hasEditor	= ($cfg['isPublic'] == 3) ? true : array_intersect($groups, $cfg['groupId']['editor']);
$hasAdmin	= ($cfg['isPublic'] == 4) ? true : array_intersect($groups, $cfg['groupId']['admin']);
// define permissões de execução
$cfg['canAdd']		= ($hasAuthor || $hasEditor || $hasAdmin);
$cfg['canEdit']		= ($hasEditor || $hasAdmin);
$cfg['canDelete']	= $hasAdmin;
?>
