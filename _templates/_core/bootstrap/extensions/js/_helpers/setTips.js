//JQUERY
jQuery(function() {

  // SET TIPS
  // Defineções default para tooltips e popovers
	window.setTips = function() {

		// reset Tips
		jQuery('[data-toggle="tooltip"], *[rel=tooltip], .hasTooltip, .setTooltip').tooltip('dispose'); // reset Tooltips {when ajax reload}
		jQuery('[data-toggle="popover"], .hasPopover, .setPopover').popover('dispose'); // reset Popovers {when ajax reload}
		// remove current 'tooltips' or 'popovers' created instances
		hideTips();

		// set Tips
		setTimeout(function() { // evita conflito com o 'destroy'
			// hasTooltip
			jQuery('[data-toggle="tooltip"], *[rel=tooltip], .hasTooltip').each(function() {
				var trg = (jQuery(this).data('trigger')) ? jQuery(this).data('trigger') : 'hover';
				jQuery(this).tooltip({
					container: 'body',
					html: true,
					trigger: trg
				});
			});
			// hasPopover
			jQuery('[data-toggle="popover"], .hasPopover, .setPopover').each(function() {
				var trg = (jQuery(this).data('trigger')) ? jQuery(this).data('trigger') : (jQuery(this).hasClass('hasPopover') ? 'hover' : 'click focus');
				jQuery(this).popover({
					container: 'body',
					html: true,
					trigger: trg
				}).on('show.bs.popover', function () {
					// TODO: Refazer funcionalidade
					// -> A sintaxe abaixo não funciona na versão 4 do Bootstrap
					// remove a área do conteúdo caso não haja informação
					// if(!jQuery(this).data('content')) jQuery(this).tip().find('.popover-content').remove();
				});
			});
		}, 1000);

	};

	// Force tooltip close
	window.hideTips = function() { setTimeout(function() { jQuery('.tooltip.in, .popover').remove(); }, 1000) };

});
