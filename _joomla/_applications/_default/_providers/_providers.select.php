<?
if($hasAdmin) :

	// database connect
	$db = JFactory::getDbo();

	// PROVIDERS
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE state = 1 ORDER BY name';
	$db->setQuery($query);
	$providers = $db->loadObjectList();

?>

	<script>
	jQuery(function() {
		// SELECT USER -> Selecionar um usuário no formulário de edição
		window.<?php echo $APPTAG?>_selectUser = function(el) {
			var val = jQuery(el).val();
			location.href = '<?php echo JURI::current()?>'+((!isEmpty(val) && val != 0) ? '?pID='+val : '');
		};
	});
	</script>

	<div class="hidden-print">
		<select name="pID" id="<?php echo $APPTAG?>-pID" style="width:400px" onchange="<?php echo $APPTAG?>_selectUser(this)">
			<option value="0">- <?php echo JText::_('TEXT_SELECT')?> -</option>
			<?php
				foreach ($providers as $obj) {
					echo '<option value="'.$obj->id.'"'.($pID == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
				}
			?>
		</select>
		<hr class="mt-2" />
	</div>

<?php endif;?>
