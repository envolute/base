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
			location.href = '<?php echo JURI::current()?>'+((!isEmpty(val) && val != 0) ? '?regID='+val : '');
		};
	});
	</script>

	<div class="hidden-print b-bottom my-3 pb-3">
		<select name="regID" id="<?php echo $APPTAG?>-regID" style="width:400px" onchange="<?php echo $APPTAG?>_selectUser(this)">
			<option value="0">- <?php echo JText::_('TEXT_SELECT')?> -</option>
			<?php
				foreach ($providers as $obj) {
					echo '<option value="'.$obj->id.'"'.($regID == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
				}
			?>
		</select>
		<button class="btn btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_setParent(0)" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_NEW')?></button>
	</div>

<?php endif;?>
