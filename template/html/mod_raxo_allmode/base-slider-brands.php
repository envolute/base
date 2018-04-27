<?php
/**
 * =============================================================
 * RAXO All-mode PRO J3.x - Envolute Base Template
 * -------------------------------------------------------------
 * @copyright		Copyright (C) 2009-2014 Envolute
 * @license		GNU General Public License v2.0
 * 			http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.envolute.com
 * =============================================================
 */


// no direct access
defined('_JEXEC') or die;

// add template CSS
$app = JFactory::getApplication();
?>

<?php if ($toplist) { ?>
	<div class="allmode-base slider-brands no-container">
		<?php
		// Slider
		$doc = JFactory::getDocument();
		// Importa biblioteca bxslider
		$doc->addStyleSheet(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.css');
		$doc->addScript(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.js');
		// CHAMADA DO SLIDER
		// esconde imagens no carregamento
		$doc->addStyleDeclaration('#allmode-slider-brands-'.$module->id.' > li:not(:first-child) { position: absolute; top: 0; visibility: hidden; }');
		// Habilita o 'carousel' caso no número de itens visíveis seja maior que 1
		$carousel = '
			minSlides: 1,
			maxSlides: mI'.$module->id.',
			slideWidth: iW'.$module->id.',
			slideMargin: iS'.$module->id.',
		';
		if(count($toplist) == 1) $carousel = '';
		$script = '
		jQuery(window).on('load', function(){
			// pega a largura do container
			var cW'.$module->id.' = jQuery("#allmode-slider-brands-'.$module->id.'").closest(".slider-brands").width();
			// "maxSlides" é definido pela quantidade de "TOP Items" do módulo
			var mI'.$module->id.' = '.count($toplist).';
			// A quantidade total de itens é definida pela soma dos "TOP Items" e os "Items"
			var tI'.$module->id.' = '.count($toplist).' + '.count($list).';
			// Define o total de itens visíveis
			// Se o "total" for menor que o "máx. visível", o "máx" passa a ser o total...
			//if(tI'.$module->id.' < mI'.$module->id.') mI'.$module->id.' = tI'.$module->id.';
			// Espaço entre os Itens
			var iS'.$module->id.' = 15;
			// Define a largura do item "slideWidth", de acordo com o
			// "tamanho do container" dividido pelo número de itens visíveis "maxSlides" menos o espaço entre os itens
			var iW'.$module->id.' = (cW'.$module->id.' / mI'.$module->id.') - iS'.$module->id.';
			// Chamada do Slider
			jQuery("#allmode-slider-brands-'.$module->id.'").bxSlider({
				mode: "horizontal",
				autoHover: true,
				auto: true,
				pause: 10000,
				controls: true,
				pager: false,
				infiniteLoop: true,
				'.$carousel.'
				onSliderLoad:function(currentIndex){
					// mostra as imagens após o carregamento do plugin
					jQuery("#allmode-slider-brands-'.$module->id.' img").attr("title","");
					jQuery("#allmode-slider-brands-'.$module->id.' > li:not(:first-child)").css("visibility", "visible");
				}
			});
		});
		';
		$doc->addScriptDeclaration($script);
		?>
		<ul id="allmode-slider-brands-<?php echo $module->id; ?>" class="bxslider">
			<?php
			// All-mode TOP Items Output
			// Items visíveis
			foreach ($toplist as $item) {
			?>
				<li class="allmode-brand clearfix">
					<figure class="img-fluid">
						<?php if ($item->image) echo $item->image; ?>
						<?php if ($item->title) { ?>
							<figcaption>
								<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
							</figcaption>
						<?php } ?>
					</figure>
				</li>
			<?php } ?>
			<?php
			// All-mode Items Output
			foreach ($list as $item) {
			?>
				<li class="allmode-brand clearfix">
					<figure class="img-fluid">
						<?php if ($item->image) echo $item->image; ?>
						<?php if ($item->title) { ?>
							<figcaption>
								<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
							</figcaption>
						<?php } ?>
					</figure>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
