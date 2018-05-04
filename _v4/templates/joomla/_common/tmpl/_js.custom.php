<?php
// CUSTOM JS TEMPLATE
// Carrega arquivos Javascript após as chamadas padrão do Joomla
defined('_JEXEC') or die;
if(isset($jsCustom)) :
  $jc = explode("\n", $jsCustom);
  foreach ($jc as $item) {
    if(!empty($item)) echo "<script src=\"".$item."\" type=\"text/javascript\"></script>\n";
  }
endif;
?>
