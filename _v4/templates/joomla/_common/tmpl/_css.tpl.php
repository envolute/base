<?php
defined('_JEXEC') or die;
?>

<!-- css template -->
<link rel="stylesheet" href="templates/base/css/style.css" type="text/css" />

<?php
// ADDICIONAL CSS FILES
// Carrega arquivos Css adicionais
defined('_JEXEC') or die;
if(isset($ssCustom)) :
  $ssAdds = explode("\n", $ssCustom);
  foreach ($ssAdds as $item) {
    if(!empty($item)) echo "<link rel=\"stylesheet\" href=\"".$item."\" type=\"text/css\" />\n";
  }
endif;
?>

<!-- css print -->
<link rel="stylesheet" href="templates/base/css/style.print.css" type="text/css" media="print" />

<!-- For IE support -->
<!--[if IE]>
	<link href="templates/base/css/style.ie.css" rel="stylesheet">
<![endif]-->
