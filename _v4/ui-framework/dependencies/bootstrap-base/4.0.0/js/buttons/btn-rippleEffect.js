//JQUERY
jQuery(function() {

  // BUTTON CLICK EFFECT
	window.btnRippleEffect = function() {
		jQuery('.btn, .nav-link, .nav-pills, .set-ripple').each(function() {
			jQuery(this).click(function(e) {
				// Remove any old one
				jQuery(".ripple").remove();
				// Setup
				var posX = jQuery(this).offset().left,
				posY = jQuery(this).offset().top,
				buttonWidth = jQuery(this).width(),
				buttonHeight =  jQuery(this).height();
				// Add the element
				jQuery(this).prepend("<span class='ripple'></span>");
				// Make it round!
				if(buttonWidth >= buttonHeight) buttonHeight = buttonWidth;
				else buttonWidth = buttonHeight;
				// Get the center of the element
				var x = e.pageX - posX - buttonWidth / 2;
				var y = e.pageY - posY - buttonHeight / 2;
				// Add the ripples CSS and start the animation
				jQuery(".ripple").css({
					width: buttonWidth,
					height: buttonHeight,
					top: y + 'px',
					left: x + 'px'
				}).addClass("rippleEffect");
			});
		});
	};

});
