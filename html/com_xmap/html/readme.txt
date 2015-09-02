IMPORTANTE:
Aversão do XMAP para o joomla! 3.0 vem com um bug que ocasiona 'erro 500'.
A correção foi alterar no arquivo views/html/tmp/default.php -> templates/base/html/com_xmap/default.php as seguintes linhas:

LINHA #19
De	'JHTML::_('behavior.mootools');'
Para	'JHtml::_('behavior.framework');'
	
LINHA #20
De	$ajaxurl = "{$live_site}index.php?option=com_xmap&format=json&task=ajax.editElement&action=toggleElement&".JUtility::getToken().'=1';
Para	$ajaxurl = "{$live_site}index.php?option=com_xmap&format=json&task=ajax.editElement&action=toggleElement";


O resto foi customização de layout!