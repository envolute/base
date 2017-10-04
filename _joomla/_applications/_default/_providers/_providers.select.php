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
		window.<?php echo $APPTAG?>_selectItem = function(el) {
			var val = jQuery(el).val();
			location.href = '<?php echo JURI::current()?>'+((!isEmpty(val) && val != 0) ? '?vID='+val : '');
		};
	});
	</script>

	<div class="hidden-print b-bottom my-3 pb-3">
		<div class="input-group" style="width:400px">
			<select name="vID" id="<?php echo $APPTAG?>-vID" class="form-control" onchange="<?php echo $APPTAG?>_selectItem(this)">
				<option value="0">- <?php echo JText::_('TEXT_SELECT')?> -</option>
				<?php
					foreach ($providers as $obj) {
						echo '<option value="'.$obj->id.'"'.($vID == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
					}
				?>
			</select>
			<span class="input-group-btn">
				<button class="btn btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_setParent(0)" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_NEW')?></button>
			</span>
		</div>
	</div>

<?php endif;?>
