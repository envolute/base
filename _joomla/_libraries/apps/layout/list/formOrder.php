<?php
$html .= '
  <form id="form-order-'.$APPTAG.'" action="'.$_SERVER['REQUEST_URI'].'" class="float-right form-inline" method="post">
    <input type="hidden" name="'.$APPTAG.'oF" id="'.$APPTAG.'oF" value="'.$_SESSION[$APPTAG.'oF'].'" />
    <input type="hidden" name="'.$APPTAG.'oT" id="'.$APPTAG.'oT" value="'.$_SESSION[$APPTAG.'oT'].'" />
  </form>
';
?>
