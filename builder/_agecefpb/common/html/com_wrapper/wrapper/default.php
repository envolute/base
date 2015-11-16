<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_wrapper
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$id	= 'blockrandom'.mt_rand(); // id do iframe
$url	= $this->escape($this->wrapper->url);
$cross	= (strpos($url,$_SERVER['SERVER_NAME']) === false) ? true : false; // verifica se o conteúdo do iframe é local
		
$height = $this->escape($this->params->get('height'));
$scroll = $this->escape($this->params->get('scrolling'));

if ($this->params->get('height_auto')) :

	if($cross) :
	
		echo '<!-- Não é possível redimensionar o iframe (conteúdo externo). Por favor, desabilite o redimensionamento automático! -->';
	
	else :
		
		$height = 'auto';
		$scroll = 'no';
		$tpl =& JFactory::getApplication()->getTemplate();
		$doc =& JFactory::getDocument();
		$doc->addScript(JURI::base(true).'/templates/'.$tpl.'/core/js/content/jquery.ba-resize.min.js');
?>
		<script type="text/javascript">
			
			// iframe Resize
			function iframeHeight(iframe){
			
				if(iframe.length){
				
					// The Iframe's child page BODY element.
					var iframe_content = iframe.contents().find('body');
					iframe_content.css('height','auto');
					
					// Bind the resize event. When the iframe's size changes, update its height as
					// well as the corresponding info div.
					iframe_content.resize(function(){
						var elem = jQuery(this);
						// Resize the IFrame.
						iframe.css({ height: elem.outerHeight(true) });
					});
						
					// Resize the Iframe and update the info div immediately.
					iframe_content.resize();
					
				}
			
			}
			// redimensiona os iframes após a página ser carregada
			jQuery(window).load(function(){
				var iframe = jQuery('#<?php echo $id?>');
				iframeHeight(iframe);
				iframe.on("load", function () {
					iframeHeight(iframe);
				});
			});
		
		</script>
	
	<?php endif; ?>
	
<?php endif; ?>

<div class="contentpane">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h4 class="page-header">
			<?php if ($this->escape($this->params->get('page_heading'))) :?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h4>
	<?php endif; ?>
	<iframe id="<?php echo $id; ?>"
		name="iframe"
		src="<?php echo $url; ?>"
		width="<?php echo $this->escape($this->params->get('width')); ?>"
		height="<?php echo $height; ?>"
		scrolling="<?php echo $scroll; ?>"
		frameBorder="<?php echo $this->escape($this->params->get('frameborder', 0)); ?>"
		class="wrapper">
		<?php echo JText::_('COM_WRAPPER_NO_IFRAMES'); ?>
	</iframe>
</div>