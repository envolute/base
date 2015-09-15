<?php
/* ERROR PAGE
 * Essa página faz com que seja possível carregar a mensagem de erro dentro do template.
 * Se fosse utilizada a página de erro default, teríamos que configurar um template para essa página.
*/
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$templatePath = JURI::base() . 'templates/' . $app->getTemplate();

// pega a variável com o valor do status http
// essa variável foi enviada por 'templates/base/error.php'
$var = base64_decode(JRequest::getVar('r',''));

// determina a mensagem que será mostrada
switch ($var) {
	case '400':
		// bad request
		$strErr = JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST');
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
	<h3 class="page-header no-margin-top text-live">
		<span class="base-icon-attention"></span> 
		<?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?><?php echo ($var) ? ' ('.$var.')' : ''; ?><br />
		<small><?php echo $strErr; ?></small>
	</h3>
	<div class="btn-group pull-left">
		<a class="btn btn-default" href="javascript:history.back()">
			&laquo; <?php echo JText::_('JPREVIOUS'); ?>
		</a>
		<a class="btn btn-default" href="<?php echo JURI::root(); ?>" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>">
			<?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?>
		</a>
		<a class="btn btn-default" href="search" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
			<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?> &raquo;
		</a>
	</div>
	
	<div class="clearfix bottom-space"></div>
	
	<p class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>&nbsp; 
		<span class="glyphicon glyphicon-info-sign"></span> <?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>
	</p>
</div>