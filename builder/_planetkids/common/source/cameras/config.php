<?php
// define o caminho para a 'RAÍZ' do site
$JRoot = isset($_root) ? $_root : JURI::root();

// App Define
$APPNAME  = 'cameras';

// App Configuration's Vars
$cfg = array();

if(!$ajaxRequest) :
  unset($_SESSION[$APPTAG.'limitAccess'], $_SESSION[$APPTAG.'accessTime'], $_SESSION[$APPTAG.'periodTime']);
  // total de acessos simultâneos
  $totalAcessos = 10;
  if(!isset($_SESSION[$APPTAG.'limitAccess'])) $_SESSION[$APPTAG.'limitAccess'] = $totalAcessos;
  // tempo máximo de acesso
  $tempoAcesso = 10; // minutos
  if(!isset($_SESSION[$APPTAG.'accessTime'])) $_SESSION[$APPTAG.'accessTime'] = $tempoAcesso;
  // período máximo de espera
  $tempoEspera = 10; // minutos
  if(!isset($_SESSION[$APPTAG.'periodTime'])) $_SESSION[$APPTAG.'periodTime'] = $tempoEspera;
endif;

?>
