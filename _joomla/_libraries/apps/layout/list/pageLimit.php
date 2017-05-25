<?php
// ITENS POR PÁGINA
// seta o parametro 'start = 0' na URL sempre que o limit for refeito
// isso evita erro quando estiver navegando em páginas subjacentes

$a = preg_replace("#\?start=.*#", '', $_SERVER['REQUEST_URI']);
$a = preg_replace("#&start=.*#", '', $a);

$html .= '
  <form id="form-limit-'.$APPTAG.'" action="'.$a.'" class="float-right form-inline hidden-print" method="post">
    <label>'.JText::_('LIST_PAGINATION_LIMIT').'</label>
    <select name="list-lim-'.$APPTAG.'" onchange="'.$APPTAG.'_setListLimit()">
      <option value="5" '.($_SESSION[$APPTAG.'plim'] === 5 ? 'selected' : '').'>5</option>
      <option value="20" '.($_SESSION[$APPTAG.'plim'] === 20 ? 'selected' : '').'>20</option>
      <option value="50" '.($_SESSION[$APPTAG.'plim'] === 50 ? 'selected' : '').'>50</option>
      <option value="100" '.($_SESSION[$APPTAG.'plim'] === 100 ? 'selected' : '').'>100</option>
      <option value="1" '.($_SESSION[$APPTAG.'plim'] === 1 ? 'selected' : '').'>Todos</option>
    </select>
  </form>
';

?>
