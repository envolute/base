<?php
// INITIALIZE JS TEMPLATE
// Carrega arquivos Javascript antes das chamadas padrão do Joomla
defined('_JEXEC') or die;
if(isset($jsInit)) :
  $ji = explode("\n", $jsInit);
  foreach ($ji as $item) {
    if(!empty($item)) echo "<script src=\"".$item."\" type=\"text/javascript\"></script>\n";
  }
endif;
?>
