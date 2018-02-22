<?php
	// database connect
	$db = JFactory::getDbo();

	// CLIENTS
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE state = 1 ORDER BY name';
	$db->setQuery($query);
	$components = $db->loadObjectList();
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

<div class="hidden-print b-bottom mb-2 pb-3 clearfix">
	<div class="input-group input-group-sm float-left mw-100" style="width:400px; max-width:100%">
		<select name="vID" id="<?php echo $APPTAG?>-vID" class="form-control" onchange="<?php echo $APPTAG?>_selectItem(this)">
			<option value="0">- <?php echo JText::_('TEXT_SELECT')?> -</option>
			<?php
				$clientName = '';
				foreach ($components as $obj) {
					echo '<option value="'.$obj->id.'"'.($vID == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
					if($vID == $obj->id) $clientName = baseHelper::nameFormat($obj->name);
				}
			?>
		</select>
		<?php if($canEdit) :?>
			<span class="input-group-btn">
				<?php if(isset($vID) && !empty($vID)) :?>
					<button class="btn btn-warning base-icon-pencil float-right" onclick="<?php echo $APPTAG?>_loadEditFields(<?php echo $vID?>, false, false)"><span class="d-none d-sm-inline"> <?php echo JText::_('TEXT_EDIT')?></span></button>
				<?php endif;?>
				<button class="btn btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_setParent(0)" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>" data-backdrop="static" data-keyboard="false"><span class="d-none d-sm-inline"> <?php echo JText::_('TEXT_NEW')?></span></button>
			</span>
		<?php endif;?>
	</div>
	<div class="btn-group btn-group-sm float-right d-none d-md-flex">
		<a href="<?php echo JURI::root()?>apps/<?php echo $APPPATH?>" class="btn btn-default base-icon-cog"><span class="d-none d-lg-inline"> <?php echo JText::_('LIST_TITLE')?></span></a>
		<button class="btn btn-default base-icon-print" onclick="javascript:print()"></button>
	</div>
</div>
