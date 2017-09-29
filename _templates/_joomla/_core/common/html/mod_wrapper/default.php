<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_wrapper
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = ModWrapperHelper::getParams($params);
$h	= htmlspecialchars($params->get('height_auto', 0));

$id	= 'blockrandom'.mt_rand(); // id do iframe
$cross	= (strpos($url,JURI::root()) === false) ? true : false; // verifica se o conteúdo do iframe é local

// mensagem no código fonte

if ($h) :

	if($cross) :

		echo '<!-- Não é possível redimensionar o iframe (conteúdo externo). Por favor, desabilite o redimensionamento automático! -->';

	else :
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::base(true).'/templates/base/libs/content/jquery.ba-resize.min.js');
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
			jQuery(window).on('load', function(){
				var iframe = jQuery('#<?php echo $id?>');
				iframeHeight(iframe);
				iframe.on("load", function () {
					iframeHeight(iframe);
				});
			});

		</script>

	<?php endif; ?>

<?php endif; ?>

<iframe id="<?php echo $id; ?>"
	name="<?php echo $target; ?>"
	src="<?php echo $url; ?>"
	width="<?php echo $width; ?>"
	height="<?php echo $height; ?>"
	scrolling="<?php echo $scroll; ?>"
	frameborder="<?php echo $frameborder; ?>"
	class="wrapper" >
	<?php echo JText::_('MOD_WRAPPER_NO_IFRAMES'); ?>
</iframe>
