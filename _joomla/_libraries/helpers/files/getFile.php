<?php
if(isset($_REQUEST['fn']) && !empty($_REQUEST['fn']) && isset($_REQUEST['mt']) && !empty($_REQUEST['mt'])) :
  $fileName = htmlspecialchars(base64_decode($_REQUEST['fn']), ENT_QUOTES);
  $mimeType = htmlspecialchars(base64_decode($_REQUEST['mt']), ENT_QUOTES);
  $tag = htmlspecialchars(base64_decode($_REQUEST['tag']), ENT_QUOTES);
  $tag = !empty($tag) ? $tag : '';
  $filePath = __DIR__.DS.$tag.DS.$fileName;
  if(file_exists($filePath)) :
    header("Content-type: ".$mimeType);
    header("Content-disposition: attachment; filename=".$fileName);
    flush();
    ob_clean();
    readfile($filePath);
  endif;
endif;
exit();
?>
