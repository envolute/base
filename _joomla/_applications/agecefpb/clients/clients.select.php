<?
if($hasAdmin) :

	// Carrega o arquivo de tradução
	if(isset($_SESSION[$APPTAG.'langDef'])) :
		$lang->load('base_clients', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	endif;

	// database connect
	$db = JFactory::getDbo();

	// CLIENTS
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE user_id <> 0 AND state = 1 ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();

?>

	<script>
	jQuery(function() {
		// SELECT USER -> Selecionar um usuário no formulário de edição
		window.<?php echo $APPTAG?>_selectUser = function(el) {
			var val = jQuery(el).val();
			location.href = '<?php echo JURI::current()?>'+((!isEmpty(val) && val != 0) ? '?uID='+val : '');
		};
	});
	</script>

	<div class="hidden-print">
		<div class="row">
			<div class="col-md-4">
				<?php echo JText::_('MSG_ADMIN_EDIT'); ?>
			</div>
			<div class="col-md-8">
				<fieldset class="fieldset-embed fieldset-sm">
					<legend><?php echo JText::_('FIELD_LABEL_CLIENT_SELECT'); ?></legend>
					<select name="uID" id="<?php echo $APPTAG?>-uID" class="form-control" onchange="<?php echo $APPTAG?>_selectUser(this)">
						<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
						<?php
							foreach ($clients as $obj) {
								echo '<option value="'.$obj->user_id.'"'.($uID == $obj->user_id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</fieldset>
			</div>
		</div>
		<hr class="mt-0" />
	</div>

<?php endif;?>
