<?php
/* SISTEMA PARA CADASTRO DE PROJETOS
 * AUTOR: IVO JUNIOR
 * EM: 29/01/2018
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = 1; // Público -> acesso aberto a todos

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');
?>
<script>
jQuery(document).ready(function($) {
	window.<?php echo $APPTAG?>_sidebarReload = function() {
		// inicia o loader
		toggleLoader();
		jQuery.ajax({
			url: "<?php echo $URL_APP_FILE?>.sidebar.user.php",
			type: 'GET',
			method: 'get',
			cache: false,
			success: function(data) {
				toggleLoader(); // encerra o loader
				// load content
				jQuery('#<?php echo $APPTAG?>-sidebar-user-card').html(data);
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
		return false;
	};

	// chamada da função
	<?php echo $APPTAG?>_sidebarReload();
});
</script>

<div id="<?php echo $APPTAG?>-sidebar-user-card"></div>
