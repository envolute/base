<?php
// REMOVE JS/CSS TEMPLATE
// Força a remoção de arquivos Javascript e Css antes das chamadas padrão do Joomla
$js_removed = isset($jsRemove) ? explode("\n", $jsRemove) : array(); // arquivos 'javascripts' removidos
$ss_removed = isset($ssRemove) ? explode("\n", $ssRemove) : array(); // arquivos 'styleSheets' removidos
$removed = array_merge($js_removed, $ss_removed);
$headData = $doc->getHeadData();
$scripts = $headData['scripts'];
$styles = $headData['styleSheets'];
foreach ($removed as $key => $item) {
  if(!empty($item)) :
    $item = ($item[0] == '/' || strpos($item, 'http') === 0) ? $item : JURI::root(true).'/'.$item;
    $itemPath = strtok($item, '?'); // only URL, no Params
    $ext = pathinfo($itemPath, PATHINFO_EXTENSION);
    if(trim($ext) === 'js') unset($scripts[trim($item)]);
    else if(trim($ext) === 'css') unset($styles[trim($item)]);
  endif;
}
// Define JS/CSS Files
$headData['scripts'] = $scripts;
$headData['styleSheets'] = $styles;
// Reload Files
if(!empty($scripts) || !empty($styles)) $doc->setHeadData($headData);
// Caso o array venha vazio, remove todos os scripts/styleSheets
if(empty($scripts)) unset($this->_scripts);
if(empty($styles)) unset($this->_styleSheets);
?>
