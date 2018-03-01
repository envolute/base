<?php
/* SISTEMA PARA CADASTRO DE PROJETOS
 * AUTOR: IVO JUNIOR
 * EM: 29/01/2018
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

$startInfo = '<span class="base-icon-info-circled text-info float-right cursor-help hasTooltip" title="'.JText::_('MSG_START_COUNTER').'"></span>';

?>
<script>
jQuery(function() {
	window.<?php echo $APPTAG?>Counter = 0; // ID do setInterval
	window.<?php echo $APPTAG?>TimerID = 0; // ID do Item
	window.<?php echo $APPTAG?>_checkTimer = function() {
		setTimeout(function() {
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE?>.display.model.php",
				dataType: 'json',
				type: 'GET',
				method: 'get',
				cache: false,
				success: function(data) {
					jQuery.map( data, function( item ) {

						clearInterval(<?php echo $APPTAG?>Counter);
						setHidden(jQuery('#<?php echo $APPTAG?>-btn-play'), item.status, jQuery('#<?php echo $APPTAG?>-btn-stop'));
						if(item.status) {
							<?php echo $APPTAG?>TimerID = item.id;
							var timerInfo = '<span class="base-icon-info-circled text-info float-right cursor-help hasTooltip" title="#'+item.task_id+' - '+item.subject+'"></span>';
							jQuery('#<?php echo $APPTAG?>-display-info').html(timerInfo);
							// SET COUNTER
							var d1 = new Date(item.start_date+' '+item.start_hour);
							var d2 = new Date();
							var start_time =  (d2- d1) / 1000;
							var display = jQuery('#<?php echo $APPTAG?>-display-counter');
							setTimer(start_time, display);
							// notify about activity
							$.baseNotify({ msg: '<?php echo JText::_('MSG_ACTIVITY_COUNTER_PREFIX')?> <strong>ID: #'+item.task_id+'</strong> <?php echo JText::_('MSG_ACTIVITY_COUNTER_SUFFIX')?>', type: "info" });
						} else {
							<?php echo $APPTAG?>TimerID = 0;
							jQuery('#<?php echo $APPTAG?>-display-counter').text('00:00:00');
							var timerInfo = '<?php echo $startInfo?>';
							jQuery('#<?php echo $APPTAG?>-display-info').html(timerInfo);
						}

					});
				},
				error: function(xhr, status, error) {
					toggleLoader(); // encerra o loader
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
				},
				complete: function() {
					// Reload Javascript Base
					// como o ajax carrega 'novos elementos'
					// é necessário recarrega o DOM para atribuir o JS default à esses elementos
					setCoreDefinitions(); // core
					setCustomDefinitions(); // custom
					// TODO: Reload Modal 'Regular Labs'
					// RegularLabsModals.init();
				}
			});
		}, 2000);
		return false;
	};

	window.setTimer = function(start, display) {
		var timer = start, hours, minutes, seconds;
		<?php echo $APPTAG?>Counter = setInterval(function () {
			hours = parseInt((timer /3600)%24, 10)
			minutes = parseInt((timer / 60)%60, 10)
			seconds = parseInt(timer % 60, 10);

			hours = hours < 10 ? "0" + hours : hours;
			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;
			display.text(hours +":"+minutes + ":" + seconds);

			++timer;
		}, 1000);
	}

	// chamada da função
	<?php echo $APPTAG?>_checkTimer();

});

</script>

<div class="d-inline-flex p-2 rounded bg-dark-opacity-75 text-white">
	<button id="<?php echo $APPTAG?>-btn-play" class="btn btn-xs btn-link base-icon-play" onclick="<?php echo $APPTAG?>_setParent();" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>" data-backdrop="static" data-keyboard="false"></button>
	<button id="<?php echo $APPTAG?>-btn-stop" class="btn btn-xs btn-link base-icon-pause" onclick="<?php echo $APPTAG?>_loadEditFields(<?php echo $APPTAG?>TimerID, false, false)" hidden></button>
	<span id="<?php echo $APPTAG?>-display-counter"><span class="text-gray-400">00:00:00</span></span>
	<span id="<?php echo $APPTAG?>-display-info" class="ml-2 pl-2 b-left b-gray-800"><?php echo $startInfo?></span>
</div>
