<?php
/* ERROR PAGE
* Essa página faz com que seja possível carregar a mensagem de erro dentro do template.
* Se fosse utilizada a página de erro default, teríamos que configurar um template para essa página.
*/
defined('_JEXEC') or die;

$app = JFactory::getApplication();
// pega a variável com o valor do status http
// essa variável foi enviada por 'templates/base/error.php'
$var = base64_decode($app->input->get('r', '', 'str'));

// determina a mensagem que será mostrada
switch ($var) {

  case'400':
    // bad request
    $strErr=JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST');
    break;
  case '401':
    // unauthorized
    $strErr = JText::_('JERROR_LOGIN_DENIED');
    break;
  case '403':
    // forbidden
    $strErr = JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE');
    break;
  case '404':
    // not found
    $strErr = JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND');
    break;
  default:
    // not found
    $strErr = JText::_('JERROR_LAYOUT_SEARCH');
    break;
}
?>
<div id="error">
  <h4 class="mb-3 text-live">
    <span class="base-icon-attention"></span>
    <?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?>
    <?php echo ($var) ? ' ('.$var.')' : ''; ?><br />
    <small><?php echo $strErr; ?></small>
  </h4>
  <div class="btn-group">
    <a href="javascript:history.back()" class="btn btn-default base-icon-left-big hasTooltip" data-animation="false" title="<?php echo JText::_('JPREVIOUS'); ?>"></a>
    <a href="<?php echo JURI::root(); ?>" class="btn btn-default base-icon-home hasTooltip" data-animation="false" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"></a>
    <a href="search" class="btn btn-default base-icon-search hasTooltip" data-animation="false" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"></a>
  </div>
</div>
